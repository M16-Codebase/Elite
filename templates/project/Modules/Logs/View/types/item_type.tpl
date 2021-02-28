{?$a_data = $l.additional_data}
{if $l.type == 'create' || $l.type == 'delete'}
    {if $l.type == 'create'}Создан{if !$a_data.t_is_c}а{/if} {else}Удален{if !$a_data.t_is_c}а{/if} {/if}
    {if $a_data.t_is_c}каталог{else}категория{/if} <strong>
    «{$a_data.t}»</strong>{if !$a_data.t_is_c} в каталоге <strong>«{$a_data.t_c}»</strong>{/if}
    
{elseif $l.type == 'edit' && $l.attr_id != 'cover_image_id' && $l.attr_id != 'default_image_id'}
    {if !empty($l.attr_id)}
        В {if $a_data.t_is_c}каталоге{else}категории{/if} <strong>«{$a_data.t}»</strong>
        изменен параметр <strong>«{$logged_fields[$l.attr_id]}»</strong> на:<br />{$a_data.v}
        <br />
    {/if}
{elseif $l.type == 'images'}
    {if $l.comment == 'create'}
        Добавлена {if $l.attr_id == 'cover'}обложка{else}картинка по умолчанию{/if} в {if $a_data.t_is_c}каталог{else}категорию{/if}
    {elseif $l.comment == 'delete'}
        Удалена {if $l.attr_id == 'cover'}обложка{else}картинка по умолчанию{/if} из {if $a_data.t_is_c}каталога{else}категории{/if}
    {else}
        Изменена {if $l.attr_id == 'cover'}обложка{else}картинка по умолчанию{/if} в {if $a_data.t_is_c}каталоге{else}категории{/if}
    {/if}
    &nbsp;<strong>«{$a_data.t}»</strong>
{elseif $l.type == 'post'}
    {if $l.comment == 'edit'}Изменено <a href="/logs-view/?type=post&entity_id={$a_data.v}">описание</a> {if $a_data.t_is_c}каталога{else}категории{/if} <strong>«{$a_data.t}»</strong>{/if}
{else}
    ???
{/if}