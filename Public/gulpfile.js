var gulp = require('gulp');
var $    = require('gulp-load-plugins')();
var vueify = require('gulp-vueify');
gutil = require('gulp-util')

var sassPaths = [
  'node_modules/uikit/src/scss',
];

gulp.task('sass', function() {
  return gulp.src('src/scss/app.scss')
    .pipe($.sass({
      includePaths: sassPaths,
      outputStyle: 'compressed' // if css compressed **file size**
    })
      .on('error', $.sass.logError))
    .pipe($.autoprefixer({
      browsers: ['last 2 versions', 'ie >= 9']
    }))
    .pipe($.concat('style.css'))
    .pipe(gulp.dest('dist'));
});


gulp.task('apps', function() {
	return gulp.src('src/scss/apps/*.scss')
    .pipe($.sass({
    	outputStyle: 'compressed' // if css compressed **file size**
    }))
    .pipe(gulp.dest('./dist'));
});


var vendorFiles = [
		'node_modules/axios/dist/axios.min.js',
		'node_modules/vue/dist/vue.min.js',
		'node_modules/codemirror/lib/codemirror.js',
		'node_modules/vue-codemirror/dist/vue-codemirror.js',
		'node_modules/vue-shortkey/dist/index.js',
		'src/js/common.js'
];
	
var musicFiles = [
	'src/js/musiclib.js',
	'src/js/music.js'
];

var documentFiles = [
	'src/js/documents.js',
];


var uiKitFiles = [
	'node_modules/uikit/dist/js/uikit-core.js',
	'node_modules/uikit/dist/js/uikit-icons.min.js'
];


gulp.task('uiKit', function() {
    return gulp.src(uiKitFiles)
        .pipe($.concat('uikit.js'))
        .pipe($.uglify())
        .pipe(gulp.dest('dist'));
});


gulp.task('vendor', function() {
    return gulp.src(vendorFiles)
        .pipe($.concat('vendor.js'))
        .pipe($.uglify())
        .pipe(gulp.dest('dist'));
});


gulp.task('media', function() {
    return gulp.src(['src/js/media.js'])
        .pipe($.concat('media.js'))
        .pipe($.uglify())
        .pipe(gulp.dest('dist'));
});

gulp.task('music', function() {
    return gulp.src(musicFiles)
        .pipe($.concat('music.js'))
        .pipe($.uglify())
        .on('error', function (err) { gutil.log(gutil.colors.red('[Error]'), err.toString()); })
        .pipe(gulp.dest('dist'));
});


gulp.task('documents', function() {
    return gulp.src(documentFiles)
        .pipe($.concat('documents.js'))
        .pipe($.uglify())
        .on('error', function (err) { gutil.log(gutil.colors.red('[Error]'), err.toString()); })
        .pipe(gulp.dest('dist'));
});

gulp.task('default', ['sass', 'apps'], function() {
	gulp.watch(['src/scss/**/*.scss'], ['sass']);
	gulp.watch(['src/js/**/*.js'], ['js']);
});
