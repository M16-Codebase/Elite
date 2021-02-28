<?php
/**
 * Description of Cron
 *
 * @author olga
 */
namespace Modules\CliModules;
use App\Configs\SphinxConfig;
use Models\CatalogManagement\Catalog;
use Models\CatalogManagement\Type;
use Models\Exchange;
use Models\Seo\SiteMap;
use Models\SphinxManagement\SphinxSearch;

class Cron extends \LPS\Module{
    const TABLE = 'cron_current_task';
    protected function log($msg){
        $db = \App\Builder::getInstance()->getDB();
        $db->query('DELETE FROM `'.self::TABLE.'` WHERE 1');
        $db->query('INSERT INTO `'.self::TABLE.'` SET `name` = ?s, `start` = NOW()', $msg);
        echo date('H:i d.m.Y')."\t".$msg."\n";
    }
    function index(){
    	echo 'select method'."\n";
    }

    public function weekly(){
         $this->log('start weekly');
         $this->log('weekly end');
    }

	/**
	  Функция, которая вызывается ежедневно.
      Прописывается в крон так:
     # 17 5 * * * /usr/bin/php /LPSCMS_DIRECTORY_ROOT/index.php cron daily > /LPSCMS_DIRECTORY_ROOT/logs/cron_daily.log 2>&1
     "/LPSCMS_DIRECTORY_ROOT" - полный путь к папке с системой, а "17 5" - это порядковый номер минуты и часа в которые запускается скрипт. Хорошо использовать разные простые числа, тогда не будут накладываться задачи
     *
	 */
	public function daily(){
        $this->log('start daily');

        if (\App\Configs\SphinxConfig::ENABLE_SPHINX){
            $this->log('sphinx delta index merge start');
            $this->log('index name: ' . SphinxConfig::CATALOG_KEY);
            SphinxSearch::factory(SphinxConfig::CATALOG_KEY)->mergeDeltaIndex();
            $this->log('index name: ' . SphinxConfig::POSTS_KEY);
            SphinxSearch::factory(SphinxConfig::POSTS_KEY)->mergeDeltaIndex();
            $this->log('sphinx delta index merge end');
        }
        $this->log('Генерация sitemap.xml');
        SiteMap::getInstance()->generateSiteMap();
        if (\App\Configs\SeoConfig::SEO_LINKS_ENABLE){
            $this->log('Перелинковка');
            SphinxSearch::factory(SphinxConfig::METATAGS_KEY)->mergeDeltaIndex();
            \Models\Seo\SeoLinks::getInstance()->buildLinks();
        }
        if (\LPS\Config::SENDSAY_ENABLE && !\LPS\Config::isDev()){
            \Models\SubscribeManagement\SubscribeController::cronSynchronize();
        }
        $this->log('Технические работы');
        Catalog::checkBase();
        $this->log('start end');
	}
	/**
	  Функция, которая вызывается ежечасно.
      Прописывается в крон так:
     # 13 * * * * /usr/bin/php /LPSCMS_DIRECTORY_ROOT/index.php cron hourly > /LPSCMS_DIRECTORY_ROOT/logs/cron_hourly.log 2>&1
     "/LPSCMS_DIRECTORY_ROOT" - полный путь к папке с системой, а "13" - это порядковый номер минуты в которую запускается скрипт. Хорошо использовать разные простые числа, тогда не будут накладываться задачи
     *
	 */
	public function hourly(){
		$this->log('start hourly');

        if (\App\Configs\SphinxConfig::ENABLE_SPHINX){
            $this->log('sphinx delta index update start');
            $this->log('index name: ' . SphinxConfig::CATALOG_KEY);
            SphinxSearch::factory(SphinxConfig::CATALOG_KEY)->updateDeltaIndex();
            $this->log('index name: ' . SphinxConfig::POSTS_KEY);
            SphinxSearch::factory(SphinxConfig::POSTS_KEY)->updateDeltaIndex();
            $this->log('sphinx delta index update end');
        }
        if (\LPS\Config::SENDSAY_ENABLE && !\LPS\Config::isDev()){
            \Models\SubscribeManagement\SubscribeController::resumeCronSynchronize();
        }

		$this->log('hourly end');
	}
	/**
	 *Функция, которая вызывается раз в 3 минуты.
	 */
    public function minutely(){
        $this->log('start minutely');
        $this->log('Подготавливаем задачи для импорта');
        \App\Exchange::importFromFtp();
        $this->log('Импортируем');
        \App\Exchange::catalogCsvImport();
        $this->log('Экспортируем');
        \App\Exchange::catalogCsvExport();
        $this->log('minutely end');
    }

    public function monthly(){
        $this->log('start monthly');
        \Models\ImageManagement\TmpCollection::garbageCollector();
        $this->log('monthly end');
    }
    /**
     * Новый вид крон задач
     * @TODO логи
     * @throws \Exception
     */
    public static function checkPlan(){
        $shedule = \Models\CronTasks\Task::getShedule();
        $types = \Models\CronTasks\Task::getTypes();
        foreach ($shedule as $sh){
            if (empty($sh['status'])){
                continue;
            }
            $type_class = $types[$sh['type']];
            if (empty($sh['status'])){
                continue;
            }
            //если задача ручной установки, то всё просто, берем следующую и запускаем
            if (empty($sh['plan']) || $sh['plan'] == 1){
                $task = \Models\CronTasks\Task::getNext($sh['type'], array(\Models\CronTasks\Task::STATUS_NEW, \Models\CronTasks\Task::STATUS_PROCESS));
                if (!empty($task)){
                    $task->start();
                    $shedule[$sh['type']]['last_time'] = time();
                }
            }else{
                if (!is_array($sh['plan'])){
                    $plan = array();
                    $tmp = explode('|', $sh['plan']);
                    foreach ($tmp as $t){
                        $h = explode('=', $sh['plan']);
                        $plan[$h[0]] = $h[0] == 'minutes' ? $h[1] : explode(',', $h[1]);
                    }
                }else{
                    $plan = $sh['plan'];
                }
                //если задачи с периодичностью, то надо создать и запустить следующую
                if (array_key_exists('minutes', $plan)){//каждые несколько минут
                    if ($sh['last_time'] + $plan['minutes'] * 60 <= time()){
                        $type_class::createAndStart();//сразу и создать (задача сама знает, надо ли ей создаваться) и запустить
                        $shedule[$sh['type']]['last_time'] = time();
                    }
                }elseif (array_key_exists('time', $plan)){//или в четко заданное время
                    
                    throw new \Exception('Для данного плана функционал не готов: ', json_encode($sh['plan']));
                }else{
                    throw new \Exception('Неверный план: ', json_encode($sh['plan']));
                }
            }
        }
        \Models\CronTasks\Task::setShedule($shedule);
    }
}
