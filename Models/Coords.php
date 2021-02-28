<?php
/**
 * Description of Coords
 *
 * @author olga
 */
namespace Models;
class Coords {
	/* ***** Сферическая проекция ****/
	
//	function lon2x($lon) { return deg2rad($lon) * 6378137.0; }
//	function lat2y($lat) { return log(tan(M_PI_4 + deg2rad($lat) / 2.0)) * 6378137.0; }
//	function x2lon($x) { return rad2deg($x / 6378137.0); }
//	function y2lat($y) { return rad2deg(2.0 * atan(exp($y / 6378137.0)) - M_PI_2); }
	
	
	/* ****** Эллиптическая проекция *****/
	private static function merc_x($lon){
		$r_major = 6378137.000;
		return $r_major * deg2rad($lon);
	}

	private static function merc_y($lat){
		if ($lat > 89.5) $lat = 89.5;
		if ($lat < -89.5) $lat = -89.5;
		$r_major = 6378137.000;
		$r_minor = 6356752.3142;
		$temp = $r_minor / $r_major;
		$es = 1.0 - ($temp * $temp);
		$eccent = sqrt($es);
		$phi = deg2rad($lat);
		$sinphi = sin($phi);
		$con = $eccent * $sinphi;
		$com = 0.5 * $eccent;
		$con = pow((1.0-$con)/(1.0+$con), $com);
		$ts = tan(0.5 * ((M_PI*0.5) - $phi))/$con;
		$y = - $r_major * log($ts);
		return $y;
	}

	public static function merc($lon, $lat) {
		return array('x'=>self::merc_x($lon),'y'=>self::merc_y($lat));
	}
}
