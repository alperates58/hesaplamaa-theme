<?php
$cats     = get_the_category();
$cat      = $cats ? $cats[0] : null;
$cat_id   = $cat ? $cat->term_id : 0;
$cgp_opts = get_option( 'hcg_settings', [] );
$icon     = $cgp_opts['icons'][$cat_id]  ?? 'fa-solid fa-calculator';
$img      = $cgp_opts['images'][$cat_id] ?? '';
$color    = $cgp_opts['colors'][$cat_id] ?? get_theme_mod( 'htheme_accent_color', '#FA6162' );
$show_excerpt = (bool) get_theme_mod( 'htheme_archive_show_excerpt', true );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'calc-card' ); ?>>
    <a href="<?php the_permalink(); ?>" class="calc-card__inner" style="--cat-color:<?php echo esc_attr( $color ); ?>">

        <div class="calc-card__icon">
            <?php if ( $img ) : ?>
                <img src="<?php echo esc_url( $img ); ?>" alt="" loading="lazy">
            <?php else : ?>
                <i class="<?php echo esc_attr( $icon ); ?>" aria-hidden="true"></i>
            <?php endif; ?>
        </div>

        <div class="calc-card__body">
            <h3 class="calc-card__title"><?php the_title(); ?></h3>
            <?php if ( $show_excerpt && has_excerpt() ) : ?>
            <p class="calc-card__excerpt"><?php echo wp_trim_words( get_the_excerpt(), 12, '…' ); ?></p>
            <?php endif; ?>
            <?php if ( $cat ) : ?>
            <span class="calc-card__cat"><?php echo esc_html( $cat->name ); ?></span>
            <?php endif; ?>
        </div>

        <svg class="calc-card__arrow" xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M9 18l6-6-6-6"/></svg>

    </a>
</article>
