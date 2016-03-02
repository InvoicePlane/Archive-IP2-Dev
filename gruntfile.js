"use strict";

module.exports = function (grunt) {

    var default_theme_path = "themes/InvoicePlane/";

    // load grunt tasks
    require("load-grunt-tasks")(grunt);

    grunt.initConfig({

        config: {
            src: "themes/InvoicePlane",
            dest: "themes/InvoicePlane"
        },
        jshint: {
            options: {
                jshintrc: ".jshintrc"
            },
            all: [
                "themes/core/js/app.js"
            ]
        },
        sass: {
            dist: {
                options: {
                    style: "compressed",
                    compass: true,
                    sourcemap: false
                },
                files: {
                    "themes/InvoicePlane/css/app.min.css": "themes/InvoicePlane/app.scss",
                    "themes/InvoicePlane/css/basic.min.css": "themes/InvoicePlane/basic.scss",
                    "themes/InvoicePlane/css/monospace.min.css": "themes/InvoicePlane/monospace.scss",
                    "themes/InvoicePlane/css/reports.min.css": "themes/InvoicePlane/reports.scss",
                    "themes/InvoicePlane/css/template.min.css": "themes/InvoicePlane/templates.scss"
                }
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
        uglify: {
            dist: {
                files: {
                    "themes/core/js/dependencies.min.js": [
                        "themes/vendor/jquery/dist/jquery.js",
                        "themes/vendor/tether/dist/js/tether.js",
                        "themes/vendor/bootstrap/dist/js/bootstrap.js",
                        "themes/vendor/jqueryui/ui/core.js",
                        "themes/vendor/jqueryui/ui/widget.js",
                        "themes/vendor/jqueryui/ui/mouse.js",
                        "themes/vendor/jqueryui/ui/position.js",
                        "themes/vendor/jqueryui/ui/draggable.js",
                        "themes/vendor/jqueryui/ui/droppable.js",
                        "themes/vendor/jqueryui/ui/resizable.js",
                        "themes/vendor/jqueryui/ui/selectable.js",
                        "themes/vendor/jqueryui/ui/sortable.js",
                        "themes/vendor/select2/dist/js/select2.js",
                        "themes/vendor/dropzone/dist/dropzone.js",
                    ],
                    "themes/core/js/app.min.js": [
                        "themes/core/js/app.js"
                    ]
                },
                options: {
                    sourceMap: "themes/core/js/app.min.js.map",
                    sourceMappingURL: "/themes/core/js/app.min.js.map"
                }
            }
        },
        watch: {
            sass: {
                files: [
                    "themes/core/sass/*.scss",
                    "themes/core/sass/modules/*.scss",
                    "themes/InvoicePlane/*.scss"
                ],
                tasks: ["sass"]
            },
            js: {
                files: [
                    "<%= jshint.all %>"
                ],
                tasks: ["jshint", "uglify"]
            }
        },
        clean: {
            dist: [
                "themes/core/fonts/font-awesome/*",
                "themes/core/js/locales/*",
                "themes/InvoicePlane/css/*.min.css",
                "themes/InvoicePlane/css/*.min.css.map",
                "themes/core/js/*.min.js",
                "themes/core/js/*.min.js.map"
            ]
        }
    });

    // Register tasks
    grunt.registerTask('default', ['dev']);

    grunt.registerTask("dev", [
        "clean",
        "sass",
        "autoprefixer:main",
        "jshint",
        "uglify",
        "watch"
    ]);

};
