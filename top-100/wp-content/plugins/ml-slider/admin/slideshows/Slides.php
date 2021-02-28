<?php

if (!defined('ABSPATH')) die('No direct access.');

/** 
 * Class to handle individual slides
 */
class MetaSlider_Slides {
	
	/**
	 * The id of the slideshow 
	 * 
	 * @var string
	 */
    protected $slideshow_id;

	/**
	 * Constructor
	 * 
	 * @param string $slideshow - The id of the slideshow
	 */
	public function __construct($slideshow) {

	}

	/**
	 * Method to get all slides assigned to the slideshow
	 * Can be called statically to get the entire collection of slides
	 */
	public static function all() {}

}
