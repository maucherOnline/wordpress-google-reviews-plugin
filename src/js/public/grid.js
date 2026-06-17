/**
 * Grid "Load more" button
 *
 * Multi-row: gradient fade over last row + button overlaid on gradient.
 * Single-row: no gradient – button sits cleanly below the grid.
 * Resize: re-evaluates the mode so it stays correct at every viewport width.
 */
export function initShowMore() {
    const showMoreText =
        (typeof swiperSettings !== 'undefined' && swiperSettings.showMoreText)
            ? swiperSettings.showMoreText
            : 'Load more';

    document.querySelectorAll('[data-grwp-show-more]').forEach(function (wrapper) {
        const initial = parseInt(wrapper.getAttribute('data-grwp-show-more'), 10);
        if ( isNaN(initial) || initial < 1 ) return;

        const body = wrapper.querySelector('.grwp_body');
        if ( !body ) return;

        const cards        = Array.from( body.querySelectorAll('.g-review') );
        if ( cards.length <= initial ) return;

        const visibleCards = cards.slice(0, initial);
        const extraCards   = cards.slice(initial);

        // Hide extra cards once (until "Load more" is clicked)
        extraCards.forEach(function (card) { card.classList.add('grwp-card-hidden'); });

        // Create button once; it will be re-parented on resize as needed
        const btn = document.createElement('button');
        btn.type        = 'button';
        btn.className   = 'grwp-show-more-btn';
        btn.textContent = showMoreText;

        let currentMode = null; // 'gradient' | 'simple'
        let expanded    = false;

        function applyMode() {
            if ( expanded ) return;

            const firstTop    = Math.round( visibleCards[0].getBoundingClientRect().top );
            const isSingleRow = visibleCards.every(
                function (c) { return Math.round( c.getBoundingClientRect().top ) === firstTop; }
            );
            const newMode = isSingleRow ? 'simple' : 'gradient';
            if ( newMode === currentMode ) return; // nothing changed
            currentMode = newMode;

            // Swap wrapper class
            wrapper.classList.remove('grwp-truncated', 'grwp-truncated-simple');

            // Move button to the correct parent
            if ( btn.parentNode ) btn.parentNode.removeChild(btn);

            if ( newMode === 'simple' ) {
                wrapper.classList.add('grwp-truncated-simple');
                wrapper.appendChild(btn);          // below the grid body
            } else {
                wrapper.classList.add('grwp-truncated');
                body.appendChild(btn);             // inside body, over gradient
            }
        }

        // Initial evaluation
        applyMode();

        // Re-evaluate on resize (debounced 150 ms)
        var resizeTimer;
        window.addEventListener('resize', function () {
            clearTimeout(resizeTimer);
            resizeTimer = setTimeout(applyMode, 150);
        });

        // "Load more" click: reveal all cards and tear down
        btn.addEventListener('click', function () {
            expanded = true;
            extraCards.forEach(function (card) { card.classList.remove('grwp-card-hidden'); });
            wrapper.classList.remove('grwp-truncated', 'grwp-truncated-simple');
            btn.remove();
        });
    });
}
