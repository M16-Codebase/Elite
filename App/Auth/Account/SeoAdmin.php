<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 22.10.14
 * Time: 18:44
 */

namespace App\Auth\Account;


class SeoAdmin extends Admin{
    protected $access_roles = array('SeoAdmin');
}