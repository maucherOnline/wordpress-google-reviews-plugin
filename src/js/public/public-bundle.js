import './slider';
import './badge';
import { initShowMore } from './grid';

// Run as soon as the DOM is ready. Using only addEventListener('DOMContentLoaded')
// silently does nothing if the script happens to evaluate after that event already
// fired (e.g. when an optimization plugin defers/async-loads the bundle).
if ( document.readyState === 'loading' ) {
    document.addEventListener('DOMContentLoaded', initShowMore);
} else {
    initShowMore();
}
