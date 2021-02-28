<?php
/**
 * Экспорт пользователей
 *
 * @author olga
 */
namespace App\Auth\Users\Exchange;
class ExportCSV {
	const FILE_EXT = 'csv';
	const TEMP_FILE_PATH = '/data/exchange/users/export/';
	const FILE_NAME = 'export';
	const SEPARATOR_CELL = ';';
	const SEPARATOR_ROW = "\n";
	private static $instance;
	protected $user_fields = array(
		'inn' => 'inn',//ИНН
		'company_name' => 'company_name',//Наименование компании
		'ogrn' => 'ogrn',//ОГРН
		'okpo' => 'okpo',//ОКПО
		'kpp' => 'kpp',//КПП
		'name' => 'name',//Имя
		'surname' => 'surname',//Фамилия
		'patronymic' => 'patronymic',//Отчество
		'requisites' => 'requisites',//Банковские реквизиты
		'email' => 'email',//Электронная почта
		'phone' => 'phone',//Телефон фирмы
		'organisation_fax' => 'organisation_fax',//Факс фирмы
		'jure_address' => 'jure_address',//Юридический адрес
		'document_address' => 'document_address',//Почтовый адрес
		'discount_tool' => 'discount_tool',//Уровень скидки для Инструмента
		'discount_equip' => 'discount_equip',//Уровень скидки для Пожарного оборудования
		'money_balance' => 'money_balance',//Баланс
		'user_id' => 'id',//ИД пользователя на сайте
		'1C_id' => 'external_id'//ИД 1С
	);
	/**
	 * 
	 * @return ExportCSV
	 */
	public static function getInstance(){
		if (empty(self::$instance)){
			self::$instance = new self();
		}
		return self::$instance;
	}
	public function write(){
		$file_path = \LPS\Config::getRealDocumentRoot() . self::TEMP_FILE_PATH . self::FILE_NAME . '.' . self::FILE_EXT;
		if (file_exists($file_path)){
			return FALSE;
		}
		if (!file_exists(\LPS\Config::getRealDocumentRoot() . self::TEMP_FILE_PATH)){
			\LPS\Components\FS::makeDirs(\LPS\Config::getRealDocumentRoot() . self::TEMP_FILE_PATH);
		}
		$site_config = \App\Builder::getInstance()->getSiteConfig();
		$users = $this->getUserData($site_config['user_export_last_date']);
		if (empty($users)){
			return FALSE;
		}
		$d = fopen($file_path, 'w');
		$first_row = array();
		//with BOM
		foreach ($this->user_fields as $f_name => $f_key){
            $first_row[] = (empty($first_row) ? chr(239) . chr(187) . chr(191) : '') . $f_name; //mb_convert_encoding($f_name, 'Windows-1251', 'utf-8');
		}
		fputcsv($d, $first_row, self::SEPARATOR_CELL);
		foreach ($users as $u){
			fputcsv($d, $u, self::SEPARATOR_CELL);
		}
		fclose($d);
        chmod($file_path, 0666);
		$site_config->set('user_export_last_date', 'basic', date('Y-m-d H:i:s'), '', 'text');
        \App\Builder::getInstance()->getAccountController()->setParamsToAllUsers(array('import' => 0));
		return TRUE;
	}
	
	protected function getUserData($last_update){
		$data = array();
		$params = array();
		if (!empty($last_update)){
			$params['last_update'] = $last_update;
			$params['import'] = 1;
			$params['role'] = 'User';
		}
		$users = \App\Auth\Users\Factory::getInstance()->getUsers($params);
		foreach ($users as $us){
			foreach ($this->user_fields as $uf){
				$value = $us[$uf];
				if ($uf == 'birth_date' || $uf == 'reg_date'){
					$value = !empty($us[$uf]) ? date('d.m.Y', strtotime($us[$uf])) : NULL;
				}elseif ($uf == 'subscribe'){
					$value = !empty($us[$uf]) ? 'Подписан' : 'Не подписан';
				}elseif($uf == 'addresses'){
					$value = implode('|', $us[$uf]);
                }elseif($uf == 'phone'){
                    $value = '' . $us['phone'];//типа строка
                }
				$data[$us['id']][$uf] = preg_replace('~[\n\r\t;]~', ' ', $value); //iconv('UTF-8', 'windows-1251//TRANSLIT', $value);
			}
		}
		return $data;
	}
}