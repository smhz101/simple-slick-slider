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
function sss_admin_enqueue( $hook ) {

	global $pagenow, $typenow;

	if ( ! is_admin() ) return;

	wp_enqueue_media();

	if ( ( $pagenow == 'post-new.php' || $pagenow == 'post.php' ) && 'simple_slick_slider' === $typenow ) {
		wp_enqueue_style('sss-admin-css', SSS_PLUGIN_URL . 'dist/css/admin.css', [], SSS_VERSION );

		wp_enqueue_script('sss-admin-js', SSS_PLUGIN_URL . 'dist/js/admin.js', ['jquery', 'jquery-ui-sortable'], SSS_VERSION );
		wp_localize_script( 'sss-admin-js', 'meta_image',
			array(
				'title' => __( 'Choose or Upload Media', SSS_TEXT_DOMAIN ),
				'button' => __( 'Use this media', SSS_TEXT_DOMAIN ),
			)
		);
		wp_enqueue_script( 'sss-meta-box' );
	}
}
add_action('admin_enqueue_scripts', 'sss_admin_enqueue', 10, 1);


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
										<label class="col-sm-3 col-form-label">Title</label>
										<div class="col-sm-9">
											<input type="text"
											       name="simple_slick_slider[<?php echo $k; ?>][_slide_name]"
											       class="reqular-text slideTitle"
											       value="<?php echo esc_attr( $slide_meta['_slide_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label for="sss-main-image-<?php echo $k; ?>" class="col-sm-3 col-form-label">
											Slider Image
										</label>
										<div class="col-sm-9">
											<div class="form-group">
												<input id="sss-main-image-<?php echo $k; ?>"
												       type="text"
												       name="simple_slick_slider[<?php echo $k; ?>][_slide_main_image]"
												       class="regular-text main_image_url"
												       value="<?php echo esc_attr( $slide_meta['_slide_main_image'] ); ?>" />
												<div class="preview">
													<?php if( isset( $slide_meta['_slide_main_image'] ) && $slide_meta['_slide_main_image'] ) : ?>
													<img src="<?php echo esc_attr( $slide_meta['_slide_main_image'] ); ?>" alt="" width="120" height="120" />
													<?php endif; ?>
												</div>
												<input type="button"
												       name="upload-btn"
												       class="button-primary slideMainImage"
												       value="Upload Slide Image"
												       data-media-uploader-target="#sss-main-image-<?php echo $k; ?>">
											</div>
										</div>
									</div>
									<div class="form-group row">
										<label for="slideThumbnail" class="col-sm-3 col-form-label">Slider Thumbnail</label>
										<div class="col-sm-9">
											<div class="form-group">
												<input id="sss-thumb-image-<?php echo $k; ?>"
												       type="text"
												       name="simple_slick_slider[<?php echo $k; ?>][_slide_thumb_image]"
												       class="regular-text thumb_img_url"
												       value="<?php echo esc_attr( $slide_meta['_slide_thumb_image'] ); ?>" />
												<div class="preview">
													<?php if( isset( $slide_meta['_slide_thumb_image'] ) && $slide_meta['_slide_thumb_image'] ) : ?>
														<img src="<?php echo esc_attr( $slide_meta['_slide_thumb_image'] ); ?>" alt="" width="120" height="120" />
													<?php endif; ?>
												</div>
												<input type="button"
												       name="upload-btn"
												       class="button-primary slideThumbnail"
												       value="Upload Thumbnail"
												       data-media-uploader-target="#sss-thumb-image-<?php echo $k; ?>">
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

/**
 * Save CPT: Simple Slick Slider post_meta data
 *
 * @param $post_id
 */
function sss_save_simple_slick_slider_metaboxes( $post_id ) {

	if ( ! isset( $_POST['simple_slick_slider_meta_nonce'] ) ||
	     ! wp_verify_nonce( $_POST['simple_slick_slider_meta_nonce'], 'simple_slick_slider_meta_nonce' ) )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$old_slide_meta = get_post_meta( $post_id, 'simple_slick_slider_slide', true);
	$new_slide_meta = [];

	$slides = $_POST['simple_slick_slider'];

//		print_r( $_POST['simple_slick_slider'] );
//		$tmp = fopen(dirname(__file__).'/my_logs.txt', "a+"); fwrite($tmp,"\r\n\r\n".ob_get_contents());fclose($tmp);
//		die();

	$_slides = [];
	$slides_count = count( $slides );
	// reindexing array
	if ( $slides_count && $slides_count > 0 ) {
		foreach($slides as $index => $slide) {
			$_slides[] = $slide;
		}
	}

	// data sanitization
	foreach( $_slides as $i => $slide ) {
		++$i;
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

////////////////////////////////////////////////////////////////////////////////////////////////////
/// Helper utilities
////////////////////////////////////////////////////////////////////////////////////////////////////



////////////////////////////////////////////////////////////////////////////////////////////////////
/// Public
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Enqueue scripts
 */
function sss_enqueue_public_scripts() {
	wp_enqueue_script( 'sss-public-js', SSS_PLUGIN_URL . 'dist/js/public.js', ['jquery'], SSS_VERSION, TRUE );
	wp_enqueue_style( 'sss-public-css', SSS_PLUGIN_URL . 'dist/css/public.css', [], SSS_VERSION );
}
add_action( 'wp_enqueue_scripts', 'sss_enqueue_public_scripts' );

/**
 * Shortcode
 */
function get_slider( $id ) {

	if ( empty( $id ) )
		return new WP_Error('missing_id', 'please supply valid post id' );

	$slider_arr = get_post( (int) $id, ARRAY_A );

	if ( ! post_type_exists( $slider_arr['post_type'] ) || $slider_arr['post_type'] !== 'simple_slick_slider' )
		return new WP_Error( 'posttype_not_found', __( 'Post type does not match with given ID' ) );

	$slider_arr_meta = get_post_meta( $slider_arr['ID'], 'simple_slick_slider_slide', true );
	$slider_arr['slider_meta'] = $slider_arr_meta;

	return $slider_arr;
}

function render_slider( $slider_array ) {
//	echo '<pre>';
//	var_dump( $slider_array['slider_meta'] );
//	echo '</pre>';

	if ( $slider_array['slider_meta'] && !empty ( $slider_array['slider_meta'] ) ) {
		echo '<div class="slider slider-single">';
		foreach( $slider_array['slider_meta'] as $slider_meta ) {
			echo sprintf( '<<div><img src="%s" /></div>', $slider_meta['_slide_main_image'] );
		}
		echo '</div>';
		echo '<div class="slider slider-nav">';
		foreach( $slider_array['slider_meta'] as $slider_meta ) {
			echo sprintf( '<div class="tippyRender" data-tippy-content="Hello world %2$s"><img src="%1$s"></div>', $slider_meta['_slide_thumb_image'], $slider_meta['_slide_name'] );
		}
		echo '</div>';
	} else {
		new WP_Error( 'slides_not_found', 'slides not found' );
	}
}

// shortcodes
function simple_slick_slider_shortcode( $atts ) {
	$a = shortcode_atts( [
		'slide_id' => '46'
	], $atts );

	return render_slider( get_slider( $a['slide_id'] ) );
}
add_shortcode('simple_slick_slider', 'simple_slick_slider_shortcode' );


////////////////////////////////////////////////////////////////////////////////////////////////////
/// Widget
////////////////////////////////////////////////////////////////////////////////////////////////////

class SSS_Simple_Slick_Slider_Widget extends WP_Widget {

	protected $defaults;

	/**
	 * Sets up the widgets name etc
	 */
	public function __construct() {

		$this->defaults = [
			'title'         => '',
			'select_slider' => '',
		];

		$widget_ops = array(
			'classname' => 'sss_simple_slick_slider_widget',
			'description' => __( 'Simple Slick Slider Widget.', SSS_TEXT_DOMAIN ),
		);
		parent::__construct( 'sss_simple_slick_slider_widget', __( 'Simple Slick Slider', SSS_TEXT_DOMAIN ), $widget_ops );
	}

	/**
	 * Outputs the content of the widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		echo $args['before_widget'];

		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . apply_filters( 'widget_title', $instance['title'] ) . $args['after_title'];
		}

		// outputs the content of the widget
		echo render_slider( get_slider( $instance['select_slider'] ) );

		echo $args['after_widget'];
	}

	/**
	 * Outputs the options form on admin
	 *
	 * @param array $instance The widget options
	 */
	public function form( $instance ) {

		// Merge with defaults.
		$instance = wp_parse_args( (array) $instance, $this->defaults );

		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_attr_e( 'Title:', SSS_TEXT_DOMAIN ); ?></label>
			<input class="widefat"
			       id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"
			       name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>"
			       type="text" value="<?php echo esc_attr( $instance['title'] ); ?>">
		</p>
		<p>
			<?php

			$sliders_posts = get_posts( [
				'post_type' => 'simple_slick_slider',
				'post_status' => 'publish'
			] );

			if ( $sliders_posts && isset( $sliders_posts ) ) { ?>

				<label for="<?php echo esc_attr( $this->get_field_id( 'select_slider' ) ); ?>"><?php esc_html_e( 'Select Slider:', SSS_TEXT_DOMAIN ); ?></label>
				<select id="<?php echo esc_attr( $this->get_field_id( 'select_slider' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'select_slider' ) ); ?>" class="widefat">

				<?php foreach ( $sliders_posts as $sliders_post ) { ?>
					<option value="<?php echo absint( $sliders_post->ID ); ?>" <?php selected( absint( $sliders_post->ID ), $instance['select_slider'] ); ?>>
						<?php esc_html_e( $sliders_post->post_title ); ?>
					</option>
				<?php } ?>

				</select>
				<span style="display:block;padding-top:7px;">
					<a href="<?php echo esc_url_raw( '/wp-admin/post-new.php?post_type=simple_slick_slider' ); ?>">
						<?php esc_html_e( 'Create new slider', SSS_TEXT_DOMAIN ); ?>
					</a>
				</span>

			<?php } else { ?>

				<span style="display:block;padding-top:7px;">
					<?php esc_html_e( 'Currently you don\'t have any slider, please ', SSS_TEXT_DOMAIN ); ?>
					<a href="<?php echo esc_url_raw( '/wp-admin/post-new.php?post_type=simple_slick_slider' ); ?>">
						<?php esc_html_e( 'Create new slider', SSS_TEXT_DOMAIN ); ?>
					</a>
				</span>

			<?php } ?>

		</p>
		<?php
	}

	/**
	 * Processing widget options on save
	 *
	 * @param array $new_instance The new options
	 * @param array $old_instance The previous options
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {
		// processes widget options to be saved
		$instance = array();
		$instance['title'] = ( ! empty( $new_instance['title'] ) ) ? sanitize_text_field( $new_instance['title'] ) : '';
		$instance['select_slider'] =  ( ! empty( $new_instance['select_slider'] ) ) ? absint( $new_instance['select_slider'] ) : '';

		return $instance;
	}
}

function sss_simple_slick_slider_widget_init() {
	register_widget( 'SSS_Simple_Slick_Slider_Widget' );
}
add_action( 'widgets_init', 'sss_simple_slick_slider_widget_init' );
