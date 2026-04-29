<?php get_header(); ?>
<?php while (have_posts()): the_post(); ?>
<?php htheme_breadcrumb(); ?>
<div class="single-layout single-full-width">
    <article <?php post_class('single-main'); ?>>
        <h1 class="single-title"><?php the_title(); ?></h1>
        <div class="entry-box">
            <div class="entry-inner"><?php the_content(); ?></div>
        </div>
    </article>
</div>
<?php endwhile; ?>
<?php get_footer(); ?>
