<?php
/*
Plugin Name: post_date_GMT fixer
Plugin URI: https://github.com/lostfocus
Version: 0.0.1
Description: This fixes the GMT post date field for posts where the GMT post date field is set to 1970-01-01 00:00:00.
Author: Dominik Schwind
Author URI: http://lostfocus.de/
*/

class ds_fix_post_date {
	static public function admin_menu() {
		add_management_page(
			'Fix post_date_GMT',
			'Fix post_date_GMT',
			'edit_posts',
			'fix_post_date_gmt',
			array('ds_fix_post_date','page')
		);
	}

	static public function page() {
		$posts = self::_get_post_ids_to_fix();
		if(isset($_POST['submit'])) {
			check_admin_referer( 'ds_fix_post_date' );
			foreach ( $posts as $post_data ) {
				self::_set_post_date_gmt($post_data->id,$post_data->post_date);
			}
			$posts = self::_get_post_ids_to_fix();
		}
		if(count($posts) > 0):
			?>
			<h2>Fix post_date_GMT</h2>
			<form method="post">
				Fix <?php echo count ( $posts ); ?> posts?
				<?php wp_nonce_field( 'ds_fix_post_date' ); ?>
				<?php submit_button('Sure'); ?>
			</form>
			<?php
		else:
			?>
			<div class="notice"><p>Nothing to do!</p></div>
			<?php
		endif;
	}

	protected function _set_post_date_gmt($id,$post_date){
		global $wpdb;
		$wpdb->query("UPDATE $wpdb->posts SET post_date_gmt = \"".get_gmt_from_date($post_date)."\" WHERE ID = ".$id);
	}

	protected function _get_post_ids_to_fix(){
		global $wpdb;
		$post_ids = $wpdb->get_results("SELECT id, post_date FROM $wpdb->posts WHERE post_date_gmt = '1970-01-01 00:00:00'");
		return $post_ids;
	}
}

/*
 * Hooks
 */

add_action( 'admin_menu', array('ds_fix_post_date','admin_menu') );
