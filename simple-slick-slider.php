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

function sss_setup() { }
add_action( 'init', 'sss_setup' );

////////////////////////////////////////////////////////////////////////////////////////////////////
/// Helper utilities
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Save post meta / custom field data for simple_slick_slider CPT.
 *
 * It verifies the nonce, then checks we're not doing autosave, ajax or a future post request. It then checks the
 * current user's permissions, before finally* either updating the post meta, or deleting the field if the value was not
 * truthy.
 *
 * By passing an array of fields => values from the same meta box (and therefore same nonce) into the $data argument,
 * repeated checks against the nonce, request and permissions are avoided.
 *
 * @param array $data
 * @param       $nonce_action
 * @param       $nonce_name
 * @param       $post
 */
function sss_save_custom_fields( array $data, $nonce_action, $nonce_name, $post ) {

	// Verify the nonce.
	if ( ! isset( $_POST[ $nonce_name ] ) || ! wp_verify_nonce( $_POST[ $nonce_name ], $nonce_action ) ) {
		return;
	}

	// Don't try to save the data under autosave, ajax, or future post.
	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
		return;
	}
	if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
		return;
	}
	if ( defined( 'DOING_CRON' ) && DOING_CRON ) {
		return;
	}

	// Grab the post object.
	$post = get_post( $post );

	// Don't save if WP is creating a revision (same as DOING_AUTOSAVE?).
	if ( 'revision' === get_post_type( $post ) ) {
		return;
	}

	// Check that the user is allowed to edit the post.
	if ( ! current_user_can( 'edit_post', $post->ID ) ) {
		return;
	}

	// Cycle through $data, insert value or delete field.
	foreach ( (array) $data as $field => $value ) {
		// Save $value, or delete if the $value is empty.
		if ( $value ) {
			update_post_meta( $post->ID, $field, $value );
		} else {
			delete_post_meta( $post->ID, $field );
		}
	}

}

/**
 * Return custom field post meta data.
 *
 * Return only the first value of custom field. Return empty string if field is blank or not set.
 *
 * @param string $field   Custom field key.
 * @param int    $post_id Optional. Post ID to use for Post Meta lookup, defaults to `get_the_ID()`.
 * @return string|bool Return value or empty string on failure.
 */
function sss_get_custom_field( $field, $post_id = null ) {

	// Use get_the_ID() if no $post_id is specified.
	$post_id = empty( $post_id ) ? get_the_ID() : $post_id;

	if ( ! $post_id ) {
		return '';
	}

	$custom_field = get_post_meta( $post_id, $field, true );

	if ( ! $custom_field ) {
		return '';
	}

	return is_array( $custom_field ) ? $custom_field : wp_kses_decode_entities( $custom_field );

}

function sss_get_template_part( $slug, $name = '' ) {
	$template = '';
	if ( $name ) {
		$fallback = SSS_PLUGIN_PATH . "templates/{$slug}-{$name}.php";
		$template = file_exists( $fallback ) ? $fallback : '';
	}

	if ( ! $template ) {
		$fallback = SSS_PLUGIN_PATH . "templates/{$slug}.php";
		$template = file_exists( $fallback ) ? $fallback : '';
	}

	if ( $template ) {
		load_template( $template, false );
	}
}

////////////////////////////////////////////////////////////////////////////////////////////////////
/// Admin Enqueue
////////////////////////////////////////////////////////////////////////////////////////////////////
function sss_admin_enqueue() {

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
		'insert_into_item' => __( /** @lang text */ 'Insert into Slick Slider', SSS_TEXT_DOMAIN ),
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

function sss_simple_slick_slider_column( $columns ) {
	unset($columns['date']);

	$columns['shortcode'] = __( 'Shortcode' );
	$columns['date'] = __( 'Date' );

	return $columns;
}
add_filter( 'manage_simple_slick_slider_posts_columns', 'sss_simple_slick_slider_column' );

function sss_simple_slick_slider_columns( $column, $post_id ) {
	switch ( $column ) {
		case 'shortcode' :
			printf( '<code>[simple-slick-slider slide_id="' . $post_id . '"]</code>' );
			break;
	}
}
add_filter( 'manage_simple_slick_slider_posts_custom_column', 'sss_simple_slick_slider_columns', 10, 3);


////////////////////////////////////////////////////////////////////////////////////////////////////
/// Meta Boxes
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * Register Meta boxes for Custom Post Type Slick Slider
 */
function sss_create_simple_slick_slider_metaboxes() {
	add_meta_box(
		'sss-simple-slick-slider-data',
		__( 'Slides', SSS_TEXT_DOMAIN ),
		'sss_slides_metabox',
		'simple_slick_slider',
		'normal',
		'high'
	);

	add_meta_box(
		'sss-simple-slick-slider-settings',
		__( 'Settings', SSS_TEXT_DOMAIN ),
		'sss_settings_metabox',
		'simple_slick_slider',
		'side',
		'high'
	);
}
add_action( 'add_meta_boxes', 'sss_create_simple_slick_slider_metaboxes', 30 );

function sss_settings_metabox( $post ) {

	wp_nonce_field( 'sss_slider_settings_save', 'sss_slider_settings_nonce' );

	?>
	<p>
		<label for="select_slider_template"><?php esc_html_e( 'Select Templete', SSS_TEXT_DOMAIN ); ?></label>
		<select id="select_slider_template" name="sss_settings[_slider_template]" class="regular-text">
			<option value="template-1" <?php selected('template-1', esc_attr( sss_get_custom_field( '_slider_template' ) ) ); ?>>Templete 1</option>
			<option value="template-2" <?php selected('template-2', esc_attr( sss_get_custom_field( '_slider_template' ) ) ); ?>>Templete 2</option>
			<option value="template-3" <?php selected('template-3', esc_attr( sss_get_custom_field( '_slider_template' ) ) ); ?>>Templete 3</option>
		</select>
	</p>
	<p>You may choose templete for quick settings:</p>

	<hr />

	<div class="accordion">
		<div class="accordion__item">
			<div class="accordion__header">
				<h3>Main Settings</h3>
			</div>
			<div class="accordion__body">
				<p>
					<label for="slides_to_show"><?php esc_html_e( 'Slides To Show', SSS_TEXT_DOMAIN ); ?></label>
					<select id="slides_to_show" name="sss_settings[_slides_to_show]" class="regular-text">
						<option value="1" <?php selected('1', esc_attr( sss_get_custom_field( '_slides_to_show' ) ) ); ?>>1</option>
						<option value="2" <?php selected('2', esc_attr( sss_get_custom_field( '_slides_to_show' ) ) ); ?>>2</option>
						<option value="3" <?php selected('3', esc_attr( sss_get_custom_field( '_slides_to_show' ) ) ); ?>>3</option>
					</select>
				</p>

				<p>
					<label for="slides_to_scroll"><?php esc_html_e( 'Slides to Scroll', SSS_TEXT_DOMAIN ); ?></label>
					<select id="slides_to_scroll" name="sss_settings[_slides_to_scroll]" class="regular-text">
						<option value="1" <?php selected('1', esc_attr( sss_get_custom_field( '_slides_to_scroll' ) ) ); ?>>1</option>
						<option value="2" <?php selected('2', esc_attr( sss_get_custom_field( '_slides_to_scroll' ) ) ); ?>>2</option>
						<option value="3" <?php selected('3', esc_attr( sss_get_custom_field( '_slides_to_scroll' ) ) ); ?>>3</option>
					</select>
				</p>
			</div>
		</div>

		<div class="accordion__item">
			<div class="accordion__header">
				<h3>Transition</h3>
			</div>
			<div class="accordion__body"></div>
		</div>

		<div class="accordion__item">
			<div class="accordion__header">
				<h3>Lazy Loading</h3>
			</div>
			<div class="accordion__body"></div>
		</div>
	</div>
	<?php
}

function sss_slides_metabox ( $post ) {
	wp_nonce_field( 'simple_slick_slider_meta_nonce', 'simple_slick_slider_meta_nonce' );
	$slides_meta = get_post_meta( $post->ID, 'simple_slick_slider_slide', true );

//	echo '<pre>';
//	var_export( $slides_meta );
//	echo '</pre>';

	?>
	<div class="panel-wrap slides-source">

		<div class="slide-pannels">
			<p class="mt-3">
				<button id="addNewSlideBtn" class="button button-primary button-large"><?php esc_html_e( 'Add a slide', SSS_TEXT_DOMAIN ); ?></button>
			</p>
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
										<label for="sss-slide-title-<?php echo $k; ?>" class="col-sm-3 col-form-label"><?php esc_attr_e( 'Title:', SSS_TEXT_DOMAIN ); ?></label>
										<div class="col-sm-9">
											<input id="sss-slide-title-<?php echo $k; ?>"
											       type="text"
											       name="simple_slick_slider[<?php echo $k; ?>][_slide_name]"
											       class="reqular-text slideTitle"
											       value="<?php echo esc_attr( $slide_meta['_slide_name'] ); ?>" />
										</div>
									</div>
									<div class="form-group row">
										<label for="sss-slide-desc-<?php echo $k; ?>" class="col-sm-3 col-form-label"><?php esc_attr_e( 'Description:', SSS_TEXT_DOMAIN ); ?></label>
										<div class="col-sm-9">
											<textarea id="sss-slide-desc-<?php echo $k; ?>"
											          name="simple_slick_slider[<?php echo $k; ?>][_slide_desc]"
											          cols="5" rows="3"
											          class="regular-text"><?php esc_attr_e( $slide_meta['_slide_desc'] ); ?></textarea>
										</div>
									</div>
									<div class="form-group row">
										<label for="sss-main-image-<?php echo $k; ?>" class="col-sm-3 col-form-label">
											<?php esc_attr_e( 'Image:', SSS_TEXT_DOMAIN ); ?>
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
										<label for="sss-thumb-image-<?php echo $k; ?>" class="col-sm-3 col-form-label">
											<?php esc_attr_e( 'Thumbnail:', SSS_TEXT_DOMAIN ); ?>
										</label>
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

	if ( ! isset( $_POST['simple_slick_slider_meta_nonce'] ) || ! wp_verify_nonce( $_POST['simple_slick_slider_meta_nonce'], 'simple_slick_slider_meta_nonce' ) )
		return;

	if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) return;
	if ( ! current_user_can( 'edit_post', $post_id ) ) return;

	$old_slide_meta = get_post_meta( $post_id, 'simple_slick_slider_slide', true);
	$new_slide_meta = [];


	$slides = $_POST['simple_slick_slider'];

//	    print_r( $slides );
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

	if ( !empty( $new_slide_meta ) && $new_slide_meta != $old_slide_meta ) {
		update_post_meta( $post_id, 'simple_slick_slider_slide', $new_slide_meta );
	} elseif ( empty($new_slide_meta) && $old_slide_meta ) {
		delete_post_meta( $post_id, 'simple_slick_slider_slide', $old_slide_meta );
	}

}
add_action( 'save_post_simple_slick_slider', 'sss_save_simple_slick_slider_metaboxes' );
function sss_save_slider_settings( $post_id, $post ) {

	if ( ! isset( $_POST['sss_settings'] ) ) {
		return;
	}

	// Merge user submitted options with fallback defaults.
	$data = wp_parse_args(
		$_POST['sss_settings'],
		array(
			'_slider_template'  => 'template-1',
			'_slides_to_show'   => 1,
			'_slides_to_scroll' => 2,
		)
	);

	// Sanitize the title, description, and tags.
	foreach ( (array) $data as $key => $value ) {
		if ( in_array( $key, array( '_slider_template', '_slides_to_show', '_slides_to_scroll' ) ) ) {
			$data[ $key ] = wp_strip_all_tags( $value );
		}
	}

	sss_save_custom_fields( $data, 'sss_slider_settings_save', 'sss_slider_settings_nonce', $post );

}
add_action( 'save_post', 'sss_save_slider_settings', 1, 2 );

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


////////////////////////////////////////////////////////////////////////////////////////////////////
/// Shortcodes
////////////////////////////////////////////////////////////////////////////////////////////////////

/**
 * @param $id
 *
 * @return string|void
 */
function render_slider( $id ) {

	if ( empty( $id ) ) return __( 'Invalid ID', SSS_TEXT_DOMAIN );

	$slider_array = get_post( (int) $id, ARRAY_A );

	if ( ! post_type_exists( $slider_array['post_type'] ) || $slider_array['post_type'] !== 'simple_slick_slider' )
		return __( 'Post type does not match with given ID' );

	$_slides = sss_get_custom_field('simple_slick_slider_slide', $id );
	$_template = sss_get_custom_field('_slider_template', $id );

//	$_settings = get_post_meta( $slider_array['ID'], 'sss_settings', true );
//	$_template_loader = $_settings['_slider_template'];
//
	set_query_var( 'slides', $_slides );

	if ( $_template && isset( $_template ) ) {
		sss_get_template_part( $_template );
	} else {
		sss_get_template_part( 'template-1' );
	}

//	$output = '';
//	if ( $slider_array['slider_meta'] ) {
//		$output .= '<div class="slider slider-single">';
//			foreach( $slider_array['slider_meta'] as $slider_meta ) {
//				$output .= sprintf( '<<div><img src="%s" alt="" /></div>', $slider_meta['_slide_main_image'] );
//			}
//		$output .= '</div>';
//		$output .= '<div class="slider slider-nav">';
//		foreach( $slider_array['slider_meta'] as $slider_meta ) {
//			$output .= sprintf( '<div class="tippyRender" data-tippy-content="Hello world %2$s"><img src="%1$s" alt="" /></div>', $slider_meta['_slide_thumb_image'], $slider_meta['_slide_name'] );
//		}
//		$output .= '</div>';
//	} else {
//		$output .= __( 'Slider is empty', SSS_TEXT_DOMAIN );
//	}
//
//	return $output;
}

// shortcodes
function simple_slick_slider_shortcode( $atts ) {
	$attr = shortcode_atts( [
		'slide_id' => ''
	], $atts );

	return render_slider( $attr['slide_id'] );
}
add_shortcode('simple-slick-slider', 'simple_slick_slider_shortcode' );


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
		echo render_slider( $instance['select_slider'] );

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
