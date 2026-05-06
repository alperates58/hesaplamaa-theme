<?php
/**
 * Hesaplamaa Theme — Customizer v3.1
 * WP_Customize_Control sadece customize_register hook'u içinde tanımlanır
 */
if ( ! defined( 'ABSPATH' ) ) exit;

add_action( 'customize_register', 'htheme_customizer_register' );

function htheme_customizer_register( $wp_customize ) {

    /* ═══════════════════════════════════════════════════════
       ÖZEL SLIDER KONTROLÜ — burada tanımlanıyor (hook içinde)
    ═══════════════════════════════════════════════════════ */
    class Htheme_Slider_Control extends WP_Customize_Control {
        public $type = 'htheme_slider';
        public $min  = 0;
        public $max  = 200;
        public $step = 1;
        public $unit = 'px';

        public function render_content() {
            $val = $this->value();
            ?>
            <label>
                <span class="customize-control-title"><?php echo esc_html( $this->label ); ?></span>
                <?php if ( $this->description ) : ?>
                    <span class="description customize-control-description"><?php echo esc_html( $this->description ); ?></span>
                <?php endif; ?>
            </label>
            <div style="display:flex;align-items:center;gap:10px;margin-top:6px">
                <input type="range"
                       min="<?php echo esc_attr( $this->min ); ?>"
                       max="<?php echo esc_attr( $this->max ); ?>"
                       step="<?php echo esc_attr( $this->step ); ?>"
                       value="<?php echo esc_attr( $val ); ?>"
                       style="flex:1;accent-color:#3B6FE8"
                       oninput="this.nextElementSibling.textContent = this.value + '<?php echo esc_js( $this->unit ); ?>'"
                       <?php $this->link(); ?>>
                <span style="min-width:48px;text-align:right;font-weight:700;font-size:13px;color:#3B6FE8;background:#EEF3FD;padding:3px 8px;border-radius:6px">
                    <?php echo esc_html( $val . $this->unit ); ?>
                </span>
            </div>
            <?php
        }
    }

    /* ── ANA PANEL ── */
    $wp_customize->add_panel( 'htheme_panel', [
        'title'    => '🎨 Hesaplamaa Tema Ayarları',
        'priority' => 5,
    ] );

    /* ══════════════════════════════════════════
       1. RENKLER & TİPOGRAFİ
    ══════════════════════════════════════════ */
    $wp_customize->add_section( 'htheme_colors', [
        'title' => '🎨 Renkler & Tipografi', 'panel' => 'htheme_panel', 'priority' => 10,
    ] );
    c_color( $wp_customize, 'htheme_primary_color', 'Ana Renk (linkler, butonlar)', '#3B6FE8' );
    c_color( $wp_customize, 'htheme_accent_color',  'Vurgu Rengi (kategori kartları)', '#FA6162' );
    c_color( $wp_customize, 'htheme_bg_color',      'Sayfa Arka Plan Rengi', '#F7F8FC' );
    c_color( $wp_customize, 'htheme_surface_color', 'Kart / Yüzey Arka Planı', '#FFFFFF' );
    c_color( $wp_customize, 'htheme_border_color',  'Kenarlık Rengi', '#E4E7F0' );
    c_color( $wp_customize, 'htheme_text_color',    'Ana Metin Rengi', '#1A1D2E' );
    c_color( $wp_customize, 'htheme_text2_color',   'İkincil Metin Rengi', '#4A5068' );

    c_select( $wp_customize, 'htheme_heading_font', 'Başlık Fontu', 'Plus Jakarta Sans', [
        'Plus Jakarta Sans' => 'Plus Jakarta Sans (varsayılan)',
        'Nunito'   => 'Nunito',   'Poppins'    => 'Poppins',
        'Montserrat'=> 'Montserrat','Inter'    => 'Inter',
        'Roboto'   => 'Roboto',   'Open Sans'  => 'Open Sans',
        'Raleway'  => 'Raleway',  'Lato'       => 'Lato',
    ], 'htheme_colors' );

    c_select( $wp_customize, 'htheme_body_font', 'Gövde Metin Fontu', 'Nunito', [
        'Nunito'   => 'Nunito (varsayılan)', 'Plus Jakarta Sans' => 'Plus Jakarta Sans',
        'Poppins'  => 'Poppins',  'Inter'      => 'Inter',
        'Roboto'   => 'Roboto',   'Open Sans'  => 'Open Sans',
        'Lato'     => 'Lato',
    ], 'htheme_colors' );

    c_slider( $wp_customize, 'htheme_base_font_size', 'Temel Font Boyutu', 16, 13, 20, 1, 'px', 'htheme_colors', Htheme_Slider_Control::class );
    c_slider( $wp_customize, 'htheme_border_radius',  'Genel Köşe Yuvarlatma', 12, 0, 28, 2, 'px', 'htheme_colors', Htheme_Slider_Control::class );

    /* ══════════════════════════════════════════
       2. HEADER & LOGO
    ══════════════════════════════════════════ */
    $wp_customize->add_section( 'htheme_header', [
        'title' => '🔝 Header & Logo', 'panel' => 'htheme_panel', 'priority' => 20,
    ] );
    c_slider( $wp_customize, 'htheme_header_height', 'Header Yüksekliği', 72, 50, 120, 1, 'px', 'htheme_header', Htheme_Slider_Control::class );
    c_slider( $wp_customize, 'htheme_logo_height',   'Logo Yüksekliği',   44, 20, 110, 1, 'px', 'htheme_header', Htheme_Slider_Control::class );
    c_color(  $wp_customize, 'htheme_header_bg',     'Header Arka Planı', '#FFFFFF', 'htheme_header' );
    c_color(  $wp_customize, 'htheme_header_border', 'Header Alt Çizgi',  '#E4E7F0', 'htheme_header' );
    c_checkbox( $wp_customize, 'htheme_header_sticky',     'Sticky Header (yapışık)', true, 'htheme_header' );
    c_checkbox( $wp_customize, 'htheme_header_shadow',     'Header Gölgesi', true, 'htheme_header' );
    c_checkbox( $wp_customize, 'htheme_header_hide_scroll','Aşağı kaydırınca header gizlensin', true, 'htheme_header' );
    c_checkbox( $wp_customize, 'htheme_search_in_header',  'Arama İkonu Göster', true, 'htheme_header' );

    /* ══════════════════════════════════════════
       3. ANA SAYFA
    ══════════════════════════════════════════ */
    $wp_customize->add_section( 'htheme_homepage', [
        'title' => '🏠 Ana Sayfa', 'panel' => 'htheme_panel', 'priority' => 30,
    ] );
    c_checkbox( $wp_customize, 'htheme_show_hero',    'Hero arama bölümünü göster', true, 'htheme_homepage' );
    c_text(     $wp_customize, 'htheme_hero_title',   'Hero Başlık', 'En Kullanışlı Hesaplama Araçları', 'htheme_homepage' );
    c_textarea( $wp_customize, 'htheme_hero_subtitle','Hero Alt Metin',
        'Matematikten finansal hesaplamalara, sağlıktan günlük yaşama kadar — hızlı, doğru ve güvenilir.', 'htheme_homepage' );
    c_color(    $wp_customize, 'htheme_hero_bg',      'Hero Arka Planı', '#F7F8FC', 'htheme_homepage' );
    c_slider(   $wp_customize, 'htheme_hero_padding_v','Hero Dikey Boşluk', 48, 16, 120, 4, 'px', 'htheme_homepage', Htheme_Slider_Control::class );
    c_checkbox( $wp_customize, 'htheme_show_latest',  '"Son Eklenen Araçlar" bölümü', true, 'htheme_homepage' );
    c_slider(   $wp_customize, 'htheme_latest_count', 'Kaç son yazı gösterilsin', 6, 3, 24, 1, '', 'htheme_homepage', Htheme_Slider_Control::class );
    c_select(   $wp_customize, 'htheme_latest_cols',  'Son yazılar sütun sayısı', '3col', [
        '1col'=>'1 Sütun','2col'=>'2 Sütun','3col'=>'3 Sütun','4col'=>'4 Sütun',
    ], 'htheme_homepage' );

    /* ══════════════════════════════════════════
       4. KATEGORİ GRİD
    ══════════════════════════════════════════ */
    $wp_customize->add_section( 'htheme_catgrid', [
        'title' => '🗂 Kategori Grid', 'panel' => 'htheme_panel', 'priority' => 40,
    ] );
    c_select( $wp_customize, 'htheme_grid_style', 'Kart Stili', 'card-modern', [
        'card-modern'  => '⬛ Modern (ikon üstte, ortalı)',
        'card-inline'  => '▶️ Yatay (ikon solda)',
        'card-minimal' => '○ Minimal',
        'card-hero'    => '🏆 Hero (büyük)',
    ], 'htheme_catgrid' );
    c_select( $wp_customize, 'htheme_icon_shape', 'İkon Arka Plan Şekli', 'rounded', [
        'circle'=>'⬤ Daire','rounded'=>'▣ Yuvarlatılmış Kare',
        'square'=>'■ Kare','none'=>'✕ Arka plan yok',
    ], 'htheme_catgrid' );
    c_select( $wp_customize, 'htheme_grid_cols_desktop', 'Masaüstü Kolon Sayısı', '6', [
        '2'=>'2 Kolon','3'=>'3 Kolon','4'=>'4 Kolon','5'=>'5 Kolon','6'=>'6 Kolon',
    ], 'htheme_catgrid' );
    c_select( $wp_customize, 'htheme_grid_cols_tablet', 'Tablet Kolon Sayısı (≤1024px)', '3', [
        '2'=>'2 Kolon','3'=>'3 Kolon','4'=>'4 Kolon',
    ], 'htheme_catgrid' );
    c_select( $wp_customize, 'htheme_grid_cols_mobile', 'Mobil Kolon Sayısı (≤600px)', '2', [
        '1'=>'1 Kolon','2'=>'2 Kolon','3'=>'3 Kolon',
    ], 'htheme_catgrid' );
    c_slider( $wp_customize, 'htheme_icon_size',      'İkon Alanı Boyutu',    76, 40, 130, 4, 'px', 'htheme_catgrid', Htheme_Slider_Control::class );
    c_slider( $wp_customize, 'htheme_card_padding_v', 'Kart Dikey Padding',   26, 10,  52, 2, 'px', 'htheme_catgrid', Htheme_Slider_Control::class );
    c_slider( $wp_customize, 'htheme_card_padding_h', 'Kart Yatay Padding',   14,  8,  36, 2, 'px', 'htheme_catgrid', Htheme_Slider_Control::class );
    c_slider( $wp_customize, 'htheme_card_radius',    'Kart Köşe Yuvarlatma', 20,  0,  36, 2, 'px', 'htheme_catgrid', Htheme_Slider_Control::class );
    c_slider( $wp_customize, 'htheme_card_gap',       'Kartlar Arası Boşluk', 14,  4,  32, 2, 'px', 'htheme_catgrid', Htheme_Slider_Control::class );
    c_checkbox( $wp_customize, 'htheme_show_desc_grid',  'Açıklama metnini göster', true,  'htheme_catgrid' );
    c_checkbox( $wp_customize, 'htheme_show_count_grid', 'Araç sayısını göster', true, 'htheme_catgrid' );
    c_select( $wp_customize, 'htheme_card_count_text', '"Araç" yazısı', 'araç', [
        'araç'=>'araç','hesaplama'=>'hesaplama','içerik'=>'içerik','tool'=>'tool',
    ], 'htheme_catgrid' );
    c_checkbox( $wp_customize, 'htheme_card_hover_lift', 'Hover: kart yükselsin', true, 'htheme_catgrid' );
    c_checkbox( $wp_customize, 'htheme_card_top_strip',  'Hover: üst renkli şerit', true, 'htheme_catgrid' );
    c_checkbox( $wp_customize, 'htheme_card_bg_effect',  'Hover: arka plan tonu', true, 'htheme_catgrid' );

    /* ══════════════════════════════════════════
       5. TEKİL YAZI
    ══════════════════════════════════════════ */
    $wp_customize->add_section( 'htheme_single', [
        'title' => '📄 Tekil Yazı', 'panel' => 'htheme_panel', 'priority' => 50,
    ] );
    c_select( $wp_customize, 'htheme_single_layout', 'Sayfa Düzeni', 'sidebar-right', [
        'sidebar-right'=>'▐ Sidebar Sağda','sidebar-left'=>'▌ Sidebar Solda',
        'full-width'=>'◻ Tam Genişlik','centered'=>'▭ Ortalanmış Dar',
    ], 'htheme_single' );
    c_slider( $wp_customize, 'htheme_single_sidebar_width', 'Sidebar Genişliği', 320, 220, 420, 10, 'px', 'htheme_single', Htheme_Slider_Control::class );
    c_slider( $wp_customize, 'htheme_single_max_width',     'İçerik Maks. Genişliği', 900, 600, 1200, 20, 'px', 'htheme_single', Htheme_Slider_Control::class );
    c_checkbox( $wp_customize, 'htheme_show_breadcrumb', 'Breadcrumb göster', true, 'htheme_single' );
    c_checkbox( $wp_customize, 'htheme_show_cat_tags',   'Kategori etiketleri', true, 'htheme_single' );
    c_checkbox( $wp_customize, 'htheme_show_post_tags',  'Yazı etiketleri', true, 'htheme_single' );
    c_checkbox( $wp_customize, 'htheme_show_result_share', 'Sonuç paylaşım butonları', true, 'htheme_single' );
    c_checkbox( $wp_customize, 'htheme_show_related',    'İlgili yazılar bölümü', true, 'htheme_single' );
    c_slider(   $wp_customize, 'htheme_related_count',   'İlgili yazı sayısı', 3, 2, 8, 1, '', 'htheme_single', Htheme_Slider_Control::class );
    c_select(   $wp_customize, 'htheme_related_cols',    'İlgili yazılar sütun', '2', [
        '2'=>'2 Sütun','3'=>'3 Sütun','4'=>'4 Sütun',
    ], 'htheme_single' );
    c_checkbox( $wp_customize, 'htheme_sidebar_toc',          'Sidebar: İçindekiler (TOC)', true,  'htheme_single' );
    c_checkbox( $wp_customize, 'htheme_sidebar_ad',           'Sidebar: Reklam Alanı 1',   true,  'htheme_single' );
    c_checkbox( $wp_customize, 'htheme_sidebar_ad2',          'Sidebar: Reklam Alanı 2',   true,  'htheme_single' );
    c_slider(   $wp_customize, 'htheme_sidebar_ad_height',    'Reklam alanı yüksekliği', 200, 80, 400, 10, 'px', 'htheme_single', Htheme_Slider_Control::class );
    c_checkbox( $wp_customize, 'htheme_sidebar_widget_area',  'Sidebar: Widget Alanı (tekil-yazı-sidebar)', false, 'htheme_single' );

    /* ══════════════════════════════════════════
       6. ARŞİV
    ══════════════════════════════════════════ */
    $wp_customize->add_section( 'htheme_archive', [
        'title' => '📂 Arşiv / Kategori', 'panel' => 'htheme_panel', 'priority' => 60,
    ] );
    c_select( $wp_customize, 'htheme_archive_layout', 'Yazı Kartı Sütun Sayısı', '2col', [
        '1col'=>'1 Sütun (liste)','2col'=>'2 Sütun','3col'=>'3 Sütun','4col'=>'4 Sütun',
    ], 'htheme_archive' );
    c_checkbox( $wp_customize, 'htheme_archive_show_thumb',   'Görsel göster', true, 'htheme_archive' );
    c_checkbox( $wp_customize, 'htheme_archive_show_excerpt', 'Özet göster', true, 'htheme_archive' );
    c_checkbox( $wp_customize, 'htheme_archive_show_sidebar', 'Sidebar göster', false, 'htheme_archive' );

    /* ══════════════════════════════════════════
       7. FOOTER
    ══════════════════════════════════════════ */
    $wp_customize->add_section( 'htheme_footer', [
        'title' => '⬇️ Footer', 'panel' => 'htheme_panel', 'priority' => 70,
    ] );
    c_select( $wp_customize, 'htheme_footer_layout', 'Footer Düzeni', 'cols-3', [
        'cols-0'=>'✕ Footer yok','cols-1'=>'1 Kolon',
        'cols-2'=>'2 Kolon','cols-3'=>'3 Kolon','cols-4'=>'4 Kolon',
    ], 'htheme_footer' );
    c_color(    $wp_customize, 'htheme_footer_bg',      'Footer Arka Plan', '#1A1D2E', 'htheme_footer' );
    c_color(    $wp_customize, 'htheme_footer_text',    'Footer Metin', '#7a82a0', 'htheme_footer' );
    c_slider(   $wp_customize, 'htheme_footer_padding_v','Footer Dikey Boşluk', 52, 20, 100, 4, 'px', 'htheme_footer', Htheme_Slider_Control::class );
    c_textarea( $wp_customize, 'htheme_footer_brand_text', 'Marka Açıklaması',
        'Matematikten finansal hesaplamalara, sağlıktan günlük yaşama kadar geniş bir yelpazede hesaplama araçları.', 'htheme_footer' );
    c_text( $wp_customize, 'htheme_footer_copyright', 'Copyright Metni',
        '© {yıl} Hesaplamaa.com — Tüm hakları saklıdır.', 'htheme_footer' );
    c_checkbox( $wp_customize, 'htheme_footer_col1_enable', 'Kolon 1 göster', true, 'htheme_footer' );
    c_text(     $wp_customize, 'htheme_footer_col1_title',  'Kolon 1 Başlık', 'Hızlı Linkler', 'htheme_footer' );
    c_checkbox( $wp_customize, 'htheme_footer_col2_enable', 'Kolon 2 göster', true, 'htheme_footer' );
    c_text(     $wp_customize, 'htheme_footer_col2_title',  'Kolon 2 Başlık', 'Kategoriler', 'htheme_footer' );
    c_checkbox( $wp_customize, 'htheme_footer_col3_enable', 'Kolon 3 göster', true, 'htheme_footer' );
    c_text(     $wp_customize, 'htheme_footer_col3_title',  'Kolon 3 Başlık', 'Kurumsal', 'htheme_footer' );
}

/* ═══════════════════════════════════════════════════════
   YARDIMCI KAYIT FONKSİYONLARI
═══════════════════════════════════════════════════════ */
function c_color( $wpc, $id, $label, $default, $section = 'htheme_colors' ) {
    $wpc->add_setting( $id, ['default'=>$default,'sanitize_callback'=>'sanitize_hex_color','transport'=>'postMessage'] );
    $wpc->add_control( new WP_Customize_Color_Control( $wpc, $id, ['label'=>$label,'section'=>$section] ) );
}
function c_text( $wpc, $id, $label, $default, $section ) {
    $wpc->add_setting( $id, ['default'=>$default,'sanitize_callback'=>'sanitize_text_field'] );
    $wpc->add_control( $id, ['label'=>$label,'section'=>$section,'type'=>'text'] );
}
function c_textarea( $wpc, $id, $label, $default, $section ) {
    $wpc->add_setting( $id, ['default'=>$default,'sanitize_callback'=>'sanitize_textarea_field'] );
    $wpc->add_control( $id, ['label'=>$label,'section'=>$section,'type'=>'textarea'] );
}
function c_checkbox( $wpc, $id, $label, $default, $section ) {
    $wpc->add_setting( $id, ['default'=>$default,'sanitize_callback'=>'htheme_sanitize_cb'] );
    $wpc->add_control( $id, ['label'=>$label,'section'=>$section,'type'=>'checkbox'] );
}
function c_select( $wpc, $id, $label, $default, $choices, $section ) {
    $wpc->add_setting( $id, ['default'=>$default,'sanitize_callback'=>'sanitize_text_field'] );
    $wpc->add_control( $id, ['label'=>$label,'section'=>$section,'type'=>'select','choices'=>$choices] );
}
function c_slider( $wpc, $id, $label, $default, $min, $max, $step, $unit, $section, $class ) {
    $wpc->add_setting( $id, ['default'=>$default,'sanitize_callback'=>'absint','transport'=>'postMessage'] );
    $wpc->add_control( new $class( $wpc, $id, [
        'label'=>$label,'section'=>$section,'min'=>$min,'max'=>$max,'step'=>$step,'unit'=>$unit,
    ] ) );
}
function htheme_sanitize_cb( $v ) { return (bool)$v; }

/* ═══════════════════════════════════════════════════════
   LIVE PREVIEW JS
═══════════════════════════════════════════════════════ */
add_action( 'customize_preview_init', function() {
    wp_add_inline_script( 'customize-preview', htheme_preview_js() );
} );

function htheme_preview_js() { return "
(function(api){
    function cssVar(n,v){ document.documentElement.style.setProperty(n,v); }
    function setStyle(id,css){ var el=document.getElementById('hls-'+id); if(!el){el=document.createElement('style');el.id='hls-'+id;document.head.appendChild(el);} el.textContent=css; }

    api('htheme_primary_color',  function(v){ v.bind(function(c){ cssVar('--color-primary',c); }); });
    api('htheme_accent_color',   function(v){ v.bind(function(c){ cssVar('--color-accent',c); }); });
    api('htheme_bg_color',       function(v){ v.bind(function(c){ cssVar('--color-bg',c); }); });
    api('htheme_surface_color',  function(v){ v.bind(function(c){ cssVar('--color-surface',c); }); });
    api('htheme_border_color',   function(v){ v.bind(function(c){ cssVar('--color-border',c); }); });
    api('htheme_text_color',     function(v){ v.bind(function(c){ cssVar('--color-text',c); }); });
    api('htheme_text2_color',    function(v){ v.bind(function(c){ cssVar('--color-text-2',c); }); });
    api('htheme_header_bg',      function(v){ v.bind(function(c){ setStyle('hbg','.site-header{background:'+c+'}'); }); });
    api('htheme_header_border',  function(v){ v.bind(function(c){ setStyle('hbd','.site-header{border-bottom-color:'+c+'}'); }); });
    api('htheme_hero_bg',        function(v){ v.bind(function(c){ setStyle('hro','.page-hero{background:'+c+'}'); }); });

    api('htheme_header_height',  function(v){ v.bind(function(n){ cssVar('--header-h',n+'px'); }); });
    api('htheme_logo_height',    function(v){ v.bind(function(n){
        cssVar('--logo-h',n+'px');
        document.querySelectorAll('.site-logo img,.custom-logo-link img').forEach(function(i){i.style.height=n+'px';});
    }); });
    api('htheme_base_font_size', function(v){ v.bind(function(n){ document.documentElement.style.fontSize=n+'px'; }); });
    api('htheme_border_radius',  function(v){ v.bind(function(n){ cssVar('--radius-md',n+'px');cssVar('--radius-lg',(+n+6)+'px');cssVar('--radius-xl',(+n+12)+'px'); }); });
    api('htheme_hero_padding_v', function(v){ v.bind(function(n){ setStyle('hpv','.page-hero{padding-top:'+n+'px;padding-bottom:'+n+'px}'); }); });
    api('htheme_icon_size',      function(v){ v.bind(function(n){ document.querySelectorAll('.hcg-grid').forEach(function(g){g.style.setProperty('--icon-size',n+'px');}); }); });
    api('htheme_card_padding_v', function(v){ v.bind(function(n){ setStyle('cpv','.hcg-card{padding-top:'+n+'px;padding-bottom:'+Math.round(n*.8)+'px}'); }); });
    api('htheme_card_padding_h', function(v){ v.bind(function(n){ setStyle('cph','.hcg-card{padding-left:'+n+'px;padding-right:'+n+'px}'); }); });
    api('htheme_card_radius',    function(v){ v.bind(function(n){ setStyle('cr','.hcg-card{border-radius:'+n+'px}'); }); });
    api('htheme_card_gap',       function(v){ v.bind(function(n){ setStyle('cg','.hcg-grid{gap:'+n+'px}'); }); });
    api('htheme_grid_cols_desktop',function(v){ v.bind(function(n){ setStyle('gcd','.hcg-grid{grid-template-columns:repeat('+n+',1fr)}'); }); });
    api('htheme_single_sidebar_width',function(v){ v.bind(function(n){ setStyle('ssw','.single-sidebar-right{grid-template-columns:1fr '+n+'px}.single-sidebar-left{grid-template-columns:'+n+'px 1fr}'); }); });
    api('htheme_single_max_width',function(v){ v.bind(function(n){ setStyle('smw','.single-full-width,.single-centered{max-width:'+n+'px}'); }); });
    api('htheme_sidebar_ad_height',function(v){ v.bind(function(n){ setStyle('sah','.ad-slot{height:'+n+'px}'); }); });
    api('htheme_footer_padding_v',function(v){ v.bind(function(n){ setStyle('fpv','.site-footer{padding-top:'+n+'px;padding-bottom:'+Math.round(n*.5)+'px}'); }); });
}(wp.customize));
"; }

add_action( 'customize_register', 'htheme_archive_count_customizer_register', 20 );
function htheme_archive_count_customizer_register( $wp_customize ) {
    $wp_customize->add_setting( 'htheme_archive_posts_per_page', [
        'default'           => 12,
        'sanitize_callback' => 'htheme_sanitize_posts_per_page',
    ] );

    $wp_customize->add_control( 'htheme_archive_posts_per_page', [
        'label'       => 'Kategori sayfasinda gosterilecek yazi sayisi',
        'description' => 'Kategori arsivlerinde her sayfada kac yazi listelenecegini belirler.',
        'section'     => 'htheme_archive',
        'type'        => 'number',
        'input_attrs' => [
            'min'  => 3,
            'max'  => 60,
            'step' => 1,
        ],
    ] );
}

function htheme_sanitize_posts_per_page( $value ) {
    return min( 60, max( 3, absint( $value ) ) );
}
