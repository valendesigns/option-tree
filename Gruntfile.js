/* global module */
/* jshint node:true */
module.exports = function( grunt ) {
	'use strict';

	grunt.initConfig( {

		// Build a deploy-able plugin
		copy: {
			build: {
				src: [
					'!.*',
					'!.*/**',
					'assets/**',
					'includes/**',
					'composer.json',
					'LICENSE',
					'ot-loader.php',
					'readme.txt'
				],
				dest: 'build',
				expand: true,
				dot: true
			}
		},

		// Clean up the build
		clean: {
			build: {
				src: [ 'build' ]
			}
		},

		// Deploys a git Repo to the WordPress SVN repo
		wp_deploy: {
			deploy: {
				options: {
					plugin_slug: 'option-tree',
					build_dir: 'build',
					plugin_main_file: 'ot-loader.php'
				}
			}
		}
	} );

	// Load tasks
	grunt.loadNpmTasks( 'grunt-contrib-clean' );
	grunt.loadNpmTasks( 'grunt-contrib-copy' );
	grunt.loadNpmTasks( 'grunt-wp-deploy' );

	grunt.registerTask( 'deploy', [
		'copy',
		'wp_deploy',
		'clean'
	] );
};
