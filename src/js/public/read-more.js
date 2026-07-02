/**
 * "Read more" content overflow mode (Display Settings → Content overflow)
 *
 * Instead of a vertical scrollbar, review text taller than the max height is
 * cut off (overflow:hidden via inline style from PHP) and a "Read more"
 * button is appended to each overflowing card. Clicking it expands only that
 * card. Inside the slider all cards normally stretch to the tallest slide
 * (.g-review { height: 100% }), so before expanding, the current heights of
 * the sibling cards are frozen as inline styles — the expanded card grows
 * while every other slide keeps its initial height. The frozen heights are
 * released once no card in that slider is expanded anymore.
 */
export function initReadMore() {
    if (typeof swiperSettings === 'undefined' || swiperSettings.contentOverflow !== 'read_more') {
        return;
    }

    const readMoreText = swiperSettings.readMoreText || 'Read more';
    const readLessText = swiperSettings.readLessText || 'Read less';

    function attach(body) {
        // already processed, or fits (also true for display:none cards, which
        // measure 0x0 — they are re-checked via the grwp:layout-change event)
        if (body.dataset.grwpReadmore) return;
        if (body.scrollHeight <= body.clientHeight + 1) return;

        body.dataset.grwpReadmore = '1';

        const btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'grwp-read-more-btn';
        btn.textContent = readMoreText;
        body.insertAdjacentElement('afterend', btn);

        btn.addEventListener('click', function () {
            const willExpand = !body.classList.contains('grwp-expanded');
            const card = body.closest('.g-review');
            const wrapper = body.closest('.swiper-wrapper');

            if (wrapper && willExpand) {
                // Freeze the other cards at their current height BEFORE the
                // expansion reflows the slider, so only this card grows.
                wrapper.querySelectorAll('.g-review').forEach(function (other) {
                    if (other !== card && !other.style.height) {
                        other.style.height = other.offsetHeight + 'px';
                    }
                });
                card.style.height = 'auto';
            }

            body.classList.toggle('grwp-expanded', willExpand);
            btn.textContent = willExpand ? readLessText : readMoreText;

            if (wrapper && !willExpand) {
                card.style.height = '';
                // release the frozen heights once nothing is expanded anymore,
                // so responsive/equal-height behavior returns to normal
                if (!wrapper.querySelector('.grwp-expanded')) {
                    wrapper.querySelectorAll('.g-review').forEach(function (other) {
                        other.style.height = '';
                    });
                }
            }
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
