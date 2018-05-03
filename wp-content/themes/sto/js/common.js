$(function() {

	// Custom JS

	//masked input init
	$( "input[type = 'tel'] " ).mask('+375 (99) 999-99-99');

	// open popup providers
	$( '.popup-providers-js' ).click(function(event) {
		event.preventDefault();
		$( '.overlay' ).addClass( 'overlay_active' );
		$( 'body' ).toggleClass( 'hide-scroll' );
		$( '.popup-form__providers' ).addClass( 'popup-form_active' );
	} );
	// / open popup providers

	// open popup call
	$( '.popup-call-js' ).click(function(event) {
		event.preventDefault();
		$( '.overlay' ).addClass( 'overlay_active' );
		$( 'body' ).toggleClass( 'hide-scroll' );
		$( '.popup-form__call' ).addClass( 'popup-form_active' );
	} );
	// / open popup call

	// close popup form on click close
	$( '.popup-form__close, .el-close-form' ).click(function(event) {
		event.preventDefault();
		$( 'body' ).toggleClass( 'hide-scroll' );
		$( '.overlay' ).removeClass( 'overlay_active' );
		$( '.popup-form' ).removeClass( 'popup-form_active' );
	} );
	// /close popup form on click close

	// close popup form on click outside
	$( '.overlay__content' ).click(function(event) {
		if ( $( '.overlay__content' ).has(event.target).length === 0 ){
			$('.overlay').removeClass('overlay_active');
			$( 'body' ).toggleClass( 'hide-scroll' );
			$('.popup-form').removeClass('popup-form_active');
		}
	} );
	// /close popup form on click outside

	// close popup on press Esc
	$(document).keyup(function(event) {
		if ( event.keyCode === 27 ) {
			$( '.overlay' ).removeClass( 'overlay_active' );
			$( '.popup-form' ).removeClass( 'popup-form_active' );
			$( 'body' ).toggleClass( 'hide-scroll' );
		}
	} );
	// / close popup on press Esc

	// slider

	$( '.slider' ).slick({
		centerMode: true,
		slidesToShow: 3,
		slidesToScroll: 1,
		autoplay: true,
		autoplaySpeed: 1000,
		arrows: false,
		variableWidth: true,
		responsive: [
			{
				breakpoint: 768,
				settings: {
					slidesToShow: 1
				}
			}
		]
	});

	// /slider

	// tabs
	if (document.querySelector('.tabs')) {

		$('.tabs_types .tabs-nav__li:first-of-type').addClass('tabs-nav__li_active');
		$('.tabs_types .tabs-content__tab:first-of-type').addClass('tabs-content__tab_active');

		$('.tabs_types .tabs-nav__li').click(function (event) {
			var current = +this.getAttribute('data-tab');
			$('.tabs_types .tabs-nav__li').removeClass('tabs-nav__li_active');
			$('.tabs_types .tabs-content__tab').removeClass('tabs-content__tab_active');
			$('.tabs_types .tabs-nav__li:nth-of-type(' + (current + 1) + ')').addClass('tabs-nav__li_active');
			$('.tabs_types .tabs-content__tab:nth-of-type(' + (current + 1) + ')').addClass('tabs-content__tab_active');
		});


		$('.tabs_faces .tabs-nav__li:nth-of-type(2)').addClass('tabs-nav__li_active');
		$('.tabs_faces .tabs-content__tab:nth-of-type(2)').addClass('tabs-content__tab_active');

		$('.tabs_faces .tabs-nav__li').click(function (event) {
			var current = +this.getAttribute('data-tab');
			$('.tabs_faces .tabs-nav__li').removeClass('tabs-nav__li_active');
			$('.tabs_faces .tabs-content__tab').removeClass('tabs-content__tab_active');
			$('.tabs_faces .tabs-nav__li:nth-of-type(' + (current + 1) + ')').addClass('tabs-nav__li_active');
			$('.tabs_faces .tabs-content__tab:nth-of-type(' + (current + 1) + ')').addClass('tabs-content__tab_active');
		});

	}
	// /tabs

	// fixed-link
	ShowLinkToTop();
	$(window).scroll(ShowLinkToTop);

	function ShowLinkToTop() {
		if( $(window).scrollTop() > $(window).height() * 1.25) {
			$('.fixed-link').fadeIn();
		} else {
			$('.fixed-link').fadeOut();
		}
	}

	//Click event to scroll to top
	$('.fixed-link').click(function(){
		$('html, body').animate({scrollTop : 0},800);
		return false;
	});
	// /fixed-link

	// send form

	$('form').submit(function (event) {
		event.preventDefault();
		var data = $(this).serializeArray();
		$(this).find('input').val('');
		$(this).removeClass('popup-form_active');
		$.ajax({
			type: "POST",
			url: 'mail/mail.php',
			data: data,
			success: function (data) {
				$( '.overlay' ).addClass( 'overlay_active' );
				$( 'body' ).addClass( 'hide-scroll' );
				$( '.popup-form' ).removeClass( 'popup-form_active' );
				$( '.popup-form.popup-form__succ' ).addClass( 'popup-form_active' );
			},
			error: function(data) {
				console.log(data);
			}
		});
	})

	// /send form

});
