<?php
/**
 * Атрибут диапазон
 *
 * @author charles manson
 */
namespace Models\CatalogManagement\Properties;
class Range extends Property{
    const TYPE_NAME = 'range';
    const ALLOW_SET = FALSE;
    /**
     * запрещаем использовать фильтрацию
     */
    const ALLOW_FILTER = FALSE;
    /**
     * запрещаем использовать подбор похожих
     */
    const ALLOW_MAJOR = FALSE;
    /**
     * значение свойства собирается из значений указанного свойства вариантов, поэтому может быть только у айтемов
     */
    const ALLOW_MULTIPLE = FALSE;
	
	const ALOE_DEFAULT = FALSE;

    const VALUES_TYPE_ARRAY = FALSE;
    
    public function isRequired($type_id) {
        //свойство Range не может быть обязательным, т.к. оно автоматически генерируется
        return false;
    }
}

?>
