<?php
/**
 * Просмотр логов изменений
 *
 * @author olga
 */
namespace Modules\Logs;
use Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use Models\CatalogManagement\Type AS TypeEntity;
use Models\CatalogManagement\CatalogConfig;
use Models\CatalogManagement\Import\CSV as ImportCSV;
class View extends \LPS\AdminModule{
    const DEFAULT_ACCESS = \App\Configs\AccessConfig::ACCESS_NO_BROKERS;
    const PAGE_SIZE = 50;
    const IMPORT_LOGS_PAGE_SIZE = 50;
    public function index(){
        $this->logsList(TRUE);
    }
    
    public function logsList($inner = FALSE){
        if (!$inner){
            $this->setJsonAns();
        }
        $params = $this->getParams();
        $count = 0;
        $page = $this->request->query->get('page', $this->request->request->get('page', 1));
        if (!empty($params['entity_type']) || !empty($params['user_id'])){
            if (!empty($params['entity_type']) && preg_match('~catalog_([0-9]*)~', $params['entity_type'], $match)){
                $params['catalog_id'] = $match[1];
                $params['entity_type'] = array(\Models\CatalogManagement\Item::CATALOG_IDENTITY_KEY, \Models\CatalogManagement\Variant::CATALOG_IDENTITY_KEY);
            }
            if (!($this->account instanceof \App\Auth\Account\SuperAdmin)){
                $params['not_hidden'] = 2;
            }
            $logs = \Models\Logger::search($params, $count, ($page-1)*self::PAGE_SIZE, self::PAGE_SIZE);
        }else{
            $logs = array();
            $this->getAns()->add('empty_necessary_params', 1);
        }
        $user_ids = \Models\Logger::getUsers();
        $this->getAns()
            ->add('params', $params)
            ->add('logged_fields', $this->getLoggedFields($params))
            ->add('users', !empty($user_ids) ? \App\Auth\Users\Factory::getInstance()->getUsers(array('ids' => $user_ids)) : array())
            ->add('logs', $logs)
            ->add('pageNum', $page)
            ->add('pageSize', self::PAGE_SIZE)
            ->add('count', $count)
            ->add('segments', \App\Segment::getInstance()->getAll())
            ->add('catalogs', TypeEntity::getCatalogs())
            ->add('properties_key', \App\Configs\CatalogConfig::getPropertiesKeys());
    }
    
    private function getParams(){
        $params = array();
        $user_email = $this->request->query->get('email', $this->request->request->get('email'));
        $entity_type = $this->request->query->get('type');
        if (!empty($entity_type)){
            $params['entity_type'] = $entity_type;
            if ($params['entity_type'] == 'catalog'){
                $params['entity_type'] = array(
                    'item',
                    'variant',
                    'item_type',
                    'property',
                    'manuf'
                );
            }
        }
        if (!empty($user_email)){
            $user = \App\Auth\Users\Factory::getInstance()->getUser(NULL, array('email' => $user_email));
            if (!empty($user)){
                $params['user_id'] = $user['id'];
            }
        }
        $user_id = $this->request->request->get('user', $this->request->query->get('user'));
        if (!empty($user_id)){
            $params['user_id'] = $user_id;
        }
        $data = $this->request->query->all();
        if (!empty($data['time']['min']) && !empty($data['date']['min'])){
            $params['from'] = $data['date']['min'] . ' ' . $data['time']['min'] . ':00:00';
        }
        if (!empty($data['time']['max']) && !empty($data['date']['max'])){
            $params['to'] = $data['date']['max'] . ' ' . $data['time']['max'] . ':00:00';
        }
        if (empty($params['time']['min']) && empty($params['time']['max'])){
            if (!empty($data['date']['min'])){
                $params['from'] = $data['date']['min'] . ' ' . '00:00:01';
            }
            if (!empty($data['date']['max'])){
                $params['to'] = $data['date']['max'] . ' ' . '23:59:59';
            }
        }
        if (!empty($data['attr_id'])){
            $params['attr_id'] = $data['attr_id'];
        }
        if (!empty($data['entity_id'])){
            $params['entity_id'] = $data['entity_id'];
        }
        if (!empty($data['action'])){
            $params['type'] = $data['action'];
        }
        return $params;
    }
    private function getLoggedFields($params){
        $ip = array();
        if (!empty($params['entity_type']) && $params['entity_type'] == 'item' && !empty($params['entity_id'])){
            $item = \Models\CatalogManagement\Item::getById($params['entity_id']);
            if (!empty($item)){
                $item_properties = $item->getPropertyList();
                foreach ($item_properties as $p){
                    if ($p['default_prop']){
                        continue;
                    }
                    $ip[$p['id']] = $p['title'];
                }
            }
        }
        $vp = array();
        if (!empty($params['entity_type']) && $params['entity_type'] == 'variant' && !empty($params['entity_id'])){
            $variant = \Models\CatalogManagement\Variant::getById($params['entity_id']);
            if (!empty($variant)){
                $variant_properties = $variant->getPropertyList();
                foreach ($variant_properties as $p){
                    if ($p['default_prop']){
                        continue;
                    }
                    $vp[$p['id']] = $p['title'];
                }
            }
        }
        $logged_fields = array(
            'item_type' => \App\Configs\CatalogConfig::getFields('type'),
            'property' => \App\Configs\CatalogConfig::getFields('property'),
            'item' => $ip,
            'variant' => $vp,
            'file' => \App\Configs\FileConfig::getFields(),
            'post' => \App\Configs\PostConfig::getFields()
        );
        return $logged_fields;
    }

    public function property_changes(){
        $this->setJsonAns();
        $entity_id = $this->request->request->get('entity_id');
        $property_id = $this->request->request->get('property_id');
        $property = \Models\CatalogManagement\Properties\Factory::getById($property_id);
        if (empty($property)){
            $this->getAns()->addErrorByKey('exception', 'Property not found')->setEmptyContent();
            return;
        }
        $entity_type = $property['multiple'] ? 'variant' : 'item';
        $params = array('attr_id' => $property_id, 'entity_id' => $entity_id, 'entity_type' => $entity_type, 'type' => 'attr');
        $logs = \Models\Logger::search($params);
        $user_ids = array();
        if (!empty($logs)){
            foreach ($logs as $k => &$l){
                $l['additional_data'] = json_decode($l['additional_data'], true);
                if (!isset($l['additional_data']['complete_value'])){
                    unset($logs[$k]);//изменение комментов нам тут не надо, поэтому смотрим что есть
                }else{
                    $user_ids[] = $l['user_id'];
                }
            }
        }
        $users = \App\Auth\Users\Factory::getInstance()->getUsers(array('ids' => $user_ids));
        foreach ($logs as &$l){
            $l['user'] = !empty($users[$l['user_id']]) ? $users[$l['user_id']] : NULL;
        }
        $english_segment = \App\Builder::getInstance()->getSegmentByKey('en');
        $rus_segment = \App\Builder::getInstance()->getSegmentByKey('ru');
//		echo '<pre>';
//		print_r($logs);
//		echo '</pre>';
        $this->getAns()->add('logs', $logs)
            ->add('property', $property)
            ->add('english_segment', $english_segment)
            ->add('rus_segment', $rus_segment);
    }
}
