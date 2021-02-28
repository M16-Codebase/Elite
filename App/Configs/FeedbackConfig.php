<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 20.07.15
 * Time: 20:07
 */

namespace App\Configs;


class FeedbackConfig
{
    /**
     * Длина номера обращения (количество цифр)
     */
    const NUMBER_LENGTH = 5;

    const TYPE_FEEDBACK = 'feedback';       // Форма обратной связи
    const TYPE_CALLBACK = 'callback';       // Форма заказа звонка
    const TYPE_VACANCY = 'vacancy';         // Отклик на вакансию
    const TYPE_APPLICANT = 'applicant';     // Анкета соискателя
    const TYPE_APART_REQUEST = 'apart_request';
    const TYPE_APART_REQUEST_PRIMARY = 'apart_primary';
    const TYPE_APART_REQUEST_RESALE = 'apart_resale';
    const TYPE_OWNER = 'owner';
    const TYPE_FLAT_SELECTION = 'flat_selection';
    const TYPE_VIEW_APARTMENTS = 'view_apartments';

    const KEY_FEEDBACK_NUMBER = 'number';
    const KEY_FEEDBACK_AUTHOR = 'author';
    const KEY_FEEDBACK_EMAIL = 'email';
    const KEY_FEEDBACK_PHONE = 'phone';
    const KEY_FEEDBACK_REFERRER_URL = 'referrer_url';
    const KEY_FEEDBACK_STATUS = 'treat_status';
    const KEY_FEEDBACK_MESSAGE = 'message';
    const KEY_VACANCY_ENTITY = 'vacancy';
    const KEY_VACANCY_SUMMARY = 'summary';
    const KEY_VACANCY_SUMMARY_LINK = 'summary_link';
    const KEY_REQUEST_COMPLEX = 'complex';
    const KEY_REQUEST_APARTMENT = 'apartments';
    const KEY_REQUEST_APARTMENT_RESALE = 'apartments_resale';

    const KEY_SELECTION_DISTRICT = 'district';
    const KEY_SELECTION_BED_NUMBER = 'bed_number';
    const KEY_SELECTION_AREA = 'area';
    const KEY_SELECTION_PRICE = 'price';
    const KEY_SELECTION_PRIMARY = 'primary';
    const KEY_SELECTION_RESALE = 'resale';
    const KEY_SELECTION_SPECIES = 'species';

    const KEY_OWNER_ESTATE_TYPE = 'estate_type';
    const KEY_OWNER_ADDRESS = 'address';
    const KEY_OWNER_BED_NUMBER = 'bed_number';
    const KEY_OWNER_AREA = 'area';
    const KEY_OWNER_PRICE = 'price';
    const KEY_OWNER_SPECIES = 'species';

    const STATUS_NEW = 'new';
    const STATUS_PROCESSED = 'processed';
    const STATUS_REJECTED = 'rejected';

}