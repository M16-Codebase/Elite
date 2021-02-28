<?php
namespace Modules\Catalog;
use App\Configs\AccessConfig;
use \Models\CatalogManagement\Properties\Factory AS PropertyFactory;
use \Models\CatalogManagement\Type AS TypeEntity;
/**
 * Description of SuperAdmin
 *
 * @author olga
 */
class SuperAdmin extends \LPS\AdminModule{

    protected static $module_custom_permissions = array(
        AccessConfig::ROLE_SUPER_ADMIN => true
    );

    const DEFAULT_ACCESS = AccessConfig::ACCESS_DISALLOW_ALL;
    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        return $this->account instanceof \App\Auth\Account\SuperAdmin;
    }
    public function index(){
        return $this->notFound();
    }
    /**
     * Изменение параметра fixed у свойства
     * @return string
     */
    public function updatePropertyFixed(){
        $property_id = $this->request->request->get('id', $this->request->query->get('id'));
        $fixed = $this->request->request->get('fix', 0);
        if ($fixed == 3){
            $fixed = implode('.', array_keys($this->request->request->get('fields', $this->request->query->get('fields'))));
        }
        if (!empty($property_id)){
            PropertyFactory::getById($property_id)->updateFixed($fixed);
        }
        return '';
    }
    /**
     * попап фиксирования свойства
     */
    public function fixProperty(){
        $this->setAjaxResponse();
        $property_id = $this->request->request->get('id', $this->request->query->get('id'));
        $property = PropertyFactory::getById($property_id);
        $form_data = array();
        $fixed = $property['fixed'];
        if (!is_numeric($fixed)){
            $fixed = explode('.', $fixed);
            $form_data['fields'] = array_fill_keys($fixed, 1);//заполняет массив с ключами $fixed и значениями 1
            $form_data['fix'] = 3;
        }else{
            $form_data['fix'] = $fixed;
        }
        $form_data['id'] = $property_id;
        $this->getAns()->add('property', $property)->setFormData($form_data);
    }
    /**
     * Изменение параметра fixed у типа
     */
    public function updateTypeFixed(){
        $type_id = $this->request->request->get('type_id');
        $fixed = $this->request->request->get('fixed', 0);
        if (!empty($type_id)){
            TypeEntity::getById($type_id)->updateFixed($fixed);
        }
        return '';
    }

    /**
     * очищает весь кэш
     * @return type
     */
    public function clearItemsCache(){
        $this->db->query('DELETE FROM `'.\Models\CatalogManagement\CatalogPosition::TABLE_DATA_CACHE.'`');
        return $this->redirect('/site/');
    }
    /**
     * Пересоздать все view свойства
     * (если крон не работает, можно воспользоваться данной функцией)
     * если дофига товаров, то съест всё вермя. это скорее для отладки
     */
    public function recreateAllViews(){
        $default_segment = \App\Segment::getInstance()->getDefault();
        \Models\CatalogManagement\Catalog::recreateAllViews(100000000, $default_segment['id']);
        return $this->redirect($this->request->server->get('HTTP_REFERER'));
    }
	/**
	 * Ручной парсинг валют
	 * (если крон не работает, можно воспользоваться данной функцией)
	 * @return string
	 */
	public function parseCurrency(){
		\Models\Currency::parse();
		return $this->redirect($this->request->server->get('HTTP_REFERER'));
	}
}

?>
