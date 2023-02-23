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

// https://css-tricks.com/gulp-for-wordpress-creating-the-tasks/
// https://stackoverflow.com/questions/68417640/gulp-sass-5-does-not-have-a-default-sass-compiler-please-set-one-yourself
export const stylesPublic = () => {

    if (PRODUCTION) {
        return src('src/scss/public/**/*.scss')
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(postcss(autoPrefixerPlugins))
            .pipe(cleanCss({compatibility:'ie8'}))
            .pipe(concat('google-reviews-public.css'))
            .pipe(dest('public/css'))
    } else {
        return src('src/scss/public/**/*.scss', { sourcemaps: true })
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility:'ie8'}))
            .pipe(concat('google-reviews-public.css'))
            .pipe(dest('public/css', { sourcemaps: '.' }))
    }

}

export const stylesAdmin = () => {

    if (PRODUCTION) {
        return src('src/scss/admin/**/*.scss')
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility: 'ie8'}))
            .pipe(concat('google-reviews-admin.css'))
            .pipe(dest('admin/css'))
    } else {
        return src('src/scss/admin/**/*.scss', { sourcemaps: true })
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility: 'ie8'}))
            .pipe(concat('google-reviews-admin.css'))
            .pipe(dest('admin/css', { sourcemaps: '.' }))
    }

}

export const build = series(stylesPublic, stylesAdmin);
export default build;
