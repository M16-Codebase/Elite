<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 20.07.15
 * Time: 21:40
 */

namespace Models\CatalogManagement\Positions;


use App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use App\Configs\Settings as SettingsConfig;
use Models\CatalogManagement\Properties;
use Models\CatalogManagement\Type;
use Models\FilesManagement\File;

class Feedback extends \Models\CatalogManagement\Item
{
    const FORM_HANDLER_URL = '/feedback/makeRequest/';
    const MAIL_TEMPLATE_DIR = 'mails/feedback/';
    /**
     * Свои хелперы
     * @var array
     */
    static protected $dataProviders = array();
    static protected $dataProvidersByFields = array();
    /**
     * @var array список полей, которые не попадают в форму
     */
    static protected $ignoreFormFields = array(
        FeedbackConfig::KEY_FEEDBACK_NUMBER,
        FeedbackConfig::KEY_FEEDBACK_REFERRER_URL,
        FeedbackConfig::KEY_FEEDBACK_STATUS
    );

    /**
     * Отправка заявки с сайте
     * @param string $type — тип заявки (callback, feedback, vacancy etc)
     * @param array $post_data
     * @param array $files
     * @param array $errors
     * @return bool|\Models\CatalogManagement\Item
     */
    public static function make($type, $post_data = array(), $files = array(), &$errors = array()){
        $catalog = Type::getByKey(CatalogConfig::FEEDBACK_KEY);
        $feedback_category = Type::getByKey($type, $catalog['id']);
        if (empty($feedback_category)){
            $errors[] = array(
                'key' => 'feedbackType',
                'error' => \Models\Validator::ERR_MSG_INCORRECT
            );
        } else {
            if (!empty($post_data['subscr'])) {
                $subscriber_data = array(
                    'name' => !empty($post_data['author']) ? $post_data['author'] : '',
                    'email' => !empty($post_data['email']) ? $post_data['email'] : ''
                );
            }
            $propValues = array();
            $properties = $feedback_category->getProperties();
            $feedback_category->getProperties();
            $file_objects = array();
            $attach_files = array();
            foreach($properties as $prop) {
                if ($prop instanceof \Models\CatalogManagement\Properties\View) {
                    continue;
                }
                $key = $prop['key'];
                if ($key == FeedbackConfig::KEY_FEEDBACK_STATUS) {
                    $val = NULL;
                    foreach($prop['values'] as $v){
                        if ($v['key'] == FeedbackConfig::STATUS_NEW){
                            $val = $v['id'];
                            break;
                        }
                    }
                    $propValues[$key] = array(0 => array(
                        'val_id' => NULL,
                        'value' => $val
                    ));
                } elseif ($prop['data_type'] != \Models\CatalogManagement\Properties\File::TYPE_NAME) {
                    if (isset($post_data[$key])) {
                        if (is_array($post_data[$key])) {
                            $propValues[$key] = array();
                            foreach($post_data[$key] as $value) {
                                $propValues[$key][] = array(
                                    'val_id' => NULL,
                                    'value' => $value
                                );
                            }
                        } else {
                            $propValues[$key] = array(0 => array(
                                'val_id' => NULL,
                                'value' => $post_data[$key]
                            ));
                        }
                    } else {
                        $propValues[$key] = array(0 => array(
                            'val_id' => NULL,
                            'value' => NULL
                        ));
                    }
                } elseif (!empty($files[$key])) {
                    $file_objects[$key] = File::add('', $files[$key], $err);
                    $propValues[$key] = array(0 => array('val_id' => NULL, 'value' => $file_objects[$key]));
                    $f = File::getById($file_objects[$key]);
                    $attach_files[$key] = array('file_name' => $files[$key]->getClientOriginalName(), 'url' => $f->getUrl('absolute'));
                }

            }
            $item_id = self::create($feedback_category['id'], self::S_TMP, $propValues, $errors);
            if (!empty($subscriber_data) && empty($post_data['email'])) {
//                $errors[] = array(
//                    'key' => 'email',
//                    'error' => \Models\Validator::ERR_MSG_EMPTY
//                );
            }
            if ($item_id && empty($errors)){
                $item = self::getById($item_id);
                $item->update(array('status' => self::S_PUBLIC), $propValues, $errors);
                $mail = new \LPS\Container\WebContentContainer(self::MAIL_TEMPLATE_DIR . $feedback_category['key'] . '.tpl');
                $mail->add('item', $item);
                if ($feedback_category['key'] == FeedbackConfig::TYPE_APART_REQUEST) {
                    $emails = array();
                    if (!empty($post_data['complex']) || !empty($post_data['apartments'])) {
                        $emails = \Models\SiteConfigManager::getInstance()->get(FeedbackConfig::TYPE_APART_REQUEST_PRIMARY, \App\Configs\CatalogConfig::CONFIG_NOTIFICATION_KEY);
                    }
                    if (!empty($post_data['apartments_resale'])) {
                        $emails = !empty($emails)
                            ? array_merge($emails, \Models\SiteConfigManager::getInstance()->get(FeedbackConfig::TYPE_APART_REQUEST_RESALE, \App\Configs\CatalogConfig::CONFIG_NOTIFICATION_KEY))
                            : \Models\SiteConfigManager::getInstance()->get(FeedbackConfig::TYPE_APART_REQUEST_RESALE, \App\Configs\CatalogConfig::CONFIG_NOTIFICATION_KEY);
                    }
                    $emails = !empty($emails) ? array_unique($emails) : array();
                } else {
                    $emails = \Models\SiteConfigManager::getInstance()->get($feedback_category['key'], \App\Configs\CatalogConfig::CONFIG_NOTIFICATION_KEY);
                }
                // Пока прикроем отправку писем с локалки
                if (!\LPS\Config::isLocal()){
                    $site_config = \App\Builder::getInstance()->getSiteConfig(\App\Segment::getInstance()->getDefault()['id']);
                    \Models\Email::send($mail, !empty($emails) ? $emails : \LPS\Config::getParametr('email', 'to'),
                        $site_config[SettingsConfig::KEY_LETTER_SENDER_EMAIL], $site_config[SettingsConfig::KEY_LETTER_SENDER_NAME], $attach_files);
                }
                if (!empty($subscriber_data) && !empty($subscriber_data['email'])) {
                    \Models\SubscribeManagement\SubscribeController::getInstance()->setMember($subscriber_data);
                }
                return $item;
            } elseif (!empty($file_objects)) {
                foreach($file_objects as $file_id){
                    \Models\FilesManagement\File::del($file_id);
                }
            }
        }
        return FALSE;
    }

    /**
     * Установка статуса заявки
     * @param bool $status — TRUE - processed, FALSE - rejected
     */
    public function setStatus($status){
        $status_key = $status ? FeedbackConfig::STATUS_PROCESSED : FeedbackConfig::STATUS_REJECTED;
        $value = NULL;
        foreach($this['properties'][FeedbackConfig::KEY_FEEDBACK_STATUS]['values'] as $v){
            if ($v['key'] == $status_key){
                $value = $v['id'];
                break;
            }
        }
        if (!empty($value)) {
            $this->updateValues(array(FeedbackConfig::KEY_FEEDBACK_STATUS => array(0 => array('val_id' => NULL, 'value' => $value))));
        }
    }

    public static function getForm($feedbackType, $hidden_fields_values = array()){
        $catalog = Type::getByKey(CatalogConfig::FEEDBACK_KEY);
        $feedback_category = Type::getByKey($feedbackType, $catalog['id']);
        if (empty($feedback_category)){
            throw new \ErrorException("Неизвестный тип заявки #${feedbackType}");
        }
        $properties = $feedback_category->getProperties();
        $form_construct = \Models\FormConstruct::getInstance();
        $form_fields = array();
        $hidden_fields = array();
        foreach($properties as $prop){
            if (in_array($prop['key'], self::$ignoreFormFields)){
                // Пропускаем поля, не нужные в форме
                continue;
            }
            if ($prop instanceof Properties\CatalogPosition){
                $hidden_fields[$prop['key']] = '';
            } else {
                $field_type = NULL;
                switch($prop['data_type']){
                    case Properties\Text::TYPE_NAME:
                        $field_type = 'textarea';
                        break;
                    case Properties\File::TYPE_NAME:
                    case Properties\Image::TYPE_NAME:
                        $field_type = 'file';
                        break;
                    case Properties\Flag::TYPE_NAME:
                        $field_type = 'checkbox';
                        break;
                    default:
                        $field_type = 'text';
                }
                if (!empty($field_type)){
                    $form_fields[$prop['key']] = array(
                        'type' => $field_type,
                        'title' => $prop['title']
                    );
                    if ($field_type == 'checkbox'){
                        $form_fields[$prop['key']]['value'] = '1';
                        $form_fields[$prop['key']]['default_value'] = '0';
                        $form_fields[$prop['key']]['label'] = '';
                    }
                }
            }
        }
        $form_data = array(
            'title' => $feedback_category['title'],
            'action' => static::FORM_HANDLER_URL,
            'antispam' => TRUE,
            'hidden_fields' => array(
                    'feedbackType' => $feedbackType
                ) + $hidden_fields,
            'fields' => $form_fields
        );
        if (!empty($hidden_fields_values)){
            foreach($hidden_fields_values as $k => $v){
                $form_data['hidden_fields'][$k] = $v;
            }
        }
        return $form_construct->getForm($form_data);
    }

}