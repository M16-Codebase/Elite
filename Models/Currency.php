<?php
/**
 * Работа с валютой
 *
 * @author olga
 */
namespace Models;
use App\Configs\CatalogConfig;
class Currency {
	const TABLE = 'currency';
	const CB_URL = 'http://www.cbr.ru/scripts/XML_daily.asp';
	/**
	 * Доллар США
	 */
	const C_USD = 'USD';
	/**
	 * Евро
	 */
	const C_EUR = 'EUR';
	/**
	 * Фунт стерлингов
	 */
	const C_GBP = 'GBP';
	/**
	 * Гривна
	 */
	const C_UAH = 'UAH';
	/**
	 * Болгарский лев
	 */
	const C_BGN = 'BGN';
	/**
	 * Белорусских рублей
	 */
	const C_BYR = 'BYR';
	/**
	 * Канадский доллар
	 */
	const C_CAD = 'CAD';
	/**
	 * Швейцарский франк
	 */
	const C_CHF = 'CHF';
	/**
	 * Чешских крон
	 */
	const C_CZK = 'CZK';
	/**
	 * Датских крон
	 */
	const C_DKK = 'DKK';
	/**
	 * Литовский лит
	 */
	const C_LTL = 'LTL';
	/**
	 * Латвийский лат
	 */
	const C_LVL = 'LVL';
	/**
	 * Польский злотый
	 */
	const C_PLN = 'PLN';
	/**
	 * Шведских крон
	 */
	const C_SEK = 'SEK';
	/**
	 * Турецкая лира
	 */
	const C_TRY = 'TRY';
	/**
	 * Используемые валюты
	 * @var array
	 */
	private static $uses_currency = array(
		self::C_USD => '$',
		self::C_EUR => '€',
		self::C_GBP => '£',
		self::C_UAH => '₴',
		self::C_BGN => 'BGN',
		self::C_BYR => 'BYR',
		self::C_CAD => 'CAD',
		self::C_CHF => 'CHF',
		self::C_CZK => 'CZK',
		self::C_DKK => 'DKK',
		self::C_LTL => 'LTL',
		self::C_LVL => 'LVL',
		self::C_PLN => 'PLN',
		self::C_SEK => 'SEK',
		self::C_TRY => 'TRY'
	);
	private static $valutes = NULL;
	public static function getUsesCurrency(){
		return self::$uses_currency;
	}
	public static function parse(){
		try {
			$full_xml = simplexml_load_file(self::CB_URL);
		}catch(Exception $e){
			return NULL;
		}
		$valutes = $full_xml->Valute;
		if (empty($valutes)){
			return NULL;
		}
		$ours_valutes = self::get();
		$result = array();
		foreach ($valutes as $v){
			$charCode = (string) $v->CharCode;
			if (empty(self::$uses_currency[$charCode])){
				continue;
			}
			$result['nominal'] = str_replace(',', '.', $v->Nominal);
			$result['name'] = $v->Name;
			$result['value'] = str_replace(',', '.', $v->Value);
			if (empty($ours_valutes[$charCode])){
				self::create($charCode, $result);
			}else{
				if ($ours_valutes[$charCode]['nominal'] != $result['nominal'] || $ours_valutes[$charCode]['value'] != $result['value']){
					self::update($charCode, $result);
				}
			}
		}
	}
	
	public static function get(){
		if (empty(self::$valutes)){
			$db = \App\Builder::getInstance()->getDB();
			self::$valutes = $db->query('SELECT * FROM `'.self::TABLE.'`')->select('code');
		}
		return self::$valutes;
	}
	
	private static function create($code, $params){
		$db = \App\Builder::getInstance()->getDB();
		$db->query('INSERT INTO `'.self::TABLE.'` SET ?a', array('code' => $code) + $params);
		self::$valutes[$code] = $params;
	}
	
	private static function update($code, $params){
		$db = \App\Builder::getInstance()->getDB();
		$db->query('UPDATE `'.self::TABLE.'` SET ?a WHERE `code` = ?s', $params, $code);
		self::$valutes[$code] = $params + self::$valutes[$code];
	}
}

?>