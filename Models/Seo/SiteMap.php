<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 13.10.14
 * Time: 12:45
 */

namespace Models\Seo;


use App\Configs\SeoConfig;
use App\Configs\Settings;
use Models\Seo\Helpers\SiteMap\iSitemapHelper;
use Models\Validator;
use Modules\Seo\SuperAdmin;

class SiteMap {
    protected static $i = NULL;

    const ADDITIONAL_RULES_TABLE = 'seo_sitemap_additional_url_rules';
    const ALLOW_URLS_TABLE = 'seo_sitemap_allow_urls';

    const USER_AGENT_DIRECTIVE = 'User-agent:';
    const ALLOW_DIRECTIVE = 'Allow:';
    const DISALLOW_DIRECTIVE = 'Disallow';

    const TMP_FOLDER = '/data/temp/sitemap';

    const URLS_PER_FILE = 30000;

    private $robots_rules = array();
    private $additional_rules = array();

    private $allow_user_agents = array();
    private $db = NULL;

    private $url = NULL;
    private $changefreq = 'weekly';

    private $current_file = NULL;
    private $files_count = 0;
    private $written_urls = 0;
    private $loaded_aditional_urls = array();
    private $canonical_urls = array();

    private $current_segment = null;
    /**
     * @var iSitemapHelper[]
     */
    private static $helpers = array();

    /**
     * @param iSitemapHelper $helper
     */
    static function addHelper(iSitemapHelper $helper){
        self::$helpers[get_class($helper)] = $helper;
    }

    /**
     * @static
     * @param iSitemapHelper $helper
     */
    static function delHelper(iSitemapHelper $helper){
        unset(self::$helpers[get_class($helper)]);
    }

    /**
     * @return SiteMap
     */
    public static function getInstance(){
        if (empty(self::$i)){
            self::$i = new self;
        }
        return self::$i;
    }

    private function __construct(){
        $this->db = \App\Builder::getInstance()->getDB();
        $this->url = \LPS\Config::getParametr('site', 'url');
	if (substr($this->url, 0, 4) != 'https'&&substr($this->url, 0, 4) != 'http'){
            $this->url = 'https://' . $this->url;
        }
        if (substr($this->url, -1) == '/'){
            $this->url = substr($this->url, 0, -1);
        }
    }
 
    /**
     * Парсит robots.txt, правила из роботса записываются в $this->robots_rules в формате
     * array(
     *    array(
     *       'allow' => bool,
     *       'regex' => bool,
     *       'url' => string
     *    ),
     *    ...
     * )
     */
    public function loadRobotsTxt(){
        $this->robots_rules = array();
        $robots_file = fopen(\LPS\Config::getRealDocumentRoot() . '/robots.txt', 'r');
        if ($robots_file !== FALSE){
            $current_user_agent = NULL;
            $rules_array = array();
            while($line = fgets($robots_file)) {
                if (mb_substr($line, 0, mb_strlen(self::USER_AGENT_DIRECTIVE)) == self::USER_AGENT_DIRECTIVE) {
                    $user_agent = trim(mb_substr($line, mb_strlen(self::USER_AGENT_DIRECTIVE)));
                    $current_user_agent = $user_agent;
                } elseif (empty($line)) {
                    $current_user_agent = NULL;
                } elseif (!empty($current_user_agent)
                    && ($current_user_agent == SeoConfig::getParam(Settings::KEY_SITEMAP_USERAGENT) || $current_user_agent == '*')
                    && (mb_strpos($line, self::ALLOW_DIRECTIVE) === 0 || mb_strpos($line, self::DISALLOW_DIRECTIVE) === 0))
                {
                    $rules_array[$current_user_agent][] = $line;
                }
            }
            fclose($robots_file);
            // если нашли правила для выбранного юзерагента - берем их
            // нет - общие
            $rules_array = isset($rules_array[SeoConfig::getParam(Settings::KEY_SITEMAP_USERAGENT)])
                ? $rules_array[SeoConfig::getParam(Settings::KEY_SITEMAP_USERAGENT)]
                : (!empty($rules_array['*']) ? $rules_array['*'] : array());

            usort($rules_array, function($a, $b){
                $a = explode(':', $a);
                $b = explode(':', $b);
                $a = isset($a[1]) ? $a[1] : '';
                $b = isset($b[1]) ? $b[1] : '';
                return strlen($a) > strlen($b) ? '+1' : '-1';
            });
            foreach($rules_array as $line){
                $line = trim($line);
                $allow = (mb_strpos($line, self::ALLOW_DIRECTIVE) === 0);
                $url = $this->prepareRuleUrl(trim(mb_substr($line, ($allow ? mb_strlen(self::ALLOW_DIRECTIVE) : mb_strlen(self::DISALLOW_DIRECTIVE)) + 1)), $is_regex);
                if (empty($url)){
                    // Пропускаем пустые директывы Allow:, Disallow:
                    continue;
                }
                $this->robots_rules[] = array(
                    'allow' => $allow,
                    'regex' => $is_regex,
                    'url' => $url
                );
            }
        }
    }

    /**
     * Проверяем, позволяют ли правила передавать url в sitemap
     * @param string $url - проверяемый url
     * @param array $rules - набор правил в формате
     *          array(
     *             array(
     *                'allow' => bool,
     *                'regex' => bool,
     *                'url' => string
     *             ),
     *             ...
     *          )
     * @return bool
     */
    private function checkUrlViaRulesGroup($url, $rules){
        $allow = true;
        foreach($rules as $rule){
            $match = $rule['regex'] ? preg_match($rule['url'], $url) : (mb_substr($url, 0, mb_strlen($rule['url'])) == $rule['url']);
            if ($match){
                $allow = $rule['allow'];
            }
        }

        return $allow;
    }

    /**
     * Проверяем, разрешен ли url для размещения в sitemap.xml
     * @param string $url
     * @return bool
     */
    private function isUrlAllowed($url){
        // сначала проверяем по правилам robots.txt
        $allow = $this->checkUrlViaRulesGroup($url, $this->robots_rules);
        if ($allow){
            // если robots.txt позволяет - проверяем по дополнительным правилам (заданным в админке)
            $allow = $this->checkUrlViaRulesGroup($url, $this->additional_rules);
        }

        return $allow;
    }

    public function generateSiteMap()
    {
        $segments = \App\Segment::getInstance()->getAll();
        foreach($segments as $s) {
            $this->generateSiteMapBySegment($s);
        }
    }

    private function generateSiteMapBySegment($segment)
    {
        $this->current_segment = $segment;
        if (!file_exists(\LPS\Config::getRealDocumentRoot() . self::TMP_FOLDER . '/' . $segment['key'])){
            mkdir(\LPS\Config::getRealDocumentRoot() . self::TMP_FOLDER . '/' . $segment['key'], 0770, true);
        }
        SeoConfig::loadSiteMapHelpers(); // Хелперы отвечающие за разные типы контента
        $this->changefreq = SeoConfig::getParam(Settings::KEY_CHANGEFREQ);
        $this->files_count = 0;
        $this->loaded_aditional_urls = array();
        $this->current_file = NULL;

        $this->canonical_urls = $this->db->query('SELECT CONCAT(`page_uid`, "/") AS `url` FROM `seo` WHERE `canonical` IS NOT NULL AND `canonical` != ""')->getCol('url', 'url');

        if (empty($this->robots_rules)){
            $this->loadRobotsTxt();
        }
        if (empty($this->additional_rules)){
            $this->getAdditionalUrlRules(TRUE);
        }
        // Сначала записываем урлы из кастомного списка чтобы избежать пересечений по урлам
        $urls = $this->getUrlList();
        foreach($urls as $url_data){
            $url = urldecode($url_data['url']);
            if (!$this->addUrlToSiteMapFile($url, $url_data['last_modification'], $url_data['priority'], FALSE)){
                $this->db->query('UPDATE `' . self::ALLOW_URLS_TABLE . '` SET `valid` = 0 WHERE `id` = ?d', $url_data['id']);
            } else {
                // Урлы из списка сеошников добавляются в список, по которому мы потом проверяем
                // отсутствие пересечений кастомного списка с автоматически добавляемыми урлами
                $this->loaded_aditional_urls[$url] = $url_data['priority'];
                $this->db->query('UPDATE `' . self::ALLOW_URLS_TABLE . '` SET `valid` = 1 WHERE `id` = ?d', $url_data['id']);
            }
        }
        if (!empty(self::$helpers)){
            foreach(self::$helpers as $helper){
                $helper->writeUrls($segment['id']);
            }
        }

        $this->finalizeSiteMap();
    }

    /**
     * Запись урла в sitemap
     * Метод публичный для возможности добавления урлов из хелперов
     * @param string $loc - урл страницы
     * @param string $lastmod дата обновления страницы
     * @param string $priority
     * @param bool $check_is_loaded нужно ли проверять, попадание урла в кастомный список (FALSE только для кастомного списка) @TODO нужно ли?
     * @return bool FALSE если урл запрещен в robots.txt или дополнительными правилами от сеошников
     */
    public function addUrlToSiteMapFile($loc, $lastmod, $priority, $check_is_loaded = TRUE){
        // Проверяем, разрешен ли урл к индексации в роботс
        if (!$this->isUrlAllowed($loc)){
            return FALSE;
        }
        if ($check_is_loaded && isset($this->loaded_aditional_urls[$loc])){
            return FALSE;
        }
        if (isset($this->canonical_urls[$loc])){
            return FALSE; // canonical страницы не должны попадать в sitemap.xml
        }
        if (empty($this->current_file)){
            $this->createSiteMapFile();
        }
        fputs($this->current_file, '<url>' . PHP_EOL);
        fputs($this->current_file, '<loc>' . $this->url . $loc . '</loc>'  . PHP_EOL);
        if (SeoConfig::getParam(Settings::KEY_USE_LASTMOD)){
            fputs($this->current_file, '<lastmod>' . date('c', strtotime($lastmod)) . '</lastmod>' . PHP_EOL);
        }
        fputs($this->current_file, '<changefreq>' . $this->changefreq . '</changefreq>' . PHP_EOL);
        fputs($this->current_file, '<priority>' . $priority . '</priority>' . PHP_EOL);
        fputs($this->current_file, '</url>' . PHP_EOL);
        $this->written_urls ++;
        if ($this->written_urls >= self::URLS_PER_FILE){
            $this->saveSiteMapFile();
        }
        return TRUE;
    }

    /**
     * Создание файла sitemap и запись заголовка
     */
    private function createSiteMapFile(){
        $this->files_count += 1;
        $this->written_urls = 0;
        $this->current_file = fopen(\LPS\Config::getRealDocumentRoot() . self::TMP_FOLDER . '/' . $this->current_segment['key'] . '/sitemap-' . $this->files_count . '.xml', 'w');
        fputs($this->current_file, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
        fputs($this->current_file, '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"'
            . ' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"'
            . ' xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9http://www.sitemaps.org/schemas/sitemap/0.9/sitemap.xsd">'
            . PHP_EOL);
    }

    /**
     * Сохранение и закрытие текущего файла sitemap
     */
    private function saveSiteMapFile(){
        fputs($this->current_file, '</urlset>');
        fclose($this->current_file);
        $this->current_file = NULL;
    }

    /**
     * Перенос созданных сайтмапов в корень, создание общего индекса при необходимости
     */
    private function finalizeSiteMap(){
        if (!empty($this->current_file)){
            $this->saveSiteMapFile();
        }
        if ($this->files_count > 0){
            // Удаляем старые файлы sitemap
            array_map('unlink', glob(\LPS\Config::getRealDocumentRoot() . '/data/sitemap/' . $this->current_segment['key'] . '/*'));
            if (!file_exists(\LPS\Config::getRealDocumentRoot() . '/data/sitemap/' . $this->current_segment['key'])){
                mkdir(\LPS\Config::getRealDocumentRoot() . '/data/sitemap/' . $this->current_segment['key'], 0770, true);
            }
            if ($this->files_count == 1){
                // Файл один - просто переименовываем его в sitemap.xml
                rename(\LPS\Config::getRealDocumentRoot() . self::TMP_FOLDER . '/' . $this->current_segment['key'] . '/sitemap-1.xml',
                    \LPS\Config::getRealDocumentRoot() . '/data/sitemap/' . $this->current_segment['key'] . '/sitemap.xml');
            } else {
                // Файлов несколько - создаем sitemap index
                $sitemaps_index = fopen(\LPS\Config::getRealDocumentRoot() . '/data/sitemap/sitemap.xml', 'w');
                fputs($sitemaps_index, '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL);
                fputs($sitemaps_index, '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL);
                for ($i = 1; $i <= $this->files_count; $i ++){
                    rename(\LPS\Config::getRealDocumentRoot() . self::TMP_FOLDER . '/' . $this->current_segment['key'] . '/sitemap-' . $i . '.xml', \LPS\Config::getRealDocumentRoot() . '/data/sitemap/' . $this->current_segment['key'] . '/sitemap-' . $i . '.xml');
                    fputs($sitemaps_index, '<sitemap>' . PHP_EOL);
                    fputs($sitemaps_index, '<loc>' . $this->url . '/data/sitemap/sitemap-' . $i . '.xml</loc>' . PHP_EOL);
                    fputs($sitemaps_index, '<lastmod>' . date('c', filemtime(\LPS\Config::getRealDocumentRoot() . '/data/sitemap/' . $this->current_segment['key'] . '/sitemap-' . $i . '.xml')) . '</lastmod>' . PHP_EOL);
                    fputs($sitemaps_index, '</sitemap>' . PHP_EOL);
                }
                fputs($sitemaps_index, '</sitemapindex>');
                fclose($sitemaps_index);
            }
            // Если стоит галка в конфиге - копируем содержимое /data/sitemap/sitemap.xml в /sitemap.xml, нет - очищаем /sitemap.xml
//            $doc_root = \App\Builder::getInstance()->getConfig()->getRealDocumentRoot() . SuperAdmin::SITEMAP_FILE;
//            if (file_exists($doc_root) && is_writable($doc_root)){
//                if (\Models\SiteConfigManager::getInstance()->checkFlag(SeoConfig::USE_ROOT_SITEMAP, \App\Configs\CatalogConfig::CONFIG_SEO_KEY)){
//                    file_put_contents($doc_root, file_get_contents(\LPS\Config::getRealDocumentRoot() . '/data/sitemap/' . $segment['key'] . '/sitemap.xml'));
//                } else {
//                    file_put_contents($doc_root, '');
//                }
//            }
        }
    }

    /**
     * возврат дополнительных правил разрешения/запрета
     * @param bool $generate_inner_rules
     * @return array
     */
    public function getAdditionalUrlRules($generate_inner_rules = FALSE){
        // в robots.txt урлы упорядочиваются по возрастанию длины урла и при анализе учитывается последний подходящий, у нас тот же принцип
        $result = $this->db->query('SELECT `id`, `type`, `url`, `regex` FROM `' . self::ADDITIONAL_RULES_TABLE . '` ORDER BY LENGTH(`url`)')->select();
        if ($generate_inner_rules){
            $this->additional_rules = array();
            foreach($result as $result_line){
                $this->additional_rules[] = array(
                    'allow' => ($result_line['type'] == 'allow'),
                    'regex' => !empty($result_line['regex']),
                    'url' => !empty($result_line['regex']) ? $result_line['regex'] : $result_line['url']
                );
            }
        }
        return $result;
    }

    /**
     * @param $id
     * @return array|null
     */
    public function getUrlRuleById($id){
        return $this->db->query('SELECT `id`, `type`, `url`, `regex` FROM ?# WHERE `id` = ?d', self::ADDITIONAL_RULES_TABLE, $id)->getRow();
    }

    /**
     * @param string $url
     * @param string $type (allow/disallow)
     * @param array $errors
     * @return bool|int
     */
    public function addUrlRule($url, $type, &$errors = array()){
        if (empty($url)){
            $errors['url'] = Validator::ERR_MSG_EMPTY;
        }
        if (!in_array($type, array('allow', 'disallow'))){
            $errors['type'] = Validator::ERR_MSG_INCORRECT_FORMAT;
        }
        if (empty($errors)){
            $regex = $this->prepareRuleUrl($url, $is_regex);
            $rule_id = $this->db->query('INSERT IGNORE `' . self::ADDITIONAL_RULES_TABLE . '` SET `type`=?s, `url`=?s, `regex`=?', $type, $url, $is_regex ? $regex : NULL);
            if (empty($rule_id)){
                $errors['url'] = Validator::ERR_MSG_EXISTS;
            }
        }
        return empty($errors) ? $rule_id : FALSE;
    }

    /**
     * @param int $rule_id
     * @param string $url
     * @param string $type
     * @param array $errors
     * @return bool
     */
    public function editUrlRule($rule_id, $url, $type, &$errors = array()){
        if (empty($rule_id)){
            $errors['id'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        $rule = $this->db->query('SELECT `id`, `type`, `url`, `regex` FROM `' . self::ADDITIONAL_RULES_TABLE . '` WHERE `id` = ?d', $rule_id)->getRow();
        if (empty($rule)){
            $errors['rule'] = 'not_found';
        } else {
            if ($rule['url'] == $url && $rule['type'] == $type){
                return FALSE;
            } else {
                if (empty($url)){
                    $errors['url'] = Validator::ERR_MSG_EMPTY;
                }
                if (!in_array($type, array('allow', 'disallow'))){
                    $errors['type'] = Validator::ERR_MSG_INCORRECT_FORMAT;
                }
                if (empty($errors)){
                    $regex = $this->prepareRuleUrl($url, $is_regex);
                    $this->db->query('UPDATE `' . self::ADDITIONAL_RULES_TABLE . '` SET `type`=?s, `url`=?s, `regex`=? WHERE `id` = ?d', $type, $url, $is_regex ? $regex : NULL, $rule_id);
                }
            }
        }
        return empty($errors) ? $rule_id : false;
    }

    /**
     * @param int[] $ids
     * @return bool
     */
    public function deleteUrlRule($ids){
        if (empty($ids)){
            return FALSE;
        }
        if (!is_array($ids)){
            $ids = array($ids);
        }
        return $this->db->query('DELETE FROM `' . self::ADDITIONAL_RULES_TABLE . '` WHERE `id` IN (?i)', $ids);
    }

    /**
     * Проверяем урл, при необходимости преобразуем его в регулярку
     * @param string $url исходный урл
     * @param bool $is_regex
     * @return string преобразованный урл
     */
    private function prepareRuleUrl($url, &$is_regex){
        $is_regex = FALSE;
        $strict = mb_substr($url, -1) == '$';
        if ($strict) {
            $url = mb_substr($url, 0, -1);
        }
        if (mb_strpos($url, '*') !== FALSE || $strict){
            $is_regex = true;
            $url = '~^' . implode('.+', array_map('preg_quote', explode('*', $url))) . ($strict ? '$' : '') . '~';
        }
        return $url;
    }

    /**
     * @return array
     */
    public function getUrlList(){
        return $this->db->query('SELECT `id`, `url`, `priority`, `last_modification`, `valid` FROM `' . self::ALLOW_URLS_TABLE . '`')->select();
    }

    /**
     * @param $id
     * @return array|null
     */
    public function getUrlById($id){
        return $this->db->query('SELECT `id`, `url`, `priority`, `last_modification`, `valid` FROM ?# WHERE `id` = ?d', self::ALLOW_URLS_TABLE, $id)->getRow();
    }

    /**
     * Добавить урлы для sitemap
     * @param string[] $urls
     * @param string $priority (decimal 0.00)
     * @param string $last_modification дата изменения
     * @param array $errors
     * @return BOOL
     */
    public function addUrls($urls, $priority, $last_modification, &$errors = array()){
        if (empty($urls)){
            $errors['urls'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        if (!in_array($priority, SeoConfig::getParam('priority_list'))){
            $errors['priority'] = Validator::ERR_MSG_INCORRECT_FORMAT;
            return FALSE;
        }
        if (!is_array($urls)){
            $urls = array($urls);
        }
        // отрезаем у урлов домен если есть
        foreach($urls as $key => $url){
            $url = parse_url(urldecode($url));
            $urls[$key] = (!empty($url['path']) ? $url['path'] : '/') . (!empty($url['query']) ? '?'.$url['query'] : '');
        }
        $insert_values = '';
        foreach($urls as $url){
            $insert_values .= (!empty($insert_values) ? ', ' : '') . '("' . $url . '", "' . $priority . '", ' . (!empty($last_modification) ? '"'.date('Y-m-d H:i:s', strtotime($last_modification)).'"' : 'NOW()') . ', 1)';
        }
        return $this->db->query('REPLACE INTO `' . self::ALLOW_URLS_TABLE . '` (`url`, `priority`, `last_modification`, `valid`) VALUES ' . $insert_values);
    }

    /**
     * @param int $id
     * @param string $url
     * @param string $priority
     * @param string $last_modification
     * @param array $errors
     * @return bool
     */
    public function editUrl($id, $url, $priority, $last_modification, &$errors = array()){
        if (empty($id)){
            $errors['id'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        $url_data = $this->db->query('SELECT `id`, `url`, `priority`, `last_modification`, `valid` FROM `' . self::ALLOW_URLS_TABLE . '` WHERE `id` = ?d', $id)->getRow();
        if (empty($url_data)){
            $errors['id'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        if (empty($url)){
            $errors['url'] = Validator::ERR_MSG_EMPTY;
            return FALSE;
        }
        $url = parse_url(urldecode($url));
        $url = (!empty($url['path']) ? $url['path'] : '/') . (!empty($url['query']) ? '?'.$url['query'] : '');
        if (empty($priority) || !in_array($priority, SeoConfig::getParam('priority_list'))){
            $errors['priority'] = empty($priority) ? Validator::ERR_MSG_EMPTY : Validator::ERR_MSG_INCORRECT_FORMAT;
            return FALSE;
        }
        $last_modification = date('Y-m-d', !empty($last_modification) ? strtotime($last_modification) : time());
        if ($url == $url_data['url'] && $priority == $url_data['priority'] && $last_modification == $url_data['last_modification']){
            return FALSE; // Ничего не изменилось
        } else {
            $this->db->query('UPDATE `' . self::ALLOW_URLS_TABLE . '` SET `url` = ?s, `last_modification` = ?s, `priority` = ?s WHERE `id` = ?d', $url, $last_modification, $priority, $id);
            return TRUE;
        }
    }

    /**
     * удалить урлы из sitemap
     * @param int[] $ids
     * @return bool
     */
    public function deleteUrls($ids){
        if (empty($ids)){
            return FALSE;
        }
        if (!is_array($ids)){
            $ids = array($ids);
        }
        return $this->db->query('DELETE FROM `' . self::ALLOW_URLS_TABLE . '` WHERE `id` IN (?i)', $ids);
    }
}