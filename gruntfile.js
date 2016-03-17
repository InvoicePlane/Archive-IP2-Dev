'use strict';

module.exports = function (grunt) {

  // load grunt tasks
  require('time-grunt')(grunt);
  require('load-grunt-tasks')(grunt);

  grunt.initConfig({

    config: {
      src: 'themes/InvoicePlane',
      dest: 'themes/InvoicePlane'
    },
    sass: {
      options: {
        outputStyle: 'compact',
        sourceMap: true
      },
      dist: {
        files: [
          {
            expand: true,
            cwd: 'themes/InvoicePlane/',
            src: [
              '**/*.scss',
              '!**/*scsslint*.scss'
            ],
            dest: 'themes/InvoicePlane/css/',
            ext: '.min.css',
            extDot: 'first'
          }
        ]
      }
    },
    autoprefixer: {
      options: {
        browsers: ['last 2 versions']
      },
      main: {
        expand: true,
        flatten: true,
        src: 'themes/InvoicePlane/css/*.min.css',
        dest: 'themes/InvoicePlane/css/'
      }
    },
    scsslint: {
      allFiles: [
        'themes/core/sass/*.scss',
        'themes/core/sass/modules/*.scss',
        'themes/InvoicePlane/*.scss'
      ],
      options: {
        config: '.scss-lint.yml',
        exclude: [
          'themes/InvoicePlane/_variables.scss'
        ],
        colorizeOutput: true
      }
    },
    uglify: {
      dist: {
        files: {
          'themes/core/js/dependencies.min.js': [
            'themes/vendor/jquery/dist/jquery.js',
            'themes/vendor/tether/dist/js/tether.js',
            'themes/vendor/bootstrap/dist/js/bootstrap.js',
            'themes/vendor/jqueryui/ui/core.js',
            'themes/vendor/jqueryui/ui/widget.js',
            'themes/vendor/jqueryui/ui/mouse.js',
            'themes/vendor/jqueryui/ui/position.js',
            'themes/vendor/jqueryui/ui/draggable.js',
            'themes/vendor/jqueryui/ui/droppable.js',
            'themes/vendor/jqueryui/ui/resizable.js',
            'themes/vendor/jqueryui/ui/selectable.js',
            'themes/vendor/jqueryui/ui/sortable.js',
            'themes/vendor/select2/dist/js/select2.js',
            'themes/vendor/dropzone/dist/dropzone.js'
          ],
          'themes/core/js/app.min.js': [
            'themes/core/js/app.js'
          ]
        },
        options: {
          sourceMap: 'themes/core/js/app.min.js.map',
          sourceMappingURL: '/themes/core/js/app.min.js.map'
        }
      }
    },
    eslint: {
      options: {
        configFile: '.eslintrc'
      },
      target: ['themes/core/js/app.js']
    },
    watch: {
      sass: {
        files: [
          'themes/core/sass/*.scss',
          'themes/core/sass/modules/*.scss',
          'themes/InvoicePlane/*.scss',
          '!**/*scsslint*.scss'
        ],
        tasks: ['scsslint', 'sass']
      },
      js: {
        files: [
          'themes/core/js/app.js'
        ],
        tasks: ['eslint', 'uglify']
      }
    },
    clean: {
      dist: [
        'themes/core/fonts/font-awesome/*',
        'themes/core/js/locales/*',
        'themes/InvoicePlane/css/*.min.css',
        'themes/InvoicePlane/css/*.min.css.map',
        'themes/core/js/*.min.js',
        'themes/core/js/*.min.js.map'
      ]
    }
  });

  // Register tasks
  grunt.registerTask('default', ['dev']);

  grunt.registerTask('dev', [
    'clean',
    'scsslint',
    'sass',
    'autoprefixer:main',
    'eslint',
    'uglify',
    'watch'
  ]);

};
