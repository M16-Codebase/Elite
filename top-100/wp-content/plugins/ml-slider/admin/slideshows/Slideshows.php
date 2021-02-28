<?php

if (!defined('ABSPATH')) die('No direct access.');

/**
 *  Class to handle slideshows
 */
class MetaSlider_Slideshows {

	/**
	 * Constructor
	 */
	public function __construct() {}

	/**
	 * Method to get all slideshows from the database
	 * 
	 * @return array 
	 */
	public function all() {

        $args = array(
            'post_type' => 'ml-slider',
            'post_status' => 'publish',
            'orderby' => 'date',
            'suppress_filters' => 1, // wpml, ignore language filter
            'order' => 'ASC',
            'posts_per_page' => -1
		);

		$slideshows = get_posts(apply_filters('metaslider_all_meta_sliders_args', $args));

        return array_map(array($this, 'build_slideshow_object'), $slideshows);
	}

	/**
     * Method to build out the slideshow object
	 * For now this wont include slides. They will be handled separately.
     *
	 * @param object $slideshow - The slideshow object
     * @return array
     */
	public function build_slideshow_object($slideshow) {
		return array(
			'id' => $slideshow->ID,
			'title' => $slideshow->post_title,
			'created_at' => $slideshow->post_date,
			'modified_at' => $slideshow->post_modified
		);
	}

	/**
     * Method to get the latest slideshow
     */
	public function recently_modified() {}

	/**
     * Method to get all slideshows from the database
	 * 
	 * @param string $id - The id of a slideshow
     */
	public function single($id) {}


}