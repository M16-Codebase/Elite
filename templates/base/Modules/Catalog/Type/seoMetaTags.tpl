{?$currentCatalog = $current_type->getCatalog()}
<h3>Мета-теги</h3>

переменные для типа
<select>
    {foreach from=$meta_tags_type_vars key=key item=item}
        <option value="{$key}">{$item}</option>
    {/foreach}
</select>
переменные для айтема
<select>
    {foreach from=$meta_tags_item_vars key=key item=item}
        <option value="{$key}">{$item}</option>
    {/foreach}
</select>
<h4>Теги текущего типа</h4>
{?$tags_assigned = !empty($type_seo_tags) && $type_seo_tags.parent_type.id == $current_type.id}
{if !empty($type_seo_tags) && $type_seo_tags.parent_type.id != $current_type.id}
    Параметры унаследованы от типа <a href="/catalog-type/catalog/?id={$type_seo_tags.parent_type.id}&tab=seo">{$type_seo_tags.parent_type.title}</a>
{/if}
{include file="Modules/Catalog/Type/seoMetaTagsForm.tpl" seo_tags_data=$type_seo_tags tags_assigned=$tags_assigned tags_action_value='items' seo_page_uid=$current_type.url}

{if $current_type.allow_children}
    <h4>Теги дочерних типов</h4>
    {?$tags_assigned = !empty($type_children_seo_tags) && $type_children_seo_tags.parent_type.id == $current_type.id}
    {if !empty($type_children_seo_tags) && $type_children_seo_tags.parent_type.id != $current_type.id}
        Параметры унаследованы от типа <a href="/catalog-type/catalog/?id={$type_children_seo_tags.parent_type.id}&tab=seo">{$type_children_seo_tags.parent_type.title}</a>
    {/if}
    {include file="Modules/Catalog/Type/seoMetaTagsForm.tpl" seo_tags_data=$type_children_seo_tags tags_assigned=$tags_assigned tags_action_value='items' seo_page_uid=$current_type.url . '*'}
{/if}

<h4>Теги {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['i'] : $currentCatalog.word_cases['i']['2']['i']} типа</h4>
{?$tags_assigned = !empty($type_items_seo_tags) && $type_items_seo_tags.parent_type.id == $current_type.id}
{if !empty($type_items_seo_tags) && $type_items_seo_tags.parent_type.id != $current_type.id}
    Параметры унаследованы от типа <a href="/catalog-type/catalog/?id={$type_items_seo_tags.parent_type.id}&tab=seo">{$type_items_seo_tags.parent_type.title}</a>
{/if}
{include file="Modules/Catalog/Type/seoMetaTagsForm.tpl" seo_tags_data=$type_items_seo_tags tags_assigned=$tags_assigned tags_action_value='viewItem' seo_page_uid=$current_type.url . '*'}