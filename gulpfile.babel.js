import { src, dest, watch, series, parallel } from 'gulp';
import yargs from 'yargs';

// Other
import del from 'del';

// styling
import sass from 'gulp-sass';
import cleanCss from 'gulp-clean-css';
import gulpif from 'gulp-if';
import postcss from 'gulp-postcss';
import sourcemaps from 'gulp-sourcemaps';
import autoprefixer from 'autoprefixer';
import lineec from 'gulp-line-ending-corrector';

// image
import imagemin from 'gulp-imagemin';

// scripts
import webpack from 'webpack-stream';
import named from 'vinyl-named';

import wpPot from 'gulp-wp-pot';

// browser
import browserSync from 'browser-sync';

// utilities
import zip from 'gulp-zip';
import info from './package.json';
import replace from 'gulp-replace';

const PRODUCTION = yargs.argv.prod;
const server = browserSync.create();

export const serve = done => {
	server.init({
		proxy: 'http://wpx.test/wp-admin/'
	});
	done();
};
export const reload = done => {
	server.reload();
	done();
};

export const styles = () => {
	return src([ 'src/scss/admin.scss', 'src/scss/public.scss' ])
		.pipe( gulpif( ! PRODUCTION, sourcemaps.init() ) )
		.pipe( sass().on( 'error', sass.logError ) )
		.pipe( gulpif( PRODUCTION, postcss([ autoprefixer ]) ) )
		.pipe( gulpif( PRODUCTION, cleanCss({ compatibility: 'ie8' }) ) )
		.pipe( gulpif( ! PRODUCTION, sourcemaps.write() ) )
		.pipe( lineec() )
		.pipe( dest( 'dist/css' ) )
		.pipe( server.stream() );
};

export const images = () => {
	return src( 'src/images/**/*.{jpg,jpeg,png,svg,gif}' )
		.pipe(
			gulpif( PRODUCTION,
				imagemin([
					imagemin.gifsicle({ interlaced: true }),
					imagemin.mozjpeg({ progressive: true }),
					imagemin.optipng({ optimizationLevel: 3 }), // 0-7 low-high.
					imagemin.svgo({
						plugins: [ { removeViewBox: true }, { cleanupIDs: false } ]
					})
				])
			)
		)
		.pipe( dest( 'dist/images' ) );
};

export const scripts = () => {
	return src([ 'src/js/admin.js', 'src/js/public.js' ])
		.pipe( named() )
		.pipe( webpack({
			module: {
				rules: [
					{
						test: /\.js$/,
						use: {
							loader: 'babel-loader',
							options: {
								presets: [ '@babel/preset-env' ]
							}
						}
					},
					{
						test: /\.(svg|gif|png|eot|woff|ttf)$/,
						use: [
							{
								loader: 'url-loader',
								options: {
									limit: 8192
								}
							}
						]
					}
				]
			},
			mode: PRODUCTION ? 'production' : 'development',
			devtool: ! PRODUCTION ? 'inline-source-map' : false,
			output: {
				filename: '[name].js'
			},
			externals: {
				jquery: 'jQuery'
			}
		}) )
		.pipe( dest( 'dist/js' ) );
};

/**
 * Copy task is responsible for copying all files except
 * above tasks glob files
 *
 * @returns {*}
 */
export const copy = () => {
	return src([ 'src/**/*', '!src/{images,js,scss}', '!src/{images,js,scss}/**/*' ])
		.pipe( dest( 'dist' ) );
};

/**
 * Clean task allow to delete the destination folder completely
 */
export const clean = () => del([ 'dist' ]);

export const pot = () => {
	return src( '**/*.php' )
		.pipe(
			wpPot({
				domain: '_themename',
				package: info.name
			})
		)
		.pipe( dest( `languages/${info.name}.pot` ) );
};

export const compress = () => {
	return src([
		'**/*',
		'!node_modules{,/**}',
		'!bundled{,/**}',
		'!src{,/**}',
		'!.babelrc',
		'!.gitignore',
		'!gulpfile.babel.js',
		'!package.json',
		'!package-lock.json',
		'!.eslintignore',
		'!.editorconfig',
		'!.eslintrc.json',
		'!.jshintrc'
	])
		.pipe(
			gulpif(
				// prevent bug if there are zip files inside the theme
				file => file.relative.split( '.' ).pop() !== 'zip',
				replace( '_themename', info.name )
			)
		)
		.pipe( zip( `${info.name}.zip` ) )
		.pipe( dest( 'bundled' ) );
};

export const watchForChanges = () => {
	watch( 'src/scss/**/*.scss', styles );
	watch( 'src/images/**/*.{jpg,jpeg,png,svg,gif}', series( images, reload ) );
	watch([ 'src/**/*', '!src/{images,js,scss}', '!src/{images,js,scss}/**/*' ], series( copy, reload ) );
	watch( 'src/js/**/*.js', series( scripts, reload ) );
	watch( '**/*.php', reload );
};

export const dev = series( clean, parallel( styles, images, copy, scripts ), serve, watchForChanges );
export const build = series( clean, parallel( styles, images, copy, scripts ), pot, compress );

export default dev;
