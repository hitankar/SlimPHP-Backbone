<?php

namespace Roots\Sage\Extras;

use Roots\Sage\Setup;
use Roots\Sage\Assets;

/**
 * Add <body> classes
 */
function body_class($classes) {
	// Add page slug if it doesn't exist
	if (is_single() || is_page() && !is_front_page()) {
		if (!in_array(basename(get_permalink()), $classes)) {
			$classes[] = basename(get_permalink());
		}
	}

	// Add class if sidebar is active
	if (Setup\display_sidebar()) {
		$classes[] = 'sidebar-primary';
	}

	return $classes;
}
add_filter('body_class', __NAMESPACE__ . '\\body_class');

/**
 * Clean up the_excerpt()
 */
// function excerpt_more() {
//   return ' &hellip; <a href="' . get_permalink() . '">' . __('Continued', 'sage') . '</a>';
// }
// add_filter('excerpt_more', __NAMESPACE__ . '\\excerpt_more');

if (!function_exists('get_posts_by_meta_key_value')) {
	/**
	 * Get post id from meta key and value
	 * @param string $key
	 * @param mixed $value
	 * @return int|bool
	 * @author Hitankar Ray
	 */
	function get_posts_by_meta_key_value($meta_key, $meta_value) {
		global $wpdb;
		$posts = $wpdb->get_results("SELECT * FROM {$wpdb->posts} p LEFT JOIN {$wpdb->postmeta} pm ON p.ID = pm.post_id WHERE pm.meta_key = '{$wpdb->escape($meta_key)}' AND pm.meta_value = '{$wpdb->escape($meta_value)}' ORDER BY menu_order ASC");
		if (count($posts) > 0) {
			return $posts;
		} else {
			return false;
		}
	}
}

add_shortcode( 'toplist', __NAMESPACE__ . '\\toplist');
function toplist($atts) {
	$a = shortcode_atts( array(
		'id'        => null,
		'limit'     => 5,
		'paged'     => 3,
		'template'  => 1
	), $atts );

	$toplist = new TopList($a['id'], $a['limit'], $a['paged'], $a['template']);
	return $toplist->generate();
}

\add_action( 'admin_enqueue_scripts', function($hook) {
	if ( 'post-new.php' != $hook && 'post.php' != $hook ) {
			return;
	}
	wp_enqueue_script('forextraders/bootstrapjs', '//maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js', ['jquery'], null, true);
	wp_enqueue_script('forextraders/select2js', Assets\asset_path('scripts/select2.js'), ['jquery'], null, true);
	wp_enqueue_style('forextraders/select2css', Assets\asset_path('styles/select2.css'));
} );

add_filter('excerpt_more', function() {
	global $post;
	return '...<br /><br /><a class="btn btn-success" href="'. get_permalink($post->ID) . '"> Read More </a>';
});

add_filter( 'excerpt_length', function($length) {
	 return 25;
}, 99 );

add_filter( 'pre_get_posts', __NAMESPACE__ . '\\lesson_archive' );

function lesson_archive( $query ) {
		//if page is an archive and post_parent is not set and post_type is the post type in question
		if ( is_archive() && false == $query->query_vars['post_parent'] &&  $query->query_vars['post_type'] == 'lesson' && is_admin() == false ) {
				//set post_parent to 0, which is the default post_parent for top level posts
				$query->set( 'order_by', 'menu_order' );
				$query->set( 'order', 'ASC' );
				$query->set( 'posts_per_page', '-1' );
				$query->set( 'post__not_in', array( 372 ) );
		}
		return $query;

}

\add_action( 'toplist_type_add_form_fields', function($taxonomy) {
	?>
	<tr class="form-field term-conversion_tag-wrap">
		<th scope="row"><label for="leaderboard_string"><?php _e( 'GA Leaderboard AD', 'sage' ); ?></th>
		<td>
			<input type="text" name="leaderboard_string" id="leaderboard_string" value="">
			<p class="description"><?php _e( 'Enter a value for this field','sage' ); ?></p>
		</td>
	</tr>
<?php
});
\add_action( 'toplist_type_edit_form_fields', function($term) {
	$leaderboard_string = get_term_meta( $term->term_id, 'leaderboard_string', true );
?>
	<tr class="form-field term-conversion_tag-wrap">
		<th scope="row"><label for="leaderboard_string"><?php _e( 'GA Leaderboard AD', 'sage' ); ?></th>
		<td>
			<input type="text" name="leaderboard_string" id="leaderboard_string" value="<?= $leaderboard_string; ?>">
			<p class="description"><?php _e( 'Enter a value for this field','sage' ); ?></p>
		</td>
	</tr>
<?php
});
/** Save Custom Field Of Category Form */
add_action( 'created_toplist_type', __NAMESPACE__ . '\\toplist_type_form_custom_field_save', 10, 2 ); 
add_action( 'edited_toplist_type', __NAMESPACE__ . '\\toplist_type_form_custom_field_save', 10, 2 );
 
function toplist_type_form_custom_field_save( $term_id, $tt_id ) {

		if ( isset( $_POST['leaderboard_string'] ) ) {           
			update_term_meta( $term_id, 'leaderboard_string', $_POST['leaderboard_string'] );
		}
}
add_action( 'init', function() {
	$object = get_post( url_to_postid( "http://".$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'] ) );
	$itemID = null;
	if (is_object($object) && $object->post_type == 'toplist_item_review' ) {
		$itemID = get_post_meta( $object->ID, 'toplist_item_4review', true );
	}
 
	if (is_numeric($itemID)) {
		$terms = wp_get_post_terms( $itemID, 'toplist_type');
		$leaderboard_ad = get_term_meta( $terms[0]->term_id, 'leaderboard_string', true );
		define('LEADERBOARD_STRING', $leaderboard_ad);
	} else {
		define('LEADERBOARD_STRING', 'div-gpt-ad-1490989602610-0');
	}
});

\add_filter('comments_template', function() {
	if (get_theme_mod('facebook_comments') == 1) {
		return TEMPLATEPATH .'/templates/facebook-comments.php';
	}
}, 99);

\add_filter( 'get_the_archive_title', function( $title ) {
	if ( is_category() ) {
			$title = single_cat_title( '', false );
	} elseif ( is_tag() ) {
			$title = single_tag_title( '', false );
	} elseif ( is_author() ) {
			$title = '<span class="vcard">' . get_the_author() . '</span>';
	} elseif ( is_post_type_archive() ) {
			$title = post_type_archive_title( '', false );
	} elseif ( is_tax() ) {
			$title = single_term_title( '', false );
	}

	return $title;
});

\add_shortcode( 'forex_charts', function() {
	wp_reset_query();
	$query = new \WP_Query('post_type=forex_chart&status=publish&orderby=title&order=ASC&posts_per_page=-1');
	ob_start();
	if ($query->have_posts()) :
		?><div class="row forex-chart-container"><?php
		while ($query->have_posts()):
			$query->the_post();?>
			<div class="col-xs-6 col-sm-4 col-md-3">
			<a href="<?php the_permalink(); ?>" class="btn btn-forex-chart" title="<?php the_title_attribute(); ?>" ><?= the_post_thumbnail( array(50, 24) ); ?>&nbsp;<?php the_title(); ?></a>
			</div>
		<?php endwhile;
		?></div><?php
	endif;
	return ob_get_clean();
});

function widget_area_prefix($echo = false, $default_pagetype = 'page') {
	$post_type = get_post_type( get_the_ID() );
	if (!$post_type || is_search() || is_404()) $post_type = $default_pagetype;
	$single = 'single';
	if (is_singular('lesson')) {
		$single = $single . '_depth_' . get_current_page_depth();
	}
	$archive = (is_archive())? 'archive': $single;
	$response = $post_type . '-' . $archive;
	if ($echo) {
		echo $response;
		return;
	} else {
		return $response;
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

\add_shortcode( 'featured_broker_ad', function() {
ob_start(); ?>
<section class="clearfix featured_broker_ad_container">
	<div class="header">
		<span class="featured_broker_ad_model"></span>
		<span class="featured_broker_ad_content">
			<h3 class="h2">Want to be featured in this Broker list?</h3>
			<h4>Send email to: <a class="btn btn-success">brokers[@]forextraders.com</a></h4>
		</span>
	</div>
	<div class="fineprint">
		• LEVERAGE: Controls the equity you need to take a margin position. E.g. 50:1 leverage means you can take a $5,000  trade with just $100 in your account. <br />
		Note that a high degree of leverage can work against you as well as for you. <br />
		• Leverage over 50:1 for majors and 20:1 for minors is not available to traders in the U.S.<br />
		• As indicated in the list, only NFA regulated brokers are available to U.S. customers.<br />
		* Brokers offer variable spreads which means that the spreads are subject to current market conditions. 
	</div>
	<hr>
</section>
<?php return ob_get_clean();
});

\add_action( 'pre_get_posts',  __NAMESPACE__ . '\\set_searches_per_page'  );
function set_searches_per_page( $query ) {

	global $wp_the_query;

	if ( ( ! is_admin() ) && ( $query === $wp_the_query ) && ( $query->is_search() ) ) {
		$query->set( 'posts_per_page', 10 );
	}

	return $query;
}

/**
 *  Adding to profiles
 **/
 
 function modify_profile_fields($profile_fields) {

	// Add new fields
	$profile_fields['gplus'] = 'Google+ URL <br /><small>(Will be shown as post meta)</small>';

	return $profile_fields;
}
\add_filter('user_contactmethods',  __NAMESPACE__ . '\\modify_profile_fields');

function on_technical_analysis_publish( $new_status, $old_status, $post ) {
	if (!is_admin() || $new_status == $old_status || $new_status !== 'publish')
		return false;

	$url = $_POST['tavideo_url'];

	if ($url) {
		set_theme_mod( 'featured_video', $id );
	}
}
\add_action(  'transition_post_status',  __NAMESPACE__ . '\\on_technical_analysis_publish', 10, 3 );

function no_nopaging($query) {
	if (is_post_type_archive('forex_chart')) {
		$query->set('nopaging', 1);
	}
}

add_action('parse_query', __NAMESPACE__ . '\\no_nopaging');

function toplist_item_review_columns_head($defaults) {
		return array(
				'cb' => '<input type="checkbox" />',
				'title' => __('Title'),
				'demo_button' => __('Hide Demo'),
				'signup_button' =>__( 'Hide SignUp'),
		);
}
 
function toplist_item_review_columns_content($column_name, $post_id) {
		if ($column_name == 'demo_button') {
			$hide_signup = get_post_meta( $post_id, 'hide_toplist_signup_btn', true );
			if ($hide_signup >= 0) {
				echo 'No';
			} else {
				echo 'Yes';
			}
		}
		if ($column_name == 'signup_button') {
			$hide_demo = get_post_meta( $post_id, 'hide_toplist_demo_btn', true );
			if ($hide_demo >= 0) {
				echo 'No';
			} else {
				echo 'Yes';
			}
		}
}
add_filter('manage_toplist_item_review_posts_columns', __NAMESPACE__ . '\\toplist_item_review_columns_head');
add_action('manage_toplist_item_review_posts_custom_column', __NAMESPACE__ . '\\toplist_item_review_columns_content', 10, 2);


function schedule_no_indexing($post_id, $post) {
	if ($post->post_type !== 'forex_news' || $post->post_type !== 'technical_analysis')
		return false;
		// put this line inside a function, 
		// presumably in response to something the user does
		// otherwise it will schedule a new event on every page visit
		$number = rand(1, 10);
		$return = wp_schedule_single_event( time() + $number, 'set_no_index' );
		if ($return === false) {
			add_action( 'admin_notices', function() {
		?>
		<div class="notice notice-warning is-dismissible">
		<p><?php _e( 'No Index failed running! Will be run within the next hour', 'sage' ); ?></p>
		</div>
		<?php
			wp_schedule_single_event( time() + rand(1, 10), 'set_no_index' );
			} );
		}
}

add_action( 'save_post', __NAMESPACE__ . '\\schedule_no_indexing', 10, 2 );
// set noindex
function set_no_index() {
	global $wpdb;
	$ids = $wpdb->get_col(
		$wpdb->prepare(
			"SELECT ID FROM {$wpdb->posts} WHERE post_type = '%s' OR post_type = '%s' ORDER BY post_modified_gmt DESC LIMIT 200, 18446744073709551615",
			'forex_news',
			'technical_analysis'
		)
	);
	error_log(json_encode($ids) . EOL, 3, '/var/www/noindex.log');

	foreach ($ids as $id) {
		update_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', '1' );
		error_log($id . update_post_meta( $id, '_yoast_wpseo_meta-robots-noindex', '1' ) . EOL, 3, '/var/www/noindex.log');
	}
}
add_action( 'set_no_index',  __NAMESPACE__ . '\\set_no_index' );
