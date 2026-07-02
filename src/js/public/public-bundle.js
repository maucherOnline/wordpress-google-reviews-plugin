import './slider';
import './badge';
import { initShowMore } from './grid';
import { initReadMore } from './read-more';

// Run as soon as the DOM is ready. Using only addEventListener('DOMContentLoaded')
// silently does nothing if the script happens to evaluate after that event already
// fired (e.g. when an optimization plugin defers/async-loads the bundle).
function init() {
    initShowMore();
    initReadMore();
}

if ( document.readyState === 'loading' ) {
    document.addEventListener('DOMContentLoaded', init);
} else {
    init();
}
