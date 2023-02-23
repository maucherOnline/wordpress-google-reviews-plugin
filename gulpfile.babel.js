import { src, dest, watch, series, parallel } from 'gulp';

import gulpSass from 'gulp-sass';
import dartSass from 'sass';
import cleanCss from 'gulp-clean-css';
import concat from 'gulp-concat';
import yargs from 'yargs';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';

const PRODUCTION = yargs.argv.prod;
const sass = gulpSass(dartSass);

const autoPrefixerPlugins = [
    autoprefixer({browsers: ['last 1 version']}),
];

const publicSCSSglob = 'src/scss/public/**/*.scss';
const adminSCSSglob = 'src/scss/admin/**/*.scss';

// https://css-tricks.com/gulp-for-wordpress-creating-the-tasks/
// https://stackoverflow.com/questions/68417640/gulp-sass-5-does-not-have-a-default-sass-compiler-please-set-one-yourself
export const stylesPublic = () => {

    if (PRODUCTION) {
        return src(publicSCSSglob)
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(postcss(autoPrefixerPlugins))
            .pipe(cleanCss({compatibility:'ie8'}))
            .pipe(concat('google-reviews-public.css'))
            .pipe(dest('public/css'))
    } else {
        return src(publicSCSSglob, { sourcemaps: true })
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility:'ie8'}))
            .pipe(concat('google-reviews-public.css'))
            .pipe(dest('public/css', { sourcemaps: '.' }))
    }

}

export const stylesAdmin = () => {

    if (PRODUCTION) {
        return src(adminSCSSglob)
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility: 'ie8'}))
            .pipe(concat('google-reviews-admin.css'))
            .pipe(dest('admin/css'))
    } else {
        return src(adminSCSSglob, { sourcemaps: true })
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility: 'ie8'}))
            .pipe(concat('google-reviews-admin.css'))
            .pipe(dest('admin/css', { sourcemaps: '.' }))
    }

}

/**
 * Filewatchers
 */
export const watchForChanges = () => {
    watch(publicSCSSglob, stylesPublic);
    watch(adminSCSSglob, stylesAdmin);
}

export const build = series(stylesPublic, stylesAdmin);
export default build;
