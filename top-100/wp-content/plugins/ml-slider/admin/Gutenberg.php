<?php
if (!defined('ABSPATH')) die('No direct access.');

/**
 * Adds a MetaSlider block to Gutenberg
 */
class MetaSlider_Gutenberg { 

	/**
	 * Init
	 */
	public function __construct() {
		$this->plugin = MetaSliderPlugin::get_instance(); 
		add_action('enqueue_block_editor_assets', array($this,'enqueue_block_scripts'));
	}

	/**
	 * Enqueues gutenberg scripts
	 */
	public function enqueue_block_scripts() {
		$version = MetaSliderPlugin::get_instance()->version;

		// Enqueue the bundled block JS file
		wp_enqueue_script(
			'metaslider-blocks-js',
			plugins_url('assets/js/editor-block.js', __FILE__),
			array('wp-i18n', 'wp-element', 'wp-blocks', 'wp-components', 'wp-api'),
			$version
		);

		// Enqueue optional editor only styles
		wp_enqueue_style(
			'metaslider-blocks-editor-css',
			plugins_url('assets/css/gutenberg/editor-block.css', __FILE__),
			array('wp-blocks'),
			$version
		);
	}

}