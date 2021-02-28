{?$current_catalog = $current_type->getCatalog()}
<form action="/order-admin/save/" class="edit_properties_form">
    {if !empty($catalog_item)}
        <input name="id" type="hidden" value="{$catalog_item.id}" />
    {/if}    
    <div class="content-top">
		<h1>
			{if $catalog_item.status == 1}Добавление {$current_catalog.word_cases.i.1.r} {else}Редактирование {$current_catalog.word_cases.i.1.r}{if !empty($catalog_item)} №{$catalog_item.number}{/if} {/if}
			от {if $current_type.key == 'orders_fiz'}физического{else}юридического{/if} лица
		</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => empty($smarty.post.id)? 'Создать' : 'Сохранить',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>

	<div class="content-scroll">
		<div class="white-blocks viewport order-props" data-type="{if $current_type.key == 'orders_fiz'}fiz{else}org{/if}">
			{include file="Modules/Catalog/Item/edit_item_properties.tpl"}
		</div>
    </div>
    <input type="submit" class="a-hidden" />
</form>