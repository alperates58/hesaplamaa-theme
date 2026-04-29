<?php get_header(); ?>
<div class="search-header">
    <div class="search-icon">🔍</div>
    <div>
        <h1 style="font-size:1.3rem;margin-bottom:4px">Arama: "<?php echo esc_html(get_search_query()); ?>"</h1>
        <p style="font-size:.875rem;color:var(--color-text-3)"><?php global $wp_query; echo $wp_query->found_posts; ?> sonuç bulundu</p>
    </div>
</div>

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
<p class="no-posts" style="padding:60px 0;text-align:center;color:var(--color-text-3)">
    "<?php echo esc_html(get_search_query()); ?>" için sonuç bulunamadı.
</p>
<?php endif; ?>
<?php get_footer(); ?>
