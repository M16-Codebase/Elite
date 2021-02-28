<?php
namespace Modules\Payment;

use App\Payment;
/**
 * Description of Main
 *
 * @author olya
 */
class Main extends \LPS\AdminModule{
	public function index(){
		$db = \App\Builder::getInstance()->getDB();
		$this->getAns()->add('pay_methods', Payment::getSystems())
			->add('groups', $db->query('SELECT * FROM `'.Payment::TABLE_PAY_GROUPS.'`')->select('id'));
	}
	
	public function setSystemUsed(){
		$this->setJsonAns()->setEmptyContent();
		$db = \App\Builder::getInstance()->getDB();
		$system_key = $this->request->request->get('key');
		$used = $this->request->request->get('used');
		$db->query('UPDATE `'.Payment::TABLE_PAY_TYPES.'` SET `used` = ?d WHERE `key` = ?s', !empty($used) ? 1 : 0, $system_key);
	}
	
	public function setSystemGroup(){
		$this->setJsonAns()->setEmptyContent();
		$db = \App\Builder::getInstance()->getDB();
		$system_key = $this->request->request->get('key');
		$group_id = $this->request->request->get('group_id');
		$db->query('UPDATE `'.Payment::TABLE_PAY_TYPES.'` SET `group_id` = ?d WHERE `key` = ?s', !empty($group_id) ? $group_id : 0, $system_key);
	}
}
