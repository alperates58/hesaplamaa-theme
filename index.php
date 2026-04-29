<?php get_header(); ?>
<?php
$show_hero   = (bool)get_theme_mod('htheme_show_hero', true);
$hero_title  = get_theme_mod('htheme_hero_title', 'En Kullanışlı Hesaplama Araçları');
$hero_sub    = get_theme_mod('htheme_hero_subtitle', 'Matematikten finansal hesaplamalara, sağlıktan günlük yaşama kadar — hızlı, doğru ve güvenilir.');
$show_latest = (bool)get_theme_mod('htheme_show_latest', true);
$latest_cnt  = absint(get_theme_mod('htheme_latest_count', 6));
$latest_cols = get_theme_mod('htheme_latest_cols', '3col');
?>

<?php if (is_front_page() && is_home()): ?>

    <?php if ($show_hero): ?>
    <section class="page-hero">
        <h1><?php echo esc_html($hero_title); ?></h1>
        <p><?php echo esc_html($hero_sub); ?></p>
        <div class="hero-search-wrap">
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="hero-search">
                <input type="search" name="s" placeholder="Hesaplama aracı ara… (ör: faiz, vücut kitle)" autocomplete="off">
                <button type="submit">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    Ara
                </button>
            </form>
        </div>
    </section>
    <?php endif; ?>

    <?php while (have_posts()): the_post(); the_content(); endwhile; ?>

    <?php if ($show_latest):
        $latest = new WP_Query(['posts_per_page'=>$latest_cnt,'ignore_sticky_posts'=>1]);
        if ($latest->have_posts()): ?>
    <section class="latest-section">
        <h2 class="section-heading">Son Eklenen <span>Araçlar</span></h2>
        <div class="posts-grid posts-grid--<?php echo esc_attr($latest_cols); ?>">
            <?php while ($latest->have_posts()): $latest->the_post();
                get_template_part('template-parts/card','post');
            endwhile; wp_reset_postdata(); ?>
        </div>
    </section>
    <?php endif; endif; ?>

<?php else: ?>

    <?php if (have_posts()): ?>
    <div class="posts-grid posts-grid--3col">
        <?php while (have_posts()): the_post();
            get_template_part('template-parts/card','post');
        endwhile; ?>
    </div>
    <div class="pagination">
        <?php the_posts_pagination(['mid_size'=>2,'prev_text'=>'‹','next_text'=>'›']); ?>
    </div>
    <?php else: ?>
    <p class="no-posts">İçerik bulunamadı.</p>
    <?php endif; ?>

<?php endif; ?>
<?php get_footer(); ?>
