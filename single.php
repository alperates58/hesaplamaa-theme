<?php get_header(); ?>

<?php
$layout        = get_theme_mod('htheme_single_layout', 'sidebar-right');
$show_bc       = (bool)get_theme_mod('htheme_show_breadcrumb', true);
$show_cat_tags = (bool)get_theme_mod('htheme_show_cat_tags', true);
$show_post_tags= (bool)get_theme_mod('htheme_show_post_tags', true);
$show_result_share = (bool)get_theme_mod('htheme_show_result_share', true);
$show_related  = (bool)get_theme_mod('htheme_show_related', true);
$related_count = absint(get_theme_mod('htheme_related_count', 3));
$related_cols  = get_theme_mod('htheme_related_cols', '3');
$show_s_search = (bool)get_theme_mod('htheme_sidebar_search', true);
$show_s_ad     = (bool)get_theme_mod('htheme_sidebar_ad', true);
$show_s_recent = (bool)get_theme_mod('htheme_sidebar_recent', true);
$s_recent_cnt  = absint(get_theme_mod('htheme_sidebar_recent_count', 8));
$show_s_cats   = (bool)get_theme_mod('htheme_sidebar_cats', false);
$has_sidebar   = in_array($layout, ['sidebar-right','sidebar-left']);
?>

<?php if ($show_bc) htheme_breadcrumb(); ?>

<div class="single-wrap single-<?php echo esc_attr($layout); ?>">

    <!-- ─── Ana içerik ─── -->
    <article id="post-<?php the_ID(); ?>" <?php post_class('single-main'); ?>>

        <?php while (have_posts()): the_post(); ?>

        <header class="single-header">
            <?php if ($show_cat_tags): ?>
            <div class="single-cats">
                <?php foreach (get_the_category() as $cat):
                    $cls = htheme_cat_class($cat->slug); ?>
                <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
                   class="cat-tag <?php echo esc_attr($cls); ?>">
                    <?php echo esc_html($cat->name); ?>
                </a>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            <h1 class="single-title"><?php the_title(); ?></h1>
            <?php if (has_excerpt()): ?>
            <p class="single-excerpt"><?php the_excerpt(); ?></p>
            <?php endif; ?>
        </header>

        <div class="entry-box">
            <div class="entry-inner">
                <?php the_content(); ?>
            </div>
        </div>

        <?php if ($show_result_share): ?>
        <section class="result-share-panel" aria-labelledby="result-share-title" data-share-panel>
            <h2 id="result-share-title">Sonucu Paylaş veya İşlem Yap</h2>
            <p class="result-share-status" data-share-status aria-live="polite"></p>
            <p class="result-share-print-url" data-share-print-url></p>
            <div class="result-share-actions">
                <button type="button" class="share-btn share-facebook" data-share-action="facebook">
                    <i class="fa-brands fa-facebook-f" aria-hidden="true"></i><span>Facebook</span>
                </button>
                <button type="button" class="share-btn share-x" data-share-action="x">
                    <i class="fa-brands fa-x-twitter" aria-hidden="true"></i><span>X</span>
                </button>
                <button type="button" class="share-btn share-instagram" data-share-action="instagram">
                    <i class="fa-brands fa-instagram" aria-hidden="true"></i><span>Instagram</span>
                </button>
                <button type="button" class="share-btn share-telegram" data-share-action="telegram">
                    <i class="fa-brands fa-telegram" aria-hidden="true"></i><span>Telegram</span>
                </button>
                <button type="button" class="share-btn share-whatsapp" data-share-action="whatsapp">
                    <i class="fa-brands fa-whatsapp" aria-hidden="true"></i><span>WhatsApp</span>
                </button>
                <button type="button" class="share-btn share-email" data-share-action="email">
                    <i class="fa-solid fa-envelope" aria-hidden="true"></i><span>E-posta</span>
                </button>
                <button type="button" class="share-btn share-pdf" data-share-action="pdf">
                    <i class="fa-solid fa-file-pdf" aria-hidden="true"></i><span>PDF Kaydet</span>
                </button>
                <button type="button" class="share-btn share-print" data-share-action="print">
                    <i class="fa-solid fa-print" aria-hidden="true"></i><span>Yazdır</span>
                </button>
            </div>
            <button type="button" class="share-btn share-recalculate" data-share-action="recalculate">
                <i class="fa-solid fa-rotate-right" aria-hidden="true"></i><span>Yeniden Hesapla</span>
            </button>
        </section>
        <?php endif; ?>

        <?php if ($show_post_tags && has_tag()): ?>
        <div class="post-tags">
            <span class="tags-label">Etiketler:</span>
            <?php foreach (get_the_tags() as $tag): ?>
            <a href="<?php echo esc_url(get_tag_link($tag->term_id)); ?>" class="tag-pill">
                #<?php echo esc_html($tag->name); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <?php if ($show_related):
            $rel = new WP_Query([
                'category__in'   => wp_get_post_categories(get_the_ID()),
                'post__not_in'   => [get_the_ID()],
                'posts_per_page' => $related_count,
                'orderby'        => 'rand',
            ]);
            if ($rel->have_posts()): ?>
        <section class="related-section">
            <h2 class="related-title">İlgili <span>Araçlar</span></h2>
            <div class="posts-grid posts-grid--<?php echo esc_attr($related_cols); ?>col">
                <?php while ($rel->have_posts()): $rel->the_post();
                    get_template_part('template-parts/card','post');
                endwhile; wp_reset_postdata(); ?>
            </div>
        </section>
        <?php endif; endif; ?>

        <?php endwhile; ?>

    </article>

    <!-- ─── Sidebar ─── -->
    <?php if ($has_sidebar): ?>
    <aside class="single-sidebar" role="complementary">

        <?php if ($show_s_search): ?>
        <div class="sidebar-widget">
            <h3 class="sidebar-widget-title">Ara</h3>
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="sw-search">
                <input type="search" name="s" placeholder="Hesaplama aracı…" value="<?php echo get_search_query(); ?>">
                <button type="submit" aria-label="Ara">
                    <svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                </button>
            </form>
        </div>
        <?php endif; ?>

        <?php if ($show_s_ad): ?>
        <div class="sidebar-widget">
            <h3 class="sidebar-widget-title">Reklam</h3>
            <div class="ad-slot">Reklam Alanı</div>
        </div>
        <?php endif; ?>

        <?php if ($show_s_recent):
            $recent = new WP_Query(['posts_per_page'=>$s_recent_cnt,'post__not_in'=>[get_the_ID()]]);
            if ($recent->have_posts()): ?>
        <div class="sidebar-widget">
            <h3 class="sidebar-widget-title">Son Eklenenler</h3>
            <ul class="sw-recent">
                <?php while ($recent->have_posts()): $recent->the_post(); ?>
                <li>
                    <span class="rp-dot" style="background:var(--color-primary)"></span>
                    <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
                </li>
                <?php endwhile; wp_reset_postdata(); ?>
            </ul>
        </div>
        <?php endif; endif; ?>

        <?php if ($show_s_cats): ?>
        <div class="sidebar-widget">
            <h3 class="sidebar-widget-title">Kategoriler</h3>
            <ul class="sw-cats">
                <?php foreach (get_categories(['parent'=>0,'hide_empty'=>true]) as $c): ?>
                <li>
                    <a href="<?php echo esc_url(get_category_link($c->term_id)); ?>">
                        <?php echo esc_html($c->name); ?>
                        <span><?php echo $c->count; ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php endif; ?>

        <?php if (is_active_sidebar('sidebar-single')) dynamic_sidebar('sidebar-single'); ?>

    </aside>
    <?php endif; ?>

</div><!-- .single-wrap -->

<?php get_footer(); ?>
