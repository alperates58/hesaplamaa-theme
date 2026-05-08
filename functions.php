<?php
/**
 * Hesaplamaa Theme — functions.php v3.0
 */
if ( ! defined( 'ABSPATH' ) ) exit;

define( 'HTHEME_VERSION', '3.3.1' );
define( 'HTHEME_DIR',     get_template_directory() );
define( 'HTHEME_URL',     get_template_directory_uri() );

require_once HTHEME_DIR . '/inc/category-grid.php';
require_once HTHEME_DIR . '/inc/customizer.php';
require_once HTHEME_DIR . '/inc/github-updater.php';

new HTheme_Github_Updater();

/* ── Theme Setup ── */
function htheme_setup() {
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'custom-logo', [ 'flex-width'=>true, 'flex-height'=>true ] );
    add_theme_support( 'html5', ['search-form','comment-form','comment-list','gallery','caption','script','style'] );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'responsive-embeds' );
    register_nav_menus( [
        'primary'  => 'Ana Menü',
        'footer-1' => 'Footer Kolon 1',
        'footer-2' => 'Footer Kolon 2',
        'footer-3' => 'Footer Kolon 3',
    ] );
    add_image_size( 'htheme-card',  640, 280, true );
    add_image_size( 'htheme-thumb', 120, 120, true );
}
add_action( 'after_setup_theme', 'htheme_setup' );

/* ── Enqueue ── */
function htheme_enqueue() {
    // Her GitHub güncellemesinde cache kırmak için timestamp kullan.
    // HTHEME_VERSION sabiti OpCache'de kalmış olsa bile bu option her
    // güncellemede değiştiği için tarayıcı her zaman taze varlıkları alır.
    $asset_ver = get_option( 'htheme_last_update_version', HTHEME_VERSION );

    // Google Fonts — seçilen fontları dinamik yükle
    $hfont = get_theme_mod('htheme_heading_font', 'Plus Jakarta Sans');
    $bfont = get_theme_mod('htheme_body_font',    'Nunito');
    $fonts = array_unique([$hfont, $bfont]);
    $font_families = [];
    foreach ($fonts as $f) {
        $font_families[] = urlencode($f) . ':wght@400;500;600;700;800';
    }
    $font_url = 'https://fonts.googleapis.com/css2?family=' . implode('&family=', $font_families) . '&display=swap';
    wp_enqueue_style( 'hesaplamaa-fonts', $font_url, [], null );

    wp_enqueue_style( 'hesaplamaa-main',
        HTHEME_URL . '/assets/css/main.css', ['hesaplamaa-fonts'], $asset_ver );
    wp_enqueue_style( 'hesaplamaa-category-grid',
        HTHEME_URL . '/assets/css/category-grid.css', ['hesaplamaa-main'], $asset_ver );

    if ( ! wp_style_is('font-awesome','enqueued') && ! wp_style_is('font-awesome-cgp','enqueued') ) {
        wp_enqueue_style( 'font-awesome',
            'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css', [], '6.5.0' );
    }
    wp_enqueue_script( 'hesaplamaa-main',
        HTHEME_URL . '/assets/js/main.js', [], $asset_ver, true );
    wp_localize_script( 'hesaplamaa-main', 'hthemeShare', [
        'siteName' => get_bloginfo('name'),
        'copied'   => 'Sonuç bağlantısı kopyalandı.',
        'noResult' => 'Paylaşılacak bir sonuç bulunamadı. Lütfen önce hesaplama yapın.',
    ] );

    // Dinamik CSS — tüm Customizer değerleri buraya yansır
    wp_add_inline_style( 'hesaplamaa-main', htheme_dynamic_css() );
}
add_action( 'wp_enqueue_scripts', 'htheme_enqueue' );

/* ── Dinamik CSS — Customizer değerlerini CSS'e çevirir ── */
function htheme_dynamic_css() {
    // Değerleri al
    $primary        = get_theme_mod('htheme_primary_color',     '#3B6FE8');
    $accent         = get_theme_mod('htheme_accent_color',      '#FA6162');
    $bg             = get_theme_mod('htheme_bg_color',          '#F7F8FC');
    $surface        = get_theme_mod('htheme_surface_color',     '#FFFFFF');
    $border         = get_theme_mod('htheme_border_color',      '#E4E7F0');
    $text           = get_theme_mod('htheme_text_color',        '#1A1D2E');
    $text2          = get_theme_mod('htheme_text2_color',       '#4A5068');
    $hfont          = get_theme_mod('htheme_heading_font',      'Plus Jakarta Sans');
    $bfont          = get_theme_mod('htheme_body_font',         'Nunito');
    $base_fs        = absint(get_theme_mod('htheme_base_font_size', 16));
    $radius         = absint(get_theme_mod('htheme_border_radius',  12));

    $header_h       = absint(get_theme_mod('htheme_header_height',  72));
    $logo_h         = absint(get_theme_mod('htheme_logo_height',    44));
    $header_bg      = get_theme_mod('htheme_header_bg',    '#FFFFFF');
    $header_border  = get_theme_mod('htheme_header_border','#E4E7F0');

    $hero_bg        = get_theme_mod('htheme_hero_bg',          '#F7F8FC');
    $hero_pad       = absint(get_theme_mod('htheme_hero_padding_v', 48));

    $icon_size      = absint(get_theme_mod('htheme_icon_size',      76));
    $card_pad_v     = absint(get_theme_mod('htheme_card_padding_v', 26));
    $card_pad_h     = absint(get_theme_mod('htheme_card_padding_h', 14));
    $card_radius    = absint(get_theme_mod('htheme_card_radius',    20));
    $card_gap       = absint(get_theme_mod('htheme_card_gap',       14));
    $grid_cols_d    = absint(get_theme_mod('htheme_grid_cols_desktop', 3));
    $grid_cols_t    = absint(get_theme_mod('htheme_grid_cols_tablet',  2));
    $grid_cols_m    = absint(get_theme_mod('htheme_grid_cols_mobile',  1));
    $arc_subcard_cols = absint(get_theme_mod('htheme_archive_subcard_cols', 2));
    $arc_flat_cols    = absint(get_theme_mod('htheme_archive_flat_cols',    2));

    $sidebar_w      = absint(get_theme_mod('htheme_single_sidebar_width', 320));
    $single_max     = absint(get_theme_mod('htheme_single_max_width',     900));
    $ad_h           = absint(get_theme_mod('htheme_sidebar_ad_height',    200));

    $footer_bg      = get_theme_mod('htheme_footer_bg',   '#1A1D2E');
    $footer_txt     = get_theme_mod('htheme_footer_text', '#7a82a0');
    $footer_pad     = absint(get_theme_mod('htheme_footer_padding_v', 52));

    // Türetilmiş değerler
    $radius_lg = $radius + 6;
    $radius_xl = $radius + 12;
    $card_pad_b = max(10, round($card_pad_v * 0.8));

    return "
:root {
  --color-primary:    {$primary};
  --color-primary-d:  color-mix(in srgb, {$primary} 80%, #000);
  --color-primary-l:  color-mix(in srgb, {$primary} 14%, #fff);
  --color-primary-ll: color-mix(in srgb, {$primary}  6%, #fff);
  --color-accent:     {$accent};
  --color-accent-l:   color-mix(in srgb, {$accent}  14%, #fff);
  --color-bg:         {$bg};
  --color-surface:    {$surface};
  --color-border:     {$border};
  --color-text:       {$text};
  --color-text-2:     {$text2};
  --font-display:     \'{$hfont}\', sans-serif;
  --font-body:        \'{$bfont}\', sans-serif;
  --radius-sm:        {$radius}px;
  --radius-md:        {$radius}px;
  --radius-lg:        {$radius_lg}px;
  --radius-xl:        {$radius_xl}px;
  --header-h:         {$header_h}px;
  --logo-h:           {$logo_h}px;
  --icon-size:        {$icon_size}px;
}
html { font-size: {$base_fs}px; }
.site-header { height: {$header_h}px !important; background: {$header_bg} !important; border-bottom-color: {$header_border} !important; }
.header-inner { height: {$header_h}px !important; }
.site-logo img, .site-logo .custom-logo-link img, .custom-logo { height: {$logo_h}px !important; width: auto !important; max-width: 280px !important; }
.page-hero { background: {$hero_bg}; padding-top: {$hero_pad}px !important; padding-bottom: {$hero_pad}px !important; }
.hcg-grid { grid-template-columns: repeat({$grid_cols_d}, 1fr) !important; gap: {$card_gap}px !important; --icon-size: {$icon_size}px; }
.hcg-card { padding: {$card_pad_v}px {$card_pad_h}px {$card_pad_b}px !important; border-radius: {$card_radius}px !important; }
@media (max-width: 1024px) { .hcg-grid { grid-template-columns: repeat({$grid_cols_t}, 1fr) !important; } }
@media (max-width: 600px) { .hcg-grid { grid-template-columns: repeat({$grid_cols_m}, 1fr) !important; } }
.cat-subgrid { grid-template-columns: repeat({$arc_subcard_cols}, 1fr) !important; }
.tool-flatgrid { grid-template-columns: repeat({$arc_flat_cols}, 1fr) !important; }
@media (max-width: 720px) { .cat-subgrid, .tool-flatgrid { grid-template-columns: 1fr !important; } }
@media (min-width: 1024px) {
  .single-sidebar-right { display:grid !important; grid-template-columns: 1fr {$sidebar_w}px !important; gap: 32px !important; align-items: start !important; }
  .single-sidebar-left  { display:grid !important; grid-template-columns: {$sidebar_w}px 1fr !important; gap: 32px !important; align-items: start !important; }
  .single-full-width, .single-centered { display:block !important; max-width: {$single_max}px !important; }
}
.ad-slot { height: {$ad_h}px !important; }
.site-footer { background: {$footer_bg} !important; color: {$footer_txt}; padding-top: {$footer_pad}px !important; padding-bottom: " . round($footer_pad * 0.5) . "px !important; }
.footer-col ul li a, .footer-nav a { color: {$footer_txt}; }
.pagination { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; justify-content: center !important; align-items: center !important; gap: 6px !important; margin-top: 40px !important; }
.pagination .nav-links { display: flex !important; flex-direction: row !important; flex-wrap: wrap !important; justify-content: center !important; align-items: center !important; gap: 6px !important; }
.navigation.pagination { display: block !important; }
";
}

/* ── Widgets ── */
function htheme_widgets_init() {
    $d = [ 'before_widget'=>'<div class="sidebar-widget" id="%1$s">', 'after_widget'=>'</div>',
           'before_title'=>'<h3 class="sidebar-widget-title">', 'after_title'=>'</h3>' ];
    register_sidebar( $d + ['name'=>'Tekil Yazı Sidebar', 'id'=>'sidebar-single'] );
    register_sidebar( $d + ['name'=>'Arşiv Sidebar',      'id'=>'sidebar-archive'] );
}
add_action( 'widgets_init', 'htheme_widgets_init' );

function htheme_category_posts_per_page( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_category() ) {
        return;
    }

    $posts_per_page = min( 60, max( 3, absint( get_theme_mod( 'htheme_archive_posts_per_page', 12 ) ) ) );
    if ( $posts_per_page > 0 ) {
        $query->set( 'posts_per_page', $posts_per_page );
    }
}
add_action( 'pre_get_posts', 'htheme_category_posts_per_page' );

/* ── Helpers ── */
function htheme_cat_class( $slug ) {
    $map = ['matematik'=>'cat-matematik','saglik'=>'cat-saglik','sağlık'=>'cat-saglik',
            'finans'=>'cat-finans','zaman'=>'cat-zaman','kimya'=>'cat-kimya',
            'gunluk'=>'cat-gunluk','günlük'=>'cat-gunluk','muhasebe'=>'cat-muhasebe',
            'fizik'=>'cat-fizik','astroloji'=>'cat-astroloji','sinav'=>'cat-sinav',
            'sınav'=>'cat-sinav','diger'=>'cat-diger','diğer'=>'cat-diger'];
    foreach ( $map as $k => $c ) { if ( mb_strpos($slug,$k) !== false ) return $c; }
    return 'cat-diger';
}

function htheme_breadcrumb() {
    echo '<nav class="breadcrumb"><ol>';
    echo '<li><a href="'.esc_url(home_url('/')).'">Ana Sayfa</a></li>';
    if ( is_category() ) {
        $cat = get_queried_object();
        if ( $cat->parent ) { $p=get_category($cat->parent); echo '<li><a href="'.esc_url(get_category_link($p->term_id)).'">'.esc_html($p->name).'</a></li>'; }
        echo '<li aria-current="page">'.esc_html($cat->name).'</li>';
    } elseif ( is_single() ) {
        $cats = get_the_category();
        if ($cats) echo '<li><a href="'.esc_url(get_category_link($cats[0]->term_id)).'">'.esc_html($cats[0]->name).'</a></li>';
        echo '<li aria-current="page">'.esc_html(get_the_title()).'</li>';
    } elseif ( is_search() ) {
        echo '<li>Arama: '.esc_html(get_search_query()).'</li>';
    } elseif ( is_page() ) {
        echo '<li>'.esc_html(get_the_title()).'</li>';
    }
    echo '</ol></nav>';
}

/* ── İçindekiler Tablosu (TOC) ── */
global $htheme_toc_html;
$htheme_toc_html = '';

function htheme_build_toc( $content ) {
    global $htheme_toc_html;
    $htheme_toc_html = '';

    if ( ! is_single() ) return $content;
    if ( ! preg_match_all( '/<h([23])([^>]*)>(.*?)<\/h\1>/is', $content, $matches, PREG_SET_ORDER ) ) {
        return $content;
    }
    if ( count( $matches ) < 2 ) return $content;

    $items    = [];
    $new      = $content;
    $used_ids = [];

    foreach ( $matches as $m ) {
        $level   = (int) $m[1];
        $attrs   = $m[2];
        $inner   = $m[3];
        $text    = wp_strip_all_tags( $inner );
        $base_id = sanitize_title( $text );
        $id      = $base_id;
        $n       = 1;
        while ( in_array( $id, $used_ids, true ) ) { $id = $base_id . '-' . $n++; }
        $used_ids[] = $id;
        $items[]    = [ 'level' => $level, 'text' => $text, 'id' => $id ];

        if ( strpos( $attrs, 'id=' ) === false ) {
            $new = str_replace(
                $m[0],
                '<h' . $level . $attrs . ' id="' . esc_attr( $id ) . '">' . $inner . '</h' . $level . '>',
                $new
            );
        }
    }

    $html = '<ol class="toc-list">';
    foreach ( $items as $item ) {
        $cls  = $item['level'] === 3 ? ' class="toc-sub"' : '';
        $html .= '<li' . $cls . '><a href="#' . esc_attr( $item['id'] ) . '">' . esc_html( $item['text'] ) . '</a></li>';
    }
    $html .= '</ol>';
    $htheme_toc_html = $html;

    return $new;
}
add_filter( 'the_content', 'htheme_build_toc', 5 );

function htheme_get_toc() {
    global $htheme_toc_html;
    return $htheme_toc_html;
}

/* ── AdSense / Reklam ── */
add_action( 'wp_head', function() {
    $code = get_theme_mod( 'htheme_adsense_head', '' );
    if ( $code ) {
        echo "\n" . wp_unslash( $code ) . "\n";
    }
}, 1 );

function htheme_sidebar_ad( $slot_key, $show_toggle_key, $height_key = 'htheme_sidebar_ad_height' ) {
    if ( ! (bool) get_theme_mod( $show_toggle_key, true ) ) return;
    $code = wp_unslash( get_theme_mod( $slot_key, '' ) );
    $h    = absint( get_theme_mod( $height_key, 250 ) );
    echo '<div class="sidebar-ad">';
    echo '<span class="sidebar-ad__label">Reklam</span>';
    if ( $code ) {
        echo '<div class="ad-slot ad-slot--filled">' . $code . '</div>';
    } else {
        echo '<div class="ad-slot" style="min-height:' . $h . 'px"></div>';
    }
    echo '</div>';
}

add_filter( 'excerpt_length', fn() => 18 );
add_filter( 'excerpt_more',   fn() => '…' );
remove_action( 'wp_head',         'print_emoji_detection_script', 7 );
remove_action( 'wp_print_styles', 'print_emoji_styles' );

function htheme_plugin_conflict_notice() {
    if ( ! function_exists('is_plugin_active') ) {
        require_once ABSPATH . 'wp-admin/includes/plugin.php';
    }

    if ( is_plugin_active('category-grid-shortcode/category-grid-pro-plus.php') ) {
        echo '<div class="notice notice-warning is-dismissible"><p><strong>Hesaplamaa Teması:</strong> "Category Grid Pro+" hâlâ aktif — lütfen <a href="'.admin_url('plugins.php').'">devre dışı bırakın</a>.</p></div>';
    }
}
add_action( 'admin_notices', 'htheme_plugin_conflict_notice' );
