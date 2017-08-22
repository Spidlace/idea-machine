// Requis
var gulp = require('gulp'),
	runSequence = require('run-sequence'),
	uglify = require('gulp-uglify');

// Include plugins
var plugins = require('gulp-load-plugins')(); // tous les plugins de package.json

// Variables de chemins
var source_css = 'css/'; // dossier de travail CSS
var destination_css = 'css/min/'; // dossier à livrer CSS
var source_js = 'js/'; // dossier de travail JS
var destination_js = 'js/min/'; // dossier à livrer JS

// Tâches
gulp.task('js_minify', function () {
  return gulp.src(source_js+'*.js')
    .pipe(plugins.uglify())
  	.pipe(plugins.rename(function (path) {
		path.extname = '.min.js';
	}))
    .pipe(gulp.dest(destination_js));
});

gulp.task('minify', function () {
	return gulp.src(source_css+'*.scss')
	  .pipe(plugins.sass().on('error', plugins.sass.logError))
	  .pipe(plugins.csso())
	  .pipe(plugins.autoprefixer())
	  .pipe(plugins.rename({
	    suffix: '.min'
	  }))
	  .pipe(gulp.dest(destination_css));
});

// Tâche "build"
gulp.task('prod', function(done){
	runSequence('minify', 'js_minify', function(){
		console.log('Le CSS/JS a bien été transformé et minifié !');
		done();
	})
});

// Tâche "Watch"
gulp.task('watch', function () {
  	gulp.watch([source_css+'*.scss', source_js+'*.js'], function(){
  		gulp.start('prod');
  	});
});