{?$filter_fields = ''}
{if !empty($search_properties) && !$current_type.allow_children}
	{capture assign=filter_fields name=filter_fields}
		<div class="field">
			<div class="f-title">ID</div>
			<div class="f-input">
				<input type="text" name="item_id" />
			</div>
		</div>
		{foreach from=$search_properties item=sprop key=s_prop_id name=search_properties}
			{if $sprop.data_type=='flag'}
				<div class="field">
					<div class="f-input cbx">
						<label>
							<input type="checkbox" name="{$sprop.key}" value="1" /> {if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}
						</label>
					</div>
				</div>
			{elseif $sprop.search_type=='between'}
				{if isset($sprop.search_values.min) && !empty($sprop.search_values.max)}
					<div class="field">
						<div class="f-title">{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}</div>
						<div class="f-input slider-wrap">
							<div class="clearbox slider-inputs">
								<input type="text" name="{$sprop.key}[min]" class="input-min slider-input a-left" />
								<input type="text" name="{$sprop.key}[max]" class="input-max slider-input a-right" />
								—
							</div>
							<div class="slider range" data-min="{$sprop.search_values.min}" data-max="{$sprop.search_values.max}" data-step="{$sprop.search_values.step? $sprop.search_values.step : 1}"></div>
						</div>
					</div>
				{/if}
			{elseif $sprop.search_type=='check'}
				{if !empty($sprop.search_values)}
					<div class="field">
						<div class="f-title">{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}</div>
						<div class="f-input cbx">
							{foreach from=$sprop.search_values item=sval_view key=val}
								<label>
									<input type="checkbox" name="{$sprop.key}[]" value="{$val}" />&nbsp;{$sval_view}
								</label>
							{/foreach}
						</div>
					</div>
				{/if}
			{elseif $sprop.search_type=='select'}
				{if !empty($sprop.search_values)}
					<div class="field">
						<div class="f-title">{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}</div>
						<div class="f-input">
							<select name="{$sprop.key}">
								<option value="">Выберите</option>
								{foreach from=$sprop.search_values item=sval_view key=val}
									<option value="{$val}">{$sval_view}</option>
								{/foreach}
							</select>
						</div>
					</div>
				{/if}
			{elseif $sprop.search_type=='autocomplete'}
				<div class="field">
					<div class="f-title">{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}</div>
					<div class="f-input">
						<input type="text" name="{$sprop.key}" class="autocomplete" data-url="/catalog/getPropertyValuesByKey/" data-key="{$sprop.key}" data-params="type_id:{!empty($sprop.origin_type_id) && $sprop.type_id != $sprop.origin_type_id ? $sprop.type_id : $current_type.id}"{if !empty($sprop.multiple)} data-multi=1{/if} />
					</div>
				</div>
			{/if}
		{/foreach}
	{/capture}
{/if}

{?$filter_fields = $filter_fields|trim}
{if !empty($filter_fields)}
	<section class="aside-filter">
		<form method="GET" action="{if !empty($filter_url)}{$filter_url}{else}/catalog-item/listItems/{/if}" class="user-form items-filter">
			<input type="hidden" name="id" value="{$current_type.id}" />
			{if $moduleUrl == 'order-admin' && !empty($catalog_children)}
				<div class="field">
					<div class="f-title">Тип клиента</div>
					<div class="f-input cbx">
						{foreach from=$catalog_children item=child_type key=type_key}
							<label>
								<input type="checkbox" name="type_id[]" value="{$child_type.id}" />&nbsp;{$child_type.title}
							</label>
						{/foreach}
					</div>
				</div>
			{/if}
			{$filter_fields|html}
			<div class="float-button">
				<button class="submit btn btn-main a-block">Показать <span class="num small-descr"></span></button>
			</div>
			<div class="buttons">
				<button class="submit btn btn-main a-block">Показать</button>
				<div class="link-cont">
					<span class="clear-form a-link small-descr">Сбросить фильтр</span>
				</div>
			</div>
		</form>
	</section>
	{if empty($currentCatalog)}
		{?$currentCatalog = $current_type->getCatalog()}
	{/if}
	{*if $currentCatalog.key == 'catalog'}
		<div class="get-ids-cont">
			<a href="#" class="get-ids">Получить id</a>
		</div>
	{/if*}
{else}
	<section class="aside-filter m-empty">
		<form method="GET" action="{if !empty($filter_url)}{$filter_url}{else}/catalog-item/listItems/{/if}" class="user-form items-filter" data-ignoreempty="1">
			<input type="hidden" name="id" value="{$current_type.id}" />
		</form>
	</section>
	{?$empty_filter = true}
{/if}