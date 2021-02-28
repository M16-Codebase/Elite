{if !empty($type)}
	{if $type.fixed && $accountType != 'SuperAdmin'}
		{?$type_unchangeable = 1}
	{else}
		{?$type_unchangeable = 0}
	{/if}
	<div class="wblock white-block-row" data-type_id="{$type.id}">
		<a class="type-title w8" href="/catalog-type/catalog/?id={$type.id}">
			<input type="hidden" name="type_id" value="{$type['id']}" />
			<input type="hidden" name="parent_id" value="{$current_type.id}" />
			<strong>{$type.title}</strong>&nbsp;&nbsp;<span class="descr">{$type['counters']['all_items']}</span>
		</a>
		<div class="type-buttons w4">
			{if $accountType == 'SuperAdmin'}
				<div class="dropdown a-right">
					<div class="dropdown-toggle bush-btn">
						<i class="icon-more"></i>
					</div>
					<ul class="dropdown-menu a-hidden">
						<li class="action-add" data-nest="{$type.id}"><span class="a-link">Добавить тип</span></li>
						<li class="action-delete"><span class="a-link">Удалить</span></li>
					</ul>
				</div>
				<div class="edit-type bush-btn a-right{if $type_unchangeable} m-inactive{else} m-active{/if}">
					<i class="icon-edit"></i>
				</div>
			{/if}
		</div>
	</div>
	{if !empty($child_types[$type.id])}
		<div class="wblock-tree">
			{foreach from=$child_types[$type.id] item=ctype}
				<div>
					{include file="Modules/Catalog/Type/childTypesList.tpl" type=$ctype}
				</div>
			{/foreach}
		</div>
	{/if}
{/if}