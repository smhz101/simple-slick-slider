import 'bootstrap';
import $ from 'jquery';

// document ready
$( () => {

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
			'\t\t\t\t\t\t<label for="slideTitle" class="col-sm-3 col-form-label">Title</label>\n' +
			'\t\t\t\t\t\t<div class="col-sm-9">\n' +
			'\t\t\t\t\t\t\t<input type="text" name="simple_slick_slider[' + i + '][_slide_name]" class="reqular-text" id="slideTitle" value="" />\n' +
			'\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t<div class="form-group row">\n' +
			'\t\t\t\t\t\t<label for="slideMainImage" class="col-sm-3 col-form-label">Slider Image</label>\n' +
			'\t\t\t\t\t\t<div class="col-sm-9">\n' +
			'\t\t\t\t\t\t\t<div class="form-group">\n' +
			'\t\t\t\t\t\t\t    <input type="text" name="simple_slick_slider[' + i + '][_slide_main_image]" id="main_image_url" class="regular-text" value="" />\n' +
			'\t\t\t\t\t\t\t    <input type="button" name="upload-btn" id="slideMainImage" class="button-primary" value="Upload Slide Image">\n' +
			'\t\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t</div>\n' +
			'\t\t\t\t\t<div class="form-group row">\n' +
			'\t\t\t\t\t\t<label for="slideThumbnail" class="col-sm-3 col-form-label">Slider Thumbnail</label>\n' +
			'\t\t\t\t\t\t<div class="col-sm-9">\n' +
			'\t\t\t\t\t\t\t<div class="form-group">\n' +
			'\t\t\t\t\t\t\t\t<input type="text" name="simple_slick_slider[' + i + '][_slide_thumb_image]" id="thumb_img_url" class="regular-text" value="" />\n' +
			'\t\t\t\t\t\t\t    <input type="button" name="upload-btn" id="slideThumbnail" class="button-primary" value="Upload Thumb Image">\n' +
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
		console.log( '#slide-' + buttonId );
		$( '#slide-' + buttonId ).remove();
	});

	$( '#sliderAccordion' ).sortable({
		opacity: 0.6,
		revert: true,
		cursor: 'move',
		handle: '.staticTitle'
	});

	// $( '#slideMainImage' ).click( e => {
	// 	e.preventDefault();
	// 	console.log( 'image clicked .... !' );
	//
	// 	let image = wp.media({
	// 		title: 'Upload Image',
	// 		multiple: false
	// 	})
	// 		.open()
	// 		.on( 'select', () => {
	// 			let uploadedImage = image.state().get( 'selection' ).first();
	// 			let imageUrl = uploadedImage.toJSON().url;
	// 			$( '#main_image_url' ).val( imageUrl );
	// 		});
	// });
});
