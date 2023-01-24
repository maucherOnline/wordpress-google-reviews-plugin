(function( $ ) {
	'use strict';

	 $(document).ready(function(){

		const $search = $('.js-serp-business-search');
		const $searchButtonPro = $('.button.search-business.pro');
		const $pullButtonPro = $('.button.pull-reviews.pro');
		const $buttonRow = $('.serp-container .button-row');
		const $error = $('#errors');

		// remove disabled attribute when search field is changed
		$search.on('keyup change', function () {
			$searchButtonPro.removeAttr('disabled');
		});

		// prevent 'enter' from submitting form
		$search.on('keypress', function(e) {
			if (e.keyCode == '10' || e.keyCode == '13') {
				e.preventDefault();
			}
		})

		// Search for business
		 $searchButtonPro.click(function () {

			const $that = $(this);

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

					if ( ! response ) {
						$error.html('Error in search response. Please try again.');
					}
					else if (undefined === response.data || undefined === response.data.html) {
						$error.html('Search response failed. Please try again.');
					}
					else if (response && response.data.html === '') {
						$error.html('Results empty. Please try again.');
					}
					else if ( ! response.success ) {
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

			 const $that = $(this);
			 if ($that.attr('disabled')) {
				 return;
			 }

			const $submit = $('#submit');
			 let has_error = false;

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
					// if everything's ok, do nothing
					if (response === "0") {
						return false;
					}
					else if (! response) {
						$error.html('Error in reviews response. Please try again.');
						has_error = true;
					}
					else if (undefined === response.data || undefined === response.data.html) {
						$error.html('Reviews response failed. Please try again.');
						has_error = true;
					}
					else if (response && response.data.html === '') {
						$error.html('Reviews results empty. Please try again.');
						has_error = true;
					}
					else if ( ! response.success ) {
						$error.html(response.data.html);
						has_error = true;
					}
				},
				error: function (XMLHttpRequest, textStatus, errorThrown) {

				},
				complete: function (jqXHR, textStatus) {
					$buttonRow
						.removeClass('busy');
					if (!has_error) {
						$submit.click();
					} else {
						$pullButtonPro
							.removeAttr('disabled');
					}
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
