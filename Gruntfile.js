module.exports = function (grunt) {
    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),
        concat: {
            css: {
                src: [
                    'assets/css/bootstrap/css/bootstrap.min.css',
                    'assets/css/modern-business.css',
                    'assets/css/jqueryui/smoothness/jquery-ui-1.10.4.custom.min.css',
                    'assets/css/editable/css/bootstrap-editable.css',
                    'assets/css/summernote.css',
                    'assets/css/summernote-bs3.css',
                    'assets/css/custom.css'
                    
                ],
                dest: 'assets/builds/combined.css'
            },
            js: {
                src: [
                    'assets/js/jquery.min.js',
                    'assets/js/jquery-ui-1.10.4.custom.min.js',
                    'assets/js/bootstrap.min.js',
                    'assets/js/modern-business.js',
                    'assets/js/bootstrapValidator.min.js',
                    'assets/js/bootstrap-growl.js',
                    'assets/js/bootbox.min.js',
                    'assets/js/bootstrap-editable.min.js',
                    'assets/js/summernote.min.js'
                ],
                dest: 'assets/builds/combined.js'
            }
        },
        cssmin: {
            css: {
                src: 'assets/builds/combined.css',
                dest: 'assets/builds/combined.min.css'
            }
        },
        uglify: {
            js: {
                files: {
                    'assets/builds/combined.min.js': ['assets/builds/combined.js']
                }
            }
        },
    });
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-uglify');
    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.registerTask('default', ['concat:css', 'cssmin:css', 'concat:js', 'uglify:js']);
};