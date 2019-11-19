<?php
/**
 * Template Name: Blog page template
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package mmtheme
 */

get_header();
?>

    <div id="primary" class="content-area">
        <main id="main" class="site-main">
            <header class="entry-header">
                <?php the_title( '<h1 class="entry-title">', '</h1>' ); ?>
            </header><!-- .entry-header -->

            <?php
            global $wp_query, $post;
            $args = [
                'post_type'   => 'post',
                'posts_per_page' => '5',
                'paged' => get_query_var('paged') ?: 1 // page of pagination
            ];
            $wp_query = new WP_Query( $args );
            ?>

            <?php if ( $wp_query->have_posts() ) : ?>
                <div class="top-pagination"><?php posts_nav_link(); ?></div>
                <?php while ( $wp_query->have_posts() ) : $wp_query->the_post(); ?>
                <article id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                    <header class="entry-header">
                        <?php
                            the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
                        if ( 'post' === get_post_type() ) :
                            ?>
                            <div class="entry-meta">
                                <?php
                                mmtheme_posted_on();
                                mmtheme_posted_by();
                                ?>
                            </div><!-- .entry-meta -->
                        <?php endif; ?>
                    </header><!-- .entry-header -->
                    <?php mmtheme_post_thumbnail(); ?>
                    <div class="entry-content">
                        <?php the_excerpt(); ?>
                    </div><!-- .entry-content -->
                    <footer class="entry-footer">
                        <?php mmtheme_entry_footer(); ?>
                    </footer><!-- .entry-footer -->
                <?php endwhile; ?>

                <?php posts_nav_link(); ?>
                <?php  wp_reset_query(); ?>

            <?php else : ?>
                <p><?php esc_html_e( 'Posts not found.' ); ?></p>
            <?php endif; ?>

            </article><!-- #post-<?php the_ID(); ?>-->

        </main><!-- #main -->
    </div><!-- #primary -->

<?php
get_footer();
