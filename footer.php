    </div><!-- .container -->
</main>

<?php
$layout     = get_theme_mod('htheme_footer_layout', 'cols-3');
$footer_bg  = get_theme_mod('htheme_footer_bg', '#1A1D2E');
$footer_txt = get_theme_mod('htheme_footer_text', '#7a82a0');
$brand_text = get_theme_mod('htheme_footer_brand_text', 'Matematikten finansal hesaplamalara, sağlıktan günlük yaşama kadar geniş bir yelpazede hesaplama araçları.');
$copyright  = get_theme_mod('htheme_footer_copyright', '© {yıl} Hesaplamaa.com — Tüm hakları saklıdır.');
$copyright  = str_replace('{yıl}', date('Y'), $copyright);

$col1_on    = (bool)get_theme_mod('htheme_footer_col1_enable', true);
$col1_title = get_theme_mod('htheme_footer_col1_title', 'Hızlı Linkler');
$col2_on    = (bool)get_theme_mod('htheme_footer_col2_enable', true);
$col2_title = get_theme_mod('htheme_footer_col2_title', 'Kategoriler');
$col3_on    = (bool)get_theme_mod('htheme_footer_col3_enable', true);
$col3_title = get_theme_mod('htheme_footer_col3_title', 'Kurumsal');
?>

<?php if ($layout !== 'cols-0'): ?>
<footer class="site-footer" style="background:<?php echo esc_attr($footer_bg); ?>;color:<?php echo esc_attr($footer_txt); ?>;" role="contentinfo">
    <div class="container">

        <?php if ($layout !== 'cols-0'): ?>
        <div class="footer-body footer-<?php echo esc_attr($layout); ?>">

            <!-- Marka -->
            <div class="footer-brand">
                <a href="<?php echo esc_url(home_url('/')); ?>" class="footer-logo">
                    <?php if (has_custom_logo()): the_custom_logo(); else: echo esc_html(get_bloginfo('name')); endif; ?>
                </a>
                <?php if ($brand_text): ?>
                    <p><?php echo esc_html($brand_text); ?></p>
                <?php endif; ?>
            </div>

            <!-- Kolon 1: Menü (footer-1) -->
            <?php if ($col1_on && in_array($layout, ['cols-2','cols-3','cols-4'])): ?>
            <div class="footer-col">
                <h4 style="color:#fff"><?php echo esc_html($col1_title); ?></h4>
                <?php wp_nav_menu([
                    'theme_location' => 'footer-1',
                    'container'      => false,
                    'menu_class'     => 'footer-nav',
                    'depth'          => 1,
                    'fallback_cb'    => function() {
                        // menü atanmamışsa kategori listesi göster
                        echo '<ul class="footer-nav">';
                        $cats = get_categories(['parent'=>0,'number'=>6,'hide_empty'=>true,'orderby'=>'count','order'=>'DESC']);
                        foreach($cats as $c) echo '<li><a href="'.get_category_link($c->term_id).'">'.esc_html($c->name).'</a></li>';
                        echo '</ul>';
                    }
                ]); ?>
            </div>
            <?php endif; ?>

            <!-- Kolon 2: Menü (footer-2) -->
            <?php if ($col2_on && in_array($layout, ['cols-3','cols-4'])): ?>
            <div class="footer-col">
                <h4 style="color:#fff"><?php echo esc_html($col2_title); ?></h4>
                <?php wp_nav_menu([
                    'theme_location' => 'footer-2',
                    'container'      => false,
                    'menu_class'     => 'footer-nav',
                    'depth'          => 1,
                    'fallback_cb'    => function() {
                        echo '<ul class="footer-nav">';
                        $cats = get_categories(['parent'=>0,'number'=>6,'hide_empty'=>true,'orderby'=>'name']);
                        foreach($cats as $c) echo '<li><a href="'.get_category_link($c->term_id).'">'.esc_html($c->name).'</a></li>';
                        echo '</ul>';
                    }
                ]); ?>
            </div>
            <?php endif; ?>

            <!-- Kolon 3: Menü (footer-3) -->
            <?php if ($col3_on && in_array($layout, ['cols-4'])): ?>
            <div class="footer-col">
                <h4 style="color:#fff"><?php echo esc_html($col3_title); ?></h4>
                <?php wp_nav_menu([
                    'theme_location' => 'footer-3',
                    'container'      => false,
                    'menu_class'     => 'footer-nav',
                    'depth'          => 1,
                    'fallback_cb'    => '__return_false',
                ]); ?>
            </div>
            <?php endif; ?>

        </div><!-- .footer-body -->
        <?php endif; ?>

        <div class="footer-bottom">
            <span><?php echo wp_kses_post($copyright); ?></span>
        </div>

    </div>
</footer>
<?php endif; ?>

</div><!-- .site-wrapper -->
<?php wp_footer(); ?>
</body>
</html>
