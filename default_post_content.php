<?php
/*
Plugin Name: Default Post Content
Plugin URI: http://apartmentonesix.com/plugins/default_post_content
Description: Helps save time by giving you options for default post content and default post meta for new posts.
Author: Peter Butler
Version: 1.0
Author URI: http://apartmentonesix.com
*/

// ===================== 
// = DEFAULT POST META = 
// ===================== 


add_action('transition_post_status', 'dpc_set_postmeta_flag', 10, 3);

add_action('edit_post', 'dpc_set_postmeta_defaults');

add_filter( 'default_content', 'dpc_set_content_default' );

add_action('admin_menu', 'dpc_menu');

add_filter('admin_head','dpc_editor_scripts');

add_action('init', 'update_meta_default');

register_deactivation_hook(WP_PLUGIN_DIR.'/default-post-content/default_post_content.php', 'dpc_deactivate');

//This action runs every time a post status is changed - 
//We're watching for the initial save or autosave of a post.
function dpc_set_postmeta_flag($new_status, $old_status, $post){
  //If the post is not a revision, and it's old status is "new", 
  //we know it is a newly created post, so we set the default flag
  if(!wp_is_post_revision($post->ID) && $old_status == 'new'){
    update_post_meta($post->ID, '_set_default_values', 'true');
    //Now send a backup of the default post meta to an option
    //We allow the user to change it here, so when that is done, we want
    //to be able to revert back to the original
  }
}

//This function runs on post save (edit)
//We're checking to see if the post being saved has our default
//flag.  If it does, we'll set the default post values (as they've been
//updated on the add-post page)
function dpc_set_postmeta_defaults($id){
  if($parent_id = wp_is_post_revision($id)){
    $id = $parent_id;
  }
  if(get_post_meta($id, '_set_default_values', true) == 'true'){
    $default_meta = has_meta(0);
    if(is_array($default_meta)){
      foreach($default_meta as $meta){
        update_post_meta($id, $meta['meta_key'], $meta['meta_value']);
      }
    }
    //Get rid of the flag, and reset the defaults to the real defaults
    delete_post_meta($id, '_set_default_values');
    //Now revert the default post meta back in case the user changed it
    $default_meta = get_option('dpc_postmeta');
    if(is_array($default_meta)){
      foreach($default_meta as $meta){
        update_post_meta(0, $meta['meta_key'], $meta['meta_value']);
      }
    }
  }
}


function dpc_set_content_default( $content ) {

	$content = stripslashes(get_option('dpc_content'));

	return $content;
}

// =============== 
// = ADMIN PANEL = 
// =============== 
function dpc_menu() {
  add_options_page('Default Post Content', 'Default Post Content', 8, 'dpc', 'dpc_options');
}

//Set up js to use tinyMCE on the options page
//Revert default post meta values if on new post page or dpc options page
function dpc_editor_scripts() {

  global $pagenow;
  if($pagenow == 'post-new.php' || $pagenow == 'options-general.php'){
    $default_meta = get_option('dpc_postmeta');
    if(is_array($default_meta)){
      foreach($default_meta as $meta){
        update_post_meta(0, $meta['meta_key'], $meta['meta_value']);
      }
    }
  }

  if('Default Post Content' == get_admin_page_title()){
    //Big thanks to Anthony and this post:
    //http://blog.zen-dreams.com/en/2008/11/06/how-to-include-tinymce-in-your-wp-plugin/
  	wp_admin_css('thickbox');
  	wp_print_scripts('jquery-ui-core');
  	wp_print_scripts('jquery-ui-tabs');
  	wp_print_scripts('post');
  	wp_print_scripts('editor');
  	add_thickbox();
  	wp_print_scripts('media-upload');
  	if (function_exists('wp_tiny_mce')) wp_tiny_mce();
    // use the if condition because this function doesn't exist in version prior to 2.7
  }
}

function dpc_options() {
  if(isset($_POST['content'])){
    $option_name = 'dpc_content' ; 
    $newvalue = $_POST['content'] ;
    if ( get_option($option_name) ) {
      update_option($option_name, $newvalue);
    } else {
      add_option($option_name, $newvalue);
    }
  }
  $content = stripslashes(get_option('dpc_content'));
?>
  <div class="wrap">
    <h2>Default Post Content</h2>
    <div style="width:600px;">
      <form method="post" action="">
        <h3>Post Content</h3>
        <div id="poststuff">
          <div id="postdivrich" class="postarea">
            <?php the_editor($content, $id = 'content', $prev_id = 'title', $media_buttons = true, $tab_index = 2); ?>
          </div>
        </div>       
        <p class="submit">
          <input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
        </p>
      </form>
      <h3>Post Meta</h3>
      <small>Note:  Each post meta combo must be saved by hitting the "update" button.</small>
      <div id="postcustom" class="postbox">
        <div id="postcustomstuff">
          <div id="ajax-response"></div>
          <?php
          $metadata = has_meta(0);
          list_meta($metadata);
          meta_form();
          ?>
        </div>
      </div>
    </div>
  </div>
<?php
}

function update_meta_default(){
  global $pagenow;

  if(get_option('update_dpc') == 'update'){
    //wp-admin/includes/post.php contains the has_meta function, which we need to get post meta out
    //of post id 0
    include_once(ABSPATH.'wp-admin/includes/post.php');
    $newpostmeta = has_meta(0);
    update_option('dpc_postmeta', $newpostmeta);
    update_option('update_dpc', 'noupdate');
  }  

  if(parse_url($_SERVER['HTTP_REFERER'], PHP_URL_QUERY) == 'page=dpc' && $pagenow == 'admin-ajax.php'){
    update_option('update_dpc', 'update');
  }
}

function dpc_deactivate(){
  $defaultmeta = has_meta(0);
  foreach($defaultmeta as $meta){
    delete_post_meta(0, $meta['meta_key']);
  }
}

?>