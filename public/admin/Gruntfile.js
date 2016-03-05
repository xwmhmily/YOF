/*!
 * Ace's Gruntfile
 */

module.exports = function (grunt) {
  grunt.util.linefeed = '\n';

  var fs = require('fs');
  var path = require('path');
  var generateRTL = require('./build/rtl.js');
  var fixIE = require('./build/files/fix-ie.js');//fix IE9- CSS limit issue

  function getAceJs(type) {
	var jsList = grunt.file.readJSON('assets/js/ace/scripts.json');
	var list = [];
	for(var file in jsList) 
		if(jsList.hasOwnProperty(file) && file.indexOf(type) == 0 && jsList[file] == true) 
			list.push('assets/js/ace/'+file)
	
	return list;
  }

  // Project configuration.
  grunt.initConfig({

    // Metadata
    pkg: grunt.file.readJSON('package.json'),
    banner: '/*!\n' +
            ' * Ace v<%= pkg.version %>\n' +
            ' */\n',
    // NOTE: This jqueryCheck code is duplicated in customizer.js; if making changes here, be sure to update the other copy too.
    jqueryCheck: 'if (typeof jQuery === \'undefined\') { throw new Error(\'Ace\\\'s JavaScript requires jQuery\') }\n\n',

    // Task configuration.
    clean: {
      dist: ['dist'],
	  all: ['dist', 'demo', 'html']
    },

    concat: {
      options: {
        banner: '<%= banner %>\n<%= jqueryCheck %>',
        stripBanners: false,
		separator: ';'
      },

	  'ace-functions': {
		src: getAceJs('ace'),
		dest: 'assets/js/ace.js'
	  },
      'ace-elements': {
        src: getAceJs('elements'),
        dest: 'assets/js/ace-elements.js'
      }
    },
	
	uglify: {
		options: {
			preserveComments: 'some'
		},
		
		ace: {
		  files: [{
			  expand: true,
			  cwd: 'assets/js',
			  src: ['ace.js' , 'ace-elements.js' , 'ace-extra.js'],
			  dest: 'dist/js',
			  ext: function(name) { return name.replace(/(\.src)?\.js$/i , '.min.js'); }
		  }]
		},
		
		all: {
		  files: [{
			  expand: true,
			  cwd: 'assets/js',
			  src: ['*.js' , 'date-time/*.js' , 'flot/*.js' , 'fuelux/*.js' , 'dataTables/**/*.js', 'jqGrid/*.js' , 'markdown/*.js' , 'x-editable/*.js'],
			  dest: 'dist/js',
			  ext: function(name) { return name.replace(/(\.src)?\.js$/i , '.min.js'); }
		  }]
		}
	},
	
	
	less: {
      ace: {
        files: {
          'assets/css/ace.css': 'assets/css/less/ace.less',
		  'assets/css/ace-skins.css': 'assets/css/less/skins/skins.less',
		  'assets/css/ace-rtl.less.css': 'assets/css/less/ace-rtl.less',
		  'assets/css/bootstrap.css': 'assets/css/less/bootstrap/bootstrap.less'
        }
      }
    },

	cssmin: {
      options: {
        compatibility: 'ie8',
        keepSpecialComments: '*',
        noAdvanced: true
      },
      ace: {
        files: {
          'dist/css/ace.min.css' : 'assets/css/ace.css',
          'dist/css/ace-skins.min.css' : 'assets/css/ace-skins.css',
          'dist/css/ace-rtl.min.css' : 'assets/css/ace-rtl.css',
          'dist/css/ace-part2.min.css' : 'assets/css/ace-part2.css',
          'dist/css/ace-ie.min.css' : 'assets/css/ace-ie.css',
		  'dist/css/bootstrap.min.css' : 'assets/css/bootstrap.css'
        }
      },	
	  all: {
		  files: [{
			  expand: true,
			  cwd: 'assets/css',
			  src: ['*.css'],
			  dest: 'dist/css',
			  ext: function(ext) { return ext.replace(/\.css$/i , '.min.css'); }
		  }]
	  }
    },
	
	copy: {
      assets: {
        expand: true,
        cwd: './assets',
        src: [
		  'css/**/*.{gif,png,jpg,jpeg}',
          'fonts/*',
		  'font/*',
		  'avatars/*',
		  'images/**',
		  'img/*',
		  'js/jqGrid/i18n/*.js',
		  'js/dataTables/**/*.swf'
        ],
        dest: 'dist'
      }
    },
	
	exec: {
	  html: {
        command: 'node mustache/js/index.js --output_folder="../../../html" --onpage_help=true --development=true'
      },
	  html_ajax: {
        command: 'node mustache/js/ajax.js --output_folder="../../../html" --onpage_help=true --development=true'
      },
      demo: {
        command: 'node mustache/js/index.js --output_folder="../../../demo" --path_minified="\\.min" --path_base="." --path_assets="dist" --path_images="dist/images" --demo=true --onpage_help=false --development=false --protocol=false --remote_jquery=true --remote_fonts=true --remote_bootstrap_js=true --remote_fontawesome=true'
      },
	  demo_ajax: {
        command: 'node mustache/js/ajax.js --output_folder="../../../demo" --path_minified="\\.min" --path_base=".../" --path_assets=".../dist" --path_images=".../dist/images" --demo=true --onpage_help=false --development=false --protocol=false --remote_jquery=true --remote_fonts=true --remote_bootstrap_js=true --remote_fontawesome=true'
      }
    },
	
	compress: {
	  demo: {
		options: {
		  archive: 'demo-v<%= pkg.version %>.zip',
		  mode: 'zip',
		  level: 9
		},
		files: [
		  { expand: true, cwd: './demo', src: ['**', '!**/readme**', '!**/Copy **'],	dest: '.' },
		  { expand: true, cwd: './dist', src: ['**', '!**/readme**', '!**/Copy **'],	dest: './dist' },
		  { expand: true, cwd: './build/demo', src: ['**', '!**/readme**', '!**/Copy **'],	dest: './build/demo' }
		]
	  },
	  template: {
		options: {
		  archive: 'ace-v<%= pkg.version %>.zip',
		  mode: 'zip',
		  level: 9
		},
		files: [
		  { cwd: './', src: ['changelog', 'credits.txt', 'dummy.html', 'Gruntfile.js', 'index.html', 'package.json'],	dest: '.' },
		  { expand: true, cwd: './assets', src: ['**', '!**/Copy **'], dest: './assets' },
		  { expand: true, cwd: './build', src: ['**', '!**/node_modules/**', '!**/Copy **'],	dest: './build' },
		  { expand: true, cwd: './dist', src: ['**', '!**/readme**'],	dest: './dist' },
		  { expand: true, cwd: './docs', src: ['**', '!**/Copy **'], dest: './docs' },
		  { expand: true, cwd: './examples', src: ['**', '!**/Copy **'], dest: './examples' },
		  { expand: true, cwd: './html', src: ['**'], dest: './html' },
		  { expand: true, cwd: './mustache', src: ['**', '!**/node_modules/**', '!**/_cache/*.php', '!**/Copy **'], dest: './mustache' }
		]
	  }
	}
  });


  // These plugins provide necessary tasks.
  //require('load-grunt-tasks')(grunt, { scope: 'devDependencies' });
  grunt.loadNpmTasks('grunt-contrib-clean');
  grunt.loadNpmTasks('grunt-contrib-concat');
  grunt.loadNpmTasks('grunt-contrib-uglify');
  grunt.loadNpmTasks('grunt-contrib-less');
  grunt.loadNpmTasks('grunt-contrib-cssmin');
  grunt.loadNpmTasks('grunt-contrib-copy');
  grunt.loadNpmTasks('grunt-contrib-compress');
  grunt.loadNpmTasks('grunt-exec');


  grunt.registerTask('make-rtl', 'Generate RTL file.', function () {
    generateRTL(grunt);
  });
  grunt.registerTask('fix-ie', 'Fix IE9- CSS limit issue.', function () {
    fixIE(grunt);
  });
  
  grunt.registerTask('cleanup', 'Cleanup', function () {
    var files = [
				'dist/css/ace.onpage-help.min.css'
				];
    for(var f = 0 ; f < files.length ; f++) if(fs.existsSync(files[f])) fs.unlinkSync(files[f]);
  });


  
  //register tasks
  grunt.registerTask('demo', ['exec:demo', 'exec:demo_ajax', 'compress:demo']);//build demo HTML and make zip file

  grunt.registerTask('mustache', ['exec:html', 'exec:html_ajax']);//build HTML files
  
  grunt.registerTask('css', ['less', 'make-rtl', 'fix-ie', 'cssmin:ace', 'cleanup']);//build Ace CSS
  grunt.registerTask('css-all', ['less', 'make-rtl', 'fix-ie', 'cssmin:all', 'cleanup']);//build all CSS

  grunt.registerTask('default', ['concat', 'uglify:ace', 'css', 'copy']);//build Ace files

  grunt.registerTask('rebuild', ['clean:all', 'concat', 'uglify:all', 'css-all', 'copy', 'mustache']);//all files
};
