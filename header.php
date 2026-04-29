<!DOCTYPE html>
<html <?php language_attributes(); ?>>
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <link rel="profile" href="https://gmpg.org/xfn/11">
    <?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<div class="site-wrapper">

<?php
$sticky     = (bool) get_theme_mod('htheme_header_sticky', true);
$has_shadow = (bool) get_theme_mod('htheme_header_shadow', true);
$hide_scroll= (bool) get_theme_mod('htheme_header_hide_scroll', true);
$has_search = (bool) get_theme_mod('htheme_search_in_header', true);
$header_cls = implode(' ', array_filter([
    'site-header',
    $sticky     ? 'is-sticky' : '',
    $has_shadow ? 'has-shadow' : '',
    $hide_scroll ? 'can-hide-scroll' : '',
]));
?>

<header class="<?php echo esc_attr($header_cls); ?>" role="banner">
    <div class="container header-inner">

        <!-- Hamburger — sol, sadece mobilde -->
        <button class="btn-icon mobile-toggle" id="mobile-toggle" aria-label="Menü" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <!-- Logo — ortada/sola -->
        <a href="<?php echo esc_url(home_url('/')); ?>" class="site-logo" rel="home">
            <?php if (has_custom_logo()): the_custom_logo();
            else: echo '<span class="logo-text">' . get_bloginfo('name') . '</span>';
            endif; ?>
        </a>

        <!-- Nav — masaüstü -->
        <nav class="primary-nav" id="primary-nav" aria-label="Ana Menü">
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class'     => 'nav-menu',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ]); ?>
        </nav>

        <!-- Arama — sağ -->
        <div class="header-actions">
            <?php if ($has_search): ?>
            <button class="btn-icon search-toggle" id="search-toggle" aria-label="Ara" aria-expanded="false">
                <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
            </button>
            <?php endif; ?>
        </div>

    </div>

    <!-- Mobil açılır menü -->
    <div class="mobile-nav" id="mobile-nav">
        <div class="container">
            <?php wp_nav_menu([
                'theme_location' => 'primary',
                'menu_class'     => 'mobile-menu',
                'container'      => false,
                'depth'          => 2,
                'fallback_cb'    => false,
            ]); ?>
        </div>
    </div>

    <!-- Header arama -->
    <?php if ($has_search): ?>
    <div class="header-search" id="header-search" role="search">
        <div class="container">
            <form method="get" action="<?php echo esc_url(home_url('/')); ?>" class="hs-form">
                <input type="search" name="s" placeholder="Hesaplama aracı ara…"
                       value="<?php echo esc_attr( get_search_query() ); ?>" autocomplete="off">
                <button type="submit" aria-label="Ara">
                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    Ara
                </button>
            </form>
        </div>
    </div>
    <?php endif; ?>
</header>

<main class="site-content" id="content">
    <div class="container">
