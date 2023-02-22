import { src, dest, watch, series, parallel } from 'gulp';

import gulpSass from 'gulp-sass';
import dartSass from 'sass';
import cleanCss from 'gulp-clean-css';
import concat from 'gulp-concat';

const sass = gulpSass(dartSass);

// https://css-tricks.com/gulp-for-wordpress-creating-the-tasks/
// https://stackoverflow.com/questions/68417640/gulp-sass-5-does-not-have-a-default-sass-compiler-please-set-one-yourself
export const stylesPublic = () => {
    return src('src/scss/public/**/*.scss', {sourcemaps: true})
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(cleanCss({compatibility: 'ie8'}))
        .pipe(concat('google-reviews-public.css'))
        .pipe(dest('public/css'))
}

export const stylesAdmin = () => {
    return src('src/scss/admin/**/*.scss', {sourcemaps: true})
        .pipe(sass.sync().on('error', sass.logError))
        .pipe(cleanCss({compatibility: 'ie8'}))
        .pipe(concat('google-reviews-admin.css'))
        .pipe(dest('admin/css'))
}

//export const dev = series(clean, parallel(styles, images, copy, scripts), serve, watchForChanges);
export const build = series(stylesPublic, stylesAdmin);
export default build;