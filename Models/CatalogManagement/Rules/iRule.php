<?php
/**
 *
 * @author olga
 */
namespace Models\CatalogManagement\Rules;
interface iRule {
    function _getSql($props, &$sql, $segment_id = NULL);
}

?>
