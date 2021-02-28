<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 31.08.15
 * Time: 11:36
 */

namespace App\Configs\Init\Catalog;


use App\Configs\CatalogConfig;
use App\Configs\StaffConfig;
use Models\CatalogManagement\Properties;
use Models\Segments\Lang;

class StaffListInit
{
    public static function getInitData()
    {
        return array(
            0 . CatalogConfig::CATALOG_KEY_STAFF_LIST => array(
                'data' => array(
                    'title' => array(
                        Lang::LANG_KEY_RU => 'Справочник сотрудников',
                        Lang::LANG_KEY_EN => 'Staff list'
                    ),
                    'key' => CatalogConfig::CATALOG_KEY_STAFF_LIST,
                    'parent_id' => 1,
                    'allow_children' => 0,
                    'item_prefix' => NULL,
                    'only_items' => 1,
                    'allow_item_url' => NULL,
                    'allow_item_property' => 1,
                    'allow_segment_properties' => 1
                ),
                'item_title' => 'Сотрудник',
                'properties' => array(
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Фамилия',
                            Lang::LANG_KEY_EN => 'Surname'
                        ),
                        'key' => StaffConfig::KEY_SURNAME,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1,
						'necessary' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Имя',
                            Lang::LANG_KEY_EN => 'Name'
                        ),
                        'key' => StaffConfig::KEY_NAME,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1,
						'necessary' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Отчество',
                            Lang::LANG_KEY_EN => 'Patronymic'
                        ),
                        'key' => StaffConfig::KEY_PATRONYMIC,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1,
						'necessary' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Должность',
                            Lang::LANG_KEY_EN => 'Position'
                        ),
                        'key' => StaffConfig::KEY_POSITION,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
                        'segment' => 1,
						'necessary' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Телефон',
                            Lang::LANG_KEY_EN => 'Phone'
                        ),
                        'key' => StaffConfig::KEY_PHONE,
                        'data_type' => Properties\String::TYPE_NAME,
                        'fixed' => 1,
						'necessary' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Электронная почта',
                            Lang::LANG_KEY_EN => 'E-mail'
                        ),
                        'key' => StaffConfig::KEY_EMAIL,
                        'data_type' => Properties\String::TYPE_NAME,
                        'validation' => array(
                            'mode' => 'preset',
                            'preset' => 'email'
                        ),
                        'fixed' => 1,
						'necessary' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Фотография',
                            Lang::LANG_KEY_EN => 'Photo'
                        ),
                        'key' => StaffConfig::KEY_PHOTO,
                        'data_type' => Properties\Image::TYPE_NAME,
                        'fixed' => 1,
						'necessary' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Показывать в контактах',
                            Lang::LANG_KEY_EN => 'Show in contacts'),
                        'key' => StaffConfig::KEY_SHOW_IN_CONTACTS,
                        'data_type' => Properties\Flag::TYPE_NAME,
                        'values' => array(
                            'yes' => array(Lang::LANG_KEY_RU => 'Да', Lang::LANG_KEY_EN => 'Yes'),
                            'no' => array(Lang::LANG_KEY_RU => 'Нет', Lang::LANG_KEY_EN => 'No')
                        ),
                        'fixed' => 1
                    ),
                    array(
                        'title' => array(
                            Lang::LANG_KEY_RU => 'Ключ для фильтра',
                            Lang::LANG_KEY_EN => 'Key for filter'
                        ),
                        'key' => 'title',
                        'data_type' => Properties\View::TYPE_NAME,
                        'fixed' => 2,
                        'values' => '{'.StaffConfig::KEY_EMAIL.'}'
                    )
                )
            )
        );
    }
}