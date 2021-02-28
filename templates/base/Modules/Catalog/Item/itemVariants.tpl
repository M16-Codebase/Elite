{?$currentCatalog = $current_type->getCatalog()}
{if !empty($item_variants_properties)}
	<div class="variants-block">
		<div class="new-variant tab-top select-variant">
			<form class="choose-variant-to-edit">
				<select name="show_variant_id" data-item-url="{$catalog_item->getUrl()}">
					<option value="0">Выбрать</option>
					{if !empty($catalog_item_variants)}
						{foreach from=$catalog_item_variants item=variant}
							{if !empty($catalog_variant)}{?$query_v = $catalog_variant.id}
							{elseif !empty($smarty.get.v)}{?$query_v = $smarty.get.v}
							{elseif !empty($smarty.post.v)}{?$query_v = $smarty.post.v}
							{else}{?$query_v = null}{/if}
							<option value="{$variant.id}" {if !empty($query_v) && $variant.id == $query_v} selected{?$catalog_variant = $variant}{/if}>
								{if !empty($variant.variant_title)} {$variant.variant_title}{else} No title{/if}
							</option>
						{/foreach}
					{/if}
				</select>
			</form>
			<div class="variant-actions">	
				<div class="action-button action-add" title="Добавить {$currentCatalog.word_cases['v']['1']['i']}"><i class="icon-add"></i></div>
				<div class="action-button action-sort" title="Сортировать {$currentCatalog.word_cases['v']['2']['i']}"><i class="icon-sort"></i></div>
			</div>
		</div>

		{if !empty($catalog_variant)}
			<form action="/catalog-item/saveVariant/" class="edit_properties_form content-scroll" data-id="{$catalog_item.id}" data-variant-id="{$catalog_variant.id}">
				<div class="aside-panel">
					{include file="Admin/components/actions_panel.tpl"
						buttons = array(
							'save' => array(
								'text' => 'Сохранить ' . $currentCatalog.word_cases['v']['1']['i']
							),
							'copy' => array(
								'text' => 'Копировать ' . $currentCatalog.word_cases['v']['1']['i']
							),
							'move' => $currentCatalog.key == 'catalog'? array(
								'text' => 'Переместить ' . $currentCatalog.word_cases['v']['1']['i']
							) : null,
							'delete' => array(
								'text' => 'Удалить ' . $currentCatalog.word_cases['v']['1']['i']
							)
						)}
				</div>
				<div class="white-blocks viewport one-variant one-variant-{$catalog_variant.id}" data-variant-id="{$catalog_variant.id}">
					{if !empty($variant_create)}
						<div class="wblock prop-item-cont not-send white-block-row white-header">
							<div class="w12">
								<strong>Новый {$currentCatalog.word_cases['v']['1']['i']}</strong>
							</div>
						</div>
					{/if}
					{if empty($variant_create)}
						<div class="wblock prop-item-cont not-send white-block-row">
							<div class="w2"><strong>Статус</strong></div>
							<div class="w4 variant-show-dropdown dropdown m-status">
								<div class="dropdown-toggle action-button m-status-icon" title="{if $catalog_variant.status == 2}Не отображается{else}Отображается{/if}">
									<i class="icon-{if $catalog_variant.status == 2}hide{else}show{/if}"></i>
									<span>{if $catalog_variant.status == 2}Не отображается{else}Отображается{/if}</span>
								</div>
								<ul class='dropdown-menu a-hidden'>
									<li data-type='0'><span>Отображается</span></li>
									<li data-type='2'><span>Не отображается</span></li>
								</ul>
							</div>
							<div class="w6"></div>
							{*<div class="prop-item a-inline-cont m-visible w11">
								<div class="prop-title m-nomargin h4">Видимость</div>
							</div>
							<div class="action-button action-visibility w1 action-{if $catalog_variant.status == 2}hide{else}show{/if}" title="{if $catalog_variant.status == 2}Не отображается{else}Отображается{/if}">
								<i class="icon-{if $catalog_variant.status == 2}hide{else}show{/if}"></i>
							</div>*}
						</div>
						<div class="wblock prop-item-cont white-block-row">
							<div class="prop-item w12">
								<div class="prop-title h4">ID {$currentCatalog.word_cases['v']['1']['r']}</div>
								<div class="field justify">
									<div class="f-col">
										<input type="text" value="{$catalog_variant.id}" disabled="disabled" />
									</div>
								</div>
							</div>
						</div>
					{/if}
					{include file="Modules/Catalog/Item/edit_item_properties.tpl" item_variants_properties=$item_variants_properties variant_list=true create=false editing_variant_id=$catalog_variant.id}
				</div>
			</form>
		{else}
			<div class="content-scroll">
				<div class="viewport">
				</div>
			</div>
		{/if}
	</div>
{else}
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w12">В данном типе нет расщепляемых параметров</div>
			</div>
		</div>
	</div>
{/if}