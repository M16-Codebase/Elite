<?php
/**
 * Description of View
 *
 * @author olga
 */
namespace Modules\Mails;
use Models\CatalogManagement\Positions\Order;
use Models\CatalogManagement\ProductConfig;
use Models\CatalogManagement\Search\CatalogSearch;
class View extends \LPS\AdminModule{
    public function index(){
        return $this->notFound();
    }
    public function registration(){
        $user = $this->account->getUser();
        $this->getAns()->add('new_pass', 'PaSsWoRd')
            ->add('user', $user)
            ->add('user_email', 'nanana@nanana.ru')
            ->setTemplate('mails/registration.tpl');
        return \Models\Email::send($this->getAns(), array('nanana@nanana.ru' => ''), null, null, array(), true);
    }

    public function changePass(){
        $user = $this->account->getUser();
        $this->getAns()->add('new_pass', 'PaSsWoRd')
            ->add('user', $user)
            ->add('user_name', 'Вася')
            ->add('user_surname', 'Пупкин')
            ->add('user_email', 'nanana@nanana.ru')
            ->setTemplate('mails/changePass.tpl');
        return \Models\Email::send($this->getAns(), array('nanana@nanana.ru' => ''), null, null, array(), true);
    }

    public function passremind(){
        $user = $this->account->getUser();
        $this->getAns()->add('user', $user)
                ->add('hash', '12345678876543211234567887654321')
                ->setTemplate('mails/passremind.tpl');
        return \Models\Email::send($this->getAns(), array($user['email'] => ''), null, null, array(), true);
    }
}
?>
