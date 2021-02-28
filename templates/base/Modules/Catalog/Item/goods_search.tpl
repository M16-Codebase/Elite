{?$includeJS.search_block = 'Modules/Catalog/Main/search.js'}
{?$max_show = 5}
{?$currentCatalog = $current_type->getCatalog()}
<div class="filter_params">
	<div class="filter-header{if !empty($search_params)} open{/if}">
        {?$counters = $type->getData('counters')}
		<div class="number a-right">Фильтром отобраны {$catalog_items_count} из {$counters.visible_items + $counters.hidden_items} {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['r'] : $currentCatalog.word_cases['i']['2']['r']}.</div>
		<h3 class="title">Фильтр по {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['d'] : $currentCatalog.word_cases['i']['2']['d']}</h3>
	</div>
	<div class="filter-body{if empty($search_params)} a-hidden{/if}">
		<input type="hidden" name="id" />
		{foreach from=$search_properties item=sprop key=s_prop_id name=params}
			{if !empty($sprop.search_values) || $sprop.search_type=='autocomplete'}
				<div class="details" data-prop_id ="{$sprop.id}" >
					{if $sprop.key == $global_properties_keys['nalichie-item']}{*для наличия*}
							<div class="title bold p14 blue">{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}</div>
							<ul class="list list-nalichie">
								<li class="variant clearbox">
									<label>
										<input type="checkbox" value="1" name="check_nalichie" class="cbx" />
										<div class="text p14 lblue">
											В наличии
										</div>
									</label>
								</li>
							</ul>
						{else}
							{if $sprop.data_type=='flag'}
								<ul class="list">
									<li class="variant clearbox">
										<label>
											<input type="checkbox" name="{$sprop.key}" value="1" class="cbx a-left" />
											<div class="text p14 lblue a-left">
										{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}
									</div>
								</label>
							</li>
						</ul>
					{else}
						<div class="title bold p14 blue">{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}</div>
						{if $sprop.search_type=='check'}
							<select name="{$sprop.key}" class="multiselect" multiple>
								{foreach from=$sprop.search_values item=sval_view key=val}
									<option value="{$val}">{$sval_view}</option>
								{/foreach}
							</select>
						{elseif $sprop.search_type=='select'}
							<div class="rating"> 
								<select name="{$sprop.key}" class="select selectmenu small-select">
									<option value="">Все</option>
									{foreach from=$sprop.search_values item=sval_view key=val}
										<option value="{$val}">{$sval_view}{* <span class="pre-number">(15)</span>*}</option>
									{/foreach}
								</select>
							</div>
						{elseif $sprop.search_type=='autocomplete'}
							<input type="text" name="{$sprop.key}" class="autocomplete parameter" />
						{elseif $sprop.search_type=='between'}
							<div class="input-cost p14 gray3" data-min="{$sprop.search_values.min}" data-max="{$sprop.search_values.max}" data-step="{$sprop.search_values.step}">
								<div class="slider-cont">
									<div class="slider double"></div>
								</div>
								<div class="min_max_inputs m-overflow-hidden">
									<input type="text" size="5" name="{$sprop.key}[min]" class="min cost-from" />
									&nbsp;<span class="ndash">&ndash;</span>&nbsp;
									<input type="text" size="5" name="{$sprop.key}[max]" class="max cost-to" />
									&nbsp;
								</div>
							</div>
						{/if}
					{/if}
				{/if}
			</div>
		{/if}
		{/foreach}
			<div class="details big">
				<div class="number a-right">Фильтром отобраны {$catalog_items_count} из {$counters.visible_items + $counters.hidden_items} {$currentCatalog.nested_in ? $current_type.word_cases['i']['2']['r'] : $currentCatalog.word_cases['i']['2']['r']}.</div>
				<div class="sly-submit sbutton a-button-gray clearbox"><div>Показать</div></div>{*класса submit не должно быть, вместо него обязательный sly-submit*}
				<span class="reset-link bold p12{if empty($search_params)} a-hidden{/if}"><span class="a-link-dotted">Сбросить фильтры</span></span>
			</div>
		</div>

		<div class="under-filter clearbox">
			<div class="a-left settings">
				<span>Показывать</span>
				<select name="pageSize" class="selectmenu small-select">
					<option value="25">25</option>
					<option value="50">50</option>
					<option value="100">100</option>
				</select>
				<span>позиций на страницу</span>
			</div>
		</div>
	</div>