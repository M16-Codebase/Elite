{foreach from=$prop_list item=property}
	<li class="using-type-item" data-id="{$property.id}" data-position="{$property.position}">
		<div class="using-row justify">
			<div class="drag-drop"><i></i></div>
			<div class="short-name"><a href="#" class="edit-property" data-id="{$property.id}">{$property.short_name}</a></div>
			<div class="circle">
				<div class="using-circle" style="border-color: {$property.color}; color: {$property.color};">{$property.short_name}</div>
			</div>
			<div class="title">{$property.title}</div>
			<div class="buttons a-clearbox">
				<a href="/catalog-type/usingTypeFields/" data-id="{$property.id}" class="edit-property a-left" title="Редактировать применение"><i></i></a>
				<a href="/catalog-type/usingTypeDelete/" data-id="{$property.id}" class="delete-property a-left" title="Удалить применение"><i></i></a>
			</div>			
		</div>
	</li>
{/foreach}