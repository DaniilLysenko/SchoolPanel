const gulp = require('gulp');
const less = require('gulp-less');
const concat = require('gulp-concat');
const uglify = require('gulp-uglifyjs');
const livereload = require('gulp-livereload');


gulp.task('less', function() {
    return gulp.src('./assets/less/*.less')
    .pipe(less())
    .pipe(gulp.dest('./public/web/css'))
    .pipe(livereload());
});

gulp.task('js', function() {
    return gulp.src('./assets/js/**/*.js')
    .pipe(gulp.dest('./public/web/js'))
    .pipe(livereload());
});


gulp.task('watch', function() {
	livereload.listen();
	gulp.watch('./assets/less/*.less',['less']);
	gulp.watch('./assets/js/**/*.js',['js']);
})