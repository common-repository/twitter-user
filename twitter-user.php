<?php
/*
Plugin Name: Twitter User
Plugin URI: http://www.bloggingtips.com/wordpress-plugins/twitter-user/
Description: Adds an additional box to the author page to add a twitter username in.
Author: SarahG
Version: 1.0
Author URI: http://www.stuffbysarah.net/
*/

function display_twitter($content = "") {
	global $post;
	$authorid = $post->post_author;
	$twitter = get_usermeta($authorid, 'twitter');
	
	if (!empty($twitter)) :
		$anchor = get_option('tw_anchor');
		$twimage = get_option('tw_image');
	
		if (empty($anchor)) $anchor = "Follow me on Twitter";
		
		if (!empty($twimage)) :
			$anchor = "<img src='".$twimage."' alt='".$anchor."' />";
		endif;
		
		$link = "<a href='http://www.twitter.com/".$twitter."' class='twitlink'>".$anchor."</a>";
	
		$attach = get_option('tw_display');
		
		$attach = explode(":", $attach);
	
		if (count($attach) && !empty($content)) :
			if ((is_front_page() && in_array('f', $attach)) || 
				(is_archive() && in_array('a', $attach)) || 
				(is_category() && in_array('a', $attach)) || 
				(is_single() && in_array('s', $attach)) || 
				(is_page() && in_array('p', $attach))) :
				
				$content .= "<p>".$link."</p>";
			endif;
			
			return $content;
		elseif (empty($content)) :
			echo $link;
			return TRUE;
		else :
			return $content;
		endif;
	else :
		return $content;
	endif;
}

// admin page options
function tw_adminmenu() {
	add_options_page('Twitter User', 'Twitter User', 8, basename(__FILE__), 'twitter_options');
}

function twitter_options() {
	if (isset($_POST['tw_options'])):
		update_option('tw_anchor', $_POST['tw_anchor']);
		update_option ('tw_image', $_POST['tw_image']);
		
		if (count($_POST['tw_display'])) :
			$tw_display = implode(":", $_POST['tw_display']);
		endif;
		
		update_option('tw_display', $tw_display);
		
		echo '<div id="message" class="updated fade"><p>Options Saved!</p></div>';
	endif;

	$tw_anchor = get_option('tw_anchor');
	$tw_image = get_option('tw_image');
	$tw_display = get_option('tw_display');

	if ($tw_display) :
		$tw_display = explode(":", $tw_display);
	else :
		$tw_display = array();
	endif;
	?>
    
<div class="wrap">
 	<form id="twitter_form" method="post" action="">
		<h2>Twitter Options</h2> 
		
		<div id="poststuff" class="ui-sortable">
	
		<div class="postbox">
			<h3 class="hndle"><span>Display Options</span></h3>
			<div class="inside">
	
   				<div><label for="tw_anchor">Anchor Text</label>
       				<input type="text" size="40" id="tw_anchor" name="tw_anchor" value="<?php echo $tw_anchor ?>" /></div>
       				
       			<div><label for="tw_image">Display Image (optional)</label>
       				<input type="text" size="40" id="tw_image" name="tw_image" value="<?php echo $tw_image ?>" /></div>
       			
       			<h4>Display After Post/Page On:</h4>
       				
				<div class="chkbox">
					<label for="tw_displayfp"><input type="checkbox" id="tw_displayfp" name="tw_display[]" value="f"<?php if (in_array('f', $tw_display)) echo ' checked="checked"' ?> />Front Page</label>
				</div>
       			<div class="chkbox"><label for="tw_displayac">
       				<input type="checkbox" id="tw_displayac" name="tw_display[]" value="a"<?php if (in_array('a', $tw_display)) echo ' checked="checked"' ?> />Archives/Categories</label>
       			</div>
       			<div class="chkbox"><label for="tw_displaysp">
       				<input type="checkbox" id="tw_displaysp" name="tw_display[]" value="s"<?php if (in_array('s', $tw_display)) echo ' checked="checked"' ?> />Single Post</label>
       			</div>	
       			<div class="chkbox"><label for="tw_displaystp">
       				<input type="checkbox" id="tw_displaystp" name="tw_display[]" value="p"<?php if (in_array('p', $tw_display)) echo ' checked="checked"' ?> />Static Page</label>
       			</div>
       			
       		</div>
       	</div>
	    <div class="submit"><input type="submit" name="tw_options" id="tw_options" value="Update Options" /></div>
	    </div>
	</form>
</div>
<?php	
}

//styling options page
function tw_style() {
	?>
<style type="text/css" media="screen">
  	#twitter_form label { font-weight: bold; display: block; }
  	#twitter_form div { clear: both; margin-top: 5px; }	
  	
  	#twitter_form div.chkbox label { display: inline; }
  	#twitter_form .chkbox input { margin-right: 5px }
</style>
<?php
}
		
function add_twitter_box() {
	global $user_ID;
	if (preg_match('&profile.php&', $_SERVER['REQUEST_URI'])) {
		$id = $user_ID;
	} elseif($_GET['user_id']) {
		$id = $_GET['user_id'];
	}
	
	$twitter = get_usermeta($id, 'twitter');
?>

<table class="form-table">
<tr>
	<th><label for="twitter">Twitter Username</label></th>
	<td><input type="text" name="twitter" id="twitter" value="<?php @print $twitter ?>" class="regular-text" /></td>
</tr>
</table>

<?php
}

function save_twitter() {
	global $user_ID;
	if (preg_match('&profile.php&', $_SERVER['REQUEST_URI'])) {
		$id = $user_ID;
	} elseif($_GET['user_id']) {
		$id = $_GET['user_id'];
	}
	
	$twitter = $_POST['twitter'];
	
	update_usermeta($id, 'twitter', $twitter);
}

add_action('show_user_profile', 'add_twitter_box');
add_action('edit_user_profile', 'add_twitter_box');
add_action('profile_update', 'save_twitter');
add_action('admin_menu', 'tw_adminmenu');
add_action('admin_head', 'tw_style');

add_filter('the_content', 'display_twitter');
?>