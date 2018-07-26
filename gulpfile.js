const gulp = require('gulp');
const less = require('gulp-less');
const concat = require('gulp-concat');
const uglify = require('gulp-uglifyjs');
const livereload = require('gulp-livereload');
const cssnano = require('gulp-cssnano');

gulp.task('less', function() {
    return gulp.src('./assets/less/*.less')
	.pipe(less())
	.pipe(cssnano())
	.pipe(gulp.dest('./public/web/css'))
	.pipe(livereload());
});

gulp.task('scripts', function() {
	return gulp.src([
		'assets/libs/jquery/dist/jquery.min.js'
	])
	.pipe(concat('libs.min.js'))
	.pipe(uglify())
	.pipe(gulp.dest('assets/js'));
});

gulp.task('js', function() {
    return gulp.src('./assets/js/*.js')
    .pipe(gulp.dest('./public/web/js'))
    .pipe(livereload());
});

gulp.task('watch', function() {
	livereload.listen();
	gulp.watch('./assets/less/*.less',['less']);
	gulp.watch('./assets/js/**/*.js',['js']);
});