<?php
/**
 * Snippets of code that are reused in multiple projects.
 *
 * Curated by Jenny Wong and Austin Ainley.
 *
 * References:
 *      http://wpsnipp.com
 *      http://wp-snippets.com/
 *
 */

/**
 * Truncate a string by desired character length
 *
 * @param string $content
 * @param integer $chars
 * @return string
 */
function truncate($content, $chars) {
    $content = strip_shortcodes($content);

    if (strlen($content) < $chars)
        return $content;

    $working_string = substr($content, 0, ($chars - 3));
    $pos = strrpos($working_string, " ");

    if ($pos !== false) {
        $return_string = substr($content, 0, $pos);
    } else {
        $return_string = substr($content, 0, $chars);
    }

    $return_string .= "...";
    return $return_string;
}


/**
 *  Remove admin bar when not editor or administrator
 *
 *  @return $page->ID , null
 */
if (!current_user_can('edit_pages')){
    show_admin_bar(false);
}

function get_id_by_slug($page_slug) {
    global $wpdb;
    $page = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_name = '$page_slug'");

    if ($page) {
        return $page;
    } else {
        return null;
    }
}

/**
 * Get current page depth
 *
 * @return integer
 */
function get_current_page_depth(){
    global $wp_query;

    $object = $wp_query->get_queried_object();
    $parent_id  = $object->post_parent;
    $depth = 0;
    while($parent_id > 0){
        $page = get_page($parent_id);
        $parent_id = $page->post_parent;
        $depth++;
    }

    return $depth;
}

/**
 * Login with username or email address
 *
 * @param string $username
 * @return  string
 */
function login_with_email_address($username) {
        $user = get_user_by('email',$username);
        if(!empty($user->user_login))
            $username = $user->user_login;
        return $username;
}
add_action('wp_authenticate','login_with_email_address');

/**
 * Change the text on the login page from "username" to "username / email"
 *
 * @param string $text
 * @return string
 */
function change_username_wps_text($text){
       if(in_array($GLOBALS['pagenow'], array('wp-login.php'))){
         if ($text == 'Username'){$text = 'Username / Email';}
            }
                return $text;
         }
add_filter( 'gettext', 'change_username_wps_text' );

// Check if page is direct child
function is_child($page_id) {
    global $post;

    if (is_page() && ($post->post_parent == $page_id)) {
        return true;
    } else {
        return false;
    }
}

// Check if page is an ancestor
function is_ancestor($post_id) {
    global $wp_query;

    $ancestors = $wp_query->post->ancestors;

    if (in_array($post_id, $ancestors)) {
        return true;
    } else {
        return false;
    }
}

/**
 * Remove comments section from wp-admin
 */
function rd_remove_admin_comments() {
    add_action( 'admin_menu', 'rd_remove_admin_comments_menu' );
    function rd_remove_admin_comments_menu() {
        remove_menu_page( 'edit-comments.php' );
    }

    // Removes from post and pages
    add_action('init', 'remove_comment_support', 100);
    function remove_comment_support() {
        remove_post_type_support( 'post', 'comments' );
        remove_post_type_support( 'page', 'comments' );
    }

    // Removes from admin bar
    add_action( 'wp_before_admin_bar_render', 'rd_admin_bar_render' );
    function rd_admin_bar_render() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('comments');
    }
}
add_action('init', 'rd_remove_admin_comments');

/**
 * Remove posts section from wp-admin
 */
function rd_remove_admin_posts() {
    add_action( 'admin_menu', 'rd_remove_admin_posts_menu' );
    function rd_remove_admin_posts_menu() {
        remove_menu_page( 'edit.php' );
    }

    // Removes from admin bar
    add_action( 'wp_before_admin_bar_render', 'rd_admin_bar_render_posts' );
    function rd_admin_bar_render_posts() {
        global $wp_admin_bar;
        $wp_admin_bar->remove_menu('posts');
    }
}
add_action('init', 'rd_remove_admin_posts');


function rd_pagniation($echo = true){
	// Pagination
	global $wp_query;
	$unlikely_integer = 999999999;
	echo '<pre>';
		var_dump($wp_query);
	echo '</pre>';
	$pagination_args = array(
		'base' => str_replace($unlikely_integer, '%#%', get_pagenum_link($unlikely_integer)),
		'format' => '/page/%#%',
		'current' => max(1, get_query_var('paged')),
		'total' => $wp_query->max_num_pages
	);

	$html = '';

	$html .= '<p class="pagination">';

	$html .=  paginate_links($pagination_args);

	$html .= '</p>';

	if($echo){
		echo $html;
	} else {
		return $html;
	}
}

/**
 * Generic Login Error - Don't diffenciate between Username or Password Error.
 */
 add_filter('login_errors', create_function('$a', "return '<b>Error:</b> Invalid Username or Password';"));
