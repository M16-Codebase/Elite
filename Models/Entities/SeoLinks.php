<?php
/**
 * Created by PhpStorm.
 * User: pavel
 * Date: 23.11.17
 * Time: 22:09
 */

namespace Models\Entities;


class SeoLinks extends Entity
{
    const TABLE = 'support_seo_links';

    private $href;
    private $text;
    private $work;

    protected $tableModel = [
        'id' => ['type'=> 'int'],
        'href' => ['type' => 'string'],
        'text' => ['type' => 'string'],
        'work' => ['type'=> 'int']
    ];

    public function __construct()
    {
        parent::__construct();
        $this->table = self::TABLE;
    }

    protected function tableModel()
    {
        return $this->tableModel;
    }

}
