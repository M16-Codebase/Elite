<?php
/**
 * Created by PhpStorm.
 * User: mac-proger
 * Date: 27.08.14
 * Time: 13:18
 */

namespace Models;


use LPS\Container\WebContentContainer;

class FormConstruct {
    const TEMPLATE = 'forms/form.tpl';
    private static $instance = NULL;

    public static function getInstance(){
        if (empty(self::$instance)){
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct(){}

    public function getForm(array $params){
        $container = new WebContentContainer(self::TEMPLATE);
        $container
            ->disablePersist()
            ->add('form_data', $params)->add('hash_salt_string', \LPS\Config::HASH_SOLT_STRING);
        return $container->getContent();
    }

    public static $form_data = array(
        'title' => 'Заголовок формы',
        'action' => '/action/link/',
        'method' => 'post',
        'class' => array('super-duper-form', 'test-form_constructor'),
        'antispam' => TRUE,
        'data' => array(
            'id' => 'kuischsche',
            'form-name' => 'ololo'
        ),
        'hidden_fields' => array(
            'fld_one' => 'fld_one_value',
            'fld_two' => 'fld_two_value',
            'fld_three' => array(
                'class' => 'hidden-field-class',
                'value' => 'fld_three_value'
            )
        ),
        'fields' => array(
            'email' => array(
                'type' => 'text',
                'title' => 'Электронная почта',
                'errors' => array(
                    'empty' => 'Поле email обязательно для заполнения',
                    'already_exists' => 'Пользователь с такой электронной почтой уже зарегистрирован'
                )
            ),
            array(
                'type' => 'group',
                'title' => 'Пароли джважды',
                'fields' => array(
                    'pass' => array(
                        'type' => 'password',
                        'title' => 'Пароль'
                    ),
                    'pass2' => array(
                        'type' => 'password',
                        'title' => 'Повторите пароль',
                        'description' => 'Для защиты от опечаток введите желаемый пароль повторно'
                    )
                )
            ),
            'cbx' => array(
                'type' => 'checkbox',
                'title' => 'Одиночный чекбокс',
                'label' => 'Можно клацнуть',
                'value' => '1',
                'default_value' => '0'
            ),
            'cbx2' => array(
                'type' => 'checkbox',
                'title' => 'Куча чекбоксов',
                'values' => array(
                    'one' => array(
                        'label' => 'Первый',
                        'value' => 'one'
                    ),
                    'two' => array(
                        'label' => 'Второй',
                        'value' => 'two'
                    ),
                    'three' => array(
                        'label' => 'Третий',
                        'value' => 'three'
                    )
                )
            ),
            'radio' => array(
                'type' => 'radio',
                'title' => 'Радиобатоны!!!!1111',
                'values' => array(
                    array(
                        'label' => 'Первый',
                        'value' => 'one'
                    ),
                    array(
                        'label' => 'Второй',
                        'value' => 'two'
                    ),
                    array(
                        'label' => 'Третий',
                        'value' => 'three'
                    )
                )
            ),
            'select' => array(
                'type' => 'select',
                'title' => 'Селект',
                'options' => array(
                    '' => 'Выберите',
                    1 => 'Первый',
                    2 => 'Второй'
                )
            )
        ),
        'buttons' => array(
            'submit' => 'Отправить',
            'clear' => 'Очистить'
        )
    );
} 