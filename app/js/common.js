$(document).ready(function() {
	//  ========= Variables =========
	var body = $('body'),
			html = body.width(),
			timer; // for disable scroll
	// ========= =========== =========== ===========

	// Disable hover effect when client scrolles the page
	$(window).on('scroll',function() {
		clearTimeout(timer);
		if(!body.hasClass('disable-hover')) {
			body.addClass('disable-hover');
		}

		timer = setTimeout(function() {
			body.removeClass('disable-hover');
		}, 200);
	});

	// ========= Smooth scrolling to the acnhors ===========
	$('.js-smooth-scroll-link').on('click', 'a', function (e) {
		e.preventDefault();
		var id = $(this).attr('href'),
			top = $(id).offset().top;

		if ($('.js-mobile-menu').hasClass('is-opened')) {
			$('html').removeClass('is-fixed');
			$('.js-mobile-menu').removeClass('is-opened');
			$('.js-open-mobile-menu-btn').removeClass('is-active');
		}

		$('html, body').animate({scrollTop: top}, 500);
	});	
	// ========= =========== =========== ===========

	$('.js-open-mobile-menu-btn').on('click', function(e) {
		e.preventDefault();

		$(this).toggleClass('is-active');

		$('html').toggleClass('is-fixed');
		
		$('.js-mobile-menu').toggleClass('is-opened');
	});

	setTimeout(function(){
		$('body').addClass('is-loaded');
	}, 1000);


});
