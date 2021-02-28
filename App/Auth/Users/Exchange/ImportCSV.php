<?php
/**
 * Импорт пользователей
 *
 * @author olga
 */
namespace App\Auth\Users\Exchange;
class ImportCSV {
	const TABLE_USER_DATA = 'user_data_1c';
	const FILE_PATH = '/data/exchange/users/import/';
	const FILE_EXT = 'csv';
	const SEPARATOR_CELL = ';';
	const SEPARATOR_CELL_TAB = "\t";
    const SEPARATOR_CELL_COMMON = ',';
	const SEPARATOR_ROW = "\n";
//	protected $user_fields = array(
//		'ИНН' => 'inn',
//		'Наименование компании' => 'company_name',
//		'ОГРН' => 'ogrn',
//		'ОКПО' => 'okpo',
//		'КПП' => 'kpp',
//		'Имя' => 'name',
//		'Фамилия' => 'surname',
//		'Отчество' => 'patronymic',
//		'Банковские реквизиты' => 'requisites',
//		'Электронная почта' => 'email',
//		'Телефон фирмы' => 'organisation_phone',
//		'Факс фирмы' => 'organisation_fax',
//		'Юридический адрес' => 'jure_address',
//		'Почтовый адрес' => 'document_address',
//		'Уровень скидки для Инструмента' => 'discount_tool',
//		'Уровень скидки для Пожарного оборудования' => 'discount_equip',
//		'Баланс' => 'money_balance',
//		'ИД пользователя на сайте' => 'user_id',
//		'Создать на сайте' => 'create',
//		'ИД 1С' => '1C_id'
//	);
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
        'phone' => 'phone',//телефон контактного лица
		'organisation_phone' => 'organisation_phone',//Телефон фирмы
		'organisation_fax' => 'organisation_fax',//Факс фирмы
		'jure_address' => 'jure_address',//Юридический адрес
		'document_address' => 'document_address',//Почтовый адрес
		'discount_tool' => 'discount_tool',//Уровень скидки для Инструмента
		'discount_equip' => 'discount_equip',//Уровень скидки для Пожарного оборудования
		'money_balance' => 'money_balance',//Баланс
		'user_id' => 'user_id',//ИД пользователя на сайте
		'create' => 'create',//Создать на сайте
		'external_id' => 'external_id'//ИД 1С
	);
	/**
	 * Поля которые надо редактировать у пользователя
	 * @var type 
	 */
	protected $fieldsToUpdate = array(
		'inn',
		'company_name',
		'ogrn',
		'okpo',
		'kpp',
		'name',
		'surname',
		'patronymic',
		'requisites',
		'email',
        'phone',
		'organisation_phone',
		'organisation_fax',
		'jure_address',
		'document_address',
		'discount_tool',
		'discount_equip',
		'money_balance',
		'external_id',
	);
    protected $fieldsForSubscribe = array(
        'name', 'surname', 'company_name', 'email', 'subscribe'
    );
    /**
     * числовые поля
     * @var array
     */
    private $numberFields = array(
        'money_balance'
    );
	
	protected static $instance = NULL;
	/**
	 * 
	 * @return ImportCSV
	 */
	public static function getInstance(){
		if (empty(static::$instance)){
			static::$instance = new static();
		}
		return static::$instance;
	}
	
	public function get(){
		$files = glob(\LPS\Config::getRealDocumentRoot() . static::FILE_PATH . '*.' . static::FILE_EXT);
		$user_data = array();
		$user_data_site = array();
        $keys = array();
		foreach ($files as $file_path){
			$d = fopen($file_path, 'r');
			$file_row = TRUE;
            $separator_cell = static::SEPARATOR_CELL;
            $first_row = TRUE;
			while ($file_row !== FALSE){
				$file_row = fgetcsv($d, 0, $separator_cell, static::SEPARATOR_ROW);
				if ($file_row === FALSE){
					break;
				}
                if (count($file_row) < 2){
                    $separator_cell = static::SEPARATOR_CELL_TAB;
                    $file_row = fgetcsv($d, 0, $separator_cell, static::SEPARATOR_ROW);
                    if (count($file_row) < 2){
                        $separator_cell = static::SEPARATOR_CELL_COMMON;
                        $file_row = fgetcsv($d, 0, $separator_cell, static::SEPARATOR_ROW);
                    }
                }
                foreach ($file_row as &$field){
                    $field = trim($field);
                }
//				foreach ($file_row as &$val){
//					$val = mb_convert_encoding($val, 'utf-8', 'Windows-1251');
//				}
				//в первом столбце ИНН, значит будет ключ 0
				if ($first_row){
                    foreach ($file_row as $num => $k){//нам нужны ключи свойств в нужном порядке
                        $k = preg_replace('~[^a-zA-Z_0-9]~', '', $k);
                        $keys[$num] = !isset($this->user_fields[$k]) ? 'not_exists' : $this->user_fields[$k];
                    }
                    $first_row = FALSE;
					continue;
				}
                if (empty($keys)){
                    break;
                }
                if (count($keys) != count($file_row)){
                    continue;
                }
                $user_data_row = array_combine($keys, $file_row);
                if (isset($user_data_row['not_exists'])){
                    unset($user_data_row['not_exists']);
                }
				if(!empty($user_data_row['user_id'])){//по id
					$user_data_site[$user_data_row['user_id']] = $user_data_row['user_id'];
				}
				$user_data[] = $user_data_row;
			}
			fclose($d);
			unlink($file_path);
		}
		if (empty($user_data)){
			return NULL;
		}
		$users = \App\Auth\Users\Factory::getInstance()->getUsers(array('id' => $user_data_site));
		$db = \App\Builder::getInstance()->getDB();
//		$users_1C = $db->query('SELECT * FROM `'.static::TABLE_USER_DATA.'` WHERE `email` IN (?l)', array_keys($user_data))->select('email');
////		var_dump($users_1C);
//		$users_email2id = array();
//		foreach ($users as $us){
//			if (!empty($us['email'])){
//				$users_email2id[$us['email']] = $us['id'];
//			}
//		}
		foreach ($user_data as $email => $ud){
//			if (empty($email)){
//				continue;
//			}
            foreach($ud as $ud_key => &$ud_val){
                if ($ud_key == 'subscribe'){
                    $ud_val = $ud_val == 'Подписан' ? 1 : 0;
                }elseif ($ud_val == '' && $ud_key != 'email'){//чтобы не удалили мыло
                    $ud_val = NULL;
                }elseif(in_array($ud_key, $this->numberFields)){
                    $ud_val = preg_replace('~[^0-9\.\-\+]~', '', str_replace(',', '.', $ud_val));
                }
            }
			//обновляем данные пользователя
			$update_data = array();
			foreach ($this->fieldsToUpdate as $f){
                if (isset($ud[$f])){
                    $update_data[$f] = $ud[$f];
                }
			}
			if (!empty($ud['user_id']) && !empty($users[$ud['user_id']])){
				$users[$ud['user_id']]->update($update_data);
			}elseif(!empty($ud['create']) && !empty($ud['email'])){
				$pass = \App\Auth\Controller::randomPassword();
				$checker = \Models\Validator::getInstance();
				$email = $checker->checkValue($ud['email'], 'checkEmail', $errors['email'], array('uniq' => true));
				$controller = \App\Builder::getInstance()->getAccountController();
                if (!empty($email) && $controller->regUser($email, $pass)){
                    $user = $controller->authenticate($email, $pass, $error);
                    if (!empty($user)){
                        if (!empty($update_data)){
							$update_data['import'] = 1;
                            $update_data['person_type'] = !empty($update_data['inn']) ? 'org' : 'fiz';
                            $user->update($update_data);
                        }
                        //отправляем письмо пользователю о том что он зарегался
                        $mail_ans = new \LPS\Container\WebContentContainer(
                                'mails/registration.tpl');
                        $site_config = \App\Builder::getInstance()->getSiteConfig();
                        $mail_ans->add('user', $user)
                            ->add('new_pass', $pass)
                            ->add('user_email', $user->getEmail())
                            ->add('site_config', $site_config);
                        \Models\Email::send($mail_ans, array($user->getEmail() => $user->getName()));
                    }
                }
			}
            if (array_key_exists('create', $ud)){
                unset($ud['create']);
            }
			//обновляем доп таблицу данных о фирмах
			$db->query('REPLACE INTO `'.static::TABLE_USER_DATA.'` SET ?a', $ud);
		}
	}
}