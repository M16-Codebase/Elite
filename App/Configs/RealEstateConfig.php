<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 21.08.15
 * Time: 12:26
 */

namespace App\Configs;


class RealEstateConfig
{
    /**
     * Жилой комплекс
     */
    const CATEGORY_KEY_COMPLEX = 'complex';
    /**
     * Корпус
     */
    const CATEGORY_KEY_HOUSING = 'housing';
    /**
     * Этаж
     */
    const CATEGORY_KEY_FLOOR = 'floor';
    /**
     * Квартира
     */
    const CATEGORY_KEY_FLAT = 'flat';
    
    /* ********* Группы ********** */
    const KEY_GROUP_INFORM_BLOCK = 'information_block';
    const KEY_GROUP_DESCRIPTION = 'opisanie';

	/* ********* Ключи для объектов недвижимости на первичном рынке *********/
	const KEY_OBJECT_TITLE = 'title';
	const KEY_OBJECT_SNIPPET = 'snippet';
	const KEY_OBJECT_TITLE_SEARCH = 'title_search';
	const KEY_OBJECT_PRIORITY = 'priority';
    const KEY_OBJECT_TIME = 'time';
	const KEY_OBJECT_LOGO = 'logo';
    const KEY_OBJECT_TOP = 'top';
	const KEY_OBJECT_ICON = 'icon';
	const KEY_OBJECT_PRICE_METER_FROM = 'price_meter_from';
	const KEY_OBJECT_AREA = 'app_area';
	const KEY_OBJECT_GALLERY = 'gallery';
	const KEY_OBJECT_SHEME_GET = 'sheme_get';
	const KEY_OBJECT_SHEME_VIEW = 'sheme_view';
	const KEY_OBJECT_DISTRICT = 'district';
	const KEY_OBJECT_ADDRESS = 'address';
    const KEY_OBJECT_METRO = 'metro';
    const KEY_OBJECT_APART_IN_COMPLEX = 'apart_in_complex';
    const KEY_OBJECT_APART_IN_SALE = 'apart_in_sale';
	const KEY_OBJECT_CENTER_DISTANCE = 'center_distance';
	const KEY_OBJECT_CENTER_TIME_BYCAR = 'center_time_bycar';
	const KEY_OBJECT_AIRPORT_DISTANCE = 'airport_distance';
	const KEY_OBJECT_AIRPORT_TIME_BYCAR = 'airport_time_bycar';
	const KEY_OBJECT_KAD_DISTANCE = 'kad_distance';
	const KEY_OBJECT_KAD_TIME_BYCAR = 'kad_time_bycar';
	const KEY_OBJECT_HOUSE_TYPE = 'house_type';
	const KEY_OBJECT_NUMBER_STOREYS = 'number_storeys';
	const KEY_OBJECT_CEILING_HEIGHT = 'ceiling_height';
    const KEY_OBJECT_COMPLETE_YEAR = 'complete_year';
	const KEY_OBJECT_COMPLETE = 'complete';
	const KEY_OBJECT_CONCEPT = 'concept';
	const KEY_OBJECT_MATERIALS = 'materials';
	const KEY_OBJECT_ENGINEER_SOLUTION = 'engineer_solution';
	const KEY_OBJECT_PARKING = 'parking';
	const KEY_OBJECT_PUBLIC_SPACE = 'public_space';
	const KEY_OBJECT_PROGRESS = 'progress';
	const KEY_OBJECT_CONSULTANT = 'consultant';
	const KEY_OBJECT_CONSULTANT_TEXT = 'consultant_text';
	const KEY_OBJECT_MALAFEEV_TEXT = 'malafeev_text';
	const KEY_OBJECT_DESCRIPTION = 'description';
	const KEY_OBJECT_ADVANTAGES = 'advantages';
	const KEY_OBJECT_PAYMENT_TYPES = 'payment_types';
	/* ********* Ключи для корпуса на первичном рынке *********/
	const KEY_HOUSING_TITLE = 'title';
	const KEY_HOUSING_STATE = 'state';
	const KEY_HOUSING_SCHEME_GET = 'sheme_get';
	const KEY_HOUSING_SCHEME_COORDS = 'sheme_coords';
	/* ********* Ключи для этажа на первичном рынке *********/
	const KEY_FLOOR_TITLE = 'title';
	const KEY_FLOOR_NUMBER = 'floor_number';
	const KEY_FLOOR_SHEME_GET = 'sheme_get';
	const KEY_FLOOR_SHEME_COORDS = 'sheme_coords';
	const KEY_FLOOR_APPART_NUMBER = 'appart_number';
	const KEY_FLOOR_APPART_NUMBER_SALE = 'appart_number_sale';
	/* ********* Ключи для квартиры на первичном рынке *********/
	const KEY_APPART_STATE = 'state';
	// Ключи статусов квартир
	const KEY_APPART_STATE_FOR_SALE = 'for_sale';
	const KEY_APPART_STATE_SOLD = 'sold';

	const KEY_APPART_COORDS = 'sheme_coords';
	const KEY_APPART_SHEMES = 'shemes';
	const KEY_APPART_CLOSE_PRICE = 'close_price';
	const KEY_APPART_PRICE = 'price';
	const KEY_APPART_FLOORS = 'floors';
	const KEY_APPART_AREA_ALL = 'area_all';
	const KEY_APPART_AREA_LIVING = 'area_living';
	const KEY_APPART_AREA_KITCHEN = 'area_kitchen';
	const KEY_APPART_BED_NUMBER = 'bed_number';
	const KEY_APPART_WC_NUMBER = 'wc_number';
	const KEY_APPART_CEILING_HEIGHT = 'ceiling_height';
	const KEY_APPART_OVERHANG = 'overhang';
	const KEY_APPART_FINISHING = 'finishing';
	const KEY_APPART_SPECIAL_OFFER = 'special_offer';

	const KEY_APART_SPECIAL_OFFER_GIFT = 'gift';
	const KEY_APART_SPECIAL_OFFER_DISCOUNT = 'discount';

	const KEY_APPART_SPECIAL_OFFER_COMMENT = 'special_offer_comment';
	const KEY_APPART_FEATURES = 'features';
    
    /* ********* Дополнительные ключи для квартир на вторичном рынке *********/
    const KEY_APPART_TITLE = self::KEY_OBJECT_TITLE;
    const KEY_APPART_PRIORITY = self::KEY_OBJECT_PRIORITY;
    const KEY_APPART_TIME = self::KEY_OBJECT_TIME;
    const KEY_APPART_ICON = self::KEY_OBJECT_ICON;
    const KEY_APPART_GALLERY = self::KEY_OBJECT_GALLERY;
    const KEY_APPART_TOUR = 'tour';
    const KEY_APPART_TOUR_ZIP = 'tour_zip';
    const KEY_APPART_TOUR_URL = 'tour_url';
    const KEY_APPART_VIDEO = 'video';
    const KEY_APPART_OBJECT_TITLE = 'object_title';
    const KEY_APPART_OBJECT_ADDRESS = 'object_address';
    const KEY_APPART_DISTRICT = self::KEY_OBJECT_DISTRICT;
    const KEY_APPART_ADDRESS = self::KEY_OBJECT_ADDRESS;
    const KEY_APPART_METRO = self::KEY_OBJECT_METRO;
    const KEY_APPART_CENTER_DISTANCE = self::KEY_OBJECT_CENTER_DISTANCE;
	const KEY_APPART_CENTER_TIME_BYCAR = self::KEY_OBJECT_CENTER_TIME_BYCAR;
	const KEY_APPART_AIRPORT_DISTANCE = self::KEY_OBJECT_AIRPORT_DISTANCE;
	const KEY_APPART_AIRPORT_TIME_BYCAR = self::KEY_OBJECT_AIRPORT_TIME_BYCAR;
	const KEY_APPART_KAD_DISTANCE = self::KEY_OBJECT_KAD_DISTANCE;
	const KEY_APPART_KAD_TIME_BYCAR = self::KEY_OBJECT_KAD_TIME_BYCAR;
	const KEY_APPART_HOUSE_TYPE = self::KEY_OBJECT_HOUSE_TYPE;
    const KEY_APPART_NUMBER_STOREYS = self::KEY_OBJECT_NUMBER_STOREYS;
    const KEY_APPART_FLOOR = 'floor_number';
    const KEY_APPART_REPAIR = 'repair';
    const KEY_APPART_FURNITURE = 'furniture';
    const KEY_APPART_INFRA = 'infra';
    const KEY_APPART_INFRA_TEXT = 'infra_text';
    const KEY_APPART_CONSULTANT = self::KEY_OBJECT_CONSULTANT;
	const KEY_APPART_CONSULTANT_TEXT = self::KEY_OBJECT_CONSULTANT_TEXT;
    const KEY_APPART_DESCRIPTION = self::KEY_OBJECT_DESCRIPTION;
	const KEY_APPART_TOP = 'top';
}