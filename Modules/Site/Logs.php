<?php
/**
 * Description of Logs
 *
 * @author olga
 */
namespace Modules\Site;
class Logs extends \LPS\AdminModule{
    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        if ($this->account instanceof \App\Auth\Account\SuperAdmin){
            return true;
        }
        return false;
    }
    public function index(){
        return $this->notFound();
    }
    public function clear(){
        $errors_config = \App\Builder::getInstance()->getConfig()->getErrorLogInfo();
        $file = $errors_config['file'];
        if (file_exists($file)){
            file_put_contents($file, '');
        }
        $autoload_log_file = \LPS\Config::getRealDocumentRoot() . '/' . \LPS\Config::getParametr('Dir','logs').'/autoload.log';
        if (file_exists($autoload_log_file)){
            file_put_contents($autoload_log_file, '');
        }
        return $this->redirect($this->request->server->get('HTTP_REFERER'));
    }
    public function get(){
        $errors_config = \App\Builder::getInstance()->getConfig()->getErrorLogInfo();
        $file = $errors_config['file'];
        header('Content-type: application/octet-stream');
        header('Content-Disposition: attachment; filename=errors.log');
        return readfile($file);
    }
}