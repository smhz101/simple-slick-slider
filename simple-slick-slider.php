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
define('SSS_PLUGIN_MAIN_PATH', plugin_dir_path(__FILE__));

////////////////////////////////////////////////////////////////////////////////////////////////////
/// Admin Enqueue
////////////////////////////////////////////////////////////////////////////////////////////////////
function sss_admin_enqueue() {
	wp_enqueue_script('sss-bootstrap-admin-loader', SSS_PLUGIN_URL . '', ['jquery'], SSS_VERSION, ture );
}
add_action('wp_admin_enqueue', 'sss_admin_enqueue');


////////////////////////////////////////////////////////////////////////////////////////////////////
/// Post types
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Register Custom Post Type Slick Slider
 */
function sss_create_simple_slick_slider_cpt() {

	$labels = array(
		'name' => _x( 'Slick Slides', 'Post Type General Name', 'wptp-sss' ),
		'singular_name' => _x( 'Slick Slider', 'Post Type Singular Name', 'wptp-sss' ),
		'menu_name' => _x( 'Slick Slides', 'Admin Menu text', 'wptp-sss' ),
		'name_admin_bar' => _x( 'Slick Slider', 'Add New on Toolbar', 'wptp-sss' ),
		'archives' => __( 'Slick Slider Archives', 'wptp-sss' ),
		'attributes' => __( 'Slick Slider Attributes', 'wptp-sss' ),
		'parent_item_colon' => __( 'Parent Slick Slider:', 'wptp-sss' ),
		'all_items' => __( 'All Slick Slides', 'wptp-sss' ),
		'add_new_item' => __( 'Add New Slick Slider', 'wptp-sss' ),
		'add_new' => __( 'Add New', 'wptp-sss' ),
		'new_item' => __( 'New Slick Slider', 'wptp-sss' ),
		'edit_item' => __( 'Edit Slick Slider', 'wptp-sss' ),
		'update_item' => __( 'Update Slick Slider', 'wptp-sss' ),
		'view_item' => __( 'View Slick Slider', 'wptp-sss' ),
		'view_items' => __( 'View Slick Slides', 'wptp-sss' ),
		'search_items' => __( 'Search Slick Slider', 'wptp-sss' ),
		'not_found' => __( 'Not found', 'wptp-sss' ),
		'not_found_in_trash' => __( 'Not found in Trash', 'wptp-sss' ),
		'featured_image' => __( 'Featured Image', 'wptp-sss' ),
		'set_featured_image' => __( 'Set featured image', 'wptp-sss' ),
		'remove_featured_image' => __( 'Remove featured image', 'wptp-sss' ),
		'use_featured_image' => __( 'Use as featured image', 'wptp-sss' ),
		'insert_into_item' => __( 'Insert into Slick Slider', 'wptp-sss' ),
		'uploaded_to_this_item' => __( 'Uploaded to this Slick Slider', 'wptp-sss' ),
		'items_list' => __( 'Slick Slides list', 'wptp-sss' ),
		'items_list_navigation' => __( 'Slick Slides list navigation', 'wptp-sss' ),
		'filter_items_list' => __( 'Filter Slick Slides list', 'wptp-sss' ),
	);
	$args = array(
		'label' => __( 'Slick Slider', 'wptp-sss' ),
		'description' => __( 'Simple Slick Slider', 'wptp-sss' ),
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
	add_meta_box( 'sss-simple-slick-slider-data', __( 'Slider Settings', 'wptp-sss' ), 'simple_slick_slider_metabox_callback', 'simple_slick_slider', 'normal', 'high' );
}
add_action( 'add_meta_boxes', 'sss_create_simple_slick_slider_metaboxes', 30 );


function simple_slick_slider_metabox_callback ( $post ) {
	wp_nonce_field( 'simple_slick_slider_save_data', 'simple_slick_slider_meta_nonce' );

	$value = get_post_meta( $post->ID, 'my_key', true ); //my_key is a meta_key. Change it to whatever you want

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
			<p>
				<button class="button button-primary">Add a slide</button>
			</p>
			<div class="accordion" id="accordionExample">
				<div class="card">
					<div class="card-header" id="headingOne">
						<h2 class="mb-0">
							<button class="btn btn-link" type="button" data-toggle="collapse" data-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
								Collapsible Group Item #1
							</button>
						</h2>
					</div>

					<div id="collapseOne" class="collapse show" aria-labelledby="headingOne" data-parent="#accordionExample">
						<div class="card-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header" id="headingTwo">
						<h2 class="mb-0">
							<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
								Collapsible Group Item #2
							</button>
						</h2>
					</div>
					<div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordionExample">
						<div class="card-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
						</div>
					</div>
				</div>
				<div class="card">
					<div class="card-header" id="headingThree">
						<h2 class="mb-0">
							<button class="btn btn-link collapsed" type="button" data-toggle="collapse" data-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
								Collapsible Group Item #3
							</button>
						</h2>
					</div>
					<div id="collapseThree" class="collapse" aria-labelledby="headingThree" data-parent="#accordionExample">
						<div class="card-body">
							Anim pariatur cliche reprehenderit, enim eiusmod high life accusamus terry richardson ad squid. 3 wolf moon officia aute, non cupidatat skateboard dolor brunch. Food truck quinoa nesciunt laborum eiusmod. Brunch 3 wolf moon tempor, sunt aliqua put a bird on it squid single-origin coffee nulla assumenda shoreditch et. Nihil anim keffiyeh helvetica, craft beer labore wes anderson cred nesciunt sapiente ea proident. Ad vegan excepteur butcher vice lomo. Leggings occaecat craft beer farm-to-table, raw denim aesthetic synth nesciunt you probably haven't heard of them accusamus labore sustainable VHS.
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<?php
}
