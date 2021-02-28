<?php

if (!defined('ABSPATH')) die('No direct access.');

/** 
 * Class to handle rest endpoints, specifically used by vue components
 * If possible, write / copy new methods in appropriate classes.
 */
class MetaSlider_Api extends WP_REST_Controller {

	/**
	 * Namespace and version for the API
	 * 
	 * @var string
	 */
    protected $namespace = 'metaslider/v1';

	/**
	 * Constructor
	 */
    public function __construct() {
		$this->slideshows = new MetaSlider_Slideshows();
	}

	/**
	 * Register routes
	 */
	public function register_routes() {

        register_rest_route($this->namespace, '/slideshows/all', array(
            array(
                'methods' => 'GET',
                'callback' => array($this, 'get_all_slideshows')
			)
        ));
        register_rest_route($this->namespace, '/slideshow/{id}/update', array(
            array(
                'methods' => 'POST',
                'callback' => array($this, 'update_slideshow')
			)
        ));
	}
	
	/**
	 * Returns all slideshows
	 * 
	 * @return array|WP_Error
	 */
    public function get_all_slideshows() {

		$user = wp_get_current_user();
		$capability = apply_filters('metaslider_capability', 'edit_others_posts');

		if (!current_user_can($capability)) {
            return new WP_Error('access_denied', __('You do not have access to this resource.'), array('status' => 401));
		}
		return $this->slideshows->all();
    }
}
