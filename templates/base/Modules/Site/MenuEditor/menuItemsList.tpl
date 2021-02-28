{foreach from=$menu_items item=menu_item}
	<div class="wblock" data-position="{$menu_item['position']}" data-id="{$menu_item['id']}">
		<div class="white-block-row" data-id="{$menu_item['id']}">
			<div class="w05 drag-drop"></div>
			<div class="w2">
				{$menu_item.name}
			</div>
			<div class="w2">
				{$menu_item.title}
			</div>
			<div class="w2">
				{if $menu_item.url == '/'}Главная страница{else}{$menu_item.url}{/if}
			</div>
			<div class="w2">
				{$menu_item.image_id}
			</div>
			<div class="w05"></div>
			<div class="action-button action-add w1" title="Добавить подменю">
				<i class="icon-add"></i>
			</div>
			<div class="action-button action-edit w1 m-border" title="Переименовать">
				<i class="icon-edit"></i>
			</div>
			<div class="action-button action-delete w1 m-border" title="Удалить">
				<i class="icon-delete"></i>
			</div>
		</div>
		 {if ($menu_item.has_children)}
			<div class="white-inner-cont sortable sub-menu" data-cont='.menu-sortable' data-url="/menu-editor/changePosition/" data-sendattrs="id;parent_id" data-newpositionname='position'>
				{foreach from=$menu_item.child_items item=child_item}
					<div class="white-block-row" data-id="{$child_item.id}" data-position="{$child_item.position}">
						<div class="w05 drag-drop"></div>
						<div class="w3">
							{$child_item.name}
						</div>
						<div class="w2">
							{$child_item.title}
						</div>
						<div class="w2">
							{if $child_item.url == '/'}Главная страница{else}{$child_item.url}{/if}
						</div>
						<div class="w2">
							{$child_item.image_id}
						</div>
						<div class="w05"></div>
						<div class="action-button action-edit w1" title="Переименовать">
							<i class="icon-edit"></i>
						</div>
						<div class="action-button action-delete w1 m-border" title="Удалить">
							<i class="icon-delete"></i>
						</div>
					</div>	
				{/foreach}
			</div>
		{/if}
	</div>
{/foreach}