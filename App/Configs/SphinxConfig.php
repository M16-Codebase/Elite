<?php
/**
 * Created by PhpStorm.
 * User: Charles Manson
 * Date: 03.10.14
 * Time: 20:03
 */

namespace App\Configs;


class SphinxConfig {
    const SPHINX_CONNECT_STRING = 'mysql://lps:D4in3WZjq6@mysql.loc:9306/lps';

    const CATALOG_KEY = 'm16_catalog';
    const POSTS_KEY = 'm16_posts';
    const METATAGS_KEY = 'm16_metatags';
    const DEFAULT_STOP_LIST_SIZE = 100;

    const ENABLE_SPHINX = false;
    const INDEX_TYPE = 'plain'; // Тип используемых индексов 'rt' для real-time индексов и 'plain' для дисковых
    const ENABLE_AUTOCOMPLETE = TRUE;
    const ENABLE_SEGMENTS = FALSE;

    const CATALOG_SEARCH_PROP_KEY = 'sphinx_search_value';

    const STOPWORDS_FILE = '/data/stopwords.txt';
    const WORDFORMS_FILE = '/data/wordforms.txt';


    const MATCH_BLOCK_OPTS = 'match_block_opts';
    const SEO_MATCH_BLOCK_OPTS = 'seo_match_block_opts';
    /**
     * Параметры оформления совпадающих блоков поиска
     * @var array
     */
    private static $seo_match_block_opts = array(
        "before_match" => '>>>',	    // Строка, вставляемая перед ключевым словом. По умолчанию "<b>".
        "after_match" => '<<<',	    // Строка, вставляемая после ключевого слова. По умолчанию "</b>".
        "chunk_separator" => '||',	// Строка, вставляемая между частями фрагмента. по умолчанию " ... ".
        "limit" => 256,	                // Максимальный размер фрагмента в символах. Integer, по умолчанию 256.
        "around" => 0,	                // Сколько слов необходимо выбрать вокруг каждого совпадающего с ключевыми словами блока. Integer, по умолчанию 5.
        "exact_phrase" => FALSE,	    // Необходимо ли подсвечивать только точное совпадение с поисковой фразой, а не отдельные ключенвые слова. Boolean, по умолчанию FALSE.
        "single_passage" => FALSE,	    // Необходимо ли извлечь только единичный наиболее подходящий фрагмент. Boolean, по умолчанию FALSE.
        "query_mode" => 1
    );
    private static $match_block_opts = array(
        "before_match" => '<b>',	    // Строка, вставляемая перед ключевым словом. По умолчанию "<b>".
        "after_match" => '</b>',	    // Строка, вставляемая после ключевого слова. По умолчанию "</b>".
        "chunk_separator" => ' ... ',	// Строка, вставляемая между частями фрагмента. по умолчанию " ... ".
        "limit" => 256,	                // Максимальный размер фрагмента в символах. Integer, по умолчанию 256.
        "around" => 5,	                // Сколько слов необходимо выбрать вокруг каждого совпадающего с ключевыми словами блока. Integer, по умолчанию 5.
        "exact_phrase" => FALSE,	    // Необходимо ли подсвечивать только точное совпадение с поисковой фразой, а не отдельные ключенвые слова. Boolean, по умолчанию FALSE.
        "single_passage" => FALSE,	    // Необходимо ли извлечь только единичный наиболее подходящий фрагмент. Boolean, по умолчанию FALSE.
        "query_mode" => 1
    );

    public static function getOpts($key){
        switch($key){
            case self::MATCH_BLOCK_OPTS:
                return self::$match_block_opts;
                break;
            case self::SEO_MATCH_BLOCK_OPTS:
                return self::$seo_match_block_opts;
                break;
            default:
                return NULL;
        }
    }

    private static $catalog_default_index_props = array(
        CatalogConfig::CATALOG_KEY => array(
            CatalogConfig::KEY_ITEM_TITLE,
            CatalogConfig::KEY_VARIANT_TITLE
        )
    );

    /**
     * @param string $catalog_key
     * @return string[]
     */
    public static function getCatalogDefaultIndexProps($catalog_key) {
        return !empty(self::$catalog_default_index_props[$catalog_key])
            ? self::$catalog_default_index_props[$catalog_key]
            : array();
    }

    /**
     * Индивидуальные параметры индекса
     * веса полей индекса, требуется только для тех индексов, у которых несколько индексируемых полей и их нужно распределить по значимости
     * для остальных игнорируется
     * формат - 'field_weights' => string"<field_1>=<int>, <field_2>=<int>"
     * @var array
     */
    protected static $index_params = array(
        self::CATALOG_KEY => array(
            'enable_stop_words' => FALSE
        ),
        self::POSTS_KEY => array(
            'enable_stop_words' => TRUE,
            'stop_words_count' => 100,
            'field_weights' => 'title=20, annotation=10, text=5'
        ),
        self::METATAGS_KEY => array(
            'enable_stop_words' => FALSE,
            'has_delta_index' => FALSE
        )
    );

    public static function getIndexConfig($index_key){
        return (isset(self::$index_params[$index_key])) ? self::$index_params[$index_key] : NULL;
    }

} 