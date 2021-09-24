// Automatizador de tarefas do front-end

/* Plugins
 https://owlgraphic.com/owlcarousel/#customizing
 https://www.jacklmoore.com/colorbox/
 https://www.virtuosoft.eu/code/bootstrap-touchspin/
 https://github.com/kartik-v/bootstrap-star-rating
 https://github.com/scottjehl/picturefill/blob/master/Authors.txt
 https://github.com/msurguy/ladda-bootstrap
 https://github.com/igorescobar/jQuery-Mask-Plugin
 https://www.jqwidgets.com/jquery-widgets-documentation/documentation/jqxmaskedinput/jquery-masked-input-getting-started.htm
 https://scrollme.nckprsn.com/
 https://github.com/sapegin/jquery.mosaicflow
 http://www.idangero.us/sliders/swiper/api.php
 http://bartaz.github.io/sandbox.js/jquery.highlight.html
 https://simontabor.com/labs/toggles/

 Validando cartões de crédito:
 http://jquerycreditcardvalidator.com/

 Complemento (adicionado cartão ELO ao plugin):
 http://pt.stackoverflow.com/questions/3715/express%C3%A3o-regular-para-detectar-a-bandeira-do-cart%C3%A3o-de-cr%C3%A9dito
 Ajuda:
 https://gist.github.com/erikhenrique/5931368
 */

// Tema
var theme = 'default';

var header = [
    'js/libs/jquery-2.1.1.min.js',
    'js/libs/jquery-migrate-1.2.1.min.js',
    'js/libs/picturefill.js',
    'js/header.js'
];

var footer = [
    'js/libs/pnotify.custom.js',
    'js/libs/jquery.magnific-popup.min.js',
    'js/libs/thumbelina.js',
    'js/libs/jquery.mmenu.min.all.js',
    'js/libs/idangerous.swiper.js',
    'js/libs/jquery.cookie.js',
    'js/libs/jquery.mosaicflow.min.js',
    'js/libs/owl.carousel.min.js',
    'js/libs/jquery.bootstrap-touchspin.js',
    'js/libs/star-rating.min.js',
    'js/libs/jquery.placeholder.js',
    'js/libs/jquery.colorbox-min.js',
    'js/libs/jquery.fastLiveFilter.js',
    'js/libs/url.min.js',
    'js/libs/jquery.maskedinput.js',
    'js/libs/jquery.creditCardValidator.js',
    'js/libs/jquery.highlight.js',
    'js/libs/jquery-ui-1.9.2.custom.min.js',
    'js/libs/bootstrap/affix.js',
    'js/libs/bootstrap/alert.js',
    'js/libs/bootstrap/collapse.js',
    'js/libs/bootstrap/dropdown.js',
    'js/libs/bootstrap/modal.js',
    'js/libs/bootstrap/tab.js',
    'js/libs/bootstrap/transition.js',
    'js/libs/sweet-alert.js',
    'js/libs/easyzoom.js',
    'js/libs/photoswipe.js',
    'js/libs/photoswipe-ui-default.js',
    'js/libs/jquery.form.min.js',
    'js/libs/jquery.jOrgChart.js',
    'js/libs/bootstrap-datepicker.js',
    'js/libs/toggles.js',
    'js/itau.js',
    'js/functions.js',
    'js/init.js',
    'js/pages.js'
];

module.exports = function(grunt) {

    grunt.initConfig({
        pkg: grunt.file.readJSON('package.json'),

        watch: {
            options: {
                livereload: 35729
            },
            gruntfile: {
                files: ['Gruntfile.js'],
                tasks: ['sprite','less','concat','autoprefixer']
            },
            js: {
                files: ['js/*.js', 'js/pages/*.js', 'js/libs/*.js'],
                tasks: ['concat']
            },
            less: {
                files: ['less/*.less', 'less/**/*.less'],
                tasks: ['less','autoprefixer']
            },
            img: {
                files: ['img/*.png','img/**/*.png', 'img/*.jpg','img/**/*.jpg'],
                tasks: ['sprite']
            }
        },
        autoprefixer: {
            options: {
                browsers: ['last 2 version']
            },
            multiple_files: {
                expand:     true,
                flatten:    true,
                src:        'css/*.css',
                dest:       'css/'
            }
        },

        sprite: {
            png: {
                src: 'img/icons/*.png',
                dest: 'img/min/sprite.png',
                destCss: 'less/themes/' + theme + '/icons.less',
                imgPath: '../img/min/sprite.png',
                cssFormat: 'css',
                padding: 2,
                algorithm: 'binary-tree',
                engine: 'gmsmith',
                imgOpts: {
                    format:     'png',
                    quality:    100
                }
            }
        },

        less: {
            all: {
                files: {
                    "css/custom.css": "less/custom/custom.less"
                }
            }
        },

        concat: {
            options: {
                separator:''
            },
            header: {
                src:  header,
                dest: 'js/min/header.js'
            },
            footer: {
                src:  footer,
                dest: 'js/min/footer.js'
            }
        },

        imagemin: {
            png: {
                options: {
                    optimizationLevel:  7
                },
                files: [{
                    expand:   true,
                    cwd:      'img/',
                    src:      '**/*.png',
                    dest:     'img/',
                    ext:      '.png'
                }]
            },
            jpg: {
                options: {
                    progressive:  true
                },
                files: [{
                    expand:   true,
                    cwd:      'img/',
                    src:      '**/*.jpg',
                    dest:     'img/',
                    ext:      '.jpg'
                }]
            }
        },

        cssmin: {
            pages: {
                expand: true,
                cwd:    'css/',
                src:    ['*.css'],
                dest:   'css/',
                ext:    '.css'
            }
        },

        uglify: {
            my_target: {
                files: {
                    'js/min/footer.js': 'js/min/footer.js'
                }
            }
        }
    });

    grunt.loadNpmTasks('grunt-contrib-watch');
    grunt.loadNpmTasks('grunt-contrib-less');
    grunt.loadNpmTasks('grunt-contrib-concat');
    grunt.loadNpmTasks('grunt-contrib-imagemin');
    grunt.loadNpmTasks('grunt-spritesmith');
    grunt.loadNpmTasks('grunt-autoprefixer');
    grunt.loadNpmTasks('grunt-contrib-cssmin');
    grunt.loadNpmTasks('grunt-contrib-uglify');

    grunt.registerTask('default', ['sprite','less','concat','autoprefixer','imagemin','cssmin','uglify']);
    grunt.registerTask('dev', ['sprite','less','concat','autoprefixer']);

};