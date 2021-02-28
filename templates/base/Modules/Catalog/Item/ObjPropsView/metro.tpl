<div class="field sortable m-object object-metro" data-notsend="1" data-items=".object-prop">
	<div class="origin a-hidden row m-fullwidth">
		<div class="drag-drop w05"></div>
		<div class="w55">
			<select name="{$property.key}">
				{foreach from=$stations item=station}
					<option data-id="{$station.id}" value="{$station.id}">{$station.variant_title}</option>
				{/foreach}
			</select>
		</div>
		<div class="w25">
			<input type="text" name="{$property.key}_walk_time" placeholder="Пешком"/>
		</div>
		<div class="w25">
			<input type="text" name="{$property.key}_drive_time" placeholder="На машине"/>
		</div>
		<div class="delete-object action-button w05">
			<i class="icon-prop-delete"></i>
		</div>
	</div>
	{?$prop_val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
	{?$prop_metro_by_pos = array()}
	{foreach from=$entity.properties[$property.key].position key=val_id item=pos}
		{?$prop_metro_by_pos[$pos]['metro'] = $val_id}
		{foreach from=$entity.properties[$walk_prop.key].position key=v_id item=p}
			{if $p == $pos}
				{?$prop_metro_by_pos[$pos]['walk'] = $v_id}
			{/if}
		{/foreach}
		{foreach from=$entity.properties[$drive_prop.key].position key=v_id item=p}
			{if $p == $pos}
				{?$prop_metro_by_pos[$pos]['drive'] = $v_id}
			{/if}
		{/foreach}
	{/foreach}
	{foreach from=$prop_metro_by_pos item=item}
		<div class="row object-prop m-fullwidth">
			<div class="drag-drop w05">
				<input type="hidden" class='input-object' data-val-id='{$item.metro}' name="{$property.key}" value="{$entity.properties[$property.key]['value'][$item.metro]}"/>
				<input type="hidden" class='input-object' data-val-id='{if !empty($item.walk)}{$item.walk}{/if}' name="{$property.key}_walk_time" value="{if !empty($item.walk)}{$entity.properties[$property.key.'_walk_time']['value'][$item.walk]}{/if}"/>
				<input type="hidden" class='input-object' data-val-id='{if !empty($item.drive)}{$item.drive}{/if}' name="{$property.key}_drive_time" value="{if !empty($item.drive)}{$entity.properties[$property.key.'_drive_time']['value'][$item.drive]}{/if}"/>
			</div>
			<div class="w55">
				<select name="{$property.key}" data-val-id='{$item.metro}' class='title'>
					{foreach from=$stations item=station}
						<option data-id="{$station.id}" value="{$station.id}" {if $entity.properties[$property.key]['value'][$item.metro] == $station.id}selected{/if}>{$station.variant_title}</option>
					{/foreach}
				</select>
			</div>
			<div class="w25">
				<input type="text" data-val-id='{if !empty($item.walk)}{$item.walk}{/if}' name="{$property.key}_walk_time" value="{if !empty($item.walk)}{$entity.properties[$property.key.'_walk_time']['value'][$item.walk]}{/if}" placeholder="Пешком"/>
			</div>
			<div class="w25">
				<input type="text" data-val-id='{if !empty($item.drive)}{$item.drive}{/if}' name="{$property.key}_drive_time" value="{if !empty($item.drive)}{$entity.properties[$property.key.'_drive_time']['value'][$item.drive]}{/if}" placeholder="На машине"/>
			</div>
			<div class="delete-object action-button w05">
				<i class="icon-prop-delete"></i>
			</div>
		</div>
	{/foreach}
	<div class="add-row row m-fullwidth">
		<div class="add-item-object add-btn w3">
			<i class="icon-add"></i> <span class="small-descr">Добавить станцию метро</span>
		</div>
		<div class="w8"></div>
		<div class="w1">
			<div class="prop-menu dropdown">
				<div class="dropdown-toggle">
					<i class="icon-prop-more"></i>
				</div>
				<ul class="dropdown-menu a-hidden">
					<li><a href="#" class="delete-all">Удалить все</a></li>
					<li><a href="#" class="sort-alph">Отсортировать по алфавиту</a></li>
				</ul>
			</div>
		</div>
	</div>
</div>