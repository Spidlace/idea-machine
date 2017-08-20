// Requis
var gulp = require('gulp'),
	runSequence = require('run-sequence');

// Include plugins
var plugins = require('gulp-load-plugins')(); // tous les plugins de package.json

// Variables de chemins
var source = 'css/sass/'; // dossier de travail
var destination = 'css/'; // dossier à livrer

// Tâches
gulp.task('css', function () {
  return gulp.src(source+'*.scss')
    .pipe(plugins.sass().on('error', plugins.sass.logError))
    .pipe(plugins.cssbeautify({indent: '  '}))
    .pipe(plugins.autoprefixer())
    .pipe(gulp.dest(destination));
});

gulp.task('minify', function () {
	return gulp.src(destination + 'main.css')
	  .pipe(plugins.csso())
	  .pipe(plugins.autoprefixer())
	  .pipe(plugins.rename({
	    suffix: '.min'
	  }))
	  .pipe(gulp.dest(destination));
});

// Tâche "build"
gulp.task('prod', function(done){
	runSequence('css', 'minify', function(){
		console.log('Le CSS a bien été transformé et minifié !');
		done();
	})
});

// Tâche "Watch"
gulp.task('watch', function () {
  	gulp.watch(source+'*.scss', function(){
  		gulp.start('prod');
  	});
});