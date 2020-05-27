<?php

/**
 * Plugin Name: Simple Slick Slider
 * Plugin URI: http://wordpress.org/plugins/simple-slick-slider/
 * Description: Simple Slick Slider
 * Author: Muzammil Hussain
 * Version: 1.0.0
 * Author URI: https://wpthemepress.com/
 *
 *
 * @package Simple_Slick_Slider
 * @version 1.0.0
 */

defined( 'ABSPATH' ) || exit;

define('SSS_VERSION', '1.0.0');
define('SSS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('SSS_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('SSS_PLUGIN_INC', trailingslashit( SSS_PLUGIN_PATH ) . 'includes/');
define('SSS_TEXT_DOMAIN', 'wptp-sss');

////////////////////////////////////////////////////////////////////////////////////////////////////
/// Admin Enqueue
////////////////////////////////////////////////////////////////////////////////////////////////////
function sss_admin_enqueue() {
//	wp_enqueue_script( 'thickbox' );
//	wp_enqueue_script( 'meida-upload' );
	wp_enqueue_media();

	wp_enqueue_script('sss-js-loader', SSS_PLUGIN_URL . 'dist/js/admin.js', ['jquery', 'jquery-ui-sortable'], SSS_VERSION );
	wp_enqueue_style('sss-css-loader', SSS_PLUGIN_URL . 'dist/css/admin.css', [], SSS_VERSION );
}
add_action('admin_enqueue_scripts', 'sss_admin_enqueue');


////////////////////////////////////////////////////////////////////////////////////////////////////
/// Post types
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Register Custom Post Type Slick Slider
 */
function sss_create_simple_slick_slider_cpt() {

	$labels = array(
		'name' => _x( 'Slick Slides', 'Post Type General Name', SSS_TEXT_DOMAIN ),
		'singular_name' => _x( 'Slick Slider', 'Post Type Singular Name', SSS_TEXT_DOMAIN ),
		'menu_name' => _x( 'Slick Slides', 'Admin Menu text', SSS_TEXT_DOMAIN ),
		'name_admin_bar' => _x( 'Slick Slider', 'Add New on Toolbar', SSS_TEXT_DOMAIN ),
		'archives' => __( 'Slick Slider Archives', SSS_TEXT_DOMAIN ),
		'attributes' => __( 'Slick Slider Attributes', SSS_TEXT_DOMAIN ),
		'parent_item_colon' => __( 'Parent Slick Slider:', SSS_TEXT_DOMAIN ),
		'all_items' => __( 'All Slick Slides', SSS_TEXT_DOMAIN ),
		'add_new_item' => __( 'Add New Slick Slider', SSS_TEXT_DOMAIN ),
		'add_new' => __( 'Add New', SSS_TEXT_DOMAIN ),
		'new_item' => __( 'New Slick Slider', SSS_TEXT_DOMAIN ),
		'edit_item' => __( 'Edit Slick Slider', SSS_TEXT_DOMAIN ),
		'update_item' => __( 'Update Slick Slider', SSS_TEXT_DOMAIN ),
		'view_item' => __( 'View Slick Slider', SSS_TEXT_DOMAIN ),
		'view_items' => __( 'View Slick Slides', SSS_TEXT_DOMAIN ),
		'search_items' => __( 'Search Slick Slider', SSS_TEXT_DOMAIN ),
		'not_found' => __( 'Not found', SSS_TEXT_DOMAIN ),
		'not_found_in_trash' => __( 'Not found in Trash', SSS_TEXT_DOMAIN ),
		'featured_image' => __( 'Featured Image', SSS_TEXT_DOMAIN ),
		'set_featured_image' => __( 'Set featured image', SSS_TEXT_DOMAIN ),
		'remove_featured_image' => __( 'Remove featured image', SSS_TEXT_DOMAIN ),
		'use_featured_image' => __( 'Use as featured image', SSS_TEXT_DOMAIN ),
		'insert_into_item' => __( 'Insert into Slick Slider', SSS_TEXT_DOMAIN ),
		'uploaded_to_this_item' => __( 'Uploaded to this Slick Slider', SSS_TEXT_DOMAIN ),
		'items_list' => __( 'Slick Slides list', SSS_TEXT_DOMAIN ),
		'items_list_navigation' => __( 'Slick Slides list navigation', SSS_TEXT_DOMAIN ),
		'filter_items_list' => __( 'Filter Slick Slides list', SSS_TEXT_DOMAIN ),
	);
	$args = array(
		'label' => __( 'Slick Slider', SSS_TEXT_DOMAIN ),
		'description' => __( 'Simple Slick Slider', SSS_TEXT_DOMAIN ),
		'labels' => $labels,
		'menu_icon' => 'dashicons-images-alt2',
		'supports' => array('title'),
		'taxonomies' => array(),
		'public' => false,
		'show_ui' => true,
		'show_in_menu' => true,
		'menu_position' => 20,
		'show_in_admin_bar' => false,
		'show_in_nav_menus' => false,
		'can_export' => false,
		'has_archive' => false,
		'hierarchical' => false,
		'exclude_from_search' => false,
		'show_in_rest' => true,
		'publicly_queryable' => false,
		'capability_type' => 'page',
	);
	register_post_type( 'simple_slick_slider', $args );

}
add_action( 'init', 'sss_create_simple_slick_slider_cpt', 0 );


////////////////////////////////////////////////////////////////////////////////////////////////////
/// Meta Boxes
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Register Meta boxes for Custom Post Type Slick Slider
 */
function sss_create_simple_slick_slider_metaboxes() {
	add_meta_box( 'sss-simple-slick-slider-data', __( 'Slider Settings', SSS_TEXT_DOMAIN ), 'simple_slick_slider_metabox_callback', 'simple_slick_slider', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sss_create_simple_slick_slider_metaboxes', 30 );

function simple_slick_slider_metabox_callback ( $post ) {
	wp_nonce_field( 'simple_slick_slider_meta_nonce', 'simple_slick_slider_meta_nonce' );

	$slides_meta = get_post_meta( $post->ID, 'simple_slick_slider_slide', true );

//	echo '<pre>';
//	var_export( $slides_meta );
//	echo '</pre>';

	?>
	<div class="panel-wrap slides-source">
		<?php // @TODO slider source selection ?>

<!--		<div class="slides-source">-->
<!--			<p>-->
<!--				<label for="wdm_new_field">--><?php //_e( "Choose value:", 'choose_value' ); ?><!--</label><br />-->
<!--				<input type="radio" name="the_name_of_the_radio_buttons" value="custom" --><?php //checked( $value, 'custom' ); ?><!-- >Custom-->
<!--				<input type="radio" name="the_name_of_the_radio_buttons" value="wpposts" --><?php //checked( $value, 'wpposts' ); ?><!-- >Posts-->
<!--			</p>-->
<!--		</div>-->

		<div class="slide-pannels">
			<p class="mt-3"><button id="addNewSlideBtn" class="btn btn-primary btn-sm">Add a slide</button></p>
			<div class="accordion" id="sliderAccordion">
			<?php
				if ( $slides_meta && isset( $slides_meta ) ) {
					foreach ( $slides_meta as $k => $slide_meta ) { ?>
						<div class="card" id="slide-<?php echo $k; ?>" data-index="<?php echo $k; ?>">
							<div class="card-header" id="slideHeading<?php echo $k; ?>">
								<a class="btn btn-link staticTitle" href="#collapse<?php echo $k; ?>" type="button" data-toggle="collapse" data-target="#collapse<?php echo $k; ?>" aria-expanded="true" aria-controls="collapse<?php echo $k; ?>">Slide #<?php echo $k; ?></a>
							</div>

							<div id="collapse<?php echo $k; ?>" class="collapse" aria-labelledby="slideHeading<?php echo $k; ?>" data-parent="#sliderAccordion">
								<div class="card-body">
									<div class="form-group row">
										<label for="slideTitle" class="col-sm-3 col-form-label">Title</label>
										<div class="col-sm-9">
											<input type="text"
											       name="simple_slick_slider[<?php echo $k; ?>][_slide_name]"
											       class="reqular-text"
											       id="slideTitle"
											       value="<?php echo esc_attr( $slide_meta['_slide_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label for="slideMainImage" class="col-sm-3 col-form-label">Slider Image</label>
										<div class="col-sm-9">
											<div class="form-group">
												<input type="text" name="simple_slick_slider[<?php echo $k; ?>][_slide_main_image]" id="main_image_url" class="regular-text" value="" />
												<input type="button" name="upload-btn" id="slideMainImage" class="button-primary" value="Upload Slide Image">
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label for="slideThumbnail" class="col-sm-3 col-form-label">Slider Thumbnail</label>
										<div class="col-sm-9">
											<div class="form-group">
												<input type="text" name="simple_slick_slider[<?php echo $k; ?>][_slide_thumb_image]" id="thumb_img_url" class="regular-text" value="" />
												<input type="button" name="upload-btn" id="slideThumbnail" class="button-primary" value="Upload Thumb Image">
											</div>
										</div>
									</div>
									<button type="button" id="<?php echo $k; ?>" class="btn btn-link text-danger btn_remove">Remove slider</button>
								</div>
							</div>
						</div>
						<?php
					}

				}
			?>
			</div>
		</div>
	</div>
	<?php
}

function sss_save_simple_slick_slider_metaboxes( $post_id ) {

	if ( ! isset( $_POST['simple_slick_slider_meta_nonce'] ) ||
	     ! wp_verify_nonce( $_POST['simple_slick_slider_meta_nonce'], 'simple_slick_slider_meta_nonce' ) )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$old_slide_meta = get_post_meta( $post_id, 'simple_slick_slider_slide', true);
	$new_slide_meta = [];

	$slides = $_POST['simple_slick_slider'];

	//	print_r( $_POST['simple_slick_slider'] );
	//	$tmp = fopen(dirname(__file__).'/my_logs.txt', "a+"); fwrite($tmp,"\r\n\r\n".ob_get_contents());fclose($tmp);
	//	die();

	// data sanitization
	foreach( $slides as $i => $slide ) {
		foreach( $slide as $k => $data ) {
			$new_slide_meta[$i][$k] = stripslashes( strip_tags( $data ) );
		}
	}

	if ( !empty( $new_slide_meta ) && $new_slide_meta != $old_slide_meta )
		update_post_meta( $post_id, 'simple_slick_slider_slide', $new_slide_meta );
	elseif ( empty($new_slide_meta) && $old_slide_meta )
		delete_post_meta( $post_id, 'simple_slick_slider_slide', $old_slide_meta );

}
add_action( 'save_post', 'sss_save_simple_slick_slider_metaboxes' );

