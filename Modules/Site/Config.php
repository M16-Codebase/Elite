<?php
/**
 * Description of Admin
 *
 * @author pochka
 */
namespace Modules\Site;
use App\Configs\SeoConfig;
use Models\AuthenticationManagement\SocialAuth;

class Config extends \LPS\AdminModule{
    
    const PARAMS_KEY_COMMON = 'global';
    const PARAMS_KEY_NOTIFICATIONS = 'notification';
    const PARAMS_KEY_CONTACTS = 'contacts';
	const PARAMS_KEY_SEO = 'seo';
    const PARAMS_KEY_SOCIAL_AUTH = 'social_auth';
    /** @var \Models\SiteConfigManager */
	protected $siteConfigManager = null;
	protected function init(){
		$this->siteConfigManager = \Models\TechnicalConfig::getInstance();
	}
	public function index($param_type = self::PARAMS_KEY_COMMON){
		return $this->notFound();
	}

    public function socialAuth(){
        $network_list = SocialAuth::getNetworksConfigList();
        $form_data = array();
        foreach ($network_list as $network_key => $v){
            $network_data = $this->request->request->get($network_key);
            if (!empty($network_data)){
                $this->siteConfigManager->set($network_key, self::PARAMS_KEY_SOCIAL_AUTH, $network_data, '', 'serialized');
                $need_redirect = TRUE; // если сохранили хоть один параметр нужен редирект
            }
            $form_data[$network_key] = $this->siteConfigManager->get($network_key, self::PARAMS_KEY_SOCIAL_AUTH);
        }
        if (!empty($need_redirect)){
            return $this->redirect($this->getModuleUrl() . 'socialAuth/');
        }
        foreach($network_list as $key => $network){
            if (!empty($form_data[$key])){
                foreach($form_data[$key] as $k => $v){
                    $network_list[$key][$k] = $v;
                }
            }
        }
        $this->getAns()->add('networks_list', $network_list)
            ->setFormData($form_data);
    }
}