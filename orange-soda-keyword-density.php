<?php
/*
Plugin Name: OrangeSoda Keyword Density
Plugin URI: http://orangesoda.com
Description: Displays top 6 keywords for a post/page along with the density of each word.
Version: 1.0.0
Author: OrangeSoda
Author URI: http://orangesoda.com
*/

//Creates Custom Fields for choosing a background by page
$meta_boxes[] = array(
	'id' => 'os-bkg-box',
	'title' => 'OrangeSoda Keywords',
	'pages' => array('post', 'page'),
	'context' => 'side',
	'priority' => 'low',
	'fields' => array(
		array(
			'name' => 'Textarea',
			'desc' => 'Enter big text here',
			'id' => $prefix . 'textarea',
			'type' => 'osKeyword', // text area
			'std' => 'Default value 2'
		),
	)
);

/*********************************

Do not edit the code below

*********************************/

foreach ($meta_boxes as $meta_box) {
	$my_box = new My_meta_box($meta_box);
}

class My_meta_box {

	protected $_meta_box;

	// create meta box based on given data
	function __construct($meta_box) {
		if (!is_admin()) return;

		$this->_meta_box = $meta_box;

		// fix upload bug: http://www.hashbangcode.com/blog/add-enctype-wordpress-post-and-page-forms-471.html
		$current_page = substr(strrchr($_SERVER['PHP_SELF'], '/'), 1, -4);
		if ($current_page == 'page' || $current_page == 'page-new' || $current_page == 'post' || $current_page == 'post-new') {
			add_action('admin_head', array(&$this, 'add_post_enctype'));
		}

		add_action('admin_menu', array(&$this, 'add'));

		add_action('save_post', array(&$this, 'save'));
	}

	function add_post_enctype() {
		echo '
		<script type="text/javascript">
		jQuery(document).ready(function(){
			jQuery("#post").attr("enctype", "multipart/form-data");
			jQuery("#post").attr("encoding", "multipart/form-data");
		});
		</script>';
	}

	/// Add meta box for multiple post types
	function add() {
		foreach ($this->_meta_box['pages'] as $page) {
			add_meta_box($this->_meta_box['id'], $this->_meta_box['title'], array(&$this, 'show'), $page, $this->_meta_box['context'], $this->_meta_box['priority']);
		}
	}

	// Callback function to show fields in meta box
	function show() {
		global $post;

		// Use nonce for verification
		echo '<input type="hidden" name="mytheme_meta_box_nonce" value="', wp_create_nonce(basename(__FILE__)), '" />';

		foreach ($this->_meta_box['fields'] as $field) {
			// get current post meta data
			$meta = get_post_meta($post->ID, $field['id'], true);

			switch ($field['type']) {
				case 'osKeyword':
					echo '<img style="width: 260px;" src="../wp-content/plugins/orange-soda-keyword-density/images/logo.png" alt="OrangeSoda - Internet Marketing with Fizz" /><br /><div id="os_results"></div><br /><label>Find the density of a word or phrase</label><input type="text" id="orangeSoda_search_phrase" /><button id="orangeSoda_search_button" type="button">Search</button><br /><br /><div id="orange_soda_search_density"></div><div>To update the word count, save the post/page.</div><div style="margin-top: 15px;"><a style="color: orange;" href="http://www.orangesoda.com">Click here for more information, and more SEO tips and tricks.</a></div><div id="os_word_counter" style="display:none"></div>';
					break;
			}
		}

	}

	// Save data from meta box
	function save($post_id) {
		// verify nonce
		if (!wp_verify_nonce($_POST['mytheme_meta_box_nonce'], basename(__FILE__))) {
			return $post_id;
		}

		// check autosave
		if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
			return $post_id;
		}

		// check permissions
		if ('page' == $_POST['post_type']) {
			if (!current_user_can('edit_page', $post_id)) {
				return $post_id;
			}
		} elseif (!current_user_can('edit_post', $post_id)) {
			return $post_id;
		}

		foreach ($this->_meta_box['fields'] as $field) {
			$name = $field['id'];

			$old = get_post_meta($post_id, $name, true);
			$new = $_POST[$field['id']];

			if ($field['type'] == 'file' || $field['type'] == 'image') {
				$file = wp_handle_upload($_FILES[$name], array('test_form' => false));
				$new = $file['url'];
			}

			if ($new && $new != $old) {
				update_post_meta($post_id, $name, $new);
			} elseif ('' == $new && $old && $field['type'] != 'file' && $field['type'] != 'image') {
				delete_post_meta($post_id, $name, $old);
			}
		}
	}
}

// We need some CSS to position the paragraph
function os_scripts() {

	echo "
		<script type=\"text/javascript\" src=\"../wp-content/plugins/orange-soda-keyword-density/js/jquery.wordstats.js\"></script> <!-- core code -->
		<script type=\"text/javascript\" src=\"../wp-content/plugins/orange-soda-keyword-density/js/jquery.wordstat.en.js\"></script> <!-- English stop words -->
		<script type=\"text/javascript\" src=\"../wp-content/plugins/orange-soda-keyword-density/js/keywords.js\"></script> 
		<style type=\"text/css\">
			.orangesoda_word_table tr th { text-align: left; font-size: 14px; padding-bottom: 5px; }
		</style>

	";
}

add_action('admin_footer', 'os_scripts');


?>
