/**
 * "Read more" content overflow mode (Display Settings → Content overflow)
 *
 * Instead of a vertical scrollbar, review text taller than the max height is
 * cut off (overflow:hidden via inline style from PHP) and a "Read more"
 * button is appended to each overflowing card. Clicking it expands only that
 * card.
 *
 * Cards in a row normally stretch to equal height (the slider's swiper-wrapper
 * is a flex row; the grid body is a CSS grid — both stretch items to the
 * tallest one). So when a card expands we must pin every non-expanded card to
 * its collapsed height, otherwise a collapsed card would stretch up to match
 * an expanded neighbour. Heights are (re)computed on every toggle from a
 * baseline snapshot taken while all cards are collapsed, so any combination of
 * expanded/collapsed cards stays consistent. Once nothing is expanded the
 * pinned heights are cleared and normal equal-height behaviour resumes.
 */
export function initReadMore() {
    if (typeof swiperSettings === 'undefined' || swiperSettings.contentOverflow !== 'read_more') {
        return;
    }

    const readMoreText = swiperSettings.readMoreText || 'Read more';
    const readLessText = swiperSettings.readLessText || 'Read less';

    // The stretch context a card lives in: the slider track or the grid body.
    function getContainer(el) {
        return el.closest('.swiper-wrapper') || el.closest('.grwp_body');
    }

    function isExpanded(card) {
        const body = card.querySelector('.gr-inner-body');
        return !!(body && body.classList.contains('grwp-expanded'));
    }

    // A card still hidden behind the grid "Load more" button is display:none, so
    // it has no layout box. Such cards must be excluded from height snapshotting
    // and pinning — otherwise they get measured as 0px and, once revealed, stay
    // stuck at that height (rendering compressed).
    function isHidden(card) {
        return card.offsetParent === null || card.getBoundingClientRect().height === 0;
    }

    function anyExpanded(container) {
        return !!container.querySelector('.gr-inner-body.grwp-expanded');
    }

    // Snapshot every card's current (collapsed, equal-stretched) height so we
    // can pin non-expanded cards to it. Must run while nothing is expanded,
    // otherwise the measured heights would include a stretched neighbour.
    function snapshot(container) {
        container.querySelectorAll('.g-review').forEach(function (card) {
            if (isHidden(card)) return; // skip cards behind "Load more"
            card.dataset.grwpH = Math.round(card.getBoundingClientRect().height);
        });
    }

    // Reconcile inline heights with the current expanded/collapsed state.
    function apply(container) {
        const cards = container.querySelectorAll('.g-review');

        // Several designs put `transition: all .4s` on slider cards. Without
        // suppressing it, pinning a sibling's height animates it — the sibling
        // is briefly stretched by the expanding card's taller row, then eases
        // back, producing a visible flash. Disable transitions, commit the
        // heights with a forced reflow, then restore them for future (hover)
        // changes.
        cards.forEach(function (card) { card.style.transition = 'none'; });

        if (!anyExpanded(container)) {
            // Back to normal — let the flex/grid equal-height stretch resume.
            cards.forEach(function (card) { card.style.height = ''; });
        } else {
            cards.forEach(function (card) {
                if (isHidden(card)) return; // leave cards behind "Load more" untouched
                if (isExpanded(card)) {
                    card.style.height = 'auto';
                } else if (parseInt(card.dataset.grwpH, 10) > 0) {
                    card.style.height = card.dataset.grwpH + 'px';
                } else {
                    // No valid baseline (e.g. revealed after the snapshot was
                    // taken) — let the grid give it its natural height.
                    card.style.height = '';
                }
            });
        }

        void container.offsetHeight; // commit heights under transition:none
        cards.forEach(function (card) { card.style.transition = ''; });
    }

    // Approximate line height of the review text, used as an overflow tolerance.
    function lineHeightOf(body) {
        const target = body.querySelector('p') || body;
        const lh = parseFloat(getComputedStyle(target).lineHeight);
        if (!isNaN(lh) && lh > 0) return lh;
        // 'normal' → derive from font-size (~1.4 fallback ratio)
        const fs = parseFloat(getComputedStyle(target).fontSize);
        return (!isNaN(fs) && fs > 0) ? fs * 1.4 : 20;
    }

    function attach(body) {
        // already processed, or fits (also true for display:none cards, which
        // measure 0x0 — they are re-checked via the grwp:layout-change event)
        if (body.dataset.grwpReadmore) return;

        // Only add the button when a meaningful amount is hidden. The per-style
        // max-height rarely lands on a whole line, so text that is barely over
        // the limit clips the last line mid-height and overflows by only a few
        // pixels — not worth a button that would reveal just that sliver.
        // Require at least ~half a line of hidden content.
        const hidden = body.scrollHeight - body.clientHeight;
        if (hidden <= lineHeightOf(body) * 0.5) return;

        body.dataset.grwpReadmore = '1';

        const btn = document.createElement('a');
        btn.href = '#';
        btn.setAttribute('role', 'button');
        btn.className = 'grwp-read-more-btn';
        btn.textContent = readMoreText;
        body.insertAdjacentElement('afterend', btn);

        btn.addEventListener('click', function (e) {
            e.preventDefault(); // href="#" must not scroll to the top
            const container = getContainer(body);
            const willExpand = !body.classList.contains('grwp-expanded');

            // Capture the baseline while everything is still collapsed, i.e. on
            // the transition from "none expanded" to "one expanded".
            if (container && willExpand && !anyExpanded(container)) {
                snapshot(container);
            }

            body.classList.toggle('grwp-expanded', willExpand);
            btn.textContent = willExpand ? readLessText : readMoreText;

            if (container) apply(container);
        });
    }

    function scan() {
        document
            .querySelectorAll('#g-review[class*="layout_style"] .g-review .gr-inner-body')
            .forEach(attach);
    }

    scan();

    // Re-check once everything (fonts, images) has settled, and whenever the
    // grid "Load more" button reveals cards that were display:none before.
    window.addEventListener('load', scan);
    window.addEventListener('grwp:layout-change', scan);
}
