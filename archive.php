<?php get_header(); ?>

<?php
$arc_layout      = get_theme_mod('htheme_archive_layout', '3col');
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
        <span><?php echo $queried->count; ?> araç</span>
    </div>
</header>

<!-- Alt kategoriler -->
<?php
$subs = $cat_id ? get_categories(['parent'=>$cat_id,'hide_empty'=>true]) : [];
if ($subs): ?>
<div class="sub-cats">
    <?php foreach ($subs as $sub): ?>
    <a href="<?php echo esc_url(get_category_link($sub->term_id)); ?>" class="sub-cat-chip">
        <?php echo esc_html($sub->name); ?>
        <span><?php echo $sub->count; ?></span>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="archive-body <?php echo $show_arc_sidebar ? 'has-sidebar' : ''; ?>">
    <div class="archive-posts">
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
    </div>

    <?php if ($show_arc_sidebar && is_active_sidebar('sidebar-archive')): ?>
    <aside class="single-sidebar" role="complementary">
        <?php dynamic_sidebar('sidebar-archive'); ?>
    </aside>
    <?php endif; ?>
</div>

<?php get_footer(); ?>
