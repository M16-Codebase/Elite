<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.07.15
 * Time: 15:58
 */

namespace App\Configs;


class ReviewConfig {
    /** Ключи типов */
    const REVIEWS_KEY = 'reviews';
    const QUESTIONS_KEY = 'questions';

    /** Общие ключи пропертей */
    const CREATION_DATE = 'creation_date';
    const STATUS = 'review_status';
    const PRODUCT = 'product';
    const TEXT = 'text';
    const AUTHOR = 'author';
    const CITY = 'city';
    const GENDER = 'gender';
    const ANSWER = 'answer';
    /** Ключи пропертей отзыва */
    const SOURCE = 'source';
    const RECOMMENDATION = 'recommendation';
    const AGE_GROUP = 'age_group';
    const DURATION = 'duration';
    /** Ключи пропертей вопроса */
    const EMAIL = 'email';
    const AGE = 'age';

    const STATUS_NEW = 'new';
    const STATUS_ACCEPT = 'accept';
    const STATUS_REJECT = 'reject';

    const GENDER_MALE = 'male';
    const GENDER_FEMALE = 'female';

    const SOURCE_SITE = 'site';
    const SOURCE_MANAGER = 'manager';
}