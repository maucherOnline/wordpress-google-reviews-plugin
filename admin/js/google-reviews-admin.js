(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */

	 $(document).ready(function(){
	 	function debounce(func, wait, immediate) {
			let timeout;

			return function() {
				const context = this
				const args    = arguments;
				const later   = function() {
					timeout = null;

					if ( ! immediate) {
						func.apply(context, args);
					}
				};

				const callNow = immediate && ! timeout;

				clearTimeout(timeout);

				timeout = setTimeout(later, wait);

				if (callNow) {
					func.apply(context, args);
				}
			};
		};

		 $('.js-serp-business-search').on('keyup', debounce( function(){
		 	const $this = $(this);

		 	if ( $('body').hasClass('grwp-is-loading') || ! $this.val() ) {
		 		return;
		 	}

		 	$this.siblings('.serp-results').slideUp();

		 	$.ajax({
				url: js_global.wp_ajax_url,
				data: {
					action: 'handle_serp_business_search',
					search: $this.val(),
					language: js_global.language
				},
				beforeSend: function () {
					$('body').addClass('grwp-is-loading');
				},
				success: function (response) {
					if ( ! response.success ) {
						$this.addClass('has-error');
						$this.parent().siblings('.serp-error').html(response.data.html).fadeIn();
					} else {
						if ( $this.hasClass('has-error') ) {
							$this.removeClass('has-error');
							console.log($this.parent().siblings('.serp-error'));
							$this.parent().siblings('.serp-error').fadeOut().empty();
						}

						$this.siblings('.serp-results').html(response.data.html).slideDown();
					}
				},
				complete: function () {
					$('body').removeClass('grwp-is-loading');
				}
			});
		 }, 500));

		 $('.serp-search').on('click', '.js-serp-result', function(){
		 	const $this = $(this);

		 	$this.closest('.serp-results').slideUp();

		 	$('.js-serp-data-id').attr('value', $this.val());
		 	$('.js-serp-business-search').val($.trim($this.parent().text()));
		 });

		 $('.js-serp-business-search').on('click', function(){
		 	const $this             = $(this);
		 	const $resultsContainer = $('.serp-results');

		 	if ( ! $resultsContainer.children().length || ! $this.text().length ) {
		 		return;
		 	}

		 	$resultsContainer.slideDown();
		 });

		 $('.js-serp-business-search').on('search', function(){
		 	$('.js-serp-data-id').attr('value', '');
		 	$('.serp-results').slideUp();
		 });

		$(document).on('click', function(e) {
		    const $container        = $('.serp-search');
		    const $resultsContainer = $('.serp-results');

		    if ( ! $(e.target).closest($container).length ) {
		        $resultsContainer.slideUp();
		    }
		});

		$('.button.pull-reviews').on('click', function () {
			$(this)
				.addClass('pulling');
				//.attr('disabled', true);

			$.ajax({
				url: 'http://localhost/reviews/wp-json/google-reviews/v1/reviews/',
				success: function(response) {
					console.log(respons);
				}
			});
		});

	 });
})( jQuery );
