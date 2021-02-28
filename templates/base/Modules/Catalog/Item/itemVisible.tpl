{if !empty($segments) && $account->isPermission('catalog-item', 'changeSegmentsItemVariantsVisible')}
	<form action="/catalog-item/changeSegmentsItemVariantsVisible/" class="item-var-visability">
		<input type="hidden" name="id" value="{$catalog_item.id}" />
		{?$current_region = $account->getUser()->getSegment()}
		<div class="scrolled item-visible-table">
			<div class="titles">
				<table>
					<tr>
						<th colspan="3">&nbsp;</th>
					</tr>
					{?$row_i = 0}
					{?$type_parents = $current_type->getParents()}
					{foreach from=$type_parents item=parent}
						{if $parent.id != 1}
							{?$row_i++}
							<tr class="row-type{if $row_i%2 == 0} even{/if}">
								<td class="small"><div class="table-icon type"></div></td>
								<td colspan="2">{$parent.title}</td>
							</tr>
						{/if}
					{/foreach}	
					{?$row_i++}
					<tr class="row-type{if $row_i%2 == 0} even{/if}">
						<td class="small"><div class="table-icon type"></div></td>
						<td colspan="2">{$current_type.title}</td>
					</tr>
					{?$row_i++}
					<tr class="row-item{if $row_i%2 == 0} even{/if}">
						<td class="small"><input type="checkbox" class="check-row title-item-{$catalog_item.id}" data-id="i{$catalog_item.id}" /></td>
						<td colspan="2">{if !empty($catalog_item.title)}{$catalog_item.title}{else}No title{/if}</td>
					</tr>
					{foreach from=$catalog_item_variants item=variant}
						{?$row_i++}
						<tr{if $row_i%2 == 0} class="even"{/if}>
							<td class="small">&nbsp;</td>
							<td class="small"><input type="checkbox" class="check-row title-item-{$variant.id}" data-id="{$variant.id}" /></td>
							<td>{*$variant.code*} {if !empty($variant.variant_title)}{$variant.variant_title}{/if}</td>
						</tr>
					{/foreach}
				</table>
			</div>
			<div class="scroller">
				<table>
					<tr>
						<th></th>
						{foreach from=$segments item=$region}
							<th data-reg="{$region.id}" class="region-title{if $region.id == $current_region.id} m-current{/if}">
								<input type="checkbox" class="check-col title-reg-{$region.id}" data-reg="{$region.id}" />
								{$region.title}
							</th>
						{/foreach}
					</tr>
					{?$row_i = 0}
					{foreach from=$type_parents item=parent}
						{if $parent.id != 1}
                            {?$visible = $parent['all_visible']}
							{?$row_i++}
							<tr class="row-type{if $row_i%2 == 0} even{/if}">
								<td class="hidden">{$parent.title}</td>
								{foreach from=$segments item=$region name=type_segments}
									<td class="reg-{$region.id}{if $region.id == $current_region.id} m-current{/if}">
                                        {?$vis_key = !empty($visible[$region.id]) ? $visible[$region.id] : 'none'}
										<div class="table-icon visibility m-{$vis_key}"></div>
									</td>
								{/foreach}
							</tr>
						{/if}
					{/foreach}
					{?$row_i++}
					<tr class="row-type{if $row_i%2 == 0} even{/if}">
						<td class="hidden">{$current_type.title}</td>
						{foreach from=$segments item=$region name=type_segments}
							<td class="reg-{$region.id}{if $region.id == $current_region.id} m-current{/if}">
								{?$vis_key = !empty($visible_by_segments[$region.id]) ? $visible_by_segments[$region.id] : 'none'}
								<div class="table-icon visibility m-{$vis_key}"></div>
							</td>
						{/foreach}
					</tr>
					{?$row_i++}
					<tr class="row-item row-i{$catalog_item.id}{if $row_i%2 == 0} even{/if}" data-id="{$catalog_item.id}">
						<td class="hidden">{if !empty($catalog_item.title)}{$catalog_item.title}{else}No title{/if}</td>
						{foreach from=$segments item=$region name=item_segments}
							<td class="reg-{$region.id}{if $region.id == $current_region.id} m-current{/if}" data-reg="{$region.id}">
								<input type="checkbox" name="item_check[{$region.id}]" class="check" />
								{?$vis_key = !empty($item_visible_by_segments[$region.id]) ? $item_visible_by_segments[$region.id].real_value : 'none'}
								<div class="dropdown">
									<div class="table-btn dropdown-toggle visibility m-{$vis_key}"></div>
									<ul class="dropdown-menu dd-list a-hidden">
										<li class="any{if $vis_key == 'any'} a-hidden{/if}"><a href="#" data-visible="any">Показать</a></li>
										<li class="none{if $vis_key == 'none'} a-hidden{/if}"><a href="#" data-visible="none">Скрыть</a></li>
										<li class="export{if $vis_key == 'export'} a-hidden{/if}"><a href="#" data-visible="export">Выгружать</a></li>
									</ul>
								</div>
							</td>
						{/foreach}
					</tr>
					{foreach from=$catalog_item_variants item=variant name=var_visible}
						{?$row_i++}
						<tr class="row-{$variant.id}{if $row_i%2 == 0} even{/if}{if last} row-last{/if}" data-id="{$variant.id}">
							<td class="hidden">{*$variant.code*} {if !empty($variant.variant_title)}{$variant.variant_title}{/if}</td>
							{?$var_vis = $variant->getPropertyBySegments('variant_visible')}
							{foreach from=$segments item=$region name=var_segments}
								<td class="td-vatiant reg-{$region.id}{if $region.id == $current_region.id} m-current{/if}" data-reg="{$region.id}">
									<input type="checkbox" name="check[{$region.id}][{$variant.id}][]" class="check" />
									{?$vis_key = !empty($var_vis[$region.id]) ? $var_vis[$region.id].real_value : 'none'}
									<div class="dropdown">
										<div class="table-btn dropdown-toggle visibility m-{$vis_key}"></div>
										<ul class="dropdown-menu dd-list a-hidden">
											<li class="any{if $vis_key == 'any'} a-hidden{/if}"><a href="#" data-visible="any">Показать</a></li>
											<li class="none{if $vis_key == 'none'} a-hidden{/if}"><a href="#" data-visible="none">Скрыть</a></li>
											<li class="export{if $vis_key == 'export'} a-hidden{/if}"><a href="#" data-visible="export">Выгружать</a></li>
										</ul>
									</div>
								</td>
							{/foreach}
						</tr>
					{/foreach}
				</table>
			</div>
		</div>	
	</form>			
{/if}