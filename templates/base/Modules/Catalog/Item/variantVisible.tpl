{if !empty($segments)}
	{?$current_segment = $request_segment}
	{?$current_visibility = !empty($vis_items)? $vis_items : $item_visible_by_segments}
	<table class="ribbed type-segments-table" data-prop="{!empty($vis_items)? 'variant_visible' : 'visible'}" data-url="{!empty($vis_items)? '/catalog-item/changeVariantProp/' : '/catalog-item/changeItemProp/'}">
		<tr class="current-region">
			<td><div class="region-marker">{$current_segment.title} <span class="descr">(Выбран)</span></div></td>
			<td class="segments-visible">
				<input type="hidden" name="region_id" value="{$current_segment.id}" />
				{if !empty($var_id)}
					<input type="hidden" name="variant_id" value="{$var_id}" />
				{/if}
				<div class="{*dropdown*}">
					{?$regVis = !empty($current_visibility[$current_segment.id])? $current_visibility[$current_segment.id].real_value : 'none'}
					<div class="table-btn dropdown-toggle visibility{if $regVis == 'none'} m-hide{elseif $regVis == 'export'} m-upload{/if}"></div>
					{*<ul class="dropdown-menu dd-list">
						<li{if $regVis == 'any'} class="a-hidden"{/if}><a href="#" data-visible="any">Показать</a></li>
						<li{if $regVis == 'none'} class="a-hidden"{/if}><a href="#" data-visible="none">Скрыть</a></li>
						<li{if $regVis == 'export'} class="a-hidden"{/if}><a href="#" data-visible="export">Выгружать</a></li>
					</ul>*}
				</div>
				<span class="descr visible-text">
					<span class="any{if $regVis == 'any'} m-shown{/if}">— показывать и выгружать в 1С</span>
					<span class="none{if $regVis == 'none'} m-shown{/if}">— не показывать и не выгружать в 1С</span>
					<span class="export{if $regVis == 'export'} m-shown{/if}">— выгружать в 1С, но не показывать</span>
				</span>
			</td>
		</tr>
		{if $account->isPermission('profile', 'changeRegion')}
			{foreach from=$segments item=reg}
				{if $reg.id != $current_segment.id}
					<tr>
						<td><div class="region-marker">{$reg.title}</div></td>
						<td class="segments-visible">
							<input type="hidden" name="region_id" value="{$reg.id}" />
							{if !empty($var_id)}
								<input type="hidden" name="variant_id" value="{$var_id}" />
							{/if}
							<div class="{*dropdown*}">
								{?$regVis = !empty($current_visibility[$reg.id])? $current_visibility[$reg.id].real_value : 'none'}
								<div class="table-btn dropdown-toggle visibility{if $regVis == 'none'} m-hide{elseif $regVis == 'export'} m-upload{/if}"></div>
								{*<ul class="dropdown-menu dd-list">
									<li{if $regVis == 'any'} class="a-hidden"{/if}><a href="#" data-visible="any">Показать</a></li>
									<li{if $regVis == 'none'} class="a-hidden"{/if}><a href="#" data-visible="none">Скрыть</a></li>
									<li{if $regVis == 'export'} class="a-hidden"{/if}><a href="#" data-visible="export">Выгружать</a></li>
								</ul>*}
							</div>
							<span class="descr visible-text">
								<span class="any{if $regVis == 'any'} m-shown{/if}">— показывать и выгружать в 1С</span>
								<span class="none{if $regVis == 'none'} m-shown{/if}">— не показывать и не выгружать в 1С</span>
								<span class="export{if $regVis == 'export'} m-shown{/if}">— выгружать в 1С, но не показывать</span>
							</span>
						</td>
					</tr>
				{/if}
			{/foreach}
		{/if}
	</table>
{/if}