// Устанавливаем es6-promise для правильной работы gulp-tasks, e.g. gulp-autoprefixer
require('es6-promise').polyfill();
var gulp          = require('gulp');
var sass          = require('gulp-sass');
var autoprefixer  = require('gulp-autoprefixer');
// Переменные для обработки ошибок
var plumber = require('gulp-plumber');
var gutil = require('gulp-util');
// Объявление функции логирования ошибки и передачи звукового сигнала
var onError = function (err) {
    console.log('An error occurred:', gutil.colors.magenta(err.message));
    gutil.beep();
    this.emit('end');
};
// Переменные для обработки файлов JavaScript
var concat = require('gulp-concat');
var jshint = require('gulp-jshint');
var uglify = require('gulp-uglify');
var rename = require('gulp-rename');
var rigger = require('gulp-rigger');
// Переменные для обработки изображений
var imagemin = require('gulp-imagemin');
var tiny = require('gulp-tinypng-nokey');
// Переменные для BrowserSync и LiveReload
var browserSync = require('browser-sync').create();
var reload = browserSync.reload;


// *** SASS ***
// Создаем SASS-task: компиляция, автопрефикс, вывод файла style.css
gulp.task('sass', function() {
    return gulp.src('./sass/**/*.scss')
        .pipe(plumber({ errorHandler: onError }))
        .pipe(sass())
        .pipe(autoprefixer())
        .pipe(gulp.dest('./'))
});

// *** JAVASCRIPT ***
// Создаем JS-task для обработки файлов JavaScript
gulp.task('jsjs', function() {
    return gulp.src(['./js/src/*.js'])
    //gulp.src(['./js/src/main.js'])
        .pipe(plumber())
        //.pipe(jshint())
        //.pipe(jshint.reporter('default'))
        .pipe(rigger())
        .pipe(concat('app.js'))
        .pipe(gulp.dest('./js'))
        .pipe(rename({suffix: '.min'}))
        .pipe(uglify())
        .pipe(gulp.dest('./js'))
});
// Создаем еще JS-task для обработки файлов JavaScript
gulp.task('scripts', function() {
    return gulp.src(['./js/src/*.js'])
        .pipe(plumber())
        .pipe(rigger())
        .pipe(concat('libs.js')) // Собираем их в кучу в новом файле libs.min.js
        //.pipe(uglify()) // Сжимаем JS файл
        .pipe(gulp.dest('./js')); // Выгружаем в папку js
});


// *** IMAGES ***
// Создаем images-task для минимизации изображений
gulp.task('images', function() {
    return gulp.src('./images/src/*')
        .pipe(plumber({errorHandler: onError}))
        .pipe(imagemin({optimizationLevel: 7, progressive: false})) // очень плохо сжимает изображения
        .pipe(gulp.dest('./images'));
});
// Создаем tiny-task для минимизации изображений .png
gulp.task('tinypng', function(cb) {
    gulp.src('./images/src/*')
        .pipe(tiny())
        .pipe(gulp.dest('./images'));
});

// *** WATCH ***
// Обеспечиваем watching за scss-файлами
gulp.task('watch', function() {
    browserSync.init({
        // files: ['./**/*.php'],
        // proxy: 'http://lokalrev.loc/',
        proxy: 'test4.local/',
    });
    gulp.watch('./sass/**/*.scss', ['sass', reload]);
    gulp.watch('./js/*.js', ['js', reload]);
    gulp.watch('images/src/*', ['images', reload]);
    gulp.watch('./**/*.php', ['', reload]);
});


gulp.task('default', ['sass', /*'js', /*'tinypng',*/ 'watch']);