import { src, dest, watch, series, parallel } from 'gulp';

import gulpSass from 'gulp-sass';
import dartSass from 'sass';
import cleanCss from 'gulp-clean-css';
import concat from 'gulp-concat';
import yargs from 'yargs';
import postcss from 'gulp-postcss';
import autoprefixer from 'autoprefixer';
import clean from 'gulp-clean';
import imagemin from 'gulp-imagemin';
import zip from 'gulp-zip';

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
            .pipe(dest('dist/css'))
    } else {
        return src(publicSCSSglob, { sourcemaps: true })
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility:'ie8'}))
            .pipe(concat('google-reviews-public.css'))
            .pipe(dest('dist/css', { sourcemaps: '.' }))
    }

}

export const stylesAdmin = () => {

    if (PRODUCTION) {
        return src(adminSCSSglob)
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility: 'ie8'}))
            .pipe(concat('google-reviews-admin.css'))
            .pipe(dest('dist/css'))
    } else {
        return src(adminSCSSglob, { sourcemaps: true })
            .pipe(sass.sync().on('error', sass.logError))
            .pipe(cleanCss({compatibility: 'ie8'}))
            .pipe(concat('google-reviews-admin.css'))
            .pipe(dest('dist/css', { sourcemaps: '.' }))
    }

}

/**
 * Minify images and copy them to dist
 * @returns {*}
 */
export const images = () => {
    if (PRODUCTION) {
        return src('src/images/**/*.{jpg,jpeg,png,svg,gif}')
            .pipe(imagemin())
            .pipe(dest('dist/images'));
    } else {
        return src('src/images/**/*.{jpg,jpeg,png,svg,gif}')
            .pipe(dest('dist/images'));
    }
}

/**
 * Filewatchers
 */
export const watchForChanges = () => {
    watch(publicSCSSglob, stylesPublic);
    watch(adminSCSSglob, stylesAdmin);
    watch('src/images/**/*.{jpg,jpeg,png,svg,gif}', images);

}

/**
 * Clean dist dir
 * @returns {*}
 */
export const cleanDist = () => {
    return src('dist', {
        read: false,
        allowEmpty: true
    }).pipe(clean());
}

export const cleanDeployable = () => {
  return src('deployable', {
      read: false,
      allowEmpty: true
  }).pipe(clean());
};

export const makeDeployable = () => {
    return src([
        'admin/**/*',
        'dist/**/*',
        'freemius/**/*',
        'languages/**/*',
        'public/**/*',
        'google-reviews.php',
        'index.php',
        'LICENSE.txt',
        'README.txt'
    ], {base: '.'})
        .pipe(dest('deployable'))
        .pipe(zip('google-reviews-embedder-master.zip'))
        .pipe(dest('.'));
}

export const build = series(cleanDist, parallel(stylesPublic, stylesAdmin, images));

export const deployable = series(cleanDist, parallel(stylesPublic, stylesAdmin, images), makeDeployable, cleanDeployable);
export const dev = series(cleanDist, parallel(stylesPublic, stylesAdmin, images), watchForChanges);

export default build;
