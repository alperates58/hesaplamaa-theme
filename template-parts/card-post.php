<?php
$cats    = get_the_category();
$has_img = has_post_thumbnail();
$show_thumb  = (bool)get_theme_mod('htheme_archive_show_thumb', true);
$show_excerpt= (bool)get_theme_mod('htheme_archive_show_excerpt', true);
?>
<article id="post-<?php the_ID(); ?>" <?php post_class('post-card'); ?>>

    <?php if ($show_thumb): ?>
    <div class="post-card-thumb">
        <?php if ($has_img): ?>
        <a href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
            <?php the_post_thumbnail('htheme-card'); ?>
        </a>
        <?php else: ?>
        <span class="no-thumb" aria-hidden="true">
            <svg xmlns="http://www.w3.org/2000/svg" width="38" height="38" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1"><path d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2Z"/></svg>
        </span>
        <?php endif; ?>
    </div>
    <?php endif; ?>

    <div class="post-card-body">
        <?php if ($cats): ?>
        <div class="post-card-cats">
            <?php foreach (array_slice($cats,0,2) as $cat):
                $cls = htheme_cat_class($cat->slug); ?>
            <a href="<?php echo esc_url(get_category_link($cat->term_id)); ?>"
               class="cat-tag <?php echo esc_attr($cls); ?>">
                <?php echo esc_html($cat->name); ?>
            </a>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <h3 class="post-card-title">
            <a href="<?php the_permalink(); ?>"><?php the_title(); ?></a>
        </h3>

        <?php if ($show_excerpt && has_excerpt()): ?>
        <p class="post-card-excerpt"><?php the_excerpt(); ?></p>
        <?php endif; ?>
    </div>

    <div class="post-card-footer">
        <a href="<?php the_permalink(); ?>" class="post-card-link">Hesapla</a>
    </div>
</article>
