<?php
/**
 * Created by PhpStorm.
 * User: hokum_gru
 * Date: 30.04.15
 * Time: 15:44
 */

namespace Models\CatalogManagement\Properties;

use App\Auth\Users\RegistratedUser as UserEntity;
use App\Auth\Users\Factory as UserFactory;
class User extends Entity{
    const TYPE_NAME = 'user';
    const FILTER_VIEW_KEY = \App\Configs\StaffConfig::KEY_EMAIL;
    public function getFinalValue($v, $segment_id = NULL){
        if (!empty($v)){
            UserFactory::getInstance()->prepare(is_numeric($v) ? array($v) : $v);
        }
        return $v;
    }

    public function getCompleteValue($v, $segment_id = NULL){
        if ($this['set']){
            return empty($v['value']) ? array() : UserFactory::getInstance()->factory($v['value']);
        }
        return empty($v['value']) ? NULL : UserFactory::getInstance()->getUserById($v['value']);
    }
} 