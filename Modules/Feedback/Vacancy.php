<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 21.07.15
 * Time: 19:50
 * База откликов на вакансии
 * Отличается от базы обращений тем, что здесь отображаются только отклики на вакансии и анкеты соискателей
 */

namespace Modules\Feedback;


use App\Configs\CatalogConfig;
use App\Configs\FeedbackConfig;
use Models\CatalogManagement\Rules\Rule;
use Models\CatalogManagement\Type;

class Vacancy extends Main
{

    protected function getTypeList(){
        return array(
            FeedbackConfig::TYPE_VACANCY => 'Отклик на вакансию',
            FeedbackConfig::TYPE_APPLICANT => 'Анкета соискателя'
        );
    }

    /**
     * К рулам поиска обращений добавляем фильтрацию по типам обращений (отклики на вакансии и анкеты соискателей)
     * @param $post_data
     * @return array
     */
    protected function getFilterParams($post_data){
        $rules = parent::getFilterParams($post_data);
        $types = $this->getTypeList();
        $rules['type_limit'] = Rule::make('type_id')->setValue(array_keys($types));
        return $rules;
    }

}