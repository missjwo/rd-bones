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
 * Generic Login Error - Don't diffenciate between Username or Password Error.
 */
 add_filter('login_errors', create_function('$a', "return '<b>Error:</b> Invalid Username or Password';"));
