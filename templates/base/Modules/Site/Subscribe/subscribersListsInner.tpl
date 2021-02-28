{?$group_types = array('list' => 'Список', 'filter' => 'Набор фильтров')}
{?$itval = 0}
{if !empty($groups.main)}
    {?$itval = 1}
	<div class="wblock white-block-row" data-id="{$groups.main.id}" data-name="{$groups.main.name}">
		<div class="w12">
			<a href="/subscribe/subscribers/?group_id={$groups.main.id}">{$groups.main.name}</a>
		</div>
	</div>
   {* <tr data-id="{$groups.main.id}" data-name="{$groups.main.name}">
        <td class="td-title" colspan="2">
            <a href="/subscribe/subscribers/?group_id={$groups.main.id}">{$groups.main.name}</a>
        </td>
    </tr>*}
{/if}
{if !empty($groups.main_fiz)}
	<div class="wblock white-block-row{if $itval == 1} odd{/if}" data-id="{$groups.main_fiz.id}" data-name="{$groups.main_fiz.name}">
		<div class="w12">
			<a href="/subscribe/subscribers/?group_id={$groups.main_fiz.id}">{$groups.main_fiz.name}</a>
		</div>
	</div>
{/if}
{?$iteration = 0}
{foreach from=$groups key=id item=group}
    {if $id != 'main' && $id != 'main_fiz' && $group.type != 'filter'}
        {?$iteration++}
		<div class="wblock white-block-row{if $iteration%2 != $itval} odd{/if}" data-id="{$group.id}" data-name="{$group.name}">
			<div class="w11">
				<a href="/subscribe/subscribers/?group_id={$group.id}">{$group.name}</a>
			</div>
			<div class="w1 action-button action-delete m-border" title="Удалить"><i class="icon-delete"></i></div>
		</div>
       {* <tr class="{if $iteration%2 != $itval}odd{/if}" data-id="{$group.id}" data-name="{$group.name}">
            <td>
                <a href="/subscribe/subscribers/?group_id={$group.id}">{$group.name}</a>
            </td>
            <td style="width:1px;" class="small">
                <a href="/subscribe/deleteSubscribersList/?id={$group.id}" class="i-delete table-btn delete"></a>
            </td>
        </tr>*}
    {/if}
{/foreach}