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

	// ========= Ajax form ===========
	$('.js-required-input').on('focus',function() {
		if($(this).hasClass('is-error')) {
			$(this).removeClass('is-error');
		}
	});

	var fileInputs = document.querySelectorAll( 'input[type=file]' );


	Array.prototype.forEach.call( fileInputs, function( input ) {
		var label    = input.parentNode,
		labelVal = label.innerHTML;

		input.addEventListener('change', function(e) {
			var fileName = '',
			nextElem = label.querySelector('.form-file__name');

			if(nextElem.classList.contains('is-active')) {
				nextElem.classList.remove('is-active');
			}

			if( this.files && this.files.length > 1 ) {
				fileName = ( this.getAttribute( 'data-multiple-caption' ) || '' ).replace( '{count}', this.files.length );
			}
			else {
				fileName = e.target.value.split( '\\' ).pop();
			}

			if( fileName ) {	
				nextElem.innerHTML = fileName;
				nextElem.classList.add('is-active');
			} else {

				label.innerHTML = labelVal;
			}
		});
	});

	$('form').submit(function(e) {
		e.preventDefault();

		var self = $(this),
			inputs = self.find('.js-required-input'),
			flag = true;

		var formData = new FormData(self.get(0));

		// Validate
		$(inputs).each(function() {
			if(!$(this).val() || $(this).val() == "") {
				$(this).addClass('is-error');
				flag = false;
			}
		});

		if(!flag) {return false;}

		$.ajax({
			contentType: false, 
      processData: false, 
			type: "POST",
			url: "/wp-content/themes/tato-studio-theme/mail.php", //Change
			data: formData
		}).done(function() {
			self.trigger("reset");
			$('.form-file__name').html('').removeClass('is-active');
			alert('Спасибі! Ваша заявка успішно відправлена')
		});

	});
	// ========= =========== =========== ===========


	$("input[type=tel]").inputmask({"mask": "+38 (999) 999-9999","clearIncomplete": false});

	setTimeout(function(){
		$('body').addClass('is-loaded');
	}, 1000);


});
