import 'bootstrap';
import $ from 'jquery';

// document ready
$( () => {

	/**
	 * Repeatable group fields
	 *
	 * @type {*|jQuery|Event|{}}
	 */
	let count = $( '#sliderAccordion .card:last' ).data( 'index' );
	let i =  0 < count ? count : 0;
	$( '#addNewSlideBtn' ).click( e => {
		e.preventDefault();

		i++;
		$( '#sliderAccordion' ).append(
			'<div id="slide-' + i + '" class="card" data-index="' + i + '">\n' +
			'\t\t\t<div class="card-header" id="slideHeading"' + i + '>\n' +
			'\t\t\t\t<a class="btn btn-link staticTitle" href="#collapse"' + i + ' type="button" data-toggle="collapse" data-target="#collapse"' + i + ' aria-expanded="true" aria-controls="collapse"' + i + '>Slide #' + i + '</a>\n' +
			'\t\t\t</div>\n' +
			'\t\n' +
			'\t\t    <div id="collapse"' + i + ' class="collapse" aria-labelledby="slideHeading"' + i + ' data-parent="#sliderAccordion">\n' +
			'\t\t\t\t<div class="card-body">\n' +
			'\t\t\t\t\t<div class="form-group row">\n' +
			'\t\t\t\t\t\t<label class="col-sm-3 col-form-label">Title</label>\n' +
			'\t\t\t\t\t\t<div class="col-sm-9">\n' +
			'\t\t\t\t\t\t\t<input type="text" name="simple_slick_slider[' + i + '][_slide_name]" class="reqular-text slideTitle" value="" />\n' +
			'\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t<div class="form-group row">\n' +
			'\t\t\t\t\t\t<label for="slideMainImage" class="col-sm-3 col-form-label">Slider Image</label>\n' +
			'\t\t\t\t\t\t<div class="col-sm-9">\n' +
			'\t\t\t\t\t\t\t<div class="form-group">\n' +
			'\t\t\t\t\t\t\t\t<input id="sss-main-image-' + i + '" type="text" name="simple_slick_slider[' + i + '][_slide_main_image]" class="regular-text main_image_url" value="" />\n' +
			'\t\t\t\t\t\t\t\t<div class="preview"></div>\n' +
			'\t\t\t\t\t\t\t\t<input type="button" name="upload-btn" class="button-primary slideMainImage" value="Upload Slide Image" data-media-uploader-target="#sss-main-image-' + i + '">\n' +
			'\t\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t<div class="form-group row">\n' +
			'\t\t\t\t\t\t<label for="slideThumbnail" class="col-sm-3 col-form-label">Slider Thumbnail</label>\n' +
			'\t\t\t\t\t\t<div class="col-sm-9">\n' +
			'\t\t\t\t\t\t\t<div class="form-group">\n' +
			'\t\t\t\t\t\t\t\t<input id="sss-thumb-image-' + i + '" type="text" name="simple_slick_slider[' + i + '][_slide_thumb_image]" class="regular-text thumb_img_url" value="" />\n' +
			'\t\t\t\t\t\t\t\t<div class="preview"></div>\n' +
			'\t\t\t\t\t\t\t\t<input type="button" name="upload-btn" class="button-primary slideThumbnail" value="Upload Thumb Image" data-media-uploader-target="#sss-thumb-image-' + i + '">\n' +
			'\t\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t<button type="button" id="' + i + '" class="btn btn-link text-danger btn_remove">Remove slider</button>\n' +
			'\t\t\t\t</div>\n' +
			'\t\t\t</div>\n' +
			'\t\t</div>'
		);
	});

	$( document ).on( 'click', '.btn_remove', e => {
		e.preventDefault();
		let buttonId = $( e.target ).attr( 'id' );
		$( '#slide-' + buttonId ).remove();
	});
	$( '#sliderAccordion' ).sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.staticTitle'
	});

	/**
	 * Group field media Uploader
	 */

	// Instantiates the variable that holds the media library frame.
	let metaImageFrame;

	// Runs when the media button is clicked.
	$( 'body' ).click( function( e ) {

		// Get the btn
		let btn = e.target;

		// // Check if it's the upload button
		if ( ! btn || ! $( btn ).attr( 'data-media-uploader-target' ) ) {
			return;
		}

		// Get the field target
		const field = $( btn ).data( 'media-uploader-target' );

		// Prevents the default action from occuring.
		e.preventDefault();

		// Sets up the media library frame
		metaImageFrame = wp.media.frames.metaImageFrame = wp.media({
			title: meta_image.title,
			button: { text:  'Use this file' }
		});

		// Runs when an image is selected.
		metaImageFrame.on( 'select', function() {

			// Grabs the attachment selection and creates a JSON representation of the model.
			let mediaAttachment = metaImageFrame.state().get( 'selection' ).first().toJSON();

			// Sends the attachment URL to our custom image input field.
			$( field ).val( mediaAttachment.url );
			let preview = $( field ).siblings( '.preview' ).children().length;
			if ( 0 < preview ) {
				$( field ).siblings( '.preview' ).children( 'img' ).attr( 'src', mediaAttachment.url );
			} else {
				$( field ).siblings( '.preview' ).append( '<img src="' + mediaAttachment.url + '" />' );
			}

		});

		// Opens the media library frame.
		metaImageFrame.open();

	});

});
