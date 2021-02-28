<?php
namespace MysqlSimple;
/**
 * Интерфейс логирования запросов в базу данных
 */
interface LoggerInterface{
    /**
     * Функция логирования
     * @param string $sql исполняемый sql запрос с подставленными значениями
     * @param float $start_time точное время начала исполнения запроса
     * @param float $time точное время исполнения запроса
     * @param string $comment комментарий к запросу, созданный библиотекой DB
     * @param mixed(Result|int|FALSE) $ans ответ на запрос, созданный библиотекой DB. Число если не селект, и  
     * @param string $sql_source исходный sql запрос с подставленными значениями
     * @param array $values данные для подстановки в запрос
     */
    public function log($sql, $start_time, $time, $comment, $ans, $sql_source, $values);
}
