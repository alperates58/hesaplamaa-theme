<?php
/**
 * Hesaplamaa Theme — Category Grid (inc/category-grid.php)
 * Admin ayar sayfası + [category_grid] shortcode
 */
if ( ! defined( 'ABSPATH' ) ) exit;

/* ═══ 1. ADMİN MENÜ ═══ */
add_action( 'admin_menu', function() {
    add_menu_page( 'Kategori Grid', 'Kategori Grid', 'manage_options',
        'hcg-settings', 'hcg_settings_page', 'dashicons-screenoptions', 60 );
} );

/* ═══ 2. SETTINGS API ═══ */
add_action( 'admin_init', function() {
    register_setting( 'hcg_group', 'hcg_settings', [
        'type'              => 'array',
        'sanitize_callback' => 'hcg_sanitize',
        'default'           => [],
    ] );
} );

function hcg_sanitize( $in ) {
    if ( ! is_array($in) ) return [];
    $out = [];
    foreach ( $in as $k => $v ) {
        if ( in_array($k, ['icons','images','colors','descs','order'], true) && is_array($v) ) {
            foreach ( $v as $tid => $val ) {
                $tid = intval($tid);
                if      ( $k === 'images' )  $out[$k][$tid] = esc_url_raw($val);
                elseif  ( $k === 'colors' )  $out[$k][$tid] = sanitize_hex_color($val) ?: '#FA6162';
                elseif  ( $k === 'descs'  )  $out[$k][$tid] = sanitize_text_field($val);
                elseif  ( $k === 'order'  )  $out[$k][$tid] = intval($val);
                else                         $out[$k][$tid] = sanitize_text_field($val);
            }
        } elseif ( $k === 'show_sub'                ) { $out[$k] = $v ? 1 : 0;
        } elseif ( in_array($k,['columns','mobile_columns'],true) ) { $out[$k] = intval($v) ?: 6;
        } elseif ( $k === 'custom_css'              ) { $out[$k] = wp_kses_post($v);
        } else                                        { $out[$k] = sanitize_text_field($v); }
    }
    return $out;
}

/* ═══ 3. ADMİN ASSETS ═══ */
add_action( 'admin_enqueue_scripts', function($hook) {
    if ( $hook !== 'toplevel_page_hcg-settings' ) return;
    wp_enqueue_media();
    echo '<style>' . hcg_admin_css() . '</style>';
    echo '<script>' . hcg_admin_js() . '</script>';
} );

function hcg_admin_css() { return '
.hcg-wrap{max-width:1100px}
.hcg-wrap h1{font-size:22px;margin-bottom:20px;display:flex;align-items:center;gap:10px}
.hcg-box{background:#fff;border:1px solid #e5e7eb;border-radius:12px;padding:24px;margin-bottom:20px}
.hcg-box h2{font-size:13px;font-weight:700;text-transform:uppercase;letter-spacing:.6px;color:#3B6FE8;margin:0 0 16px;padding-bottom:10px;border-bottom:2px solid #EEF3FD}
.hcg-general{display:grid;grid-template-columns:repeat(3,1fr);gap:16px}
.hcg-field label{display:block;font-size:12px;font-weight:700;color:#374151;margin-bottom:5px;text-transform:uppercase;letter-spacing:.4px}
.hcg-field select,.hcg-field input[type=number]{width:100%;padding:8px 10px;border:1px solid #d1d5db;border-radius:6px;font-size:14px}
.hcg-field textarea{width:100%;padding:8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px;min-height:70px;font-family:monospace}
.hcg-table{width:100%;border-collapse:collapse}
.hcg-table thead th{background:#f9fafb;padding:10px 12px;font-size:11px;font-weight:700;text-transform:uppercase;letter-spacing:.5px;color:#6b7280;border-bottom:1px solid #e5e7eb;text-align:left}
.hcg-table tbody td{padding:10px 12px;border-bottom:1px solid #f3f4f6;vertical-align:middle}
.hcg-table tbody tr:hover{background:#fafbff}
.hcg-cat-name{font-weight:700;font-size:13px}
.hcg-cat-slug{font-size:11px;color:#9ca3af;font-weight:400}
.hcg-img-preview img{width:48px;height:48px;object-fit:cover;border-radius:8px;margin-top:4px;border:1px solid #e5e7eb}
.hcg-icon-input{width:100%;padding:6px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:12px}
.hcg-order-input{width:52px;padding:6px 8px;border:1px solid #d1d5db;border-radius:6px;font-size:13px;text-align:center}
.hcg-code{background:#1e293b;color:#e2e8f0;padding:10px 14px;border-radius:8px;font-family:monospace;font-size:14px;display:inline-block;margin:8px 0}
';
}

function hcg_admin_js() { return "
document.addEventListener('DOMContentLoaded',function(){
    document.querySelectorAll('.hcg-upload-btn').forEach(function(btn){
        btn.addEventListener('click',function(e){
            e.preventDefault();
            var row=btn.closest('tr');
            var frame=wp.media({title:'Resim Seç',button:{text:'Seç'},multiple:false});
            frame.on('select',function(){
                var att=frame.state().get('selection').first().toJSON();
                row.querySelector('.hcg-img-field').value=att.url;
                var p=row.querySelector('.hcg-img-preview');
                p.innerHTML='<img src=\"'+att.url+'\" alt=\"\">';
            });
            frame.open();
        });
    });
    document.querySelectorAll('.hcg-remove-btn').forEach(function(btn){
        btn.addEventListener('click',function(e){
            e.preventDefault();
            var row=btn.closest('tr');
            row.querySelector('.hcg-img-field').value='';
            row.querySelector('.hcg-img-preview').innerHTML='';
        });
    });
});
";
}

/* ═══ 4. AYAR SAYFASI ═══ */
function hcg_settings_page() {
    $opts = get_option( 'hcg_settings', [] );
    $cats = get_categories( ['hide_empty'=>false,'parent'=>0] );
    usort($cats, function($a,$b) use($opts){
        return ($opts['order'][$a->term_id]??99) - ($opts['order'][$b->term_id]??99);
    });
    ?>
    <div class="wrap hcg-wrap">
        <h1>🗂 Kategori Grid Ayarları</h1>
        <form method="post" action="options.php">
            <?php settings_fields('hcg_group'); ?>

            <div class="hcg-box">
                <h2>Grid Genel Ayarlar</h2>
                <div class="hcg-general">
                    <div class="hcg-field">
                        <label>Masaüstü Kolon</label>
                        <select name="hcg_settings[columns]">
                            <?php foreach([2,3,4,5,6]as$c): ?>
                            <option value="<?=$c?>" <?=selected($opts['columns']??6,$c,false)?>>
                                <?=$c?> Kolon</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="hcg-field">
                        <label>Mobil Kolon</label>
                        <select name="hcg_settings[mobile_columns]">
                            <?php foreach([1,2,3]as$c): ?>
                            <option value="<?=$c?>" <?=selected($opts['mobile_columns']??2,$c,false)?>>
                                <?=$c?> Kolon</option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="hcg-field">
                        <label>Alt Kategoriler</label>
                        <select name="hcg_settings[show_sub]">
                            <option value="0" <?=selected($opts['show_sub']??0,0,false)?>>Hayır</option>
                            <option value="1" <?=selected($opts['show_sub']??0,1,false)?>>Evet</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="hcg-box">
                <h2>Kategori Başına Ayarlar</h2>
                <table class="hcg-table">
                    <thead>
                        <tr>
                            <th style="width:170px">Kategori</th>
                            <th style="width:60px">Sıra</th>
                            <th style="width:195px">İkon (FA sınıfı)</th>
                            <th style="width:185px">Resim</th>
                            <th style="width:80px">Renk</th>
                            <th>Kısa Açıklama</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php foreach($cats as $cat):
                        $id    = $cat->term_id;
                        $icon  = $opts['icons'][$id]  ?? 'fa-solid fa-calculator';
                        $img   = $opts['images'][$id] ?? '';
                        $color = $opts['colors'][$id] ?? '#FA6162';
                        $desc  = $opts['descs'][$id]  ?? '';
                        $order = $opts['order'][$id]  ?? 99;
                    ?>
                    <tr>
                        <td>
                            <div class="hcg-cat-name"><?=esc_html($cat->name)?></div>
                            <span class="hcg-cat-slug"><?=esc_html($cat->slug)?> · <?=$cat->count?> içerik</span>
                        </td>
                        <td>
                            <input type="number" class="hcg-order-input"
                                   name="hcg_settings[order][<?=$id?>]"
                                   value="<?=esc_attr($order)?>" min="0" max="99">
                        </td>
                        <td>
                            <input type="text" class="hcg-icon-input"
                                   name="hcg_settings[icons][<?=$id?>]"
                                   value="<?=esc_attr($icon)?>"
                                   placeholder="fa-solid fa-calculator">
                            <a href="https://fontawesome.com/search?m=free" target="_blank"
                               style="font-size:11px;color:#3B6FE8;display:block;margin-top:3px">
                                → FontAwesome'da ara
                            </a>
                        </td>
                        <td>
                            <input type="hidden" class="hcg-img-field"
                                   name="hcg_settings[images][<?=$id?>]"
                                   value="<?=esc_attr($img)?>">
                            <div style="display:flex;gap:5px;flex-wrap:wrap">
                                <button type="button" class="button button-small hcg-upload-btn">📷 Seç</button>
                                <button type="button" class="button button-small hcg-remove-btn" style="color:#dc2626">✕ Kaldır</button>
                            </div>
                            <div class="hcg-img-preview">
                                <?php if($img) echo '<img src="'.esc_url($img).'" alt="">'; ?>
                            </div>
                        </td>
                        <td>
                            <input type="color"
                                   name="hcg_settings[colors][<?=$id?>]"
                                   value="<?=esc_attr($color)?>"
                                   style="width:52px;height:34px;border:1px solid #d1d5db;border-radius:6px;cursor:pointer;padding:2px">
                        </td>
                        <td>
                            <textarea name="hcg_settings[descs][<?=$id?>]"
                                      rows="2" style="width:100%;padding:6px;border:1px solid #d1d5db;border-radius:6px;font-size:12px"><?=esc_textarea($desc)?></textarea>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <div class="hcg-box">
                <h2>Özel CSS</h2>
                <div class="hcg-field">
                    <textarea name="hcg_settings[custom_css]" rows="6" style="font-family:monospace;width:100%"><?=esc_textarea($opts['custom_css']??'')?></textarea>
                    <p style="font-size:12px;color:#9ca3af;margin-top:4px">Kapsam: <code>.hcg-grid</code></p>
                </div>
            </div>

            <?php submit_button('💾 Ayarları Kaydet','primary large'); ?>
        </form>

        <div class="hcg-box" style="background:#f8faff;margin-top:20px">
            <h2>Kullanım</h2>
            <p>Anasayfa içeriğine şu shortcode'u ekleyin:</p>
            <code class="hcg-code">[category_grid]</code>
            <p style="margin-top:8px;font-size:13px;color:#6b7280">
                Kart <em>stili, renk, ikon boyutu</em> ve diğer görünüm ayarları için:
                <strong>Görünüm → Özelleştir → Hesaplamaa Tema Ayarları → Kategori Grid Görünümü</strong>
            </p>
        </div>
    </div>
    <?php
}

/* ═══ 5. SHORTCODE ═══ */
add_shortcode( 'category_grid', 'hcg_render' );

function hcg_render( $atts ) {
    $atts = shortcode_atts( ['columns'=>''], $atts, 'category_grid' );
    $opts = get_option( 'hcg_settings', [] );

    // Grid ayarları
    $columns  = $atts['columns'] ?: ($opts['columns'] ?? 6);
    $mob_cols = $opts['mobile_columns'] ?? 2;
    $show_sub = !empty($opts['show_sub']);

    // Customizer ayarları
    $grid_style  = get_theme_mod('htheme_grid_style',   'card-modern');
    $icon_shape  = get_theme_mod('htheme_icon_shape',   'rounded');
    $icon_size   = absint(get_theme_mod('htheme_icon_size', 76));
    $show_desc   = (bool)get_theme_mod('htheme_show_desc_grid',  true);
    $show_count  = (bool)get_theme_mod('htheme_show_count_grid', true);
    $count_text  = get_theme_mod('htheme_card_count_text', 'araç');
    $hover_lift  = (bool)get_theme_mod('htheme_card_hover_lift', true);
    $top_strip   = (bool)get_theme_mod('htheme_card_top_strip',  true);
    $bg_effect   = (bool)get_theme_mod('htheme_card_bg_effect',  true);

    // Kategoriler
    $cats = get_categories(['hide_empty'=>false,'parent'=>0]);
    usort($cats, function($a,$b) use($opts){
        return ($opts['order'][$a->term_id]??99)-($opts['order'][$b->term_id]??99);
    });

    $css = $opts['custom_css'] ?? '';

    $cls = implode(' ', array_filter([
        'hcg-grid',
        'hcg-cols-'.intval($columns),
        'hcg-style-'.$grid_style,
        'hcg-icon-'.$icon_shape,
        $hover_lift ? 'hcg-hover-lift' : '',
        $top_strip  ? 'hcg-top-strip'  : '',
        $bg_effect  ? 'hcg-bg-effect'  : '',
    ]));

    ob_start();
    if ($css) echo '<style>'.wp_strip_all_tags($css).'</style>';
    ?>
    <div class="<?=esc_attr($cls)?>"
         data-mobile-cols="<?=intval($mob_cols)?>"
         style="--icon-size:<?=$icon_size?>px">

        <?php foreach($cats as $cat):
            $id    = $cat->term_id;
            $icon  = $opts['icons'][$id]  ?? 'fa-solid fa-calculator';
            $img   = $opts['images'][$id] ?? '';
            $color = $opts['colors'][$id] ?? get_theme_mod('htheme_accent_color','#FA6162');
            $desc  = $opts['descs'][$id]  ?? '';
        ?>
        <a href="<?=esc_url(get_category_link($id))?>"
           class="hcg-card"
           style="--cat-color:<?=esc_attr($color)?>">

            <div class="hcg-card__icon">
                <?php if($img): ?>
                    <img src="<?=esc_url($img)?>" alt="<?=esc_attr($cat->name)?>" loading="lazy">
                <?php else: ?>
                    <i class="<?=esc_attr($icon)?>" aria-hidden="true"></i>
                <?php endif; ?>
            </div>

            <div class="hcg-card__body">
                <h3 class="hcg-card__title"><?=esc_html($cat->name)?></h3>
                <?php if($show_desc && $desc): ?>
                    <p class="hcg-card__desc"><?=esc_html($desc)?></p>
                <?php endif; ?>
                <?php if($show_count): ?>
                    <span class="hcg-card__count">
                        <?=intval($cat->count)?> <?=esc_html($count_text)?>
                    </span>
                <?php endif; ?>
            </div>

            <span class="hcg-card__arrow" aria-hidden="true">
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none"
                     viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M9 18l6-6-6-6"/>
                </svg>
            </span>

            <?php if($show_sub):
                $subs = get_categories(['parent'=>$id,'hide_empty'=>true]);
                if($subs): ?>
                <ul class="hcg-card__subs" onclick="event.preventDefault()">
                    <?php foreach($subs as $s): ?>
                    <li><a href="<?=esc_url(get_category_link($s->term_id))?>">
                        <?=esc_html($s->name)?>
                        <span><?=$s->count?></span>
                    </a></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; endif; ?>

        </a>
        <?php endforeach; ?>
    </div>
    <?php
    return ob_get_clean();
}
