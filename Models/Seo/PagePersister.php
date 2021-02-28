<?php
namespace Models\Seo;
use App\Configs\SphinxConfig;
use LPS\Container\WebContentContainer;
use Models\SphinxManagement\SphinxSearch;

/**
* Класс позволяющий устанавливать для каждой страницы уникальное значение полей,
* необходимых для SEO через соответствующий модуль
*/
class PagePersister{
    const TABLE = 'seo';
    private static $i = NULL;
    /**
     * @var WebContentContainer
     */
    private $ans = NULL;

    private $db = NULL;

    public static function getInstance(){
        if (empty(self::$i)){
            self::$i = new self;
        }
        return self::$i;
    }

    public function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
    }

    /**
     * @param WebContentContainer $ans
     * @return $this
     */
    public function setContentContainer(WebContentContainer $ans){
        $this->ans = $ans;
        return $this;
    }
	/**
	 * Достает статические строки метатегов по конкретному урлу
	 * @param string $pageUID
	 * @return array or false if empty
	 */
	protected function getPageInfoByPageUID($pageUID){
		return $this->db->query('SELECT  `page_uid`, `title`, `keywords`, `description`, `text`, `complete_text`, `canonical` FROM `' . self::TABLE . '` WHERE `page_uid`=? AND `enabled` = 1 AND `moduleUrl` IS NULL AND `action` IS NULL', $pageUID != '/' ? rtrim($pageUID, '/') : '/')->getRow();
	}

    /**
     * Обновление page_uid метатегов при смене урла сущности
     * @param $old_url
     * @param $new_url
     */
    public function updateMetaTagBinding($old_url, $new_url){
        $this->db->query('UPDATE `' . self::TABLE . '` SET `page_uid` = REPLACE(`page_uid`, ?s, ?s) WHERE `page_uid` LIKE ?s', $old_url, $new_url, $old_url.'%');
        $this->db->query('UPDATE `' . self::TABLE . '` SET `page_uid` = ?s WHERE `page_uid` = ?s', rtrim($new_url, '/'), rtrim($old_url, '/'));
    }

	/**
	 * Устанавливает значение переменных в соответствии с данными из модуля SEO
	 * @param string $pageUID переменная шаблона
     * @param string $moduleUrl - Нужно для конструктора метатегов
     * @param string $action
	 * @param string $pageTitle переменная шаблона
	 * @param string $pageDescription переменная шаблона
	 * @param string $pageKeywords переменная шаблона
	 */
	function checkPageInfo(&$pageUID, &$pageTitle = null, &$pageDescription = null, &$pageKeywords = null, &$pageText = null, &$pageCanonical = null){
		$data = $this->getPageInfoByPageUID(urldecode($pageUID));
        if (empty($data)){
            $data = $this->pageInfoConstruct($pageUID);
        }
		if (!empty($data)){
			if (!empty($data['title'])){
				$pageTitle = $this->prepareString($data['title']);
			}
			if (!empty($data['description'])){
				$pageDescription = $this->prepareString($data['description']);
			}
			if (!empty($data['keywords'])){
				$pageKeywords = $this->prepareString($data['keywords']);
			}
			if (!empty($data['text'])){
				$pageText = $this->prepareString(!empty($data['complete_text']) ? $data['complete_text'] : $data['text']);
			}
			if (!empty($data['canonical'])){
				$pageCanonical = $data['canonical'];
			}
		}
		return;
	}

    /**
     * Достает параметры конструктора метатегов по неполному урлу
     * @param $pageUID
     * @return mixed
     */
    public function pageInfoConstruct($pageUID){
        $uri = explode('?', $pageUID);
        $uri = reset($uri);
        $uri_parts = explode('/', trim($uri, '/'));
        $tmp_url = '/';
        $search_urls = array();// array($tmp_url);
        // Собираем набор урлов для поиска
        foreach($uri_parts as $part){
            $tmp_url .= $part.'/';
            // Более длинные урлы имеют больший приорритет, поэтому выстраиваем массив по убыванию
            // Укороченные урлы со звездочкой на конце, полный - без
            // (* - наследуемый урл (распространяется только на потомков, любой вложенности), без звездочки - для конкретной сущности)
            array_unshift($search_urls, $tmp_url . (($uri != $tmp_url) ? '*' : ''));
        }
        $result = $this->db->query('SELECT `page_uid`, `title`, `keywords`, `description`, `text`, `canonical` FROM `' . self::TABLE . '`
            WHERE `page_uid` IN (?l)
                AND `enabled` = 1
                AND `moduleUrl` = ?s
                AND `action` = ?s
            ORDER BY LENGTH(`page_uid`) DESC
            LIMIT 1',
            $search_urls, $this->ans['moduleUrl'], $this->ans['action'])->getRow();
        return $result;
    }

    /**
     * Подставляем переменные шаблона в метатеги
     * @param string $string_template строка из конструктора метатегов, переменные вписываются как в шаблоне - {$var.attr}, {$var2}
     *                                  блоки с переменными могут находиться в квадратных скобках - [ some text {$var} more text {$var2.param}]
     *                                  такие блоки будут затираться
     * @throws \Exception
     * @return string
     */
    private function prepareString($string_template){
        if (empty($this->ans)){
            throw new \Exception('Ошибка в Seo/PagePersister - WebContentContainer не указан');
        }
        // Ищем блоки в квадратных скобках, они могут затираться
        preg_match_all('~\[([^]]+)\]~', $string_template, $matches);
        if (!empty($matches[1])){
            foreach($matches[1] as $num => $match){
                $val = $this->writeVariables($match, TRUE);
                $string_template = str_replace($matches[0][$num], $val, $string_template);
            }

        }
        return $this->writeVariables($string_template);
    }

    /**
     * @param string $string_template строка из конструктора метатегов
     * @param bool $collapse TRUE – затираем строку в случае появления пустых значений, FALSE – оставляем, как есть
     * @return string
     */
    private function writeVariables($string_template, $collapse = FALSE){
        preg_match_all('~\{\$([^}]+)\}~', $string_template, $matches);
        if (!empty($matches[1])){
            foreach($matches[1] as $num => $match){
                // разбиваем имя переменной на индексы массива
                // (каждая переменная добывается из шаблона как элемент массива, остальные индексы уже к самой переменной)
                $match_parts = explode('.', $match);
                $var_name = array_shift($match_parts);
                $val = isset($this->ans[$var_name]) ? $this->ans[$var_name] : NULL;
                if (!empty($val) && !empty($match_parts)){
                    foreach($match_parts as $part){
                        if (isset($val[$part])){
                            $val = $val[$part];
                        } else {
                            // требуемый ключ не определен - значение не найдено, в результирующей строке останется плейсхолдер данной переменной
                            $val = NULL;
                            break;
                        }
                    }
                }
                if (empty($val) && $collapse) {
                    return '';
                }
                if (!is_null($val) && !is_array($val) && !is_object($val)){
                    $string_template = str_replace($matches[0][$num], $val, $string_template);
                }
            }

        }
        return $string_template;
    }

    private function getPageUidList($pageUID){
        $uri = explode('?', $pageUID);
        $uri = reset($uri);
        $uri_parts = explode('/', trim($uri, '/'));
        $tmp_url = '/';
        $search_urls = array();// array($tmp_url);
        // Собираем набор урлов для поиска
        foreach($uri_parts as $part){
            $tmp_url .= $part.'/';
            // Более длинные урлы имеют больший приорритет, поэтому выстраиваем массив по убыванию
            // Укороченные урлы со звездочкой на конце, полный - без
            // (* - наследуемый урл (распространяется только на потомков, любой вложенности), без звездочки - для конкретной сущности)
            array_unshift($search_urls, $tmp_url . (($uri != $tmp_url) ? '*' : ''));
        }
        return $search_urls;
    }

    public function search($params = array(), $single_row = TRUE, &$count = FALSE, $start = 0, $limit = 10000000){
        if (isset($params['pageUID']) && isset($params['moduleUrl']) && isset($params['action'])){
            $params['pageUID'] = $this->getPageUidList($params['pageUID']);
        }
        $result = $this->db->query('SELECT' . ($count !== FALSE ? ' SQL_CALC_FOUND_ROWS ' : ' ') . '`id`, `page_uid`, `title`, `keywords`, `description`, `text`, `canonical`, `enabled`, `site_links_done`, `complete_text` FROM `' . self::TABLE . '`
            WHERE 1 {AND `enabled` = ?d}
               { AND `id` ' . (isset($params['id']) && is_array($params['id']) ? 'IN (?i)' : '= ?d') . '}
               { AND `page_uid` ' . ((isset($params['pageUID']) && is_array($params['pageUID'])) ? 'IN (?l)' : '= ?s') . '}
               { AND `page_uid` LIKE ?s}
               { AND `moduleUrl` = ?s}
               { AND `action` = ?s }
               { AND `moduleUrl` IS NULL AND `action` IS NULL AND `action` IS NULL AND `action` IS NULL AND ?d}
               { AND `page_uid` LIKE ?s}
               { AND `complete_text` IS NULL AND ?d}
               { AND `site_links_done` = ?d}'
                . ($single_row
                    ? 'ORDER BY `page_uid` DESC LIMIT 1'
                    : (isset($params['sort'])
                            ? 'ORDER BY `' . $params['sort']['field'] . '` ' . (!empty($params['sort']['desc']) ? 'DESC' : '') . ' LIMIT ' . $start . ', ' . $limit
                            : ''
                    )
                ),
            isset($params['enabled']) ? ($params['enabled'] === 'any' ? $this->db->skipIt() : ($params['enabled'] ? 1 : 0)) : 1,
            isset($params['id']) ? $params['id'] : $this->db->skipIt(),
            isset($params['pageUID']) ? $params['pageUID'] : $this->db->skipIt(),
            isset($params['pageUID_like']) ? $params['pageUID_like'] : $this->db->skipIt(),
            isset($params['moduleUrl']) && isset($params['action']) ? $params['moduleUrl'] : $this->db->skipIt(),
            isset($params['moduleUrl']) && isset($params['action']) ? $params['action'] : $this->db->skipIt(),
            isset($params['moduleUrl']) && isset($params['action']) ? $this->db->skipIt() : 1,
            !empty($params['page_uid_find']) ? '%'.$params['page_uid_find'].'%' : $this->db->skipIt(),
            !empty($params['complete_text_empty']) ? 1 : $this->db->skipIt(),
            isset($params['site_links_done']) ? (!empty($params['site_links_done']) ? 1 : 0) : $this->db->skipIt()
        );
        if ($count !== FALSE){
            $count = $this->db->query('SELECT FOUND_ROWS()')->getCell();
        }
        return $single_row ? $result->getRow() : $result->select('id');
    }

    /**
     * @param $id
     * @return array
     */
    public function getById($id){
        return $this->db->query('SELECT `id`, `page_uid`, `title`, `keywords`, `description`, `text`, `canonical`, `enabled` FROM `' . self::TABLE . '` WHERE `id` = ?d', $id)->getRow();
    }

    /**
     * @param $params
     * @return int
     */
    public function createRule($params){
        if (!isset($params['enabled'])){
            $params['enabled'] = 1;
        }
        return $this->db->query('INSERT INTO `' . self::TABLE . '` SET ?a', $params);
    }

    /**
     * @param $id
     * @param $params
     */
    public function updateRule($id, $params){
        $rule_data = $this->getById($id);
        if (!empty($rule_data)){
            // если редактируется текст, нужно удалить перелинкованный
            if (\App\Configs\SeoConfig::SEO_LINKS_ENABLE && isset($params['text']) && $params['text'] != $rule_data['text']){
                $params['complete_text'] = NULL;
                $url = (isset($params['page_uid']) && $params['page_uid'] != $rule_data['page_uid']) ? $params['page_uid'] : $rule_data['page_uid'];
                if (strpos($url, '?') !== FALSE){
                    $url = explode('?', $url);
                    $url = reset($url);
                }
                $url = strlen($url) > 1 ? rtrim($url, '/') : '/';
                $this->db->query('DELETE FROM `' . SeoLinks::INSERTED_LINKS_LIST_TABLE . '` WHERE `from` = ?s', $url);
                SphinxSearch::factory(SphinxConfig::METATAGS_KEY)->forceRecreateIndex();
            }
            $this->db->query('UPDATE `' . self::TABLE . '` SET ?a WHERE `id` = ?d', $params, $id);
        }
    }

    /**
     * @param $id
     */
    public function deleteRule($id){
        $this->db->query('DELETE FROM `' . self::TABLE . '` WHERE `id` = ?d', $id);
    }
}
?>