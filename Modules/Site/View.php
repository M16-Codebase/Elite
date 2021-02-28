<?php
/**
 * Description of View
 *
 * @author pochka
 */
namespace Modules\Site;
class View extends \LPS\AdminModule{
    /**
     * Проверка прав
     * @param string $action
     * @return boolean
     */
    public function isPermission($action){
        if ($this->account instanceof \App\Auth\Account\Admin || $this->account instanceof \App\Auth\Account\Viewer){
            return true;
        }
        return false;
    }
	public function index(){}
	public function content(){}
    public function subscribers(){
        $subscribers = $this->db->query('SELECT * FROM `mails_news`')->select();
        $this->getAns()->add('subscribers', $subscribers);
    }
    /**
     *
     */
    public function currentTask(){
        $this->getCurrentTask(TRUE);
    }
    /**
     * @ajax
     */
    public function getCurrentTask($inner = FALSE){
        if (!$inner){
            $this->setAjaxResponse();
        }
        $this->getAns()->add('current_task', $this->db->query('SELECT * FROM `'.\Modules\CliModules\Cron::TABLE.'`')->getRow());
    }
}