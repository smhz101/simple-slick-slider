import { src, dest } from 'gulp';
import yargs from 'yargs';
import sass from 'gulp-sass';
import cleanCss from 'gulp-clean-css';
import gulpif from 'gulp-if';

const PRODUCTION = yargs.argv.prod;

export const styles = () => {
	return src( 'src/scss/bundle.scss' )
		.pipe( sass().on( 'error', sass.logError ) )
		.pipe( gulpif( PRODUCTION, cleanCss({compatibility: 'ie8' }) ) )
		.pipe( dest( 'dist/css' ) );
};
