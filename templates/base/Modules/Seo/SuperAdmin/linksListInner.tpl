{if !empty($links_list)}
	{foreach from=$links_list item=link}
		<div class="wblock white-block-row" data-id="{$link.id}">
			{*<div>
				<input type="checkbox" name="id[]" value="{$link.id}">
			</div>*}
			<div class="w4">
				{$link.phrase}
			</div>
			<div class="w3">
			   {if $link.url == '/'}Главная страница{else}{$link.url}{/if}
			</div>
			<div class="w3">
				{$link.page_limit}
			</div>
			<div class="action-button action-edit w1 m-border" title="Редактировать">
				<i class="icon-edit"></i>
			</div>
			<div class="action-button action-delete w1 m-border" title="Удалить">
				<i class="icon-delete"></i>
			</div>
			{*<a href="#" data-id="{$link.id}" class="action-button action-edit edit-link-btn w1" title="Редактировать"><i></i></a>
			<a href="#" data-id="{$link.id}" class="action-button action-delete delete-link-btn w1" title="Удалить"><i></i></a>*}
			{*<td><a href="#" class="edit-link-btn" data-id="{$link.id}">ред.</a></td>
			<td><a href="#" class="delete-link-btn" data-id="{$link.id}">уд.</a></td>*}
		</div>
	{/foreach}
{else}
	<div class="white-blocks">
		<div class="wblock white-block-row">
			<div class="w12">Ссылки еще не созданы</div>
		</div>
	</div>
{/if}