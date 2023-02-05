const gulp = require('gulp');
const sass = require('gulp-sass')(require('sass'));
const sourcemaps = require('gulp-sourcemaps');

gulp.task('sass', function () {
  return gulp.src('sass/*.scss')
    .pipe(sourcemaps.init())
    .pipe(sass({outputStyle: 'compressed'}))
    .pipe(sourcemaps.write('.'))
    .pipe(gulp.dest('css'))
});

gulp.task('default', gulp.series('sass', function (done) {
  gulp.watch('sass/*.scss', gulp.series('sass'));
  done();
}));
