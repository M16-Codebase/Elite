<?php
/**
 * Description of Msg
 *
 * @author olga
 */
namespace Modules\Profile;
class Msg extends \LPS\WebModule{
    /** @var \Models\Messenger */
    private $m;
    public function init(){
        $this->m = \Models\Messenger::getInstance();
    }
    /**
     * Проверка прав
     * @param string $action
     * @return boolean 
     */
    public function isPermission($action){
        if (!empty($this->account) && !($this->account instanceof \App\Auth\Account\Guest)){
            return true;
        }
        return false;
    }
    /**
     * Отправить сообщение
     * @return type 
     */
    public function create(){
        $sended_form = $this->request->request->get('send');
        if (!empty($sended_form)){
            $from = $this->account->getUser()->getId();
            $to = \App\Auth\Users\Factory::getInstance()->getUser($this->request->request->get('to'));
            $title = !empty($_POST['title']) ? $_POST['title'] : false;
            $text = !empty($_POST['text']) ? $_POST['text'] : false;
            if (!empty($from) && !empty($to)){
                $this->m->create($from, $to, $title, $text);
                //$to->email();
                $prev_url = ''; //todo
                $response = $this->redirect($prev_url);
                return $response;
            }else{
                throw new \LogicException('Некому или неоткого отсылать сообщение.');
            }
        }
    }
    /**
     * Просмотр сообщений
     */
    public function my(){
        $this->getAns()->add('inbox', $this->m->get('inbox'));
        $this->getAns()->add('outbox', $this->m->get('outbox'));
    }
    /**
     * Удалить сообщение
     */
    public function del(){
        $folder = $this->request->request->get('folder');
        $who = $folder == 'inbox' ? 'to' : 'from';
        $this->m->delete($who, $mes_id);
        return;
    }
}

?>
