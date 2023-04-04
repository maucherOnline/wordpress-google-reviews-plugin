import $ from 'jquery';

$(document).ready(function() {

    const $connectSettings = $('#connect_settings, #connect_settings + table.form-table');
    const $connectTab = $('.nav-tab-wrapper.menu > a[href="#connect_settings"]');
    const $displaySettings = $('#display_settings, #display_settings + table.form-table');
    const $displayTab = $('.nav-tab-wrapper.menu > a[href="#display_settings"]');
    const $embeddingInstructions = $('#embedding_instructions, #embedding_instructions + table.form-table');
    const $embeddingInstructionsTab = $('.nav-tab-wrapper.menu > a[href="#embedding_instructions"]');
    const $navTabs = $('.nav-tab-wrapper.menu > .nav-tab:not(.upgrade)');

    function handle_tabs() {

        // Hide all additional settings on pageload
        $displaySettings.hide();
        $embeddingInstructions.hide();

        let currentTab = null;

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

        let hash = window.location.hash;

        if (hash === '#display_settings' || localStorage.gr_location === '#display_settings') {
            $displayTab.click();
        } else if (hash === '#embedding_instructions' || localStorage.gr_location === '#embedding_instructions') {
            $embeddingInstructionsTab.click();
        } else {
            $connectTab.click();
        }
    }

    handle_tabs();

});
