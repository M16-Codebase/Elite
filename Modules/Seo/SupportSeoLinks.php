<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 24.11.17
 * Time: 15:39
 */

namespace Modules\Seo;

use Models\CatalogManagement\Filter\FilterMap;
use Models\Entities\EntityFactory as Factory;
use Models\CatalogManagement\CatalogHelpers\District\DistrictHelper;
use Models\CatalogManagement\Filter\FilterMapHelper;


class SupportSeoLinks extends \LPS\AdminModule
{
    const MODULE_NAME = 'Вспомогательные ссылки';


    public function index()
    {
        $model = Factory::getEntity('SeoLinks');
        /*$data = [
            'href' => 'ldkfklsjdlskjd',
            'text' => 'lasdlkalsdkjaskld',
            'work' => 1
        ];
        $id = $test->insert($data);*/

        /*
         * 1 если есть гет читать его
         * изменить ссылки
         * перерисовать ссылки
         *
         */

        $method = $this->request->query->get('method');
        $id = $this->request->query->get('id');
        $href = $this->request->query->get('href');
        $text = $this->request->query->get('text');
        $work = $this->request->query->get('work');
        //exit;
        //return;
        if (!empty($method)) {
            if ($method === 'edit' || $method === 'add') {
                $ans = $this->setJsonAns()
                    ->setTemplate('Modules/Seo/SupportSeoLinks/list.tpl');

                if ($method === 'edit') {
                    if (!empty($id)) {
                        $data = [
                            'href' => $href,
                            'text' => $text,
                            'work' => $work
                        ];
                        $model->update($data, ['id' => $id]);
                    } else {
                        $ans->setEmptyContent()->addErrorByKey('id', 'empty');
                    }
                }

                if ($method === 'add') {
                    $data = [
                        'href' => $href,
                        'text' => $text,
                        'work' => $work
                    ];
                    $model->insert($data);
                }
            }
        } else {
            $ans = $this->getAns();
        }


        $seoLinks = $model->getAll();

        $moduleInfo = [
            'title' => self::MODULE_NAME
        ];
        $districtsList = DistrictHelper::getInstance()->getDistrictsList();
        $bedNums = FilterMap::getBedRooms();

        $ans->add('bedNums', !empty($bedNums) ? $bedNums : array())
            ->add('districtsList', !empty($districtsList) ? $districtsList : array())
            ->add('customCss', array('jquery-ui.css', 'customUi.css'))
            ->add('customJs', array('customUi.js', 'filterSeo.js', 'support-seo-links.js'))
            ->add('module_info', $moduleInfo)
            ->add('seo_links', $seoLinks);

    }

    public function edit()
    {
        $id = $this->request->request->get('id');

        $ans = $this->setJsonAns();
        //$ans->setEmptyContent()->addErrorByKey('id', $id);
    }


}
