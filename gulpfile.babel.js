'use strict';

import gulp from 'gulp';
import browserSync from 'browser-sync';
import gulpLoadPlugins from 'gulp-load-plugins';

const $ = gulpLoadPlugins();
const reload = browserSync.reload;

gulp.task('connect-sync', () => {
  $.connect.server({}, () => {
    browserSync({
      proxy: '127.0.0.1:8000'
    });
  });
});

gulp.task('serve', () => {
  browserSync({
    server: './'
  });

  gulp.watch('./**/*.php', [reload]);
});

gulp.task('default', ['serve']);
