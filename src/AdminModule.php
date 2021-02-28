<?php
namespace LPS;
use App\Configs\AccessConfig;

/**
 * Description of AdminModule
 *
 * @author mac-proger
 */
abstract class AdminModule extends WebModule{

    const DEFAULT_ACCESS = AccessConfig::ACCESS_ADMIN_MODULE;
    /*
     * метод вызывается только для определения глобальных переменных в не конечных в цепочке иерархии классах
     */
    protected function _init(){
        //для шаблонов, чтобы всегда знать о том, что страница админская. можно переопределить
        $this->getAns()->add('admin_page', 1);
        $this->segment = \App\Segment::getInstance()->getDefault();
    }
    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        return $this->account instanceof \App\Auth\Account\Admin;
    }
}
