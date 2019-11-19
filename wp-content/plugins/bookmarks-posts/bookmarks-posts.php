<?php
/*
Plugin Name: Bookmarks Posts
Plugin URI:
Description: Add a button and icon “Add to Bookmarks” for each post in the archives
Version: 1.0.0
Author: Oleg Kovalyov
Author URI:
License: GPLv2
*/


// don't call the file directly
if ( !defined( 'ABSPATH' ) )
    exit;

require_once dirname( __FILE__ ) . '/bookmarks-posts.php';

/**
 * Bookmarks_Posts_Plugin class
 *
 * @class The class that holds the entire Bookmarks Posts plugin
 */
class Bookmarks_Posts_Plugin {
    /**
     * @var string table name
     */
    private $table;

    /**
     * @var object $wpdb object
     */
    private $db;

    /**
     * Initializes the plugin.
     *
     * To keep the initialization fast, only add filter and action
     * hooks in the constructor.
     * @uses register_activation_hook()
     * @uses register_deactivation_hook()
     * @uses is_admin()
     * @uses add_action()
     */
    public function __construct() {
        global $wpdb;

        // setup table name
        $this->db = $wpdb;
        $this->table = $this->db->prefix . 'bookmarks_posts';

        register_activation_hook( __FILE__, array($this, 'activate') );
        register_deactivation_hook( __FILE__, array($this, 'deactivate') );

        // Localize our plugin
        add_action( 'init', array($this, 'localization_setup') );

        // Loads AJAX script and frontend scripts and styles
        add_action( 'wp_enqueue_scripts', array($this, 'enqueue_scripts') );

        // Adds [bookmarks-posts-list] shortcode on 'Your Bookmarks' page (http://test4.local/member-bookmarks/)
        add_shortcode('bookmarks-posts-list', array($this, 'display_bookmarks_posts_shortcode'));

        // Adds AJAX action handle
        add_action( 'wp_ajax_bmp_action', array($this, 'ajax_bookmarks_posts') );
    }

    /**
     * Initializes the Bookmarks_Posts_Plugin() class
     *
     * Checks for an existing Bookmarks_Posts_Plugin() instance
     * and if it doesn't find one, creates it.
     */
    public static function init() {
        static $instance = false;

        if ( !$instance ) {
            $instance = new Bookmarks_Posts_Plugin();
        }

        return $instance;
    }

    /**
     * Placeholder for activation function
     *
     * Creates DB Table and 'Your Bookmarks' page.
     */
    public static function activate() {
        global $wpdb;
        // Create the DB Table 'mm_bookmarks_posts'
        $sql = "CREATE TABLE IF NOT EXISTS {$wpdb->table} (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `user_id` int(11) unsigned NOT NULL DEFAULT '0',
          `post_id` int(11) unsigned NOT NULL DEFAULT '0',
          `post_type` varchar(20) NOT NULL,
          PRIMARY KEY (`id`),
          KEY `user_id` (`user_id`),
          KEY `post_id` (`post_id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;";

        $this->db->query( $sql );

        // Create the plugin's page 'Your Bookmarks' with [bookmarks-posts-list] shortcode
        $page_definitions = array(
            'member-bookmarks' => array(
                'title' => __('Your Bookmarks', 'bookmarks-posts'),
                'content' => '[bookmarks-posts-list]',
            )
        );

        foreach ($page_definitions as $slug => $page) {
            // Check that the page doesn't exist already
            $query = new WP_Query('pagename=' . $slug);
            if (!$query->have_posts()) {
                // Add the page using the data from the array above
                wp_insert_post(
                    array(
                        'post_content' => $page['content'],
                        'post_name' => $slug,
                        'post_title' => $page['title'],
                        'post_status' => 'publish',
                        'post_type' => 'page',
                        'ping_status' => 'closed',
                        'comment_status' => 'closed',
                    )
                );
            }
        }
    }

    /**
     * Deactevates the Plugin
     *
     * Nothing being called here yet.
     */
    public function deactivate() {
        // TODO
    }

    /**
     * Initialize plugin for localization
     *
     * @uses load_plugin_textdomain()
     */
    public function localization_setup() {
        load_plugin_textdomain( 'bmp', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
    }

    /**
     * Enqueue admin and frontend scripts
     *
     * Allows plugin assets to be loaded.
     *
     * @uses wp_enqueue_script()
     * @uses wp_localize_script()
     * @uses wp_enqueue_style
     */
    public function enqueue_scripts() {

        /**
         * All styles goes here
         */
        wp_enqueue_style( 'bmp-styles', plugins_url( 'css/style.css', __FILE__ ), false, date( 'Ymd' ) );

        /**
         * All scripts goes here
         */
        wp_enqueue_script( 'bmp-scripts', plugins_url( 'js/script.js', __FILE__ ), array('jquery'), false, true );


        /**
         * Adds 'ajaxurl' on front end of the site
         */
        wp_localize_script( 'bmp-scripts', 'bmp', array(
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'bmp_nonce' ),
            'errorMessage' => __( 'Something went wrong', 'bmp' )
        ) );
    }

    /**
     * Ajax handler for the post with a bookmark inserting
     *
     * @return void
     */
    function ajax_bookmarks_posts() {
        check_ajax_referer( 'bmp_nonce', 'nonce' );

        // bail out if not logged in
        if ( !is_user_logged_in() ) {
            wp_send_json_error();
        }

        // so, the user is logged in huh? proceed on
        $post_id = isset( $_POST['post_id'] ) ? intval( $_POST['post_id'] ) : 0;
        $user_id = get_current_user_id();

        if ( !$this->get_post_props( $post_id, $user_id ) ) {

            $this->insert_bookmark_choice( $post_id, $user_id );

            wp_send_json_success( '<i class="fa fa-bookmark" aria-hidden="true"></i> <span class="bmp-bookmark">&nbsp;'  . __( 'Remove from Bookmark', 'bmp' ) . '</span> ' );
        } else {

            $this->delete_bookmark_choice( $post_id, $user_id );

            wp_send_json_success( '<i class="fa fa-bookmark-o" aria-hidden="true"></i><span class="bmp-not-bookmark">&nbsp;'  . __( 'Add to Bookmark', 'bmp' ) . '</span> ' );
        }
    }

    /**
     * Gets the post ID and the user ID for the bookmarked post
     *
     * @param int $post_id
     * @param int $user_id
     * @return bool|object
     */
    function get_post_props( $post_id, $user_id ) {
        $sql = "SELECT post_id FROM {$this->table} WHERE post_id = %d AND user_id = %d";

        return $this->db->get_row( $this->db->prepare( $sql, $post_id, $user_id ) );
    }

    /**
     * Inserts a user bookmarked choice
     *
     * @param int $post_id
     * @param int $user_id
     * @param int $vote
     * @return bool
     */
    public function insert_bookmark_choice( $post_id, $user_id ) {
        $post_type = get_post_field( 'post_type', $post_id );

        return $this->db->insert(
            $this->table,
            array(
                'post_id' => $post_id,
                'post_type' => $post_type,
                'user_id' => $user_id,
            ),
            array(
                '%d',
                '%s',
                '%d',
            )
        );
    }

    /**
     * Delete a user bookmarked choice
     *
     * @param int $post_id
     * @param int $user_id
     * @return bool
     */
    public function delete_bookmark_choice( $post_id, $user_id ) {
        $query = "DELETE FROM {$this->table} WHERE post_id = %d AND user_id = %d";

        return $this->db->query( $this->db->prepare( $query, $post_id, $user_id ) );
    }

    /**
     * Gets bookmarked posts from DB
     *
     * @param int $post_type
     * @param int $count
     * @param int $offset
     * @return array
     */
    function get_bookmarked_posts( $post_type = 'all', $user_id = 0, $count = 10, $offset = 0 ) {
        $where = 'WHERE user_id = ';
        $where .= $user_id ? $user_id : get_current_user_id();
        $where .= $post_type == 'all' ? '' : " AND post_type = '$post_type'";


        $sql = "SELECT post_id, post_type
                FROM {$this->table}
                $where
                GROUP BY post_id
                ORDER BY post_type
                LIMIT $offset, $count";

        $result = $this->db->get_results( $sql );

        return $result;
    }

    /**
     * Changes Bookmark button link text
     *
     * @param int $post_id
     * @return void
     */
    function bookmark_button_text( $post_id ) {

        if ( !is_user_logged_in() ) {
            return;
        }

        $status = $this->get_post_props( $post_id, get_current_user_id() );
        ?>

        <a class="bmp-bookmark-link" href="#" data-id="<?php echo $post_id; ?>">
            <?php if ( $status ) { ?>
                <i class="fa fa-bookmark" aria-hidden="true"></i> <span class="bmp-bookmark">&nbsp;<?php _e( 'Remove from Bookmark', 'bmp' ); ?></span>
            <?php } else { ?>
                <i class="fa fa-bookmark-o" aria-hidden="true"></i> <span class="bmp-not-bookmark">&nbsp;<?php _e( 'Add to Bookmark', 'bmp' ); ?></span>
            <?php } ?>
        </a>

        <?php
    }

    /**
     * Shows Bookmarked Posts List
     *
     * @param string $post_type
     * @param int $user_id
     * @param int $limit
     * @param bool $show_remove
     */
    public function show_bookmarked_posts_list($post_type = 'all', $user_id = false, $limit = 10, $show_remove = true) {

        $posts = $this->get_bookmarked_posts($post_type, $user_id, $limit);

        if ($posts) {

            $remove_title = __('Remove bookmark', 'bmp');
            $remove_link = ' <a href="#" data-id="%s" title="%s" class="bmp-remove-bookmark">x</a>';

            foreach ($posts as $item) {
                $post_data = get_post($item->post_id);
                $thumbnail = get_the_post_thumbnail( $item->post_id, [172,115] );
                $content = $post_data->post_content;
                $extra = $show_remove ? sprintf($remove_link, $item->post_id, $remove_title) : '';
                echo '<header class="entry-header">';
                    printf('<h2><a href="%s">%s</a>%s</h2>', get_permalink($item->post_id), get_the_title($item->post_id), $extra);
                echo '</header>';
                echo '<div class="entry-meta">';
                if ( ! function_exists( 'posted_on' ) ) :
                    Bookmarks_Posts_Plugin::posted_on();
                endif;
                if ( ! function_exists( 'posted_by' ) ) :
                    Bookmarks_Posts_Plugin::posted_by();
                endif;
                echo '</div>';
                echo '<div>' . $thumbnail . '</div>';
                echo '<div class="entry-content"><p>' . wp_trim_words( $content, 25, '...' ) . '</p></div>';
                mmtheme_entry_footer();
            }
        } else {
            printf('<h5>%s</h5>', __('Bookmarked posts not found.', 'bmp'));
        }
    }

    /**
     * Prints HTML with meta information for the current post-date/time.
     */
    public static function posted_on() {
        $time_string = '<time class="entry-date published updated" datetime="%1$s">%2$s</time>';
        if ( get_the_time( 'U' ) !== get_the_modified_time( 'U' ) ) {
            $time_string = '<time class="entry-date published" datetime="%1$s">%2$s</time><time class="updated" datetime="%3$s">%4$s</time>';
        }

        $time_string = sprintf( $time_string,
            esc_attr( get_the_date( DATE_W3C ) ),
            esc_html( get_the_date() ),
            esc_attr( get_the_modified_date( DATE_W3C ) ),
            esc_html( get_the_modified_date() )
        );

        $posted_on = sprintf(
        /* translators: %s: post date. */
            esc_html_x( 'Posted on %s', 'post date', 'mmtheme' ),
            '<a href="' . esc_url( get_permalink() ) . '" rel="bookmark">' . $time_string . '</a>'
        );

        echo '<span class="posted-on">' . $posted_on . '</span>'; // WPCS: XSS OK.

    }	/**
 * Prints HTML with meta information for the current author.
 */
    function posted_by() {
        $byline = sprintf(
        /* translators: %s: post author. */
            esc_html_x( 'by %s', 'post author', 'mmtheme' ),
            '<span class="author vcard"><a class="url fn n" href="' . esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ) . '">' . esc_html( get_the_author() ) . '</a></span>'
        );

        echo '<span class="byline"> ' . $byline . '</span>'; // WPCS: XSS OK.

    }

    /**
     * Shortcode for displaying bookmarked posts (on 'Your Bookmarks' page)
     *
     * @global object $post
     * @param array $atts
     * @return string
     */
    function display_bookmarks_posts_shortcode( $atts ) {
        global $post;

        ob_start();
        $atts = extract( shortcode_atts( array('user_id' => 0, 'count' => 10, 'post_type' => 'all', 'remove_link' => false), $atts ) );

        if ( !$user_id ) {
            $user_id = get_current_user_id();
        }

        $this->show_bookmarked_posts_list( $post_type, $user_id, $count, $remove_link );

        return;
    }
}

// Initialize the plugin
$bookmarks_posts_plugin = Bookmarks_Posts_Plugin::init();

/**
 * Wrapper function for favorite post button
 *
 * @global type $post
 * @param type $post_id
 */
function bmp_button( $post_id = null ) {
    global $post;

    if ( !$post_id ) {
        $post_id = $post->ID;
    }

    Bookmarks_Posts_Plugin::init()->bookmark_button_text( $post_id );
}