{foreach from=$menu_list item=menu}
	<div class="wblock white-block-row">
		<a href="/menu-editor/menuItems/?id={$menu.id}" class="w4">
			<span>{$menu.key}</span>
		</a>
		<div class="w6"></div>
		<div class="action-button action-edit w1" title="Переименовать" data-id="{$menu.id}" data-key="{$menu.key}">
			<i class="icon-edit"></i>
		</div>
		<div class="action-button action-delete w1 m-border" title="Удалить" data-id="{$menu.id}">
			<i class="icon-delete"></i>
		</div>
	</div>
{/foreach}