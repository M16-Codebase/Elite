<?php
namespace App\Auth\Users;
/**
 * Физлицо
 *
 * @author mac-proger
 */
class UserFiz extends User{
    protected static $edit_user_form_fields = array(
        'name' => array(
            'type' => 'text',
            'title' => 'Имя',
            'errors' => array(
                'empty' => 'Имя обязательно для заполнения'
            )
        ),
        'surname' => array(
            'type' => 'text',
            'title' => 'Фамилия',
            'errors' => array(
                'empty' => 'Фамилия обязательна для заполнения'
            )
        ),
        'phone' => array(
            'type' => 'text',
            'title' => 'Телефон',
            'errors' => array(
                'empty' => 'Телефон обязателен для заполнения'
            )
        ),
    );
}
