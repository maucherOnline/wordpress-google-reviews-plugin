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
import webpack from 'webpack-stream';
import wpPot from "gulp-wp-pot";
import minify from "gulp-minify";
import pkg from './package.json';
import gulpif from 'gulp-if';

const PRODUCTION = yargs.argv.prod;
const sass = gulpSass(dartSass);

const autoPrefixerPlugins = [
    autoprefixer(),
];

const publicSCSSglob = 'src/scss/public/**/*.scss';
const adminSCSSglob = 'src/scss/admin/**/*.scss';

// https://css-tricks.com/gulp-for-wordpress-creating-the-tasks/

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
 * Bundle public JS files
 * @returns {*}
 */
export const scriptsPublic = () => {
    return src('src/js/public/public-bundle.js')
        .pipe(webpack({
            module: {
                rules: [
                    {
                        test: /\.js$/,
                        use: {
                            loader: 'babel-loader',
                            options: {
                                presets: []
                            }
                        }
                    }
                ]
            },
            mode: PRODUCTION ? 'production' : 'development',
            devtool: !PRODUCTION ? 'inline-source-map' : false,
            externals: {
                jquery: 'jQuery'
            },
            output: {
                filename: 'public-bundle.js'
            },
        }))
        .pipe(dest('dist/js'))
}

/**
 * Bundle admin files
 * @returns {*}
 */
export const scriptsAdmin = () => {
    return src('src/js/admin/admin-bundle.js')
        .pipe(webpack({
            module: {
                rules: [
                    {
                        test: /\.js$/,
                        use: {
                            loader: 'babel-loader',
                            options: {
                                presets: []
                            }
                        }
                    }
                ]
            },
            mode: PRODUCTION ? 'production' : 'development',
            devtool: !PRODUCTION ? 'inline-source-map' : false,
            externals: {
                jquery: 'jQuery'
            },
            output: {
                filename: 'admin-bundle.js'
            },
        }))
        .pipe(dest('dist/js'));
}


/**
 * Filewatchers
 */
export const watchForChanges = () => {
    watch(publicSCSSglob, stylesPublic);
    watch(adminSCSSglob, stylesAdmin);
    watch('src/images/**/*.{jpg,jpeg,png,svg,gif}', images);
    watch('src/js/admin/**/*.js', scriptsAdmin);
    watch('src/js/public/**/*.js', scriptsPublic);
    watch(['src/**/*','!src/{images,js,scss}','!src/{images,js,scss}/**/*'], copyVendor);
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

export const compress = () => {
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
        .pipe(zip('embedder-for-google-reviews.zip'))
        .pipe(dest('.'));
}

/**
 * Copy vendor files from src to dist
 * @returns {*}
 */
export const copyVendor = () => {
    return src(['src/**/*','!src/{images,js,scss}','!src/{images,js,scss}/**/*'])
        .pipe(dest('dist'));
}

/**
 * Create POT file
 * @returns {*}
 */
export const pot = () => {
    return src("**/*.php")
        .pipe(
            wpPot({
                domain: pkg.name,
            })
        )
        .pipe(dest(`languages/${pkg.name}.pot`));
};

export const build = series(cleanDist, parallel(stylesPublic, stylesAdmin, scriptsAdmin, scriptsPublic, images, copyVendor), pot);
export const deployable = series(build, compress);
export const dev = series(cleanDist, parallel(stylesPublic, stylesAdmin, scriptsAdmin, scriptsPublic, images, copyVendor), watchForChanges);

export default build;
