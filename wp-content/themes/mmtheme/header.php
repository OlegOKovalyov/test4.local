<?php
/**
 * The header for our theme
 *
 * This is the template that displays all of the <head> section and everything up until <div id="content">
 *
 * @link https://developer.wordpress.org/themes/basics/template-files/#template-partials
 *
 * @package mmtheme
 */

?>
<!doctype html>
<html <?php language_attributes(); ?>>
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
    <link href="https://fonts.googleapis.com/css?family=Oswald:300,400,700&display=swap" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css?family=PT+Serif:400,400i,700&display=swap" rel="stylesheet">
    <link href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css" rel="stylesheet" integrity="sha384-wvfXpqpZZVQGK6TAh5PVlGOfQNHSoD2xbE+QkPxCAFlNEevoEH3Sl0sibVcOQVnN" crossorigin="anonymous">

	<?php wp_head(); ?>
</head>

<body <?php body_class(); ?>>
<div id="page" class="site">
	<a class="skip-link screen-reader-text" href="#content"><?php esc_html_e( 'Skip to content', 'mmtheme' ); ?></a>

	<header id="masthead" class="site-header">
        <div class="container">
            <div class="row">
                <div class="site-branding">
                    <?php
                    the_custom_logo();
                    if ( is_front_page() && is_home() ) :
                        ?>
                        <!--				<h1 class="site-title"><a href="--><?php //echo esc_url( home_url( '/' ) ); ?><!--" rel="home">--><?php //bloginfo( 'name' ); ?><!--</a></h1>-->
                    <?php
                    else :
                        ?>
                        <!--				<p class="site-title"><a href="--><?php //echo esc_url( home_url( '/' ) ); ?><!--" rel="home">--><?php //bloginfo( 'name' ); ?><!--</a></p>-->
                    <?php
                    endif;
                    $mmtheme_description = get_bloginfo( 'description', 'display' );
                    if ( $mmtheme_description || is_customize_preview() ) :
                        ?>
                        <p class="site-description"><?php echo $mmtheme_description; /* WPCS: xss ok. */ ?></p>
                    <?php endif; ?>
                </div><!-- .site-branding -->
                <div class="site-login">
                    <div class="site-login-wrap">
                        <a href="/member-login/"><i class="fa fa-key" aria-hidden="true"></i> Login</a>
                        <button type="button" class="btn btn-primary contact-me">Contact Me</button>
                    </div>
                </div>
            </div><!-- .row -->
        </div><!-- .container -->

		<nav id="site-navigation" class="main-navigation">
            <div class="container">
                <div class="row">
                    <div class="main-menu">
                        <button class="menu-toggle" aria-controls="primary-menu" aria-expanded="false"><?php esc_html_e( 'Primary Menu', 'mmtheme' ); ?></button>
                        <?php
                        wp_nav_menu( array(
                            'theme_location' => 'menu-1',
                            'menu_id'        => 'primary-menu',
                        ) );
                        ?>
                    </div>
                    <div class="input-group">
                        <form action="<?php bloginfo( 'url' ); ?>" method="get">
                            <input class="form-control border-none" type="search" name="s" placeholder="Search" value="<?php if(!empty($_GET['s'])){echo $_GET['s'];}?>"/>
                            <div class="input-group-append">
                                <button class="btn btn-outline-primary" type="submit">
                                    <i class="fa fa-search" aria-hidden="true"></i>
                                </button>
                            </div>
                        </form>
                    </div>
                </div><!-- .row -->
            </div><!-- .container -->
		</nav><!-- #site-navigation -->
	</header><!-- #masthead -->

	<div id="content" class="site-content">
        <div class="container">
            <div class="row d-block">
