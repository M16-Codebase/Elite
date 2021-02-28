{if !empty($prop_groups)}
    {foreach from=$prop_groups item=group}
		{if $accountType == 'SuperAdmin'}
			<div class="wblock white-block-row{if $group.type_id != $current_type.id} unchangeable{/if}" data-group_id="{$group.id}" data-type_id="{$group.type_id}" data-position="{$group.position}">
				{if $group.type_id != $current_type.id}
					<div class="w05"></div>
					<div class="w11">
						<strong>{$group.title}</strong>
						&nbsp;&mdash;&nbsp; наследовано от типа <a href="/catalog-type/catalog/?id={$group.type_id}&tab=groups">&laquo;{$group.type_title}&raquo;</a>
					</div>
					<div class="w05"></div>
				{else}
					<div class="w05 {if empty($current_type_unchangeable) && $account->isPermission('catalog-type', 'movePropGroup')}drag-drop{/if}"></div>
					<div class="w10{if empty($current_type_unchangeable) && $account->isPermission('catalog-type', 'editPropGroup')} a-link edit-group{/if}"
						data-group-id="{$group.id}" data-key="{$group.key}" data-group="{$group.group}">
						{$group.title}
					</div>
					<div class="w05"></div>
					{if empty($current_type_unchangeable) && $account->isPermission('catalog-type', 'delPropGroup')}
						<div class="w1 action-button action-delete w1" title="Удалить" data-group-id="{$group.id}">
							<i class="icon-delete"></i>
						</div>
					{else}
						<div class="w1"></div>
					{/if}
				{/if}	
			</div>
		{/if}
    {/foreach}
{else}
	<div class="wblock white-block-row">
		<div class="w12">Группы свойств не заданы</div>
	</div>
{/if}