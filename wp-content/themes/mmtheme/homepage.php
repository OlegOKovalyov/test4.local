<?php
/**
 * Template Name: Home page template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package mmtheme
 */

get_header();
?>

	<div id="primary" class="content-area">
		<main id="main" class="site-main">
            <div class="container">
                <div class="row">
                    <?php
                    if ( have_posts() ) :

                        if ( is_home() && ! is_front_page() ) :
                            ?>
                            <header>
                                <h1 class="page-title screen-reader-text"><?php single_post_title(); ?></h1>
                            </header>
                        <?php
                        endif;

                        /* Start the Loop */
                        while ( have_posts() ) :
                            the_post();

                            /*
                             * Include the Post-Type-specific template for the content.
                             * If you want to override this in a child theme, then include a file
                             * called content-___.php (where ___ is the Post Type name) and that will be used instead.
                             */
                            get_template_part( 'template-parts/content', get_post_type() );

                        endwhile;

                        the_posts_navigation();

                    else :

                        get_template_part( 'template-parts/content', 'none' );

                    endif;
                    ?>
                </div>
            </div>

		</main><!-- #main -->

        <div class="recent-posts">
            <div class="container">
                <div class="row">
                    <h3>Recent news</h3>
                    <div class="recent-posts-wrap">
                        <?php
                        $args = array(
                            'numberposts' => 6,
                            'category'    => 3,
                            'post_type'   => 'post',
                            'post_status' => 'publish',
                        );

                        $result = wp_get_recent_posts($args); ?>
                        <div class="recent-posts-list">
                            <?php foreach( $result as $p ){ ?>
                                <div class="recent-posts-item">
                                    <?php echo get_the_post_thumbnail( $p['ID'], array(172,115)); ?>
                                    <h6><?php echo '<a href="' . get_permalink($p['ID']) . '">' . $p['post_title'] . '</a>'; ?></h6>
                                </div>
                            <?php } ?>
                        </div>
                        <?php
                            wp_reset_postdata();
                        ?>
                    </div>
                </div>
            </div>
        </div>

	</div><!-- #primary -->

<?php
get_sidebar();
get_footer();
