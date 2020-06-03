import $ from 'jquery';
import './vendors/slick-carousel';
import tippy from 'tippy.js';

// console.log( 'bundle' );

$( () => {

	/**
	 * Slick Slider Init
	 * @type {jQuery|HTMLElement}
	 */
	const $sliderSingle = $( '.slider-single' );
	const $sliderNavigator =  $( '.slider-nav' );

	$sliderSingle.slick({
		lazyLoad: 'ondemand',
		infinite: true,
		autoplay: true,
		mobileFirst: true,
		slidesToShow: 1,
		slidesToScroll: 1,
		arrows: false,
		fade: false,
		adaptiveHeight: true,
		useTransform: true,
		speed: 400,
		cssEase: 'cubic-bezier(0.77, 0, 0.18, 1)'
	});
	$sliderNavigator.on( 'init', function() {
		$( '.slider-nav .slick-slide.slick-current' ).addClass( 'is-active' );
	}).slick({
		slidesToShow: 5,
		slidesToScroll: 5,
		dots: false,
		focusOnSelect: false,
		infinite: true,
		responsive: [ {
			breakpoint: 1024,
			settings: {
				slidesToShow: 5,
				slidesToScroll: 5
			}
		}, {
			breakpoint: 640,
			settings: {
				slidesToShow: 4,
				slidesToScroll: 4
			}
		}, {
			breakpoint: 420,
			settings: {
				slidesToShow: 3,
				slidesToScroll: 3
			}
		} ]
	});
	$sliderSingle.on( 'afterChange', function( event, slick, currentSlide ) {
		$( '.slider-nav' ).slick( 'slickGoTo', currentSlide );
		let currrentNavSlideElem = '.slider-nav .slick-slide[data-slick-index="' + currentSlide + '"]';
		$( '.slider-nav .slick-slide.is-active' ).removeClass( 'is-active' );
		$( currrentNavSlideElem ).addClass( 'is-active' );
	});
	$sliderNavigator.on( 'click', '.slick-slide', function( event ) {
		event.preventDefault();
		let goToSingleSlide = $( this ).data( 'slick-index' );
		$( '.slider-single' ).slick( 'slickGoTo', goToSingleSlide );

		/**
		 * Tippy init
		 */
		tippy( '[data-tippy-content]', {
			placement: 'bottom',
			trigger: 'mouseenter focus click'
		});
	});

});
