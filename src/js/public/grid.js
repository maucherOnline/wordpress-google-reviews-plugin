/**
 * Grid "Load more" button
 *
 * The widget is configured with a number of initially visible review ROWS.
 * Cards-per-row is responsive, so the visible card count is derived from
 * rows x columns and the last visible row is always completely filled.
 * Each click reveals the configured number of additional rows. Everything
 * is recomputed on resize so the rows stay filled.
 */
const DEFAULT_LOAD_MORE_ROWS = 2; // fallback when no per-click row count is configured

export function initShowMore() {
    const showMoreText =
        (typeof swiperSettings !== 'undefined' && swiperSettings.showMoreText)
            ? swiperSettings.showMoreText
            : 'Load more';

    document.querySelectorAll('[data-grwp-show-more-rows]').forEach(function (wrapper) {
        const initialRows = parseInt(wrapper.getAttribute('data-grwp-show-more-rows'), 10);
        if ( isNaN(initialRows) || initialRows < 1 ) return;

        const loadMoreRowsAttr = parseInt(wrapper.getAttribute('data-grwp-load-more-rows'), 10);
        const loadMoreRows = ( !isNaN(loadMoreRowsAttr) && loadMoreRowsAttr > 0 )
            ? loadMoreRowsAttr
            : DEFAULT_LOAD_MORE_ROWS;

        const body = wrapper.querySelector('.grwp_body');
        if ( !body ) return;

        const cards = Array.from( body.querySelectorAll('.g-review') );
        const total = cards.length;
        // Even at one column (the narrowest case) everything fits — nothing to hide
        if ( total <= initialRows ) return;

        // Create the button and place it directly after the grid body,
        // before the "See all reviews" button
        const btn = document.createElement('a');
        btn.href        = '#';
        btn.setAttribute('role', 'button');
        btn.className   = 'grwp-show-more-btn';
        btn.textContent = showMoreText;
        body.insertAdjacentElement('afterend', btn);

        let visibleRows = initialRows;
        let columns     = 1;

        // Number of grid columns. The computed `grid-template-columns` lists one
        // track per column (e.g. "300px 300px 300px"), so counting the tracks
        // gives the column count regardless of how many cards are visible.
        function countColumns() {
            const tpl = getComputedStyle(body).gridTemplateColumns;
            if ( tpl && tpl !== 'none' ) {
                const cols = tpl.trim().split(/\s+/).length;
                if ( cols > 0 ) return cols;
            }

            // Fallback (body isn't a grid): count cards sharing the topmost row
            const firstTop = cards[0].offsetTop;
            let cols = 0;
            for ( let i = 0; i < cards.length; i++ ) {
                if ( cards[i].offsetTop === firstTop ) cols++;
                else break;
            }
            return Math.max(1, cols);
        }

        // Show exactly `visibleRows` full rows (capped at the total card count).
        // We briefly reveal every card to measure the true column count; because
        // this all happens synchronously, the browser never paints that state.
        function apply() {
            cards.forEach(function (card) { card.classList.remove('grwp-card-hidden'); });

            columns = countColumns();
            const visibleCount = Math.min(visibleRows * columns, total);

            cards.forEach(function (card, i) {
                if ( i >= visibleCount ) card.classList.add('grwp-card-hidden');
            });

            const allShown = visibleCount >= total;
            wrapper.classList.toggle('grwp-truncated', !allShown);
            btn.style.display = allShown ? 'none' : '';

            // Newly revealed cards were display:none and couldn't be measured —
            // let the "Read more" module re-check them.
            window.dispatchEvent(new CustomEvent('grwp:layout-change'));
        }

        apply();

        // "Load more": reveal the configured number of additional rows
        btn.addEventListener('click', function (e) {
            e.preventDefault(); // href="#" must not scroll to the top
            visibleRows += loadMoreRows;
            apply();
        });

        // Keep rows filled when the usable width changes (debounced).
        //
        // React to WIDTH changes only: apply() toggles card visibility, which
        // changes the body's HEIGHT — responding to that would loop.
        let resizeTimer;
        let lastWidth = -1;
        function onResize() {
            const width = body.getBoundingClientRect().width;
            if ( width === lastWidth ) return;
            lastWidth = width;
            apply();
        }

        // A plain window 'resize' listener misses the common Elementor case:
        // the grid is rendered inside an inactive tab panel (display:none, so
        // zero-size) and only gets its real width when that tab is later shown.
        // No window resize fires then, so the column count computed at 0px would
        // stick and leave the last row partly empty. A ResizeObserver on the
        // body catches that 0 -> visible transition (and ordinary resizes too).
        if ( typeof ResizeObserver !== 'undefined' ) {
            const ro = new ResizeObserver(function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(onResize, 150);
            });
            ro.observe(body);
        } else {
            window.addEventListener('resize', function () {
                clearTimeout(resizeTimer);
                resizeTimer = setTimeout(apply, 150);
            });
        }
    });
}
