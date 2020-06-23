<?php
/**
 * $slides is available as slider meta is available via query_var
 */

//echo '<pre>';
//var_export( $slides );
//echo '</pre>';

?>

<div class="slider slider-single">
	<?php if ( is_array( $slides ) || is_object( $slides ) ) {
		foreach( $slides as $slide ) { ?>
		<div style="
			background-image: url('<?php echo $slide['_slide_main_image']; ?>');
			background-origin: border-box;
			background-position: center center;
			background-size: cover;
			">
			<div style="
				padding: 25px;
				display: flex;
				flex-flow: row wrap;
				height: 380px;
				background-color: rgba(255,255,255,0.6);
				width: 35%;
				z-index: 99;
				">
				<div style="
					width:100%;
					height: 100%;
					align-self: flex-start;
					">
					<h3><?php echo $slide['_slide_name']; ?></h3>
					<p><?php echo $slide['_slide_desc']; ?></p>
					<a href="#" class="button submit">Download</a>
				</div>
			</div>
		</div>
	<?php }
	}
	?>
</div>
<div class="slider slider-nav">
	<?php if ( is_array( $slides ) || is_object( $slides ) ) {
		foreach( $slides as $slide ) { ?>
		<div class="tippyRender" data-tippy-content="<?php echo $slide['_slide_name']; ?>">
			<img src="<?php echo $slide['_slide_thumb_image']; ?>" alt="<?php echo $slide['_slide_name']; ?>" />
		</div>
	<?php }
	} ?>
</div>
