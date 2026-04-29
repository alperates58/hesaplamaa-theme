<?php
/**
 * GitHub updater panel for the Hesaplamaa theme.
 */
if ( ! defined( 'ABSPATH' ) ) exit;

class HTheme_Github_Updater {

    private $option_key = 'htheme_github_settings';
    private $page_slug  = 'hesaplamaa-theme-github';

    public function __construct() {
        add_action( 'admin_menu', [ $this, 'add_admin_page' ] );
        add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_admin_assets' ] );
        add_action( 'admin_init', [ $this, 'handle_settings_save' ] );
        add_action( 'admin_post_htheme_update_from_github', [ $this, 'handle_update' ] );
        add_action( 'wp_ajax_htheme_check_github_version', [ $this, 'ajax_check_version' ] );
    }

    public function add_admin_page() {
        add_theme_page(
            'Hesaplamaa Tema',
            'Hesaplamaa Tema',
            'manage_options',
            $this->page_slug,
            [ $this, 'render_page' ]
        );
    }

    public function enqueue_admin_assets( $hook ) {
        if ( 'appearance_page_' . $this->page_slug !== $hook ) {
            return;
        }

        $style_file = HTHEME_DIR . '/assets/css/admin-github.css';
        $script_file = HTHEME_DIR . '/assets/js/admin-github.js';
        $style_ver = file_exists( $style_file ) ? HTHEME_VERSION . '-' . filemtime( $style_file ) : HTHEME_VERSION;
        $script_ver = file_exists( $script_file ) ? HTHEME_VERSION . '-' . filemtime( $script_file ) : HTHEME_VERSION;

        wp_enqueue_style( 'htheme-admin-github', HTHEME_URL . '/assets/css/admin-github.css', [], $style_ver );
        wp_enqueue_script( 'htheme-admin-github', HTHEME_URL . '/assets/js/admin-github.js', [ 'jquery' ], $script_ver, true );
        wp_localize_script(
            'htheme-admin-github',
            'hthemeGithub',
            [
                'ajaxurl'  => admin_url( 'admin-ajax.php' ),
                'nonce'    => wp_create_nonce( 'htheme_github_ajax_nonce' ),
                'checking' => 'Kontrol ediliyor...',
                'norepo'   => 'Once repo adresini kaydedin.',
            ]
        );
    }

    public function get_settings() {
        return wp_parse_args(
            get_option( $this->option_key, [] ),
            [
                'repo'   => '',
                'branch' => 'main',
                'token'  => '',
            ]
        );
    }

    public function save_settings( $data ) {
        update_option(
            $this->option_key,
            [
                'repo'   => sanitize_text_field( wp_unslash( $data['repo'] ?? '' ) ),
                'branch' => sanitize_text_field( wp_unslash( $data['branch'] ?? 'main' ) ),
                'token'  => sanitize_text_field( wp_unslash( $data['token'] ?? '' ) ),
            ],
            false
        );
    }

    public function handle_settings_save() {
        if (
            ! isset( $_POST['htheme_save_github'] ) ||
            ! current_user_can( 'manage_options' )
        ) {
            return;
        }

        check_admin_referer( 'htheme_save_github_settings' );
        $this->save_settings( $_POST );

        wp_safe_redirect( admin_url( 'themes.php?page=' . $this->page_slug . '&saved=1' ) );
        exit;
    }

    public function get_remote_version() {
        $settings = $this->get_settings();
        if ( empty( $settings['repo'] ) ) {
            return null;
        }

        $url  = 'https://api.github.com/repos/' . rawurlencode( $settings['repo'] ) . '/commits/' . rawurlencode( $settings['branch'] );
        $url  = str_replace( '%2F', '/', $url );
        $args = [
            'timeout' => 30,
            'headers' => [
                'Accept'     => 'application/vnd.github+json',
                'User-Agent' => 'hesaplamaa-theme',
            ],
        ];

        if ( ! empty( $settings['token'] ) ) {
            $args['headers']['Authorization'] = 'Bearer ' . $settings['token'];
        }

        $response = wp_remote_get( $url, $args );
        if ( is_wp_error( $response ) || 200 !== wp_remote_retrieve_response_code( $response ) ) {
            return null;
        }

        $body = json_decode( wp_remote_retrieve_body( $response ), true );
        return $body['sha'] ?? null;
    }

    public function ajax_check_version() {
        check_ajax_referer( 'htheme_github_ajax_nonce', 'nonce' );

        if ( ! current_user_can( 'manage_options' ) ) {
            wp_send_json_error( [ 'message' => 'Yetkisiz.' ], 403 );
        }

        $sha = $this->get_remote_version();
        wp_send_json_success( [ 'sha' => $sha ? substr( $sha, 0, 7 ) : null ] );
    }

    public function handle_update() {
        if ( ! current_user_can( 'manage_options' ) ) {
            wp_die( 'Yetkisiz.' );
        }

        check_admin_referer( 'htheme_update_from_github' );
        $result = $this->download_and_install( $this->get_settings() );
        $status = true === $result ? 'success' : rawurlencode( $result );

        wp_safe_redirect( admin_url( 'themes.php?page=' . $this->page_slug . '&update=' . $status ) );
        exit;
    }

    private function download_and_install( $settings ) {
        if ( empty( $settings['repo'] ) ) {
            return 'Repo ayari eksik.';
        }

        require_once ABSPATH . 'wp-admin/includes/file.php';

        $zip_url = 'https://github.com/' . $settings['repo'] . '/archive/refs/heads/' . rawurlencode( $settings['branch'] ) . '.zip';
        $args    = [
            'timeout' => 60,
            'headers' => [
                'User-Agent' => 'hesaplamaa-theme',
            ],
        ];

        if ( ! empty( $settings['token'] ) ) {
            $args['headers']['Authorization'] = 'Bearer ' . $settings['token'];
        }

        add_filter(
            'http_request_args',
            function( $request_args, $url ) use ( $zip_url, $args ) {
                if ( $zip_url === $url ) {
                    $request_args['timeout'] = $args['timeout'];
                    $request_args['headers'] = array_merge( $request_args['headers'] ?? [], $args['headers'] );
                }
                return $request_args;
            },
            10,
            2
        );

        $tmp = download_url( $zip_url, 60 );
        if ( is_wp_error( $tmp ) ) {
            return $tmp->get_error_message();
        }

        global $wp_filesystem;
        WP_Filesystem();

        $theme_root = get_theme_root( get_template() );
        $dest       = get_template_directory();
        $unzip      = unzip_file( $tmp, $theme_root );
        @unlink( $tmp );

        if ( is_wp_error( $unzip ) ) {
            return $unzip->get_error_message();
        }

        $repo_name     = basename( $settings['repo'] );
        $branch_dir    = str_replace( '/', '-', $settings['branch'] );
        $extracted_dir = trailingslashit( $theme_root ) . $repo_name . '-' . $branch_dir;

        if ( ! is_dir( $extracted_dir ) ) {
            return 'Indirilen paket acildi ancak beklenen klasor bulunamadi.';
        }

        $wp_filesystem->delete( $dest, true );

        if ( ! @rename( $extracted_dir, $dest ) ) {
            return 'Yeni tema klasoru yerine tasinamadi.';
        }

        $remote_sha = $this->get_remote_version();
        update_option( 'htheme_last_update', current_time( 'mysql' ), false );
        update_option( 'htheme_last_update_version', (string) time(), false );
        if ( $remote_sha ) {
            update_option( 'htheme_last_update_sha', $remote_sha, false );
        }

        if ( function_exists( 'wp_clean_themes_cache' ) ) {
            wp_clean_themes_cache( true );
        }
        if ( function_exists( 'wp_cache_flush' ) ) {
            wp_cache_flush();
        }
        if ( function_exists( 'opcache_reset' ) ) {
            @opcache_reset();
        }

        return true;
    }

    public function render_page() {
        $settings = $this->get_settings();
        $saved    = isset( $_GET['saved'] );
        $update   = sanitize_text_field( wp_unslash( $_GET['update'] ?? '' ) );
        $last     = get_option( 'htheme_last_update', '-' );
        $last_sha = get_option( 'htheme_last_update_sha', '' );
        ?>
        <div class="wrap htheme-admin-wrap">
            <div class="htheme-page-head">
                <div>
                    <h1>Hesaplamaa Tema</h1>
                    <p class="htheme-page-subtitle">Tema dosyalarini ve GitHub guncellemelerini tek panelden yonetin.</p>
                </div>
                <div class="htheme-page-publisher">
                    <span class="htheme-page-publisher-label">Yayinci</span>
                    <strong>Alper ATES</strong>
                </div>
            </div>

            <?php if ( $saved ) : ?>
                <div class="notice notice-success is-dismissible"><p>Ayarlar kaydedildi.</p></div>
            <?php endif; ?>

            <?php if ( 'success' === $update ) : ?>
                <div class="notice notice-success is-dismissible"><p>Tema GitHub uzerinden basariyla guncellendi.</p></div>
            <?php elseif ( $update ) : ?>
                <div class="notice notice-error is-dismissible"><p>Guncelleme hatasi: <?php echo esc_html( rawurldecode( $update ) ); ?></p></div>
            <?php endif; ?>

            <div class="htheme-card">
                <h2>GitHub Baglantisi</h2>

                <form method="post">
                    <?php wp_nonce_field( 'htheme_save_github_settings' ); ?>

                    <table class="form-table">
                        <tr>
                            <th><label for="repo">Repository</label></th>
                            <td>
                                <input type="text" id="repo" name="repo" value="<?php echo esc_attr( $settings['repo'] ); ?>" placeholder="alperates58/hesaplamaa-theme" class="regular-text" />
                                <p class="description">GitHub kullanici adi ve repository adini birlikte girin.</p>
                            </td>
                        </tr>
                        <tr>
                            <th><label for="branch">Branch</label></th>
                            <td>
                                <input type="text" id="branch" name="branch" value="<?php echo esc_attr( $settings['branch'] ); ?>" placeholder="main" class="small-text" />
                            </td>
                        </tr>
                        <tr>
                            <th><label for="token">Token</label></th>
                            <td>
                                <input type="password" id="token" name="token" value="<?php echo esc_attr( $settings['token'] ); ?>" placeholder="ghp_xxxx" class="regular-text" autocomplete="off" />
                                <p class="description">Public repo icin bos birakabilirsiniz.</p>
                            </td>
                        </tr>
                    </table>

                    <p class="submit">
                        <button type="submit" name="htheme_save_github" class="button button-primary">Kaydet</button>
                        <button type="button" id="htheme-check-version" class="button">Son Versiyonu Kontrol Et</button>
                        <span id="htheme-version-result" class="htheme-version-result"></span>
                    </p>
                </form>
            </div>

            <div class="htheme-card htheme-update-box">
                <h2>Guncelleme</h2>
                <p>Son guncelleme: <strong><?php echo esc_html( $last ); ?></strong></p>
                <?php if ( $last_sha ) : ?>
                    <p>Son kurulan commit: <code><?php echo esc_html( substr( $last_sha, 0, 7 ) ); ?></code></p>
                <?php endif; ?>
                <p>GitHub uzerindeki en guncel surumu cekip temayi bu panelden yenileyebilirsiniz.</p>

                <form method="post" action="<?php echo esc_url( admin_url( 'admin-post.php' ) ); ?>">
                    <?php wp_nonce_field( 'htheme_update_from_github' ); ?>
                    <input type="hidden" name="action" value="htheme_update_from_github" />
                    <button type="submit" class="button button-primary htheme-update-btn" onclick="return confirm('GitHub uzerinden tema guncellemesi yapmak istediginize emin misiniz?')">
                        GitHub'dan Guncelle
                    </button>
                </form>
            </div>
        </div>
        <?php
    }
}
