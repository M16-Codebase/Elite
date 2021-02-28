<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 06.07.15
 * Time: 19:35
 *
 * Устанавливает дату отзыва/вопроса и статус
 */

namespace Models\CatalogManagement\CatalogHelpers\Review;


use App\Configs\CatalogConfig;
use App\Configs\ReviewConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Properties\Factory as PropertyFactory;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;

class DateAndStatus extends ReviewHelper{
    protected static $i = NULL;

    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        if (empty($properties)){
            return;
        }
        if (empty($item[ReviewConfig::CREATION_DATE]) && empty($properties[ReviewConfig::CREATION_DATE])){
            /**
             * Если дата не задана, и не введена с формы, задаем текущую
             */
            $properties[ReviewConfig::CREATION_DATE] = array(0 => array('val_id' => NULL, 'value' => date('d.m.Y H:i:s')));
        }
        if (empty($item[ReviewConfig::STATUS]) && empty($properties[ReviewConfig::STATUS])) {
            if ($item->getType()['key'] == ReviewConfig::REVIEWS_KEY) {
                // Отзывы из админки публикуем сразу, с сайта сначала идут на премодерацию
                $prop_status = PropertyFactory::search($item->getType()['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'parents', array('key' => ReviewConfig::STATUS));
                $prop_status = $prop_status[ReviewConfig::STATUS];
                $prop_source = PropertyFactory::search($item->getType()['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'parents', array('key' => ReviewConfig::SOURCE));
                $prop_source = $prop_source[ReviewConfig::SOURCE];
                $search_value = (!empty($properties[ReviewConfig::SOURCE][0]['value']))
                    ? (!empty($prop_source['values'][$properties[ReviewConfig::SOURCE][0]['value']]['key']) && $prop_source['values'][$properties[ReviewConfig::SOURCE][0]['value']]['key'] == ReviewConfig::SOURCE_MANAGER
                        ? ReviewConfig::STATUS_ACCEPT
                        : ReviewConfig::STATUS_NEW
                    )
                    : (!empty($item['properties'][ReviewConfig::SOURCE]['value'] && $prop_source['values'][$item['properties'][ReviewConfig::SOURCE]['value']]['key'] == ReviewConfig::SOURCE_MANAGER)
                        ? ReviewConfig::STATUS_ACCEPT
                        : ReviewConfig::STATUS_NEW);
                $val = NULL;
                foreach($prop_status['values'] as $val){
                    if ($val['key'] == $search_value){
                        $val = $val['id'];
                        break;
                    }
                }
                $properties[ReviewConfig::STATUS] = array(0 => array('val_id' => NULL, 'value' => $val));
            } elseif ($item->getType()['key'] == ReviewConfig::QUESTIONS_KEY) {
                /** @TODO Управление статусами вопросов дописать */
            }
        } else {
            /** @TODO автоскрытие вопросов дописать */
        }
    }

    /**
     * Прописываем айтему количество привязанных отзывов / вопросов
     * @param $updateKey
     * @param Item $item
     * @param array $segment_id
     * @param \Models\CatalogManagement\CatalogHelpers\Interfaces\свойства $updatedProperties
     * @throws \Exception
     */
    public function onUpdate($updateKey, Item $item, $segment_id, $updatedProperties){
        /** @var Item $product */
        $product = $item[ReviewConfig::PRODUCT];
        if (empty($product)) {
            return;
        }
        $type = $item->getType();
        $prop_status = PropertyFactory::search($type['id'], PropertyFactory::P_ALL, 'key', 'type_group', 'parents', array('key' => ReviewConfig::STATUS));
        $prop_status = $prop_status[ReviewConfig::STATUS];
        $new_val = NULL;
        $public_val = NULL;
        foreach($prop_status['values'] as $val){
            if ($val['key'] == ReviewConfig::STATUS_NEW){
                $new_val = $val['id'];
            } elseif ($val['key'] == ReviewConfig::STATUS_ACCEPT){
                $public_val = $val['id'];
            }
        }
        $item->save();
        $new_count = CatalogSearch::factory(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY)
            ->setTypeId($type['id'])
            ->setRules(array(Rule::make(ReviewConfig::PRODUCT)->setValue($product['id']), Rule::make(ReviewConfig::STATUS)->setValue($new_val)))->searchItemIds()->getTotalCount();
        $pub_count = CatalogSearch::factory(CatalogConfig::REVIEWS_AND_QUESTIONS_KEY)
            ->setTypeId($type['id'])
            ->setRules(array(Rule::make(ReviewConfig::PRODUCT)->setValue($product['id']), Rule::make(ReviewConfig::STATUS)->setValue($public_val)))->searchItemIds()->getTotalCount();
        $count_key = $type['key'] == ReviewConfig::REVIEWS_KEY ? CatalogConfig::KEY_ITEM_REVIEW_COUNT : CatalogConfig::KEY_ITEM_QUESTION_COUNT;
        $count_new_key = $type['key'] == ReviewConfig::REVIEWS_KEY ? CatalogConfig::KEY_ITEM_NEW_REVIEW_COUNT : CatalogConfig::KEY_ITEM_NEW_QUESTION_COUNT;
        $product->update(array(), array($count_key => array(0 => array('val_id' => NULL, 'value' => $pub_count)), $count_new_key => array(0 => array('val_id' => NULL, 'value' => $new_count))));

    }

}