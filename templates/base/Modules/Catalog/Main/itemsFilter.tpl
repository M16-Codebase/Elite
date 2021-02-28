{capture assign=sort_page_inputs}
	{if !empty($smarty.get.sort)}
		{foreach from=$smarty.get.sort key=sort_key item=sort_val}
			<input type="hidden" name="sort[{$sort_key}]" value="{$sort_val}" class="input-sort">
		{/foreach}
	{else}
		<input type="hidden" name="sort" class="input-sort">
	{/if}
	<input type="hidden" name="page" class="input-page">
	{if !empty($filter_popup)}
		<input type="hidden" name="item_id" value="{$catalog_item.id}">
	{/if}
{/capture}
{?$user_price_key = 'price'}
{if !empty($search_properties)}
	{?$new_search_properties = array()}
	{?$first_fields = array('district'=>1, 'napravlenie'=>1, 'rasstojanie_do_kad_simple'=>1, 'stantsii_metro'=>1, 'klass'=>1, 'ploschad_ot_offer'=>1, 'price_min_variant_closed'=>1)}
	{foreach from=$first_fields item=sprop key=sprop_key}
		{if !empty($search_properties[$sprop_key])}
			{?$new_search_properties[$sprop_key] = $search_properties[$sprop_key]}
		{/if}
	{/foreach}
	{foreach from=$search_properties item=sprop key=sprop_key}
		{if empty($new_search_properties[$sprop_key])}
			{?$new_search_properties[$sprop_key] = $sprop}
		{/if}
	{/foreach}
	
	{?$filter_fields = ''}
	{capture assign=filter_fields name=filter_fields}
		{foreach from=$new_search_properties item=sprop key=sprop_key name=search_properties}
			
			{* Частные случаи *}
                        
			{if $sprop.key == 'title' || ($sprop.group.key == 'prices' && $sprop.key != $user_price_key)}
			
			{* Общие случаи *}
			
			{else}			
				{if $sprop.data_type=='flag'}
					<section class="field covering-section" data-prop-key="{$sprop.key}">
						<label class="covering-variant-label">
							<input type="checkbox" name="{$sprop.key}" value="1" class="covering-variant cbx">
							{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}
						</label>
					</section>
				{elseif $sprop.search_type=='between'}
					{if !empty($sprop.search_values.min) && !empty($sprop.search_values.max) && $sprop.search_values.min != $sprop.search_values.max}
						<section class="field covering-section range-widget-section slider-wrap" data-prop-key="{$sprop.key}">
							<input type="hidden" name="{$sprop.key}[min]" class="input-min" />
							<input type="hidden" name="{$sprop.key}[max]" class="input-max" />
							<div class="covering-section-header">
								<div class="range-reset-button a-hidden"></div>
								{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}
							</div>
							<div class="slider range" data-min="{$sprop.search_values.min}" data-max="{$sprop.search_values.max}" data-step="{$sprop.search_values.step}"></div>
							<span class="minValue text-min">{$sprop.search_values.min}</span>
							<span> &mdash; </span>
							<span class="maxValue text-max">{$sprop.search_values.max}</span>
							{if !empty($sprop.mask) && $sprop.mask != ldelim . "!" . rdelim}
								<span>{$sprop.mask|replace:ldelim . "!" . rdelim:''}</span>
							{/if}
						</section>
					{/if}
				{elseif $sprop.search_type=='check'}
					{if !empty($sprop.search_values)}
						<section class="field covering-section" data-prop-key="{$sprop.key}">                     
							<div class="covering-section-header">
								<div class="range-reset-button a-hidden"></div>
								{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}
							</div>
							{foreach from=$sprop.search_values item=sval_view key=val}
								<label class="covering-variant-label">
									<input type="checkbox" name="{$sprop.key}[]" value="{$val}" class="covering-variant cbx">
									{$sval_view}
								</label>
							{/foreach}
						</section>
					{/if}	
				{elseif $sprop.search_type=='select'}
					{if !empty($sprop.search_values)}
						<section class="field covering-section" data-prop-key="{$sprop.key}">                     
							<div class="covering-section-header">
								<div class="range-reset-button a-hidden"></div>
								{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}
							</div>
							<select name="{$sprop.key}" class="chosen fullwidth">
								<option value="">Выберите...</option>
								{foreach from=$sprop.search_values item=sval_view key=val}
									<option value="{$val}">{$sval_view}</option>
								{/foreach}
							</select>
						</section>
					{/if}		
				{elseif $sprop.search_type=='autocomplete'}
					<section class="field covering-section" data-prop-key="{$sprop.key}">                     
						<div class="covering-section-header">
							<div class="range-reset-button a-hidden"></div>
							{if !empty($sprop.filter_title)}{$sprop.filter_title}{else}{$sprop.title}{/if}
						</div>
						<input type="text" name="{$sprop.key}" class="autocomplete" data-url="/catalog/getPropertyValuesByKey/" data-key="{$sprop.key}" data-id="{$current_type.id}"{if !empty($sprop.multiple)} data-multi=1{/if} />
					</section>
				{/if}
			{/if}
			
		{/foreach}
	{/capture}
	
	{?$filter_fields = $filter_fields|trim}
	{if !empty($filter_fields)}
		<section class="range-widget m-range-catalog">
			<form class="aside-filter user-form{if !empty($filter_popup)} slide-box m-open{/if}" method="GET">
				<div class="fields-set slide-body">
					{$sort_page_inputs|html}
					{$filter_fields|html}
				</div>
				<div class="buttons a-inline-cont">
					<div class="reset-all-widget clear-form">
						Сбросить фильтр
					</div>
					{if !empty($filter_popup)}
						<div class="hide-filter slide-header">
							<span class="open">Свернуть фильтр</span>
							<span class="close">Показать фильтр</span>
						</div>
					{/if}
				</div>
			</form>	
		</section>
	{else}
		<form class="aside-filter m-hidden" method="GET">
			{$sort_page_inputs|html}
		</form>
	{/if}
{else}
	<form class="aside-filter m-hidden" method="GET">
		{$sort_page_inputs|html}
	</form>
{/if}