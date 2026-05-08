<?php get_header(); ?>

<?php
$arc_layout      = get_theme_mod('htheme_archive_layout', '2col');
$show_thumb      = (bool)get_theme_mod('htheme_archive_show_thumb', true);
$show_excerpt    = (bool)get_theme_mod('htheme_archive_show_excerpt', true);
$show_arc_sidebar= (bool)get_theme_mod('htheme_archive_show_sidebar', false);

$queried  = get_queried_object();
$is_cat   = is_category() && $queried instanceof WP_Term;
$cat_id   = $is_cat ? (int) $queried->term_id : 0;
$cgp_opts = get_option('hcg_settings', []);
$cat_icon = $cgp_opts['icons'][$cat_id]  ?? 'fa-solid fa-layer-group';
$cat_color= $cgp_opts['colors'][$cat_id] ?? get_theme_mod('htheme_accent_color','#FA6162');
$cat_img  = $cgp_opts['images'][$cat_id] ?? '';
$arc_title= $is_cat ? $queried->name : get_the_archive_title();
$arc_desc = $is_cat ? category_description($cat_id) : get_the_archive_description();
$arc_count= $is_cat ? (int) $queried->count : (int) $GLOBALS['wp_query']->found_posts;
if ( is_object($queried) ) {
    $queried->count = $arc_count;
}

$subs    = $cat_id ? get_categories(['parent'=>$cat_id,'hide_empty'=>true,'orderby'=>'name','order'=>'ASC']) : [];
$has_subs = !empty($subs);
?>

<?php htheme_breadcrumb(); ?>

<header class="archive-header" style="--cat-color:<?php echo esc_attr($cat_color); ?>">
    <div class="archive-icon">
        <?php if ($cat_img): ?>
            <img src="<?php echo esc_url($cat_img); ?>" alt="">
        <?php else: ?>
            <i class="<?php echo esc_attr($cat_icon); ?>"></i>
        <?php endif; ?>
    </div>
    <div class="archive-info">
        <h1><?php echo esc_html($arc_title); ?></h1>
        <?php if ($arc_desc) echo '<p>'.wp_kses_post($arc_desc).'</p>'; ?>
    </div>
    <div class="archive-count">
        <span><?php echo $arc_count; ?> araç</span>
    </div>
</header>

<div class="archive-body <?php echo $show_arc_sidebar ? 'has-sidebar' : ''; ?>">
    <div class="archive-posts">

    <?php if ($has_subs && $is_cat): ?>

        <?php foreach ($subs as $sub):
            $sub_id    = (int)$sub->term_id;
            $sub_color = $cgp_opts['colors'][$sub_id] ?? $cat_color;
            // Her alt bölüm ana kategori ikonunu/resmini kullanır
            $sub_icon  = $cat_icon;
            $sub_img   = $cat_img;

            $sub_posts = get_posts([
                'category'       => $sub_id,
                'posts_per_page' => -1,
                'orderby'        => 'title',
                'order'          => 'ASC',
                'post_status'    => 'publish',
            ]);
            if (empty($sub_posts)) continue;
        ?>
        <section class="sub-section" style="--sub-color:<?php echo esc_attr($sub_color); ?>">
            <button class="sub-section__header" aria-expanded="true" type="button">
                <div class="sub-section__header-left">
                    <span class="sub-section__icon">
                        <?php if ($sub_img): ?>
                            <img src="<?php echo esc_url($sub_img); ?>" alt="">
                        <?php else: ?>
                            <i class="<?php echo esc_attr($sub_icon); ?>"></i>
                        <?php endif; ?>
                    </span>
                    <span class="sub-section__title"><?php echo esc_html($sub->name); ?></span>
                    <span class="sub-section__count"><?php echo count($sub_posts); ?></span>
                </div>
                <svg class="sub-section__chevron" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 15l7-7 7 7"/></svg>
            </button>
            <div class="sub-section__body">
                <ul class="sub-calc-list">
                    <?php foreach ($sub_posts as $p): ?>
                    <li>
                        <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="sub-calc-item">
                            <span class="sub-calc-item__name"><?php echo esc_html($p->post_title); ?></span>
                            <svg class="sub-calc-item__arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
        <?php endforeach; ?>

        <?php
        $sub_ids = array_map(fn($s) => $s->term_id, $subs);
        $direct_posts = get_posts([
            'category'         => $cat_id,
            'category__not_in' => $sub_ids,
            'posts_per_page'   => -1,
            'orderby'          => 'title',
            'order'            => 'ASC',
            'post_status'      => 'publish',
        ]);
        if (!empty($direct_posts)): ?>
        <section class="sub-section" style="--sub-color:<?php echo esc_attr($cat_color); ?>">
            <button class="sub-section__header" aria-expanded="true" type="button">
                <div class="sub-section__header-left">
                    <span class="sub-section__icon"><i class="fa-solid fa-layer-group"></i></span>
                    <span class="sub-section__title">Diğer Araçlar</span>
                    <span class="sub-section__count"><?php echo count($direct_posts); ?></span>
                </div>
                <svg class="sub-section__chevron" xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M5 15l7-7 7 7"/></svg>
            </button>
            <div class="sub-section__body">
                <ul class="sub-calc-list">
                    <?php foreach ($direct_posts as $p): ?>
                    <li>
                        <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="sub-calc-item">
                            <span class="sub-calc-item__name"><?php echo esc_html($p->post_title); ?></span>
                            <svg class="sub-calc-item__arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </section>
        <?php endif; ?>

    <?php else: ?>

        <?php if (have_posts()): ?>
        <div class="posts-grid posts-grid--<?php echo esc_attr($arc_layout); ?>">
            <?php while (have_posts()): the_post();
                get_template_part('template-parts/card','post');
            endwhile; ?>
        </div>
        <div class="pagination">
            <?php the_posts_pagination(['mid_size'=>2,'prev_text'=>'‹','next_text'=>'›']); ?>
        </div>
        <?php else: ?>
        <p class="no-posts">Bu kategoride henüz içerik yok.</p>
        <?php endif; ?>

    <?php endif; ?>

    </div>

    <?php if ($show_arc_sidebar && is_active_sidebar('sidebar-archive')): ?>
    <aside class="single-sidebar" role="complementary">
        <?php dynamic_sidebar('sidebar-archive'); ?>
    </aside>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
