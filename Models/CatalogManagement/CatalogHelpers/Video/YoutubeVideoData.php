<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 19.03.15
 * Time: 19:33
 */

namespace Models\CatalogManagement\CatalogHelpers\Video;


use App\Configs\CatalogConfig;
use App\Configs\VideoConfig;
use Models\CatalogManagement\Positions\Video;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;

class YoutubeVideoData extends VideoHelper {
    protected static $i = NULL;
    protected static $dataCache = array();

//    public function fieldsList(){return array('number', 'views', 'origin_title', 'time');}
    public function preUpdate($updateKey, \Models\CatalogManagement\Item $video, &$params, &$properties, $segment_id, &$errors){
        $props = PropertyFactory::search($video['type_id'], PropertyFactory::P_ALL, 'key');
        if (empty($props[VideoConfig::KEY_LINK])){
            throw new \Exception('Отсутствует обязательное поле '.VideoConfig::KEY_LINK);
        }
        if (empty($props[VideoConfig::KEY_VIDEO_ID])){
            throw new \Exception('Отсутствует обязательное поле '.VideoConfig::KEY_VIDEO_ID);
        }
        if (!empty($properties[VideoConfig::KEY_LINK][0]['value'])) {
            $video_data = $this->getVideoData($properties[VideoConfig::KEY_LINK][0]['value']);
            if (!empty($video_data)){
                $properties[VideoConfig::KEY_VIDEO_ID][0] = array(
                    'val_id' => !empty($video['properties'][VideoConfig::KEY_VIDEO_ID]['val_id']) ? $video['properties'][VideoConfig::KEY_VIDEO_ID]['val_id'] : NULL,
                    'value' => $video_data['number']
                );
            }
        }
    }

    /**
     * @param string $url
     * @return array keys(number, views, origin_title, time)
     */
    private function getVideoData($url){
        $params = array();
        if (!empty($url)){
            if (preg_match('~youtube\.com/watch\?v=([a-z0-9\-_]+)~i', $url, $regs) || preg_match('~youtu\.be/([a-z0-9\-_]+)~i', $url, $regs)){
                $params['number'] = $regs[1];
//                $JSON = file_get_contents("https://gdata.youtube.com/feeds/api/videos/".$params['number']."?v=2&alt=json");
//                $JSON_Data = json_decode($JSON, TRUE);
//                $params['views'] = !empty($JSON_Data['entry']['yt$statistics']) ? $JSON_Data['entry']['yt$statistics']['viewCount'] : NULL;
//                $params['origin_title'] = !empty($JSON_Data['entry']['title']) ? $JSON_Data['entry']['title']['$t'] : NULL;
//                $params['time'] = !empty($JSON_Data['entry']['media$group']) ? $JSON_Data['entry']['media$group']['yt$duration']['seconds'] : NULL;
            }
        }
        return $params;
    }


//    public function get(\Models\CatalogManagement\Item $video, $field){
//        if (in_array($field, $this->fieldsList())){
//            if (empty($this->dataCache[$video['id']])){
//                $data = $video[CatalogConfig::KEY_VIDEO_DATA];
//                if (!empty($data)){
//                    $this->dataCache[$video['id']] = json_decode($data, true);
//                }
//            }
//            return !empty($this->dataCache[$video['id']][$field]) ? $this->dataCache[$video['id']][$field] : NULL;
//        }
//    }
} 