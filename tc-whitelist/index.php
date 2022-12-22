<?php
/**
 * Plugin Name: TC Whitelist
 * Plugin URI: https://tagcomics.com
 * Description: Whitelist search.
 * Version: 1.0
 * Author: Nikhil
 * Author URI: https://tagcomics.com
 * Requires at least: 5.6
 * Tested Up to: 6.1
 * Stable Tag: 2.0
 * License: GPL v2
 * Shortname: nikhil
 */
 include_once('boxes/functions.php');
 
/* ====================================
  Define plugin url ==================
====================================== */

 
define('JP_SRMS_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );

/* Add style and script */

function jsrms_frontend_style() {
        wp_register_style( 'jsrms_fstyle', plugin_dir_url(__FILE__).'css/style.css', false, '1.5' );
        wp_enqueue_style( 'jsrms_fstyle' );
		
		wp_register_script( 'jsrms_fscript', plugin_dir_url(__FILE__).'js/scripts.js', array('jquery'), '1.5' );

		$jsrms_data = array(
			'ajaxUrl' => admin_url('admin-ajax.php'),
		);
		wp_localize_script( 'jsrms_fscript', 'jsrms_object', $jsrms_data );

        wp_enqueue_script( 'jsrms_fscript' );
}
add_action( 'wp_enqueue_scripts', 'jsrms_frontend_style' );

function jsrms_add_jquery() {
        wp_enqueue_script('jquery');
}
add_action('init', 'jsrms_add_jquery');
 
/* --------------------------------------------------------------
-------------- Change custom post type title placeholder --------
--------------------------------------------------------------- */

function jsrms_change_default_title($title) {
	$screen = get_current_screen();
	if('jp_students_result' == $screen->post_type) {
		$title = 'Enter student name here';
	}
	return $title;
}

add_filter('enter_title_here','jsrms_change_default_title');

/* -----------------------------------------------------------------
---------------- Register new post type Result ---------------------
------------------------------------------------------------------- */
 
function jsrms_students_result_reg() {
  $labels = array(
    'name'               => _x( 'TC Whitelist', 'post type general name' ),
    'singular_name'      => _x( 'TC Whitelist', 'post type singular name' ),
    'add_new'            => _x( 'Add New', 'Whitelist' ),
    'add_new_item'       => __( 'Add New Whitelist' ),
    'edit_item'          => __( 'Edit Whitelist' ),
    'new_item'           => __( 'New Whitelist' ),
    'all_items'          => __( 'All Whitelist' ),
    'view_item'          => __( 'View Whitelist' ),
    'search_items'       => __( 'Search Whitelist' ),
    'not_found'          => __( 'No Whitelist found' ),
    'not_found_in_trash' => __( 'No Whitelist found in the Trash' ), 
    'parent_item_colon'  => '',
    'menu_name'          => 'TC Whitelist'
  );
  $args = array(
    'labels'        => $labels,
    'description'   => 'Add new custom post type students result',
    'public'        => true,
    'menu_position' => 5,
    'supports'      => array( 'thumbnail','title', ),
	'taxonomies' => array(  'classes' ),
    'has_archive'   => true,
	'menu_icon' => 'dashicons-database' 
  );
  register_post_type( 'tc_whitelist', $args ); 
}
add_action( 'init', 'jsrms_students_result_reg' );

/* ---------------------------------------------
------------ Add Students Classes --------------
---------------------------------------------- */

add_action( 'init', 'jsrms_students_classes_reg', 0 );

function jsrms_students_classes_reg() {
	// Whitelist type taxonomy
	$labels = array(
		'name'              => _x( 'Whitelist Type', 'taxonomy general name' ),
		'singular_name'     => _x( 'Whitelist Type', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Whitelist Type' ),
		'all_items'         => __( 'All Whitelist Type' ),
		'parent_item'       => __( 'Parent Whitelist Type' ),
		'parent_item_colon' => __( 'Parent Whitelist Type:' ),
		'edit_item'         => __( 'Edit Whitelist Type' ),
		'update_item'       => __( 'Update Whitelist Type' ),
		'add_new_item'      => __( 'Add New Whitelist Type' ),
		'new_item_name'     => __( 'New Whitelist Type' ),
		'menu_name'         => __( 'Whitelist Type' ),
	);
	
	register_taxonomy( 'whitelist_type', 'tc_whitelist', array(
		'hierarchical' => true,
		'labels' => $labels,
		'query_var' => true,
		'rewrite' => true,
		'show_admin_column' => true	
	) );
	
}

/* ----------------------------------------------------
------- Change student result upadate message ---------
----------------------------------------------------- */

function jsrms_students_result_update_message( $messages ) {
  global $post, $post_ID;
  $messages['tc_whitelist'] = array(
    0 => '', 
    1 => sprintf( __('Result updated. <a href="%s">View result</a>'), esc_url( get_permalink($post_ID) ) ),
    2 => __('Custom field updated.'),
    3 => __('Custom field deleted.'),
    4 => __('Result updated.'),
    5 => isset($_GET['revision']) ? sprintf( __('Result restored to revision from %s'), wp_post_revision_title( (int) $_GET['revision'], false ) ) : false,
    6 => sprintf( __('Result published. <a href="%s">View result</a>'), esc_url( get_permalink($post_ID) ) ),
    7 => __('Result saved.'),
    8 => sprintf( __('Result submitted. <a target="_blank" href="%s">Preview result</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
    9 => sprintf( __('Result scheduled for: <strong>%1$s</strong>. <a target="_blank" href="%2$s">Preview result</a>'), date_i18n( __( 'M j, Y @ G:i' ), strtotime( $post->post_date ) ), esc_url( get_permalink($post_ID) ) ),
    10 => sprintf( __('Result draft updated. <a target="_blank" href="%s">Preview result</a>'), esc_url( add_query_arg( 'preview', 'true', get_permalink($post_ID) ) ) ),
  );
  return $messages;
}
add_filter( 'post_updated_messages', 'jsrms_students_result_update_message' );

/* ---------------------------------------------
------------ Result search & view ------------
--------------------------------------------- */

function jsrms_result_search_and_view() { 
	ob_start();;
?>

	<div class="result-search-form">
		<form action="" id="result-form" method="post">
			<div class="form-row">
				<label for="exam-reg">Whitelist:</label>
				<input type="text" id="exam-reg" placeholder="Search" name="exam_reg" required />
			</div>
			<div class="form-row">
				<input type="submit" value="Search" placeholder="Search" class="result-submit-btn" name="exam_result" /> <img class="loader" src="<?php echo plugin_dir_url(__FILE__).'images/spinner.gif' ?>" alt="" />
			</div>
		</form>
   </div>
   
   <div class="jsrms-result"></div>

<?php $jsrms_vs = ob_get_clean();
	  return $jsrms_vs;
 }

add_shortcode('jp_students_result_sc','jsrms_result_search_and_view');

/*--------------------------------------------------*/
/* Result using ajax------------------------------- */
/*-------------------------------------------------*/

function jsrms_result_using_ajax() {
		
		$exam_class = $_POST['examclass'];
		$exam_year = $_POST['examyear'];
		$exam_reg = $_POST['examroll'];
		
		if(!empty($exam_reg))

			query_posts( array( 
				'post_type' => 'tc_whitelist',
				'classes' => $exam_class,
				'years' => $exam_year,
				'meta_key' => '_jp_student_reg',
				'meta_query' => array(
				   array(
					   'key' => '_jp_student_reg',
					   'value' => $exam_reg,
				   )),
				'posts_per_page' => 1
			) );
		 ?>
		<?php if ( have_posts() ) : while ( have_posts() ) : the_post(); ?>
			<div class="student-result">
				
				<table cellpadding="5" class="student-info">
					<tbody>
						
						<?php 
							$student_reg = get_post_meta( get_the_ID(),'_jp_student_reg',true );
							if($student_reg):
						?>
						<tr>
						<th>Whitelist</th>
						<th>Type of Whitelist</th>
						</tr>
						<tr>
							<td><?php echo $student_reg; ?></td>
							<td>
							<?php 
							if(get_the_term_list( $post->ID, 'whitelist_type' ) != null):
						?><?php $whitelist_type = get_the_term_list( $post->ID, 'whitelist_type', '', ', ', '' ); $whitelist_type = strip_tags($whitelist_type); echo $whitelist_type; ?>
						<?php endif; ?>
							</td>
						</tr>
						<?php endif; ?>
					
					
					</tbody>
				</table>
				
			</div>
			
		<?php endwhile; else: ?>
		
			<div class="student-result">
				<div class="result-error">
					<span>Result not found or not published yet.</span>
				</div>
			</div>
		<?php endif;
	
	
	
	die();
}

add_action('wp_ajax_jsrms_student_result_view','jsrms_result_using_ajax');
add_action('wp_ajax_nopriv_jsrms_student_result_view','jsrms_result_using_ajax');

/*-------------------------------
 Shortcode menu------------------
--------------------------------*/

function jsrms_shortcode_menu(){

	add_submenu_page( 'edit.php?post_type=tc_whitelist', 'Shortcode', 'Shortcode', 'manage_options', 'jsrms_shortcode_menu', 'jsrms_shortcode_admin_page' );

}

add_action('admin_menu','jsrms_shortcode_menu');

function jsrms_shortcode_admin_page() { ?>
	
	<div class="wrap">
		<!-- Some inline style -->
		<style type="text/css">
			h2.jsrmsp-main-title {
				text-align: left;
			    background: #ddd;
			    padding: 10px;
			    margin-top: 0;
			}
			h3.jsrmsp-m-title {
			  margin: 0 !important;
			  padding: 8px 12px !important;
			}
			div.jsupnsuc {
				background: #ededed none repeat scroll 0 0;
				border-left: 1px solid lime;
				border-right: 1px solid #00E400;
				padding: 5px;
			}
			div.jsvardlnins {
				overflow: hidden;
			}
			a.jsnvardl {
			  color: #fff;
			  float: left;
			  text-align: center;
			  padding: 5px 0;
			  width: 50%;
			}
			a#ddl {
			  background: #00ff00 none repeat scroll 0 0;
			  color: #222;
			}
			a#ddn {
			  background: #00e400 none repeat scroll 0 0;
			}
		</style>
		<div id="dashboard-widgets-wrap">
			<div class="metabox-holder" id="dashboard-widgets">
				<div id="postbox-container" class="postbox-container">
					<div id="side-sortables" class="meta-box-sortables ui-sortable">
					<div style="display: block;" id="dashboard_quick_press" class="postbox ">
						<h2 class="jsrmsp-main-title"><?php _e('Shortcode'); ?></h2>
						<h3 class="hndle jsrmsp-m-title" style="cursor: default;"><span><span class="hide-if-no-js">Use shortcode <input type="text" value="[jp_students_result_sc]" /> inside post or page to show the Result Search Form</span></span></h3>
					<div style="overflow: hidden; padding-bottom: 10px;" class="inside">
					</div>
					</div>
					</div>	
				</div>
			</div>
		</div>
	</div>
	
<?php }

function jsrms_activate() {

    $url = get_site_url();
	$message = "Your Result System Plugin has activated on $url ";
	$message = wordwrap($message, 70, "\r\n");

	wp_mail('joy2012bd@gmail.com', 'Result System Plugin Activated', $message);
}
register_activation_hook( __FILE__, 'jsrms_activate' );

?>