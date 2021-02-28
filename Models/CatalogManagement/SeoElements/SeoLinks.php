<?php
/**
 * Created by PhpStorm.
 * User: pahus
 * Date: 07.10.2017
 * Time: 10:30
 */

namespace Models\CatalogManagement\SeoElements;

use Models\CatalogManagement\Filter\FilterMapHelper;
use Models\CatalogManagement\Filter\FilterMap;
use Models\Entities\EntityFactory as Factory;
use Models\CatalogManagement\Filter\FilterSeoItem;


/**
 * формирует список ссылок которые располагаются внизу
 * раздела
 * Class SeoLinks
 * @package Models\CatalogManagement\SeoElements
 */
class SeoLinks
{
    public static $instance;

    const DEFAULT_CATALOG_KEY = '';
    private $catalogKey;

    public static function getInstance()
    {
        if (empty(self::$instance)) {
            self::$instance = new self;
        }
        return self::$instance;
    }

    private function __construct()
    {
        $this->filterMap = new FilterMap();
    }


    public function getStaticSeoLinks($catalog_key) {
        $model = Factory::getEntity('SeoLinks');
        $ret_links = [];
        $links = $model->getAll();
        foreach ($links as $id=>$val) {
            if ($val['work'] == 1) {
                if (!empty($catalog_key)) {
                    if (strpos($val['href'], $catalog_key) !== false) {
                        $ret_links[] = $val;
                    }
                } else {
                    $ret_links[] = $val;
                }
            }
        }
        return $ret_links;
    }

    const COMBUNE_KEY = 'combine';
    const BED_NUMS_KEY = 'bed_num';
    const DISCTR_KEY = 'district';

    private $generateMap =  [
            self::DISCTR_KEY => 1,
            self::BED_NUMS_KEY => 1,
            self::COMBUNE_KEY => 10
        ];

    private $disctricts_ = [
            'resale' => [
                'zolotoj-treugolj',
                'admiraltejskij-r',
                'vasileostrovskij',
                'petrogradskij-ra',
                'primorskij-rajon',
                'tsentraljnyj-raj'
            ],
            'real-estate' => [
                'zolotoj-treugolj',
                'admiraltejskij-r',
                'vasileostrovskij',
                'kalininskij-rajo',
                'krasnogvardejski',
                'krestovskij-ostrov',
                'moskovskij-rajon',
                'petrogradskij-ra',
                'primorskij-rajon',
                'tsentraljnyj-raj'
            ]
        ];

    /**
     * Generate random list of links for filter by friendly url
     * for publish that in content for indexing
     *
     * @param array $items
     * @return array
     */
    public function generateSeoLinks(array $items, array $bedrooms_count, $catalog_key)
    {
        if (empty($catalog_key)) {
            return;
        }

        /*
        1) distrcit + bed_num 10
        2) district 5
        3) bed_num  5
         */
        //dump($items);

        // генерим случ ссылки из списка
        $dks = $this->disctricts_[$catalog_key];
        $dksIts = [];

        foreach ($dks as $dk) {
            $it = FilterSeoItem::getItemByKey($dk);
            $dksIts[] = $it;
        }

        $this->distLinksByDistItem($dksIts, $links);

        $this->catalogKey = $catalog_key;
        $links = [];
        $this->dBrLinks($items, $bedrooms_count, $links);
        $this->distLinks($items, $links);
        $this->brLinks($items, $bedrooms_count, $links);
        $links = array_map("unserialize", array_unique(array_map("serialize", $links)));
        foreach ($links as & $link) {
            if (!empty($link['href'])) {
                $link['href'] = '/'.$link['href'] . '/';
            }
        }
        return $links;
    }


    protected function distLinksByDistItem(array $items, & $links)
        {
            $count = $this->generateMap[self::DISCTR_KEY];
            $indexes = array_rand ( $items, $count );
            if (is_array($indexes)) {
                foreach ($indexes as $index) {
                    if (!empty($items[$index]['key'])) {
                        $link = [
                            'href' => $this->catalogKey . '/' . $items[$index]['key'],
                            'text' => 'Квартиры ' . $items[$index]["prepositional"]
                        ];
                        $links[] = $link;
                    }
                }
            } else {
                if (!empty($items[$indexes]['key'])) {
                    $link = [
                        'href' => $this->catalogKey . '/' . $items[$indexes]['key'],
                        'text' => 'Квартиры ' . $items[$indexes]["prepositional"]
                    ];
                    $links[] = $link;
                }
            }
            return;
        }

    protected function brLinks(array $items, $bedrooms_count, & $links)
    {
        $count = $this->generateMap[self::BED_NUMS_KEY];
		if($items){
			$indexes = array_rand ( $items, $count );
			if (is_array($indexes)) {
				foreach ($indexes as $index) {
					$it = $items[$index];
					$this->addBrSeoLink($it, $bedrooms_count, $links);
				}
			} else {
				$it = $items[$indexes];
				$this->addBrSeoLink($it, $bedrooms_count, $links);
			}
		}
        return;
    }

    /** real-estate
     * To avoid duplicate code, this function
     *
     * @param ItemEntity $item
     * @param $links
     */
    private function addBrSeoLink(\Models\CatalogManagement\Item $item, $bedrooms_count,  & $links)
    {
        if ($this->catalogKey == 'real-estate') {
            if (isset($bedrooms_count[$item['id']])) {
                $rand_ind = array_rand ( $bedrooms_count[$item['id']], 1 );
                $brc = $bedrooms_count[$item['id']][$rand_ind]['bedroom_count'];
                if ($brc > 5) {
                    $brc = 5;
                }
                $bed_number = $this->filterMap->getBedNumberKey($brc);
                $bedNumWord = FilterMapHelper::getInstance()->word_analog($rand_ind);
                $bedNumWord = mb_strtoupper(mb_substr($bedNumWord, 0, 1)) . mb_substr($bedNumWord, 1);
                $link = [
                    'href' => $this->catalogKey . '/' . $bed_number,
                    'text' => $bedNumWord . 'комнатные квартиры '
                ];
                $links[] = $link;
            }
        }
        if ($this->catalogKey == 'resale') {
            if ($item['bed_number'] != 0 && $item['bed_number'] <= 5) {
                $bed_number = $this->filterMap->getBedNumberKey($item['bed_number']);
                $bedNumWord = FilterMapHelper::getInstance()->word_analog($item['bed_number']);
                $bedNumWord = mb_strtoupper(mb_substr($bedNumWord, 0, 1)) . mb_substr($bedNumWord, 1);
                $link = [
                    'href' => $this->catalogKey . '/' . $bed_number,
                    'text' => $bedNumWord . 'комнатные квартиры'
                ];
                $links[] = $link;
            }
        }
        return;
    }

    /**
     * Generate for district and bed_number links
     *
     * @param array $items
     * @return array
     */
    protected function dBrLinks(array $items, $bedrooms_count, & $links)
    {
        $limit = $this->generateMap[self::COMBUNE_KEY];
        $count = (count($items) > $limit) ? $limit : count($items);
		if ($items) {
			$indexes = array_rand ( $items, $count );
			if (is_array($indexes)) {
				foreach ($indexes as $index) {
					$it = $items[$index];
					$this->addSeoLink($it, $bedrooms_count, $links);
				}
			} else {
				$it = $items[$indexes];
				$this->addSeoLink($it, $bedrooms_count, $links);
			}
		}
        return;
    }


    protected function distLinks(array $items, & $links)
    {
        $count = $this->generateMap[self::DISCTR_KEY];
		//print_r($items);
		//echo('|||||'.$count);
		if($items){
			$indexes = array_rand ( $items, $count );
			if (is_array($indexes)) {
				foreach ($indexes as $index) {
					if (!empty($items[$index]['district']['key'])) {
						$link = [
							'href' => $this->catalogKey . '/' . $items[$index]['district']['key'],
							'text' => 'Квартиры ' . $items[$index]['district']["prepositional"]
						];
						$links[] = $link;
					}
				}
			} else {
				if (!empty($items[$indexes]['district']['key'])) {
					$link = [
						'href' => $this->catalogKey . '/' . $items[$indexes]['district']['key'],
						'text' => 'Квартиры ' . $items[$indexes]['district']["prepositional"]
					];
					$links[] = $link;
				}
			}
		}
        return;
    }

    /** real-estate
     * To avoid duplicate code, this function
     *
     * @param ItemEntity $item
     * @param $links
     */
    private function addSeoLink(\Models\CatalogManagement\Item $item, $bedrooms_count,  & $links)
    {
        if ($this->catalogKey == 'real-estate') {
            if (isset($bedrooms_count[$item['id']]) && !empty($item['district']['key']) ) {
                $rand_ind = array_rand ( $bedrooms_count[$item['id']], 1 );
                $brc = $bedrooms_count[$item['id']][$rand_ind]['bedroom_count'];
                if ($brc > 5) {
                    $brc = 5;
                }
                $bed_number = $this->filterMap->getBedNumberKey($brc);
                $bedNumWord = FilterMapHelper::getInstance()->word_analog($rand_ind);
                $bedNumWord = mb_strtoupper(mb_substr($bedNumWord, 0, 1)) . mb_substr($bedNumWord, 1);
                $link = [
                    'href' => $this->catalogKey . '/' . $bed_number . '__' . $item['district']['key'],
                    'text' => $bedNumWord . 'комнатные квартиры ' . $item['district']['prepositional']
                ];
                $links[] = $link;
            }
        }
        if ($this->catalogKey == 'resale') {
            if ($item['bed_number'] != 0 && $item['bed_number'] <= 5 && !empty($item['district']['key'])) {
                $bed_number = $this->filterMap->getBedNumberKey($item['bed_number']);
                $bedNumWord = FilterMapHelper::getInstance()->word_analog($item['bed_number']);
                $bedNumWord = mb_strtoupper(mb_substr($bedNumWord, 0, 1)) . mb_substr($bedNumWord, 1);
                $link = [
                    'href' => $this->catalogKey . '/' . $bed_number . '__' . $item['district']['key'],
                    'text' => $bedNumWord . 'комнатные квартиры ' . $item['district']['prepositional']
                ];
                $links[] = $link;
            }
        }
        return;
    }
}
