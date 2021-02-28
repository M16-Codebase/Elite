<?php
namespace LPS;
/**
 * Хранилище событий
 *
 * @author olga
 */
class StoreEvents {
    const STORE_START                   = 'start';
    const STORE_PRE_ACTION_WORK         = 'preActionWork';//Вызывается до работы метода модуля, до проверок действия на доступ, но после проверки на существование
    const STORE_AFTER_DEFAULT_ANS_INIT  = 'afterDefaultAnsInit';//Вызывается только если используется стандартный ответ
    const STORE_AFTER_MODULE_WORK       = 'afterModuleWork';//Действия после работы модуля.
    const STORE_PRE_TEMPLATER_WORK      = 'preTemplaterWork';//Действия перед работой шаблона. Будет срабатывать только если используется шаблон.
    const STORE_FINISH                  = 'finish';
}

?>
