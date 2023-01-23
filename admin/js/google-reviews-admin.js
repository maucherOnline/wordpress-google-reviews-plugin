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

		const $search = $('.js-serp-business-search');
		const $searchButtonPro = $('.button.search-business.pro');
		const $pullButtonPro = $('.button.pull-reviews.pro');
		const $buttonRow = $('.serp-container .button-row');

		// remove disabled attribute when search field is changed
		$search.on('keyup change', function () {
			$searchButtonPro.removeAttr('disabled');
		});

		// Search for business
		 $searchButtonPro.click(function () {

			const $that = $(this);
		 	const $error = $('#errors');

			if ($that.attr('disabled')) {
				return;
			}

			$.ajax({
				url: js_global.wp_ajax_url,
				data: {
					action: 'handle_serp_business_search',
					search: $search.val(),
					language: js_global.language
				},
				beforeSend: function () {
					$buttonRow
						.addClass('busy');

					$searchButtonPro
						.attr('disabled', true);
				},
				success: function (response) {
					if ( ! response.success ) {
						$search.addClass('has-error');
						$error.html(response.data.html);
					} else {
						if ( $search.hasClass('has-error') ) {
							$search.removeClass('has-error');
							$error.fadeOut().empty();
						}

						$search.siblings('.serp-results').html(response.data.html).slideDown();
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {
					console.log(errorThrown);
				},
				complete: function () {

					$buttonRow
						.removeClass('busy');

					$searchButtonPro
						.removeAttr('disabled');
				}
			});
		});

		$('.serp-search').on('click', '.js-serp-result', function(){
			const $this = $(this);

			$this.closest('.serp-results').slideUp();

			$('.js-serp-data-id').attr('value', $this.val());
			$('.js-serp-business-search').val($.trim($this.parent().text()));

			$('.button.pull-reviews').attr('disabled', true);
			$('#submit').click();
		});

		 $search.on('click', function(){
			const $this             = $(this);
			const $resultsContainer = $('.serp-results');

			if ( ! $resultsContainer.children().length || ! $this.text().length ) {
				return;
			}

			$resultsContainer.slideDown();
		});

		 $search.on('search', function(){
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

		// PRO: pull reviews button
		 $pullButtonPro.on('click', function () {

			const $submit = $('#submit');

			$.ajax({
				url: js_global.wp_ajax_url,
				data: {
					action: 'handle_get_reviews_pro_api'
				},
				beforeSend: function () {
					$buttonRow
						.addClass('busy');

					$pullButtonPro
						.attr('disabled', true);
				},
				success: function(response) {
					if ( ! response.success ) {
						$error.html(response.data.html);
					} else {

					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {

				},
				complete: function () {
					$buttonRow
						.removeClass('busy');

					$pullButtonPro
						.removeAttr('disabled');

					$submit.click();
				}
			});
		});


		 /**
		  * FREE: pull reviews button
		  */
		$('.button.pull-reviews.free').on('click', function () {

			const $that = $(this);
			const $submit = $('#submit');
			const place_id = $('input[name="google_reviews_option_name[gmb_id_1]"]').val();
			const language = $('select#reviews_language_3').val();
			const $errors = $('#errors');

			$.ajax({
			 url: js_global.wp_ajax_url,
			 data: {
				 action: 'get_reviews_free_api',
				 place_id: place_id,
				 language: language
			 },
			 beforeSend: function () {
				 $that
					 .addClass('pulling')
					 .attr('disabled', true);
			 },
			 success: function(response) {

			 },
			 error: function(XMLHttpRequest, textStatus, errorThrown) {
				 const message = errorThrown + ' - Please double-check your Place ID.';
				 $errors.text(message);
			 },
			 complete: function (XMLHttpRequest, textStatus) {
				 $that
					 .removeClass('pulling')
					 .attr('disabled', false);

				 if (textStatus !== 'error') {
					 $submit.click();
				 }
			 }
			});
		});

	 });
})( jQuery );
