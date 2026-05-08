<?php get_header(); ?>

<?php
$queried  = get_queried_object();
$is_cat   = is_category() && $queried instanceof WP_Term;
$cat_id   = $is_cat ? (int) $queried->term_id : 0;
$cgp_opts = get_option('hcg_settings', []);
$cat_icon = $cgp_opts['icons'][$cat_id]  ?? 'fa-solid fa-layer-group';
$cat_color= $cgp_opts['colors'][$cat_id] ?? get_theme_mod('htheme_accent_color', '#FA6162');
$cat_img  = $cgp_opts['images'][$cat_id] ?? '';
$arc_title= $is_cat ? $queried->name : get_the_archive_title();
$arc_desc = $is_cat ? category_description($cat_id) : get_the_archive_description();
$arc_count= $is_cat ? (int) $queried->count : (int) $GLOBALS['wp_query']->found_posts;

$subs     = $cat_id ? get_categories(['parent'=>$cat_id,'hide_empty'=>true,'orderby'=>'name','order'=>'ASC']) : [];
$has_subs = !empty($subs);
?>

<?php htheme_breadcrumb(); ?>

<!-- ── Category Hero ── -->
<section class="cat-hero" style="--cat-color:<?php echo esc_attr($cat_color); ?>">
    <div class="cat-hero__top">
        <div class="cat-hero__identity">
            <div class="cat-hero__icon">
                <?php if ($cat_img): ?>
                    <img src="<?php echo esc_url($cat_img); ?>" alt="<?php echo esc_attr($arc_title); ?>">
                <?php else: ?>
                    <i class="<?php echo esc_attr($cat_icon); ?>" aria-hidden="true"></i>
                <?php endif; ?>
            </div>
            <div class="cat-hero__text">
                <h1><?php echo esc_html($arc_title); ?></h1>
                <?php if ($arc_desc): ?>
                <p><?php echo wp_kses_post($arc_desc); ?></p>
                <?php endif; ?>
            </div>
        </div>
        <div class="cat-hero__badge">
            <span><?php echo $arc_count; ?> araç</span>
        </div>
    </div>
    <div class="cat-hero__search">
        <svg class="cat-hero__search-ico" xmlns="http://www.w3.org/2000/svg" width="15" height="15" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.2" aria-hidden="true"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
        <input type="search" id="cat-search"
               placeholder="<?php echo esc_attr($arc_title); ?> içinde ara…"
               autocomplete="off" aria-label="Kategori içinde ara">
    </div>
</section>

<?php if ($has_subs && $is_cat): ?>

<!-- ── Sub-category card grid ── -->
<div class="cat-subgrid" id="cat-grid" style="--cat-color:<?php echo esc_attr($cat_color); ?>">

    <?php foreach ($subs as $sub):
        $sub_id   = (int)$sub->term_id;
        $sub_color = $cgp_opts['colors'][$sub_id] ?? $cat_color;
        $sub_desc  = trim($cgp_opts['descs'][$sub_id] ?? '');
        if (!$sub_desc) $sub_desc = trim(strip_tags(category_description($sub_id)));

        $sub_posts = get_posts([
            'category'       => $sub_id,
            'posts_per_page' => -1,
            'orderby'        => 'title',
            'order'          => 'ASC',
            'post_status'    => 'publish',
        ]);
        if (empty($sub_posts)) continue;
        $total   = count($sub_posts);
        $preview = array_slice($sub_posts, 0, 4);
    ?>
    <article class="cat-subcard"
             style="--sub-color:<?php echo esc_attr($sub_color); ?>"
             data-name="<?php echo esc_attr(mb_strtolower($sub->name . ' ' . implode(' ', array_column($sub_posts,'post_title')), 'UTF-8')); ?>">
        <div class="cat-subcard__strip"></div>
        <div class="cat-subcard__head">
            <div class="cat-subcard__icon">
                <?php if ($cat_img): ?>
                    <img src="<?php echo esc_url($cat_img); ?>" alt="" loading="lazy">
                <?php else: ?>
                    <i class="<?php echo esc_attr($cat_icon); ?>" aria-hidden="true"></i>
                <?php endif; ?>
            </div>
            <div class="cat-subcard__meta">
                <h2 class="cat-subcard__title">
                    <a href="<?php echo esc_url(get_category_link($sub_id)); ?>"><?php echo esc_html($sub->name); ?></a>
                </h2>
                <span class="cat-subcard__count"><?php echo $total; ?> araç</span>
            </div>
        </div>

        <?php if ($sub_desc): ?>
        <p class="cat-subcard__desc"><?php echo esc_html($sub_desc); ?></p>
        <?php endif; ?>

        <ul class="cat-subcard__tools">
            <?php foreach ($preview as $p): ?>
            <li>
                <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="cat-subcard__tool">
                    <span><?php echo esc_html($p->post_title); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>

        <a href="<?php echo esc_url(get_category_link($sub_id)); ?>" class="cat-subcard__footer">
            <span>Tümünü Gör</span>
            <span class="cat-subcard__footer-count"><?php echo $total; ?></span>
            <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
        </a>
    </article>
    <?php endforeach; ?>

    <?php
    $sub_ids      = array_map(fn($s) => $s->term_id, $subs);
    $direct_posts = get_posts([
        'category'         => $cat_id,
        'category__not_in' => $sub_ids,
        'posts_per_page'   => -1,
        'orderby'          => 'title',
        'order'            => 'ASC',
        'post_status'      => 'publish',
    ]);
    if (!empty($direct_posts)):
        $total   = count($direct_posts);
        $preview = array_slice($direct_posts, 0, 4);
    ?>
    <article class="cat-subcard"
             style="--sub-color:<?php echo esc_attr($cat_color); ?>"
             data-name="diğer <?php echo esc_attr(mb_strtolower(implode(' ', array_column($direct_posts,'post_title')), 'UTF-8')); ?>">
        <div class="cat-subcard__strip"></div>
        <div class="cat-subcard__head">
            <div class="cat-subcard__icon">
                <?php if ($cat_img): ?>
                    <img src="<?php echo esc_url($cat_img); ?>" alt="" loading="lazy">
                <?php else: ?>
                    <i class="<?php echo esc_attr($cat_icon); ?>" aria-hidden="true"></i>
                <?php endif; ?>
            </div>
            <div class="cat-subcard__meta">
                <h2 class="cat-subcard__title">Diğer Araçlar</h2>
                <span class="cat-subcard__count"><?php echo $total; ?> araç</span>
            </div>
        </div>
        <ul class="cat-subcard__tools">
            <?php foreach ($preview as $p): ?>
            <li>
                <a href="<?php echo esc_url(get_permalink($p->ID)); ?>" class="cat-subcard__tool">
                    <span><?php echo esc_html($p->post_title); ?></span>
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
                </a>
            </li>
            <?php endforeach; ?>
        </ul>
    </article>
    <?php endif; ?>

</div>

<?php else: ?>

<!-- ── Flat tool grid (sub-category yok) ── -->
<div class="tool-flatgrid" id="cat-grid" style="--cat-color:<?php echo esc_attr($cat_color); ?>">
    <?php if (have_posts()): while (have_posts()): the_post();
        $t_img   = $cgp_opts['images'][$cat_id] ?? '';
        $t_icon  = $cgp_opts['icons'][$cat_id]  ?? 'fa-solid fa-calculator';
    ?>
    <a href="<?php the_permalink(); ?>" class="tool-flatcard"
       data-name="<?php echo esc_attr(mb_strtolower(get_the_title(), 'UTF-8')); ?>">
        <div class="tool-flatcard__icon">
            <?php if ($t_img): ?>
                <img src="<?php echo esc_url($t_img); ?>" alt="" loading="lazy">
            <?php else: ?>
                <i class="<?php echo esc_attr($t_icon); ?>" aria-hidden="true"></i>
            <?php endif; ?>
        </div>
        <div class="tool-flatcard__body">
            <span class="tool-flatcard__name"><?php the_title(); ?></span>
            <?php if (has_excerpt()): ?>
            <p class="tool-flatcard__excerpt"><?php echo wp_trim_words(get_the_excerpt(), 10, '…'); ?></p>
            <?php endif; ?>
        </div>
        <svg class="tool-flatcard__arrow" xmlns="http://www.w3.org/2000/svg" width="14" height="14" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>
    </a>
    <?php endwhile; endif; ?>
</div>
<div class="pagination">
    <?php the_posts_pagination(['mid_size'=>2,'prev_text'=>'‹','next_text'=>'›']); ?>
</div>

<?php endif; ?>

<?php get_footer(); ?>
