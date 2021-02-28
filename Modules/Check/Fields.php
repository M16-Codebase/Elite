<?php
namespace Modules\Check;
class Fields extends \LPS\WebModule{
    /**
     * @ajax
     */
    public function index(){
        $checker = \Models\Validator::getInstance();
        $field = $this->request->request->get('field');
        $value = $this->request->request->get('value');
        $type = $this->request->request->get('type');
        $options = $this->request->request->get('options');
        $value = $checker->checkValue($value, $type, $error, $options);
        if (is_null($value)){
            return $error[$field];
        }else{
            return '';
        }
    }
}
?>
