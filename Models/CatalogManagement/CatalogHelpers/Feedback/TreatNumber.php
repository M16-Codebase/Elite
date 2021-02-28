<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 21.07.15
 * Time: 15:37
 *
 *
 */

namespace Models\CatalogManagement\CatalogHelpers\Feedback;


use App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use Models\CatalogManagement\Item;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Search\CatalogSearch;
use Models\CatalogManagement\Type;

class TreatNumber extends FeedbackHelper
{
    protected static $i = NULL;
    protected static $fieldsList = array('full_number');

    public function get(Item $i, $field){
        if ($field == 'full_number') {
            $type = $i->getType();
            $prefix = $type['number_prefix'];
            $prefix = !empty($prefix) ? $prefix : mb_strtoupper(mb_substr($type['title'], 0, 1));
            return $prefix . str_pad($i[FeedbackConfig::KEY_FEEDBACK_NUMBER], FeedbackConfig::NUMBER_LENGTH, '0', STR_PAD_LEFT);
        }
    }

    /**
     * @param $type_id
     * @param $propValues
     * @param $errors
     * @param $segment_id
     */
    public function preCreate($type_id, $propValues, &$errors, $segment_id){
        $type = Type::getById($type_id);
        /**
         * Для отклика на вакансию и анкеты соискателя проверяем наличие файла резюме, либо ссылки на резюме в интернете
         */
        if (in_array($type['key'], array(FeedbackConfig::TYPE_VACANCY, FeedbackConfig::TYPE_APPLICANT))){
            if (empty($propValues[FeedbackConfig::KEY_VACANCY_SUMMARY][0]['value'])
                 && empty($propValues[FeedbackConfig::KEY_VACANCY_SUMMARY_LINK][0]['value'])){
                $errors[] = array(
                    'key' => 'summary',
                    'field' => 'Резюме',
                    'error' => \Models\Validator::ERR_MSG_EMPTY
                );
            }
        }
        /**
         * Проверка, что привязанный объект действительно является вакансией
         */
        if ($type['key'] == FeedbackConfig::TYPE_VACANCY && !empty($propValues[FeedbackConfig::KEY_VACANCY_ENTITY][0]['value'])) {
            $item = Item::getById($propValues[FeedbackConfig::KEY_VACANCY_ENTITY][0]['value']);
            if (!empty($item)){
                if ($item->getType()->getCatalog()['key'] != CatalogConfig::VACANCY_KEY){
                    $errors[] = array(
                        'key' => FeedbackConfig::KEY_VACANCY_ENTITY,
                        'field' => 'Вакансия',
                        'error' => \Models\Validator::ERR_MSG_INCORRECT
                    );
                }
            } else {
                $errors[] = array(
                    'key' => FeedbackConfig::KEY_VACANCY_ENTITY,
                    'field' => 'Вакансия',
                    'error' => \Models\Validator::ERR_MSG_EMPTY
                );
            }
        }
    }

    /**
     * @param $updateKey
     * @param Item $item
     * @param array $params
     * @param array $properties
     * @param int|null $segment_id
     * @param array $errors
     */
    public function preUpdate($updateKey, Item $item, &$params, &$properties, $segment_id, &$errors){
        if (empty($item[FeedbackConfig::KEY_FEEDBACK_NUMBER])) {
            // Задаем номер обращения для новой заявки
            $type = $item->getType();
            $prev_item = CatalogSearch::factory(CatalogConfig::FEEDBACK_KEY)
                ->setTypeId($type['id'])
                ->setRules(
                    array(
                        Rule::make(FeedbackConfig::KEY_FEEDBACK_NUMBER)->setOrder(TRUE)
                    )
                )->searchItems(0, 1)->getFirst();
            $number = empty($prev_item) ? 1 : $prev_item[FeedbackConfig::KEY_FEEDBACK_NUMBER] + 1;
            $properties[FeedbackConfig::KEY_FEEDBACK_NUMBER] = array(0 => array(
                'val_id' => NULL,
                'value' => $number
            ));
        }
    }
}