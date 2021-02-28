<?php
/**
 * Контроллер подарочных сертификатов
 * Содержит функционал для работы с поставщиками сертификатов, типами сертификатов
 * и интерфейс для работы с сущностями сертификатов (класс Models/GiftCard)
 *
 * @author Charles Manson
 */
namespace Models\GiftCardManagement;

use Models\Email;

class GiftCardController {

    const TABLE_GIFTCARD_NOMINAL = 'giftcard_nominal';
    const TABLE_GIFTCARD_PROVIDER = 'giftcard_provider';
    const TABLE_GIFTCARD_REQUEST = 'giftcard_request';
    const TABLE_GIFTCARD_REQUEST_ITEMS = 'giftcard_request_items';
    
    const DEFAULT_PROVIDER_ID = 1;
    
    private static $prov_load_fields = array('id', 'provider_name', 'provider_site', 'giftcard_info_link', 'description', 'provider_image_id');
    private static $prov_update_fields = array('provider_name', 'provider_site', 'giftcard_info_link', 'description', 'provider_image_id');
    private static $prov_required_fields = array('provider_name' => TRUE); // Массив для проверки обязательных полей при создании поставщика, имена полей нужны в ключах
    private static $nom_load_fields = array('id', 'nominal_value', 'cost', 'provider_id', 'image_id');
    private static $nom_update_fields = array('nominal_value', 'cost', 'provider_id', 'image_id');
    private static $nom_required_fields = array('nominal_value' => TRUE, 'cost' => TRUE, 'provider_id' => TRUE);
    
    private static $instance = NULL;
    
    private $providers = NULL;
    private $nominals = NULL;
    private $nom2provider = array();
    
    /**
     * 
     * @return GiftCardController
     */
    public static function getInstance(){
        if (empty(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    /**
     * В конструкторе инициируем списки поставщиков и номиналов карт, в дальнейшем (при добавлении удалении записей) их требуется корректировать в обход базы
     */
    private function __construct() {
        $db = \App\Builder::getInstance()->getDB();
        $this->providers = $db->query('SELECT `' . implode('`, `', self::$prov_load_fields) . '` FROM `' . self::TABLE_GIFTCARD_PROVIDER . '` ORDER BY `provider_name`')->select('id');
        $this->nominals = $db->query('SELECT `' . implode('`, `', self::$nom_load_fields) . '` FROM `' . self::TABLE_GIFTCARD_NOMINAL . '` ORDER BY `provider_id`, `nominal_value`')->select('id');
        foreach($this->nominals as $id=>$nom){
            if (!empty($nom['image_id'])){
                $this->nominals[$id]['image'] = \Models\ImageManagement\Image::getById($nom['image_id']);
            }
            $this->nom2provider[$id] = $nom['provider_id'];
        }
        foreach($this->providers as $id=>$prov){
            if (!empty($prov['provider_image_id'])){
                $this->providers[$id]['provider_image'] = \Models\ImageManagement\Image::getById($prov['provider_image_id']);
            }
            if (!empty($prov['description'])){
                $this->providers[$id]['full_description'] = str_replace('{!}', '<a href="http://' . $prov['provider_site'] . '" rel="nofollow" target="_blank" class="m-bold">' . $prov['provider_site'] . '</a>', $prov['description']);
            }
        }
    }
    /**
     * Добавить поставщика сертификатов
     * @param array $data keys <ul><li>provider_name - название поставщика</li><li>provider_site - сайт поставщика</li></ul>
     * @return mixed ассоциативный массив с данными поставщика при успешном выполнении, FALSE в случае ошибки
     */
    public function addProvider($data){
        if (!empty($data['image'])){
            $image = \Models\ImageManagement\Image::add($data['image']);
            $data['provider_image_id'] = $image['id'];
        }
        foreach($data as $key=>$value){
            if (!in_array($key, self::$prov_update_fields)){
                unset($data[$key]);
            }
        }
        // Проверка наличия обязательных полей
        if (count(array_intersect_key($data, self::$prov_required_fields)) != count(self::$prov_required_fields)){
            return FALSE;
        }
        $id = \App\Builder::getInstance()->getDB()->query('INSERT INTO `' . self::TABLE_GIFTCARD_PROVIDER . '` SET ?a', $data);
        if (empty($id)){
            return FALSE;
        }
        // Дозаполняем пустые поля
        $data['id'] = $id;
        $emptyKeys = array_diff(array_keys($data), self::$prov_load_fields);
        foreach($emptyKeys as $key){
            $data[$key] = NULL;
        }
        $this->providers[$id] = $data;
        return $data;
    }
    /**
     * Редактирование поставщика
     * @param int $id id поставщика
     * @param array $data keys <ul><li>provider_name - название поставщика</li><li>provider_site - сайт поставщика</li></ul>
     */
    public function editProvider($id, $data){
        if (!isset($this->providers[$id])){
            return FALSE;
        }
        if (!empty($data['image'])){
            $image_id = !empty($this->providers[$id]['image_id']) ? $this->providers[$id]['image_id'] : NULL;
            if (empty($image_id)){
                $image = \Models\ImageManagement\Image::add($data['image']);
                $data['provider_image_id'] = $image['id'];
            } else {
                $image = \Models\ImageManagement\Image::getById($image_id);
                $image->reload($data['image']);
            }
        }
        foreach($data as $key=>$value){
            if (!in_array($key, self::$prov_update_fields)){
                unset($data[$key]);
            } else {
                $this->providers[$id][$key] = $value;
            }
        }
        \App\Builder::getInstance()->getDB()->query('UPDATE `' . self::TABLE_GIFTCARD_PROVIDER . '` SET ?a WHERE `id` = ?d', $data, $id);
        return $this->providers[$id];
    }
    /**
     * Получить информацию о поставщике по его id
     * @param int $provider_id id поставщика
     * @return array - ассоциативный массив с информацей о поставщике
     */
    public function getProvider($provider_id){
        return isset($provider_id) ? $this->providers[$provider_id] : NULL;
    }
    /**
     * Получить список поставщиков
     * @return array массив со списком поставщиков
     */
    public function getProvidersList(){
        return $this->providers;
    }
    /**
     * Удаление поставщика сертификатов
     * @param int $provider_id
     * @return boolean <ul><li>FALSE если поставщик отсутсвует или есть неудаленные номиналы</li><li>TRUE при успешном удалении</li>
     */
    public function deleteProvider($provider_id){
        if (!isset($this->providers[$provider_id]) || in_array($provider_id, $this->nom2provider)){
            return FALSE;
        }
        $image_id = $this->providers[$id]['image_id'];
        if (!empty($image_id)){
            \Models\ImageManagement\Image::del($image_id);
        }
        \App\Builder::getInstance()->getDB()->query('DELETE FROM `' . self::TABLE_GIFTCARD_PROVIDER . '` WHERE `id` = ?d', $provider_id);
        unset($this->providers[$provider_id]);
        return TRUE;
    }
    
    public function addNominal($data){
        if (!empty($data['image'])){
            $image = \Models\ImageManagement\Image::add($data['image']);
            $data['image_id'] = $image['id'];
        }
        foreach($data as $key=>$value){
            if (!in_array($key, self::$nom_update_fields)){
                unset($data[$key]);
            }
        }
        if (empty($data['cost'])){
            $data['cost'] = $data['nominal_value'];
        }
        // Проверка наличия обязательных полей и наличия требуемого поставщика
        if (count(array_intersect_key($data, self::$nom_required_fields)) != count(self::$nom_required_fields) || !isset($this->providers[$data['provider_id']])){
            return FALSE;
        }
        $id = \App\Builder::getInstance()->getDB()->query('INSERT INTO `' . self::TABLE_GIFTCARD_NOMINAL . '` SET ?a', $data);
        if (empty($id)){
            return FALSE;
        }
        // Дозаполняем пустые поля
        $data['id'] = $id;
        $emptyKeys = array_diff(array_keys($data), self::$nom_load_fields);
        foreach($emptyKeys as $key){
            $data[$key] = NULL;
        }
        if (!empty($data['image_id'])){
            $data['image'] = \Models\ImageManagement\Image::getById($data['image_id']);
        }
        $this->nominals[$id] = $data;
        $this->nom2provider[$id] = $data['provider_id'];
        return $data;
    }
    
    public function editNominal($id, $data){
        if (!isset($this->nominals[$id])){
            return FALSE;
        }
        if (isset($data['provider_id']) && !isset($this->providers[$data['provider_id']])){
            return FALSE;
        }
        if (!empty($data['image'])){
            $image_id = $this->nominals[$id]['image_id'];
            if (empty($image_id)){
                $image = \Models\ImageManagement\Image::add($data['image']);
                $data['image_id'] = $image['id'];
            } else {
                $image = \Models\ImageManagement\Image::getById($image_id);
                $image->reload($data['image']);
            }
        }
        foreach($data as $key=>$value){
            if (!in_array($key, self::$nom_update_fields)){
                unset($data[$key]);
            } else {
                $this->nominals[$id][$key] = $value;
            }
        }
        \App\Builder::getInstance()->getDB()->query('UPDATE `' . self::TABLE_GIFTCARD_NOMINAL . '` SET ?a WHERE `id` = ?d', $data, $id);
        return $this->nominals[$id];
    }
    
    public function getNominal($id){
        if (!isset($this->nominals[$id])){
            return NULL;
        }
        $nominal = $this->nominals[$id];
        $provider = $this->providers[$nominal['provider_id']];
        unset($provider['id']);
        return array_merge($nominal, $provider);
    }
    
    public function getNominalList($provider_id = NULL){
        $nom_ids = empty($provider_id) ? array_keys($this->nominals) : array_keys($this->nom2provider, $provider_id);
        $result = array();
        foreach($nom_ids as $id){
            $result[$id] = $this->getNominal($id);
        }
        return $result;
    }
    
    public function deleteNominal($id){
        if (!isset($this->nominals[$id])){
            return FALSE;
        }
        $image_id = $this->nominals[$id]['image_id'];
        if (!empty($image_id)){
            \Models\ImageManagement\Image::del($image_id);
        }
        \App\Builder::getInstance()->getDB()->query('DELETE FROM `' . self::TABLE_GIFTCARD_NOMINAL . '` WHERE `id` = ?d', $id);
        unset($this->nominals[$id]);
        unset($this->nom2provider[$id]);
        return TRUE;
    }
    /**
     * Импорт кодов подарочных сертификатов из массива
     * @param int $provider_id id поставщика
     * @param array $card_list список импортируемых карт array( array('code' => , 'nominal_value' => , 'expire_date' => ), ...)
     * @return array id list
     */
    public function importCards($provider_id, $card_list, &$failure = array()){
        $prov_ids = array_keys($this->nom2provider, $provider_id);
        $nom2id = array();
        foreach($prov_ids as $nom_id){
            $nom2id[$this->nominals[$nom_id]['nominal_value']] = $nom_id;
        }
        $ids = array();
        foreach($card_list as $card_info){
            if (empty($nom2id[$card_info['nominal_value']])){
                $failure[] = array(
                    'card' => $card_info,
                    'msg' => 'unknown_nominal'
                );
            } else{
                $card_info['nominal_id'] = $nom2id[$card_info['nominal_value']];
                $id = GiftCard::createCard($card_info);
                if ($id === FALSE){
                    $failure[] = array(
                        'card' => $card_info,
                        'msg' => 'duplicate_record'
                    );
                }
                if (!empty($id)){
                    $ids[] = $id;
                }
            }
        }
        return GiftCard::factory($ids);
    }
    
    // Работа с заявками на списание бонусов
    
    /**
     * Создание заявки на получение подарочного сертификата
     * @param \App\Auth\Users\RegistratedUser $user Аккаунт пользователя
     * @param array $cards_count массив с указанием количества требуемых карт в формате array(nominal_id => count)
     */
    public function addRequest($user, $name, $cards_count){
        $db = \App\Builder::getInstance()->getDB();
        $request_id = $db->query('INSERT INTO `' . self::TABLE_GIFTCARD_REQUEST . '` SET `user_id` = ?d{, `inn` = ?d}, `name` = ?s, `creation_date` = NOW()', 
            $user['id'],
            !empty($user['inn']) ? $user['inn'] : $db->skipIt(),
            $name
        );
        $req_nominals = array();
        foreach($cards_count as $nominal_id=>$count){
            if ($count == 0){
                continue;
            }
            $req_nominals[$nominal_id] = array(
                'nominal' => $this->getNominal($nominal_id),
                'count' => $count
            );
            $db->query('INSERT INTO `' . self::TABLE_GIFTCARD_REQUEST_ITEMS . '` SET `request_id` = ?d, `nominal_id` = ?d, `count` = ?d', $request_id, $nominal_id, $count);
        }
        $mail = new \LPS\Container\WebContentContainer('mails/requestGiftCertificate.tpl');
        $mail->add('nominals', $req_nominals)
             ->add('user', $user)
             ->add('owner_name', $name)
             ->add('request_id', $request_id);
        $emails = \Models\SiteConfigManager::getInstance()->get('giftcard_request');
        $alt_name = (!empty($user) && !empty($user['name'])) ? $user['name'] : 'none';
        $alt_phone = !empty($user) ? $user['phone'] : NULL;
        $alt_email = (!empty($user)) ? $user['email'] : NULL;
        Email::send($mail, !empty($emails) ? explode(',', str_replace(' ', '', $emails)) : \LPS\Config::getParametr('email', 'to'));
        Email::log(array('name' => $name, 'email' => $user['email'], 'type' => 'bonus'), array('id' => $request_id));
        return TRUE;
            
    }
    /**
     * 
     * @param int $id
     * @return 
     */
    public function getRequest($id){
        $result = $this->getRequestsList(array('ids' => array($id)));
        return !empty($result) ? reset($result) : NULL;
    }
    /**
     * 
     * @param type $params
     * @return type
     */
    public function getRequestsList($params = array()){
        $db = \App\Builder::getInstance()->getDB();
        if (!empty($params['filter'])){
            
        }
        // SELECT `i`.`request_id` as `id` FROM `giftcard_request_items` as `i` LEFT JOIN `giftcard_list` AS `l` ON `i`.`nominal_id` = `l`.`nominal_id` WHERE `l`.`nominal_id` IS NULL GROUP BY `i`.`request_id`
        $rows = $db->query('SELECT `r`.`id`, `r`.`user_id`, `r`.`name`, `r`.`status`, `r`.`creation_date`, `r`.`processing_date`, `r`.`comment`, '
                . 'GROUP_CONCAT(CONCAT_WS(":", `i`.`nominal_id`, `i`.`count`) ORDER BY `n`.`nominal_value` ASC SEPARATOR ",") AS `card_list` '
                . 'FROM `giftcard_request` AS `r` INNER JOIN `giftcard_request_items` AS `i` ON `r`.`id` = `i`.`request_id`'
                . ' INNER JOIN `giftcard_nominal` AS `n` ON `i`.`nominal_id` = `n`.`id`'
                . ' WHERE 1'
                . '{ AND `r`.`id` IN (?i)}'
                . '{ AND `r`.`status` = ?s}'
                . ' GROUP BY `i`.`request_id`'
                . ' ORDER BY `r`.`creation_date` DESC',
                !empty($params['ids']) ? $params['ids'] : $db->skipIt(),
                (!empty($params['status']) && $params['status'] != 'all') ? $params['status'] : $db->skipIt()
        )->select('id');
        if (empty($rows)){
            return array();
        }
        foreach($rows as $id=>$row){
            $cards_count = explode(',', $row['card_list']);
            $card_list = array();
            foreach($cards_count as $cc){
                list($nominal_id, $count) = explode(':', $cc);
                $card_list[$nominal_id] = array(
                    'nominal_id' => $nominal_id,
                    'nominal' => $this->getNominal($nominal_id),
                    'count' => $count
                );
            }
            $rows[$id]['card_list'] = $card_list;
            $rows[$id]['user'] = \App\Auth\Users\Factory::getInstance()->getUser($row['user_id']);
        }
        return $rows;
    }
    /**
     * 
     * @param type $id
     * @param type $card_ids
     * @return boolean
     */
    public function approveRequest($id, $card_ids, $comment = NULL, &$errors = array()){
        $request = $this->getRequest($id);
        if (empty($request)){
            $errors['request'] = 'not_found';
            return FALSE;
        }
        $selected_cards = array();
        $for_activation = array();
        $need_bonus = 0;
        // Сбор списка карт для выдачи и проверка соответствия количества
        foreach($request['card_list'] as $nom){
            $need_bonus += $nom['nominal']['cost'] * $nom['count'];
            $cards = GiftCard::search(array('ids' => $card_ids, 'nominal_id' => $nom['nominal_id'], 'not_assigned' => true));
            if (count($cards) != $nom['count']) {
                $errors['card'] = (count($cards) == 0) ? 'not_available' : 'insufficent_count';
                break;
            } else {
                $selected_cards[$nom['nominal_id']] = $cards;
                $for_activation = array_merge($for_activation, $cards);
            }
        }
        if ($need_bonus > $request['user']['bonus']){
            $errors['bonus'] = 'insufficient_bonus';
        }
        if (empty($errors)){
            foreach($for_activation as $card){
                $card->assignCard($request);
            }
        
            $user = $request['user'];
            $emails = array($user['email']);

            $mail_template = new \LPS\Container\WebContentContainer('mails/giftcardBonus.tpl');
            $mail_template->add('request', $request)
                 ->add('issued_cards', $selected_cards);
            \Models\Email::send($mail_template, $emails);
            $db = \App\Builder::getInstance()->getDB();
            $db->query('UPDATE `' . self::TABLE_GIFTCARD_REQUEST . '` SET `status` = "complete", `processing_date` = NOW(){, `comment` = ?s} WHERE `id` = ?d', 
                    !empty($comment) ? $comment : $db->skipIt(), $id);
            return TRUE;
        } else {
            return FALSE;
        }
    }
    /**
     * 
     * @param type $id
     * @param type $comment
     * @return boolean
     */
    public function discardRequest($id, $comment = NULL, &$errors = array()){
        $db = \App\Builder::getInstance()->getDB();
        $request = $this->getRequest($id);
        if (empty($request)){
            $errors['request'] = 'not_found';
            return FALSE;
        }
        $db->query('UPDATE `' . self::TABLE_GIFTCARD_REQUEST . '` SET `status` = "discard", `processing_date` = NOW(){, `comment` = ?s} WHERE `id` = ?d', 
                !empty($comment) ? $comment : $db->skipIt(), $id);
        return TRUE;
    }
    
}
