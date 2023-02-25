/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/js/admin/google-reviews-admin.js":
/*!**********************************************!*\
  !*** ./src/js/admin/google-reviews-admin.js ***!
  \**********************************************/
/***/ (() => {

(function ($) {
  'use strict';

  $(document).ready(function () {
    var $search = $('.js-serp-business-search');
    var $searchButtonPro = $('.button.search-business.pro');
    var $pullButtonPro = $('.button.pull-reviews.pro');
    var $pullButtonFee = $('.button.pull-reviews.free');
    var $buttonRow = $('.serp-container .button-row');
    var $error = $('#errors');
    var $languageDropdown = $('#reviews_language_3');
    var $submitButton = $('input[type="submit"]');
    var $showDummyContent = $('#show_dummy_content');
    function handle_tabs() {
      // Hide all additional settings on pageload
      var $connectSettings = $('#connect_settings, #connect_settings + table.form-table');
      var $connectTab = $('.nav-tab-wrapper.menu > a[href="#connect_settings"]');
      var $displaySettings = $('#display_settings, #display_settings + table.form-table');
      var $displayTab = $('.nav-tab-wrapper.menu > a[href="#display_settings"]');
      var $embeddingInstructions = $('#embedding_instructions, #embedding_instructions + table.form-table');
      var $embeddingInstructionsTab = $('.nav-tab-wrapper.menu > a[href="#embedding_instructions"]');
      var $navTabs = $('.nav-tab-wrapper.menu > .nav-tab:not(.upgrade)');
      $displaySettings.hide();
      $embeddingInstructions.hide();
      var currentTab = null;
      $navTabs.each(function (index) {
        $(this).click(function (e) {
          e.preventDefault();

          // for connect settings
          if (index === 0) {
            if (currentTab === 0) return;
            $connectSettings.show();
            $displaySettings.hide();
            $embeddingInstructions.hide();
            $navTabs.removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active').blur();
            history.pushState({}, '', '#connect_settings');
            localStorage.gr_location = '#connect_settings';
            currentTab = 0;
          }

          // for display settings
          if (index === 1) {
            if (currentTab === 1) return;
            $displaySettings.show();
            $connectSettings.hide();
            $embeddingInstructions.hide();
            $navTabs.removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active').blur();
            history.pushState({}, '', '#display_settings');
            localStorage.gr_location = '#display_settings';
            currentTab = 1;
          }

          // for display settings
          if (index === 2) {
            if (currentTab === 2) return;
            $embeddingInstructions.show();
            $connectSettings.hide();
            $displaySettings.hide();
            $navTabs.removeClass('nav-tab-active');
            $(this).addClass('nav-tab-active').blur();
            history.pushState({}, '', '#embedding_instructions');
            localStorage.gr_location = '#embedding_instructions';
            currentTab = 2;
          }
        });
      });
      var hash = window.location.hash;
      if (hash === '#display_settings' || localStorage.gr_location === '#display_settings') {
        $displayTab.click();
      } else if (hash === '#embedding_instructions' || localStorage.gr_location === '#embedding_instructions') {
        $embeddingInstructionsTab.click();
      } else {
        $connectTab.click();
      }
    }
    handle_tabs();

    // remove disabled attribute when search field is changed
    $search.on('keyup change', function () {
      $searchButtonPro.removeAttr('disabled');
    });

    // prevent 'enter' from submitting form
    $search.on('keypress', function (e) {
      if (e.keyCode == '10' || e.keyCode == '13') {
        e.preventDefault();
      }
    });

    // Search for business
    $searchButtonPro.click(function () {
      var $that = $(this);
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
        beforeSend: function beforeSend() {
          $buttonRow.addClass('busy');
          $searchButtonPro.attr('disabled', true);
        },
        success: function success(response) {
          if (!response) {
            $error.html('Error in search response. Please try again.');
          } else if (undefined === response.data || undefined === response.data.html) {
            $error.html('Search response failed. Please try again.');
          } else if (response && response.data.html === '') {
            $error.html('Results empty. Please try again.');
          } else if (!response.success) {
            $error.html(response.data.html);
          } else {
            if ($search.hasClass('has-error')) {
              $search.removeClass('has-error');
              $error.fadeOut().empty();
            }
            $search.siblings('.serp-results').html(response.data.html).slideDown();
          }
        },
        error: function error(XMLHttpRequest, textStatus, errorThrown) {},
        complete: function complete() {
          $buttonRow.removeClass('busy');
          $searchButtonPro.removeAttr('disabled');
        }
      });
    });

    // handle clicks on location dropdown (selection)
    $('.serp-search').on('click', '.js-serp-result', function () {
      var $this = $(this);
      var data_id = $this.val();
      var location_name = $this.parent().text();
      $this.closest('.serp-results').slideUp();
      $('.js-serp-data-id').attr('value', $this.val());
      $('.js-serp-business-search').val($.trim($this.parent().text()));
      $('.button.pull-reviews').attr('disabled', true);
      $.ajax({
        url: js_global.wp_ajax_url,
        data: {
          action: 'handle_location_saving',
          data_id: data_id,
          location_name: location_name
        },
        beforeSend: function beforeSend() {
          disableButtonsWhileSaving();
          $searchButtonPro.attr('disabled', true);
          $error.hide();
        },
        complete: function complete() {
          enableButtonsAfterSaving();
        }
      });
    });
    $search.on('click', function () {
      var $this = $(this);
      var $resultsContainer = $('.serp-results');
      if (!$resultsContainer.children().length || !$this.text().length) {
        return;
      }
      $resultsContainer.slideDown();
    });
    $search.on('search', function () {
      $('.js-serp-data-id').attr('value', '');
      $('.serp-results').slideUp();
    });

    // remove list, when user clicks anywhere else
    $(document).on('click', function (e) {
      var $container = $('.serp-search');
      var $resultsContainer = $('.serp-results');
      if (!$(e.target).closest($container).length) {
        $resultsContainer.slideUp();
      }
    });

    // save dropdown language on change
    $languageDropdown.change(function () {
      var language = $(this).val();
      $.ajax({
        url: js_global.wp_ajax_url,
        data: {
          action: 'handle_language_saving',
          search: language
        },
        beforeSend: function beforeSend() {
          disableButtonsWhileSaving();
        },
        complete: function complete() {
          enableButtonsAfterSaving();
        }
      });
    });

    // PRO: pull reviews button
    $pullButtonPro.on('click', function () {
      var $that = $(this);
      if ($that.attr('disabled')) {
        return;
      }
      var $submit = $('#submit');
      var has_error = false;
      $.ajax({
        url: js_global.wp_ajax_url,
        data: {
          action: 'handle_get_reviews_pro_api'
        },
        beforeSend: function beforeSend() {
          $buttonRow.addClass('busy');
          $pullButtonPro.attr('disabled', true);
        },
        success: function success(response) {
          // if everything's ok, do nothing
          if (response === "0") {
            return false;
          } else if (!response) {
            $error.html('Error in reviews response. Please try again.');
            has_error = true;
          } else if (undefined === response.data || undefined === response.data.html) {
            $error.html('Reviews response failed. Please try again.');
            has_error = true;
          } else if (response && response.data.html === '') {
            $error.html('Reviews results empty. Please try again.');
            has_error = true;
          } else if (!response.success) {
            $error.html(response.data.html);
            has_error = true;
          }
        },
        error: function error(XMLHttpRequest, textStatus, errorThrown) {
          var test = 1;
        },
        complete: function complete(jqXHR, textStatus) {
          $buttonRow.removeClass('busy');
          if (!has_error) {
            if ($showDummyContent.is(':checked')) {
              $showDummyContent.click();
            }
            $submit.click();
          } else {
            $pullButtonPro.removeAttr('disabled');
          }
        }
      });
    });

    /**
     * FREE: pull reviews button
     */
    $pullButtonFee.on('click', function () {
      var $that = $(this);
      var $submit = $('#submit');
      var place_id = $('input[name="google_reviews_option_name[gmb_id_1]"]').val();
      var language = $('select#reviews_language_3').val();
      var $errors = $('#errors');
      $.ajax({
        url: js_global.wp_ajax_url,
        data: {
          action: 'get_reviews_free_api',
          place_id: place_id,
          language: language
        },
        beforeSend: function beforeSend() {
          $that.addClass('pulling').attr('disabled', true);
        },
        success: function success(response) {},
        error: function error(XMLHttpRequest, textStatus, errorThrown) {
          var message = errorThrown + ' - Please double-check your Place ID.';
          $errors.text(message);
        },
        complete: function complete(XMLHttpRequest, textStatus) {
          $that.removeClass('pulling').attr('disabled', false);
          if (textStatus !== 'error') {
            if ($showDummyContent.is(':checked')) {
              $showDummyContent.click();
            }
            $submit.click();
          }
        }
      });
    });

    // disable buttons when ajax saving
    function disableButtonsWhileSaving() {
      $submitButton.attr('disabled', true);
      $pullButtonPro.attr('disabled', true);
      $pullButtonFee.attr('disabled', true);
    }

    // enable buttons after ajax saving
    function enableButtonsAfterSaving() {
      $pullButtonPro.removeAttr('disabled');
      $submitButton.removeAttr('disabled');
      $pullButtonFee.removeAttr('disabled');
    }

    // show video modal in free version
    function trigger_modal() {
      var $modal = $("#how_to_modal");
      if (localStorage.hideModal || !$modal) return;
      var $close = $("#modal_close");
      var $overlay = $('.modal .modal-overlay');
      var $body = $('body');
      var $video = $('.responsive_iframe iframe');
      $modal.show();

      // make body fixed to prevent scrolling
      $body.addClass('fixed');
      $modal.removeClass('hide');
      $close.click(function () {
        hideModal();
      });
      $overlay.click(function () {
        hideModal();
      });
      function hideModal() {
        $modal.hide();
        $body.removeClass('fixed');
        var iframeSrc = $video.attr('src');
        $video.attr('src', iframeSrc);
        localStorage.hideModal = true;
      }
    }
    trigger_modal();
  });
})(jQuery);

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!**************************************!*\
  !*** ./src/js/admin/admin-bundle.js ***!
  \**************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _google_reviews_admin__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./google-reviews-admin */ "./src/js/admin/google-reviews-admin.js");
/* harmony import */ var _google_reviews_admin__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_google_reviews_admin__WEBPACK_IMPORTED_MODULE_0__);

})();

/******/ })()
;
//# sourceMappingURL=data:application/json;charset=utf-8;base64,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QUFLLENBQUNuRSxRQUFRLENBQUMsT0FBTyxDQUFDO01BQ3ZCK0QsTUFBTSxDQUFDaEUsV0FBVyxDQUFDLE1BQU0sQ0FBQztNQUUxQmtFLE1BQU0sQ0FBQ3RFLEtBQUssQ0FBQyxZQUFZO1FBQ3JCcUUsU0FBUyxFQUFFO01BQ2YsQ0FBQyxDQUFDO01BRUZFLFFBQVEsQ0FBQ3ZFLEtBQUssQ0FBQyxZQUFZO1FBQ3ZCcUUsU0FBUyxFQUFFO01BQ2YsQ0FBQyxDQUFDO01BRUYsU0FBU0EsU0FBU0EsQ0FBQSxFQUFHO1FBQ2pCRCxNQUFNLENBQUN4RSxJQUFJLEVBQUU7UUFDYjRFLEtBQUssQ0FBQ3BFLFdBQVcsQ0FBQyxPQUFPLENBQUM7UUFDMUIsSUFBSXNFLFNBQVMsR0FBR0QsTUFBTSxDQUFDdkQsSUFBSSxDQUFDLEtBQUssQ0FBQztRQUNsQ3VELE1BQU0sQ0FBQ3ZELElBQUksQ0FBQyxLQUFLLEVBQUV3RCxTQUFTLENBQUM7UUFDN0JqRSxZQUFZLENBQUM0RCxTQUFTLEdBQUcsSUFBSTtNQUNqQztJQUVKO0lBRUFGLGFBQWEsRUFBRTtFQUVuQixDQUFDLENBQUM7QUFDTixDQUFDLEVBQUVRLE1BQU0sQ0FBQzs7Ozs7O1VDaFpWO1VBQ0E7O1VBRUE7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7VUFDQTtVQUNBO1VBQ0E7O1VBRUE7VUFDQTs7VUFFQTtVQUNBO1VBQ0E7Ozs7O1dDdEJBO1dBQ0E7V0FDQTtXQUNBO1dBQ0E7V0FDQSxpQ0FBaUMsV0FBVztXQUM1QztXQUNBOzs7OztXQ1BBO1dBQ0E7V0FDQTtXQUNBO1dBQ0EseUNBQXlDLHdDQUF3QztXQUNqRjtXQUNBO1dBQ0E7Ozs7O1dDUEE7Ozs7O1dDQUE7V0FDQTtXQUNBO1dBQ0EsdURBQXVELGlCQUFpQjtXQUN4RTtXQUNBLGdEQUFnRCxhQUFhO1dBQzdEIiwic291cmNlcyI6WyJ3ZWJwYWNrOi8vZ29vZ2xlLXJldmlld3MvLi9zcmMvanMvYWRtaW4vZ29vZ2xlLXJldmlld3MtYWRtaW4uanMiLCJ3ZWJwYWNrOi8vZ29vZ2xlLXJldmlld3Mvd2VicGFjay9ib290c3RyYXAiLCJ3ZWJwYWNrOi8vZ29vZ2xlLXJldmlld3Mvd2VicGFjay9ydW50aW1lL2NvbXBhdCBnZXQgZGVmYXVsdCBleHBvcnQiLCJ3ZWJwYWNrOi8vZ29vZ2xlLXJldmlld3Mvd2VicGFjay9ydW50aW1lL2RlZmluZSBwcm9wZXJ0eSBnZXR0ZXJzIiwid2VicGFjazovL2dvb2dsZS1yZXZpZXdzL3dlYnBhY2svcnVudGltZS9oYXNPd25Qcm9wZXJ0eSBzaG9ydGhhbmQiLCJ3ZWJwYWNrOi8vZ29vZ2xlLXJldmlld3Mvd2VicGFjay9ydW50aW1lL21ha2UgbmFtZXNwYWNlIG9iamVjdCIsIndlYnBhY2s6Ly9nb29nbGUtcmV2aWV3cy8uL3NyYy9qcy9hZG1pbi9hZG1pbi1idW5kbGUuanMiXSwic291cmNlc0NvbnRlbnQiOlsiKGZ1bmN0aW9uICgkKSB7XHJcbiAgICAndXNlIHN0cmljdCc7XHJcblxyXG4gICAgJChkb2N1bWVudCkucmVhZHkoZnVuY3Rpb24gKCkge1xyXG5cclxuICAgICAgICBjb25zdCAkc2VhcmNoID0gJCgnLmpzLXNlcnAtYnVzaW5lc3Mtc2VhcmNoJyk7XHJcbiAgICAgICAgY29uc3QgJHNlYXJjaEJ1dHRvblBybyA9ICQoJy5idXR0b24uc2VhcmNoLWJ1c2luZXNzLnBybycpO1xyXG4gICAgICAgIGNvbnN0ICRwdWxsQnV0dG9uUHJvID0gJCgnLmJ1dHRvbi5wdWxsLXJldmlld3MucHJvJyk7XHJcbiAgICAgICAgY29uc3QgJHB1bGxCdXR0b25GZWUgPSAkKCcuYnV0dG9uLnB1bGwtcmV2aWV3cy5mcmVlJyk7XHJcbiAgICAgICAgY29uc3QgJGJ1dHRvblJvdyA9ICQoJy5zZXJwLWNvbnRhaW5lciAuYnV0dG9uLXJvdycpO1xyXG4gICAgICAgIGNvbnN0ICRlcnJvciA9ICQoJyNlcnJvcnMnKTtcclxuICAgICAgICBjb25zdCAkbGFuZ3VhZ2VEcm9wZG93biA9ICQoJyNyZXZpZXdzX2xhbmd1YWdlXzMnKTtcclxuICAgICAgICBjb25zdCAkc3VibWl0QnV0dG9uID0gJCgnaW5wdXRbdHlwZT1cInN1Ym1pdFwiXScpO1xyXG4gICAgICAgIGNvbnN0ICRzaG93RHVtbXlDb250ZW50ID0gJCgnI3Nob3dfZHVtbXlfY29udGVudCcpO1xyXG5cclxuICAgICAgICBmdW5jdGlvbiBoYW5kbGVfdGFicygpIHtcclxuICAgICAgICAgICAgLy8gSGlkZSBhbGwgYWRkaXRpb25hbCBzZXR0aW5ncyBvbiBwYWdlbG9hZFxyXG4gICAgICAgICAgICBjb25zdCAkY29ubmVjdFNldHRpbmdzID0gJCgnI2Nvbm5lY3Rfc2V0dGluZ3MsICNjb25uZWN0X3NldHRpbmdzICsgdGFibGUuZm9ybS10YWJsZScpO1xyXG4gICAgICAgICAgICBjb25zdCAkY29ubmVjdFRhYiA9ICQoJy5uYXYtdGFiLXdyYXBwZXIubWVudSA+IGFbaHJlZj1cIiNjb25uZWN0X3NldHRpbmdzXCJdJyk7XHJcbiAgICAgICAgICAgIGNvbnN0ICRkaXNwbGF5U2V0dGluZ3MgPSAkKCcjZGlzcGxheV9zZXR0aW5ncywgI2Rpc3BsYXlfc2V0dGluZ3MgKyB0YWJsZS5mb3JtLXRhYmxlJyk7XHJcbiAgICAgICAgICAgIGNvbnN0ICRkaXNwbGF5VGFiID0gJCgnLm5hdi10YWItd3JhcHBlci5tZW51ID4gYVtocmVmPVwiI2Rpc3BsYXlfc2V0dGluZ3NcIl0nKTtcclxuICAgICAgICAgICAgY29uc3QgJGVtYmVkZGluZ0luc3RydWN0aW9ucyA9ICQoJyNlbWJlZGRpbmdfaW5zdHJ1Y3Rpb25zLCAjZW1iZWRkaW5nX2luc3RydWN0aW9ucyArIHRhYmxlLmZvcm0tdGFibGUnKTtcclxuICAgICAgICAgICAgY29uc3QgJGVtYmVkZGluZ0luc3RydWN0aW9uc1RhYiA9ICQoJy5uYXYtdGFiLXdyYXBwZXIubWVudSA+IGFbaHJlZj1cIiNlbWJlZGRpbmdfaW5zdHJ1Y3Rpb25zXCJdJyk7XHJcbiAgICAgICAgICAgIGNvbnN0ICRuYXZUYWJzID0gJCgnLm5hdi10YWItd3JhcHBlci5tZW51ID4gLm5hdi10YWI6bm90KC51cGdyYWRlKScpO1xyXG5cclxuICAgICAgICAgICAgJGRpc3BsYXlTZXR0aW5ncy5oaWRlKCk7XHJcbiAgICAgICAgICAgICRlbWJlZGRpbmdJbnN0cnVjdGlvbnMuaGlkZSgpO1xyXG5cclxuICAgICAgICAgICAgbGV0IGN1cnJlbnRUYWIgPSBudWxsO1xyXG5cclxuICAgICAgICAgICAgJG5hdlRhYnMuZWFjaChmdW5jdGlvbiAoaW5kZXgpIHtcclxuICAgICAgICAgICAgICAgICQodGhpcykuY2xpY2soZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgICAgICAgICBlLnByZXZlbnREZWZhdWx0KCk7XHJcblxyXG4gICAgICAgICAgICAgICAgICAgIC8vIGZvciBjb25uZWN0IHNldHRpbmdzXHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKGluZGV4ID09PSAwKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGlmIChjdXJyZW50VGFiID09PSAwKSByZXR1cm47XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICRjb25uZWN0U2V0dGluZ3Muc2hvdygpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZGlzcGxheVNldHRpbmdzLmhpZGUoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJGVtYmVkZGluZ0luc3RydWN0aW9ucy5oaWRlKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICRuYXZUYWJzLnJlbW92ZUNsYXNzKCduYXYtdGFiLWFjdGl2ZScpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkKHRoaXMpLmFkZENsYXNzKCduYXYtdGFiLWFjdGl2ZScpLmJsdXIoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaGlzdG9yeS5wdXNoU3RhdGUoe30sICcnLCAnI2Nvbm5lY3Rfc2V0dGluZ3MnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgbG9jYWxTdG9yYWdlLmdyX2xvY2F0aW9uID0gJyNjb25uZWN0X3NldHRpbmdzJztcclxuICAgICAgICAgICAgICAgICAgICAgICAgY3VycmVudFRhYiA9IDA7XHJcbiAgICAgICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgICAgICAvLyBmb3IgZGlzcGxheSBzZXR0aW5nc1xyXG4gICAgICAgICAgICAgICAgICAgIGlmIChpbmRleCA9PT0gMSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoY3VycmVudFRhYiA9PT0gMSkgcmV0dXJuO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZGlzcGxheVNldHRpbmdzLnNob3coKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJGNvbm5lY3RTZXR0aW5ncy5oaWRlKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICRlbWJlZGRpbmdJbnN0cnVjdGlvbnMuaGlkZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkbmF2VGFicy5yZW1vdmVDbGFzcygnbmF2LXRhYi1hY3RpdmUnKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJCh0aGlzKS5hZGRDbGFzcygnbmF2LXRhYi1hY3RpdmUnKS5ibHVyKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGhpc3RvcnkucHVzaFN0YXRlKHt9LCAnJywgJyNkaXNwbGF5X3NldHRpbmdzJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGxvY2FsU3RvcmFnZS5ncl9sb2NhdGlvbiA9ICcjZGlzcGxheV9zZXR0aW5ncyc7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGN1cnJlbnRUYWIgPSAxO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgICAgICAgICAgICAgLy8gZm9yIGRpc3BsYXkgc2V0dGluZ3NcclxuICAgICAgICAgICAgICAgICAgICBpZiAoaW5kZXggPT09IDIpIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKGN1cnJlbnRUYWIgPT09IDIpIHJldHVybjtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJGVtYmVkZGluZ0luc3RydWN0aW9ucy5zaG93KCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICRjb25uZWN0U2V0dGluZ3MuaGlkZSgpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZGlzcGxheVNldHRpbmdzLmhpZGUoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJG5hdlRhYnMucmVtb3ZlQ2xhc3MoJ25hdi10YWItYWN0aXZlJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICQodGhpcykuYWRkQ2xhc3MoJ25hdi10YWItYWN0aXZlJykuYmx1cigpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBoaXN0b3J5LnB1c2hTdGF0ZSh7fSwgJycsICcjZW1iZWRkaW5nX2luc3RydWN0aW9ucycpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBsb2NhbFN0b3JhZ2UuZ3JfbG9jYXRpb24gPSAnI2VtYmVkZGluZ19pbnN0cnVjdGlvbnMnO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBjdXJyZW50VGFiID0gMjtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9KTtcclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICAgICBsZXQgaGFzaCA9IHdpbmRvdy5sb2NhdGlvbi5oYXNoO1xyXG5cclxuICAgICAgICAgICAgaWYgKGhhc2ggPT09ICcjZGlzcGxheV9zZXR0aW5ncycgfHwgbG9jYWxTdG9yYWdlLmdyX2xvY2F0aW9uID09PSAnI2Rpc3BsYXlfc2V0dGluZ3MnKSB7XHJcbiAgICAgICAgICAgICAgICAkZGlzcGxheVRhYi5jbGljaygpO1xyXG4gICAgICAgICAgICB9IGVsc2UgaWYgKGhhc2ggPT09ICcjZW1iZWRkaW5nX2luc3RydWN0aW9ucycgfHwgbG9jYWxTdG9yYWdlLmdyX2xvY2F0aW9uID09PSAnI2VtYmVkZGluZ19pbnN0cnVjdGlvbnMnKSB7XHJcbiAgICAgICAgICAgICAgICAkZW1iZWRkaW5nSW5zdHJ1Y3Rpb25zVGFiLmNsaWNrKCk7XHJcbiAgICAgICAgICAgIH0gZWxzZSB7XHJcbiAgICAgICAgICAgICAgICAkY29ubmVjdFRhYi5jbGljaygpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICBoYW5kbGVfdGFicygpO1xyXG5cclxuXHJcblxyXG4gICAgICAgIC8vIHJlbW92ZSBkaXNhYmxlZCBhdHRyaWJ1dGUgd2hlbiBzZWFyY2ggZmllbGQgaXMgY2hhbmdlZFxyXG4gICAgICAgICRzZWFyY2gub24oJ2tleXVwIGNoYW5nZScsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgJHNlYXJjaEJ1dHRvblByby5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvLyBwcmV2ZW50ICdlbnRlcicgZnJvbSBzdWJtaXR0aW5nIGZvcm1cclxuICAgICAgICAkc2VhcmNoLm9uKCdrZXlwcmVzcycsIGZ1bmN0aW9uIChlKSB7XHJcbiAgICAgICAgICAgIGlmIChlLmtleUNvZGUgPT0gJzEwJyB8fCBlLmtleUNvZGUgPT0gJzEzJykge1xyXG4gICAgICAgICAgICAgICAgZS5wcmV2ZW50RGVmYXVsdCgpO1xyXG4gICAgICAgICAgICB9XHJcbiAgICAgICAgfSlcclxuXHJcbiAgICAgICAgLy8gU2VhcmNoIGZvciBidXNpbmVzc1xyXG4gICAgICAgICRzZWFyY2hCdXR0b25Qcm8uY2xpY2soZnVuY3Rpb24gKCkge1xyXG5cclxuICAgICAgICAgICAgY29uc3QgJHRoYXQgPSAkKHRoaXMpO1xyXG5cclxuICAgICAgICAgICAgaWYgKCR0aGF0LmF0dHIoJ2Rpc2FibGVkJykpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgIHVybDoganNfZ2xvYmFsLndwX2FqYXhfdXJsLFxyXG4gICAgICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbjogJ2hhbmRsZV9zZXJwX2J1c2luZXNzX3NlYXJjaCcsXHJcbiAgICAgICAgICAgICAgICAgICAgc2VhcmNoOiAkc2VhcmNoLnZhbCgpLFxyXG4gICAgICAgICAgICAgICAgICAgIGxhbmd1YWdlOiBqc19nbG9iYWwubGFuZ3VhZ2VcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgJGJ1dHRvblJvd1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAuYWRkQ2xhc3MoJ2J1c3knKTtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgJHNlYXJjaEJ1dHRvblByb1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzcG9uc2UpIHtcclxuXHJcbiAgICAgICAgICAgICAgICAgICAgaWYgKCFyZXNwb25zZSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZXJyb3IuaHRtbCgnRXJyb3IgaW4gc2VhcmNoIHJlc3BvbnNlLiBQbGVhc2UgdHJ5IGFnYWluLicpO1xyXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAodW5kZWZpbmVkID09PSByZXNwb25zZS5kYXRhIHx8IHVuZGVmaW5lZCA9PT0gcmVzcG9uc2UuZGF0YS5odG1sKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICRlcnJvci5odG1sKCdTZWFyY2ggcmVzcG9uc2UgZmFpbGVkLiBQbGVhc2UgdHJ5IGFnYWluLicpO1xyXG4gICAgICAgICAgICAgICAgICAgIH0gZWxzZSBpZiAocmVzcG9uc2UgJiYgcmVzcG9uc2UuZGF0YS5odG1sID09PSAnJykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZXJyb3IuaHRtbCgnUmVzdWx0cyBlbXB0eS4gUGxlYXNlIHRyeSBhZ2Fpbi4nKTtcclxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKCFyZXNwb25zZS5zdWNjZXNzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICRlcnJvci5odG1sKHJlc3BvbnNlLmRhdGEuaHRtbCk7XHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCRzZWFyY2guaGFzQ2xhc3MoJ2hhcy1lcnJvcicpKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICAgICAkc2VhcmNoLnJlbW92ZUNsYXNzKCdoYXMtZXJyb3InKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRlcnJvci5mYWRlT3V0KCkuZW1wdHkoKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgJHNlYXJjaC5zaWJsaW5ncygnLnNlcnAtcmVzdWx0cycpLmh0bWwocmVzcG9uc2UuZGF0YS5odG1sKS5zbGlkZURvd24oKTtcclxuICAgICAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgZXJyb3I6IGZ1bmN0aW9uIChYTUxIdHRwUmVxdWVzdCwgdGV4dFN0YXR1cywgZXJyb3JUaHJvd24pIHtcclxuXHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAkYnV0dG9uUm93XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVDbGFzcygnYnVzeScpO1xyXG4gICAgICAgICAgICAgICAgICAgICRzZWFyY2hCdXR0b25Qcm9cclxuICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XHJcbiAgICAgICAgICAgICAgICB9XHJcbiAgICAgICAgICAgIH0pO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvLyBoYW5kbGUgY2xpY2tzIG9uIGxvY2F0aW9uIGRyb3Bkb3duIChzZWxlY3Rpb24pXHJcbiAgICAgICAgJCgnLnNlcnAtc2VhcmNoJykub24oJ2NsaWNrJywgJy5qcy1zZXJwLXJlc3VsdCcsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG4gICAgICAgICAgICBjb25zdCBkYXRhX2lkID0gJHRoaXMudmFsKCk7XHJcbiAgICAgICAgICAgIGNvbnN0IGxvY2F0aW9uX25hbWUgPSAkdGhpcy5wYXJlbnQoKS50ZXh0KCk7XHJcblxyXG4gICAgICAgICAgICAkdGhpcy5jbG9zZXN0KCcuc2VycC1yZXN1bHRzJykuc2xpZGVVcCgpO1xyXG5cclxuICAgICAgICAgICAgJCgnLmpzLXNlcnAtZGF0YS1pZCcpLmF0dHIoJ3ZhbHVlJywgJHRoaXMudmFsKCkpO1xyXG4gICAgICAgICAgICAkKCcuanMtc2VycC1idXNpbmVzcy1zZWFyY2gnKS52YWwoJC50cmltKCR0aGlzLnBhcmVudCgpLnRleHQoKSkpO1xyXG5cclxuICAgICAgICAgICAgJCgnLmJ1dHRvbi5wdWxsLXJldmlld3MnKS5hdHRyKCdkaXNhYmxlZCcsIHRydWUpO1xyXG5cclxuICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgIHVybDoganNfZ2xvYmFsLndwX2FqYXhfdXJsLFxyXG4gICAgICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbjogJ2hhbmRsZV9sb2NhdGlvbl9zYXZpbmcnLFxyXG4gICAgICAgICAgICAgICAgICAgIGRhdGFfaWQ6IGRhdGFfaWQsXHJcbiAgICAgICAgICAgICAgICAgICAgbG9jYXRpb25fbmFtZTogbG9jYXRpb25fbmFtZVxyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICBkaXNhYmxlQnV0dG9uc1doaWxlU2F2aW5nKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgJHNlYXJjaEJ1dHRvblByb1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcclxuICAgICAgICAgICAgICAgICAgICAkZXJyb3IuaGlkZSgpO1xyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGNvbXBsZXRlOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgZW5hYmxlQnV0dG9uc0FmdGVyU2F2aW5nKClcclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAkc2VhcmNoLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgY29uc3QgJHRoaXMgPSAkKHRoaXMpO1xyXG4gICAgICAgICAgICBjb25zdCAkcmVzdWx0c0NvbnRhaW5lciA9ICQoJy5zZXJwLXJlc3VsdHMnKTtcclxuXHJcbiAgICAgICAgICAgIGlmICghJHJlc3VsdHNDb250YWluZXIuY2hpbGRyZW4oKS5sZW5ndGggfHwgISR0aGlzLnRleHQoKS5sZW5ndGgpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgJHJlc3VsdHNDb250YWluZXIuc2xpZGVEb3duKCk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgICRzZWFyY2gub24oJ3NlYXJjaCcsIGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgJCgnLmpzLXNlcnAtZGF0YS1pZCcpLmF0dHIoJ3ZhbHVlJywgJycpO1xyXG4gICAgICAgICAgICAkKCcuc2VycC1yZXN1bHRzJykuc2xpZGVVcCgpO1xyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvLyByZW1vdmUgbGlzdCwgd2hlbiB1c2VyIGNsaWNrcyBhbnl3aGVyZSBlbHNlXHJcbiAgICAgICAgJChkb2N1bWVudCkub24oJ2NsaWNrJywgZnVuY3Rpb24gKGUpIHtcclxuICAgICAgICAgICAgY29uc3QgJGNvbnRhaW5lciA9ICQoJy5zZXJwLXNlYXJjaCcpO1xyXG4gICAgICAgICAgICBjb25zdCAkcmVzdWx0c0NvbnRhaW5lciA9ICQoJy5zZXJwLXJlc3VsdHMnKTtcclxuXHJcbiAgICAgICAgICAgIGlmICghJChlLnRhcmdldCkuY2xvc2VzdCgkY29udGFpbmVyKS5sZW5ndGgpIHtcclxuICAgICAgICAgICAgICAgICRyZXN1bHRzQ29udGFpbmVyLnNsaWRlVXAoKTtcclxuICAgICAgICAgICAgfVxyXG4gICAgICAgIH0pO1xyXG5cclxuICAgICAgICAvLyBzYXZlIGRyb3Bkb3duIGxhbmd1YWdlIG9uIGNoYW5nZVxyXG4gICAgICAgICRsYW5ndWFnZURyb3Bkb3duLmNoYW5nZShmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgIGNvbnN0IGxhbmd1YWdlID0gJCh0aGlzKS52YWwoKTtcclxuXHJcbiAgICAgICAgICAgICQuYWpheCh7XHJcbiAgICAgICAgICAgICAgICB1cmw6IGpzX2dsb2JhbC53cF9hamF4X3VybCxcclxuICAgICAgICAgICAgICAgIGRhdGE6IHtcclxuICAgICAgICAgICAgICAgICAgICBhY3Rpb246ICdoYW5kbGVfbGFuZ3VhZ2Vfc2F2aW5nJyxcclxuICAgICAgICAgICAgICAgICAgICBzZWFyY2g6IGxhbmd1YWdlLFxyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICBkaXNhYmxlQnV0dG9uc1doaWxlU2F2aW5nKCk7XHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgY29tcGxldGU6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICBlbmFibGVCdXR0b25zQWZ0ZXJTYXZpbmcoKVxyXG4gICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICB9KTtcclxuXHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8vIFBSTzogcHVsbCByZXZpZXdzIGJ1dHRvblxyXG4gICAgICAgICRwdWxsQnV0dG9uUHJvLm9uKCdjbGljaycsIGZ1bmN0aW9uICgpIHtcclxuXHJcbiAgICAgICAgICAgIGNvbnN0ICR0aGF0ID0gJCh0aGlzKTtcclxuICAgICAgICAgICAgaWYgKCR0aGF0LmF0dHIoJ2Rpc2FibGVkJykpIHtcclxuICAgICAgICAgICAgICAgIHJldHVybjtcclxuICAgICAgICAgICAgfVxyXG5cclxuICAgICAgICAgICAgY29uc3QgJHN1Ym1pdCA9ICQoJyNzdWJtaXQnKTtcclxuICAgICAgICAgICAgbGV0IGhhc19lcnJvciA9IGZhbHNlO1xyXG5cclxuICAgICAgICAgICAgJC5hamF4KHtcclxuICAgICAgICAgICAgICAgIHVybDoganNfZ2xvYmFsLndwX2FqYXhfdXJsLFxyXG4gICAgICAgICAgICAgICAgZGF0YToge1xyXG4gICAgICAgICAgICAgICAgICAgIGFjdGlvbjogJ2hhbmRsZV9nZXRfcmV2aWV3c19wcm9fYXBpJ1xyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGJlZm9yZVNlbmQ6IGZ1bmN0aW9uICgpIHtcclxuICAgICAgICAgICAgICAgICAgICAkYnV0dG9uUm93XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIC5hZGRDbGFzcygnYnVzeScpO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAkcHVsbEJ1dHRvblByb1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBzdWNjZXNzOiBmdW5jdGlvbiAocmVzcG9uc2UpIHtcclxuICAgICAgICAgICAgICAgICAgICAvLyBpZiBldmVyeXRoaW5nJ3Mgb2ssIGRvIG5vdGhpbmdcclxuICAgICAgICAgICAgICAgICAgICBpZiAocmVzcG9uc2UgPT09IFwiMFwiKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIHJldHVybiBmYWxzZTtcclxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKCFyZXNwb25zZSkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZXJyb3IuaHRtbCgnRXJyb3IgaW4gcmV2aWV3cyByZXNwb25zZS4gUGxlYXNlIHRyeSBhZ2Fpbi4nKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgaGFzX2Vycm9yID0gdHJ1ZTtcclxuICAgICAgICAgICAgICAgICAgICB9IGVsc2UgaWYgKHVuZGVmaW5lZCA9PT0gcmVzcG9uc2UuZGF0YSB8fCB1bmRlZmluZWQgPT09IHJlc3BvbnNlLmRhdGEuaHRtbCkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZXJyb3IuaHRtbCgnUmV2aWV3cyByZXNwb25zZSBmYWlsZWQuIFBsZWFzZSB0cnkgYWdhaW4uJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGhhc19lcnJvciA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmIChyZXNwb25zZSAmJiByZXNwb25zZS5kYXRhLmh0bWwgPT09ICcnKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgICRlcnJvci5odG1sKCdSZXZpZXdzIHJlc3VsdHMgZW1wdHkuIFBsZWFzZSB0cnkgYWdhaW4uJyk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIGhhc19lcnJvciA9IHRydWU7XHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIGlmICghcmVzcG9uc2Uuc3VjY2Vzcykge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAkZXJyb3IuaHRtbChyZXNwb25zZS5kYXRhLmh0bWwpO1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBoYXNfZXJyb3IgPSB0cnVlO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBlcnJvcjogZnVuY3Rpb24gKFhNTEh0dHBSZXF1ZXN0LCB0ZXh0U3RhdHVzLCBlcnJvclRocm93bikge1xyXG5cclxuICAgICAgICAgICAgICAgICAgICBjb25zdCB0ZXN0ID0gMTtcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKGpxWEhSLCB0ZXh0U3RhdHVzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgJGJ1dHRvblJvd1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAucmVtb3ZlQ2xhc3MoJ2J1c3knKTtcclxuICAgICAgICAgICAgICAgICAgICBpZiAoIWhhc19lcnJvcikge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICBpZiAoICRzaG93RHVtbXlDb250ZW50LmlzKCc6Y2hlY2tlZCcpICkge1xyXG4gICAgICAgICAgICAgICAgICAgICAgICAgICAgJHNob3dEdW1teUNvbnRlbnQuY2xpY2soKTtcclxuICAgICAgICAgICAgICAgICAgICAgICAgfVxyXG4gICAgICAgICAgICAgICAgICAgICAgICAkc3VibWl0LmNsaWNrKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgfSBlbHNlIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgJHB1bGxCdXR0b25Qcm9cclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgIC5yZW1vdmVBdHRyKCdkaXNhYmxlZCcpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG5cclxuICAgICAgICAvKipcclxuICAgICAgICAgKiBGUkVFOiBwdWxsIHJldmlld3MgYnV0dG9uXHJcbiAgICAgICAgICovXHJcbiAgICAgICAgJHB1bGxCdXR0b25GZWUub24oJ2NsaWNrJywgZnVuY3Rpb24gKCkge1xyXG5cclxuICAgICAgICAgICAgY29uc3QgJHRoYXQgPSAkKHRoaXMpO1xyXG4gICAgICAgICAgICBjb25zdCAkc3VibWl0ID0gJCgnI3N1Ym1pdCcpO1xyXG4gICAgICAgICAgICBjb25zdCBwbGFjZV9pZCA9ICQoJ2lucHV0W25hbWU9XCJnb29nbGVfcmV2aWV3c19vcHRpb25fbmFtZVtnbWJfaWRfMV1cIl0nKS52YWwoKTtcclxuICAgICAgICAgICAgY29uc3QgbGFuZ3VhZ2UgPSAkKCdzZWxlY3QjcmV2aWV3c19sYW5ndWFnZV8zJykudmFsKCk7XHJcbiAgICAgICAgICAgIGNvbnN0ICRlcnJvcnMgPSAkKCcjZXJyb3JzJyk7XHJcblxyXG4gICAgICAgICAgICAkLmFqYXgoe1xyXG4gICAgICAgICAgICAgICAgdXJsOiBqc19nbG9iYWwud3BfYWpheF91cmwsXHJcbiAgICAgICAgICAgICAgICBkYXRhOiB7XHJcbiAgICAgICAgICAgICAgICAgICAgYWN0aW9uOiAnZ2V0X3Jldmlld3NfZnJlZV9hcGknLFxyXG4gICAgICAgICAgICAgICAgICAgIHBsYWNlX2lkOiBwbGFjZV9pZCxcclxuICAgICAgICAgICAgICAgICAgICBsYW5ndWFnZTogbGFuZ3VhZ2VcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBiZWZvcmVTZW5kOiBmdW5jdGlvbiAoKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgJHRoYXRcclxuICAgICAgICAgICAgICAgICAgICAgICAgLmFkZENsYXNzKCdwdWxsaW5nJylcclxuICAgICAgICAgICAgICAgICAgICAgICAgLmF0dHIoJ2Rpc2FibGVkJywgdHJ1ZSk7XHJcbiAgICAgICAgICAgICAgICB9LFxyXG4gICAgICAgICAgICAgICAgc3VjY2VzczogZnVuY3Rpb24gKHJlc3BvbnNlKSB7XHJcblxyXG4gICAgICAgICAgICAgICAgfSxcclxuICAgICAgICAgICAgICAgIGVycm9yOiBmdW5jdGlvbiAoWE1MSHR0cFJlcXVlc3QsIHRleHRTdGF0dXMsIGVycm9yVGhyb3duKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgY29uc3QgbWVzc2FnZSA9IGVycm9yVGhyb3duICsgJyAtIFBsZWFzZSBkb3VibGUtY2hlY2sgeW91ciBQbGFjZSBJRC4nO1xyXG4gICAgICAgICAgICAgICAgICAgICRlcnJvcnMudGV4dChtZXNzYWdlKTtcclxuICAgICAgICAgICAgICAgIH0sXHJcbiAgICAgICAgICAgICAgICBjb21wbGV0ZTogZnVuY3Rpb24gKFhNTEh0dHBSZXF1ZXN0LCB0ZXh0U3RhdHVzKSB7XHJcbiAgICAgICAgICAgICAgICAgICAgJHRoYXRcclxuICAgICAgICAgICAgICAgICAgICAgICAgLnJlbW92ZUNsYXNzKCdwdWxsaW5nJylcclxuICAgICAgICAgICAgICAgICAgICAgICAgLmF0dHIoJ2Rpc2FibGVkJywgZmFsc2UpO1xyXG5cclxuICAgICAgICAgICAgICAgICAgICBpZiAodGV4dFN0YXR1cyAhPT0gJ2Vycm9yJykge1xyXG5cclxuICAgICAgICAgICAgICAgICAgICAgICAgaWYgKCAkc2hvd0R1bW15Q29udGVudC5pcygnOmNoZWNrZWQnKSApIHtcclxuICAgICAgICAgICAgICAgICAgICAgICAgICAgICRzaG93RHVtbXlDb250ZW50LmNsaWNrKCk7XHJcbiAgICAgICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgICAgICAgICAgJHN1Ym1pdC5jbGljaygpO1xyXG4gICAgICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgICAgIH1cclxuICAgICAgICAgICAgfSk7XHJcbiAgICAgICAgfSk7XHJcblxyXG4gICAgICAgIC8vIGRpc2FibGUgYnV0dG9ucyB3aGVuIGFqYXggc2F2aW5nXHJcbiAgICAgICAgZnVuY3Rpb24gZGlzYWJsZUJ1dHRvbnNXaGlsZVNhdmluZygpIHtcclxuICAgICAgICAgICAgJHN1Ym1pdEJ1dHRvblxyXG4gICAgICAgICAgICAgICAgLmF0dHIoJ2Rpc2FibGVkJywgdHJ1ZSk7XHJcbiAgICAgICAgICAgICRwdWxsQnV0dG9uUHJvXHJcbiAgICAgICAgICAgICAgICAuYXR0cignZGlzYWJsZWQnLCB0cnVlKTtcclxuICAgICAgICAgICAgJHB1bGxCdXR0b25GZWVcclxuICAgICAgICAgICAgICAgIC5hdHRyKCdkaXNhYmxlZCcsIHRydWUpO1xyXG4gICAgICAgIH1cclxuXHJcbiAgICAgICAgLy8gZW5hYmxlIGJ1dHRvbnMgYWZ0ZXIgYWpheCBzYXZpbmdcclxuICAgICAgICBmdW5jdGlvbiBlbmFibGVCdXR0b25zQWZ0ZXJTYXZpbmcoKSB7XHJcbiAgICAgICAgICAgICRwdWxsQnV0dG9uUHJvXHJcbiAgICAgICAgICAgICAgICAucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcclxuICAgICAgICAgICAgJHN1Ym1pdEJ1dHRvblxyXG4gICAgICAgICAgICAgICAgLnJlbW92ZUF0dHIoJ2Rpc2FibGVkJyk7XHJcbiAgICAgICAgICAgICRwdWxsQnV0dG9uRmVlXHJcbiAgICAgICAgICAgICAgICAucmVtb3ZlQXR0cignZGlzYWJsZWQnKTtcclxuICAgICAgICB9XHJcblxyXG4gICAgICAgIC8vIHNob3cgdmlkZW8gbW9kYWwgaW4gZnJlZSB2ZXJzaW9uXHJcbiAgICAgICAgZnVuY3Rpb24gdHJpZ2dlcl9tb2RhbCgpIHtcclxuICAgICAgICAgICAgY29uc3QgJG1vZGFsID0gJChcIiNob3dfdG9fbW9kYWxcIik7XHJcbiAgICAgICAgICAgIGlmIChsb2NhbFN0b3JhZ2UuaGlkZU1vZGFsIHx8ICEkbW9kYWwgKSByZXR1cm47XHJcblxyXG4gICAgICAgICAgICBjb25zdCAkY2xvc2UgPSAkKFwiI21vZGFsX2Nsb3NlXCIpO1xyXG4gICAgICAgICAgICBjb25zdCAkb3ZlcmxheSA9ICQoJy5tb2RhbCAubW9kYWwtb3ZlcmxheScpO1xyXG4gICAgICAgICAgICBjb25zdCAkYm9keSA9ICQoJ2JvZHknKTtcclxuICAgICAgICAgICAgY29uc3QgJHZpZGVvID0gJCgnLnJlc3BvbnNpdmVfaWZyYW1lIGlmcmFtZScpO1xyXG5cclxuICAgICAgICAgICAgJG1vZGFsLnNob3coKTtcclxuXHJcbiAgICAgICAgICAgIC8vIG1ha2UgYm9keSBmaXhlZCB0byBwcmV2ZW50IHNjcm9sbGluZ1xyXG4gICAgICAgICAgICAkYm9keS5hZGRDbGFzcygnZml4ZWQnKTtcclxuICAgICAgICAgICAgJG1vZGFsLnJlbW92ZUNsYXNzKCdoaWRlJyk7XHJcblxyXG4gICAgICAgICAgICAkY2xvc2UuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaGlkZU1vZGFsKCk7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgJG92ZXJsYXkuY2xpY2soZnVuY3Rpb24gKCkge1xyXG4gICAgICAgICAgICAgICAgaGlkZU1vZGFsKCk7XHJcbiAgICAgICAgICAgIH0pO1xyXG5cclxuICAgICAgICAgICAgZnVuY3Rpb24gaGlkZU1vZGFsKCkge1xyXG4gICAgICAgICAgICAgICAgJG1vZGFsLmhpZGUoKTtcclxuICAgICAgICAgICAgICAgICRib2R5LnJlbW92ZUNsYXNzKCdmaXhlZCcpO1xyXG4gICAgICAgICAgICAgICAgdmFyIGlmcmFtZVNyYyA9ICR2aWRlby5hdHRyKCdzcmMnKTtcclxuICAgICAgICAgICAgICAgICR2aWRlby5hdHRyKCdzcmMnLCBpZnJhbWVTcmMpO1xyXG4gICAgICAgICAgICAgICAgbG9jYWxTdG9yYWdlLmhpZGVNb2RhbCA9IHRydWU7XHJcbiAgICAgICAgICAgIH1cclxuXHJcbiAgICAgICAgfVxyXG5cclxuICAgICAgICB0cmlnZ2VyX21vZGFsKCk7XHJcblxyXG4gICAgfSk7XHJcbn0pKGpRdWVyeSk7XHJcbiIsIi8vIFRoZSBtb2R1bGUgY2FjaGVcbnZhciBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX18gPSB7fTtcblxuLy8gVGhlIHJlcXVpcmUgZnVuY3Rpb25cbmZ1bmN0aW9uIF9fd2VicGFja19yZXF1aXJlX18obW9kdWxlSWQpIHtcblx0Ly8gQ2hlY2sgaWYgbW9kdWxlIGlzIGluIGNhY2hlXG5cdHZhciBjYWNoZWRNb2R1bGUgPSBfX3dlYnBhY2tfbW9kdWxlX2NhY2hlX19bbW9kdWxlSWRdO1xuXHRpZiAoY2FjaGVkTW9kdWxlICE9PSB1bmRlZmluZWQpIHtcblx0XHRyZXR1cm4gY2FjaGVkTW9kdWxlLmV4cG9ydHM7XG5cdH1cblx0Ly8gQ3JlYXRlIGEgbmV3IG1vZHVsZSAoYW5kIHB1dCBpdCBpbnRvIHRoZSBjYWNoZSlcblx0dmFyIG1vZHVsZSA9IF9fd2VicGFja19tb2R1bGVfY2FjaGVfX1ttb2R1bGVJZF0gPSB7XG5cdFx0Ly8gbm8gbW9kdWxlLmlkIG5lZWRlZFxuXHRcdC8vIG5vIG1vZHVsZS5sb2FkZWQgbmVlZGVkXG5cdFx0ZXhwb3J0czoge31cblx0fTtcblxuXHQvLyBFeGVjdXRlIHRoZSBtb2R1bGUgZnVuY3Rpb25cblx0X193ZWJwYWNrX21vZHVsZXNfX1ttb2R1bGVJZF0obW9kdWxlLCBtb2R1bGUuZXhwb3J0cywgX193ZWJwYWNrX3JlcXVpcmVfXyk7XG5cblx0Ly8gUmV0dXJuIHRoZSBleHBvcnRzIG9mIHRoZSBtb2R1bGVcblx0cmV0dXJuIG1vZHVsZS5leHBvcnRzO1xufVxuXG4iLCIvLyBnZXREZWZhdWx0RXhwb3J0IGZ1bmN0aW9uIGZvciBjb21wYXRpYmlsaXR5IHdpdGggbm9uLWhhcm1vbnkgbW9kdWxlc1xuX193ZWJwYWNrX3JlcXVpcmVfXy5uID0gKG1vZHVsZSkgPT4ge1xuXHR2YXIgZ2V0dGVyID0gbW9kdWxlICYmIG1vZHVsZS5fX2VzTW9kdWxlID9cblx0XHQoKSA9PiAobW9kdWxlWydkZWZhdWx0J10pIDpcblx0XHQoKSA9PiAobW9kdWxlKTtcblx0X193ZWJwYWNrX3JlcXVpcmVfXy5kKGdldHRlciwgeyBhOiBnZXR0ZXIgfSk7XG5cdHJldHVybiBnZXR0ZXI7XG59OyIsIi8vIGRlZmluZSBnZXR0ZXIgZnVuY3Rpb25zIGZvciBoYXJtb255IGV4cG9ydHNcbl9fd2VicGFja19yZXF1aXJlX18uZCA9IChleHBvcnRzLCBkZWZpbml0aW9uKSA9PiB7XG5cdGZvcih2YXIga2V5IGluIGRlZmluaXRpb24pIHtcblx0XHRpZihfX3dlYnBhY2tfcmVxdWlyZV9fLm8oZGVmaW5pdGlvbiwga2V5KSAmJiAhX193ZWJwYWNrX3JlcXVpcmVfXy5vKGV4cG9ydHMsIGtleSkpIHtcblx0XHRcdE9iamVjdC5kZWZpbmVQcm9wZXJ0eShleHBvcnRzLCBrZXksIHsgZW51bWVyYWJsZTogdHJ1ZSwgZ2V0OiBkZWZpbml0aW9uW2tleV0gfSk7XG5cdFx0fVxuXHR9XG59OyIsIl9fd2VicGFja19yZXF1aXJlX18ubyA9IChvYmosIHByb3ApID0+IChPYmplY3QucHJvdG90eXBlLmhhc093blByb3BlcnR5LmNhbGwob2JqLCBwcm9wKSkiLCIvLyBkZWZpbmUgX19lc01vZHVsZSBvbiBleHBvcnRzXG5fX3dlYnBhY2tfcmVxdWlyZV9fLnIgPSAoZXhwb3J0cykgPT4ge1xuXHRpZih0eXBlb2YgU3ltYm9sICE9PSAndW5kZWZpbmVkJyAmJiBTeW1ib2wudG9TdHJpbmdUYWcpIHtcblx0XHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgU3ltYm9sLnRvU3RyaW5nVGFnLCB7IHZhbHVlOiAnTW9kdWxlJyB9KTtcblx0fVxuXHRPYmplY3QuZGVmaW5lUHJvcGVydHkoZXhwb3J0cywgJ19fZXNNb2R1bGUnLCB7IHZhbHVlOiB0cnVlIH0pO1xufTsiLCJpbXBvcnQgJy4vZ29vZ2xlLXJldmlld3MtYWRtaW4nOyJdLCJuYW1lcyI6WyIkIiwiZG9jdW1lbnQiLCJyZWFkeSIsIiRzZWFyY2giLCIkc2VhcmNoQnV0dG9uUHJvIiwiJHB1bGxCdXR0b25Qcm8iLCIkcHVsbEJ1dHRvbkZlZSIsIiRidXR0b25Sb3ciLCIkZXJyb3IiLCIkbGFuZ3VhZ2VEcm9wZG93biIsIiRzdWJtaXRCdXR0b24iLCIkc2hvd0R1bW15Q29udGVudCIsImhhbmRsZV90YWJzIiwiJGNvbm5lY3RTZXR0aW5ncyIsIiRjb25uZWN0VGFiIiwiJGRpc3BsYXlTZXR0aW5ncyIsIiRkaXNwbGF5VGFiIiwiJGVtYmVkZGluZ0luc3RydWN0aW9ucyIsIiRlbWJlZGRpbmdJbnN0cnVjdGlvbnNUYWIiLCIkbmF2VGFicyIsImhpZGUiLCJjdXJyZW50VGFiIiwiZWFjaCIsImluZGV4IiwiY2xpY2siLCJlIiwicHJldmVudERlZmF1bHQiLCJzaG93IiwicmVtb3ZlQ2xhc3MiLCJhZGRDbGFzcyIsImJsdXIiLCJoaXN0b3J5IiwicHVzaFN0YXRlIiwibG9jYWxTdG9yYWdlIiwiZ3JfbG9jYXRpb24iLCJoYXNoIiwid2luZG93IiwibG9jYXRpb24iLCJvbiIsInJlbW92ZUF0dHIiLCJrZXlDb2RlIiwiJHRoYXQiLCJhdHRyIiwiYWpheCIsInVybCIsImpzX2dsb2JhbCIsIndwX2FqYXhfdXJsIiwiZGF0YSIsImFjdGlvbiIsInNlYXJjaCIsInZhbCIsImxhbmd1YWdlIiwiYmVmb3JlU2VuZCIsInN1Y2Nlc3MiLCJyZXNwb25zZSIsImh0bWwiLCJ1bmRlZmluZWQiLCJoYXNDbGFzcyIsImZhZGVPdXQiLCJlbXB0eSIsInNpYmxpbmdzIiwic2xpZGVEb3duIiwiZXJyb3IiLCJYTUxIdHRwUmVxdWVzdCIsInRleHRTdGF0dXMiLCJlcnJvclRocm93biIsImNvbXBsZXRlIiwiJHRoaXMiLCJkYXRhX2lkIiwibG9jYXRpb25fbmFtZSIsInBhcmVudCIsInRleHQiLCJjbG9zZXN0Iiwic2xpZGVVcCIsInRyaW0iLCJkaXNhYmxlQnV0dG9uc1doaWxlU2F2aW5nIiwiZW5hYmxlQnV0dG9uc0FmdGVyU2F2aW5nIiwiJHJlc3VsdHNDb250YWluZXIiLCJjaGlsZHJlbiIsImxlbmd0aCIsIiRjb250YWluZXIiLCJ0YXJnZXQiLCJjaGFuZ2UiLCIkc3VibWl0IiwiaGFzX2Vycm9yIiwidGVzdCIsImpxWEhSIiwiaXMiLCJwbGFjZV9pZCIsIiRlcnJvcnMiLCJtZXNzYWdlIiwidHJpZ2dlcl9tb2RhbCIsIiRtb2RhbCIsImhpZGVNb2RhbCIsIiRjbG9zZSIsIiRvdmVybGF5IiwiJGJvZHkiLCIkdmlkZW8iLCJpZnJhbWVTcmMiLCJqUXVlcnkiXSwic291cmNlUm9vdCI6IiJ9