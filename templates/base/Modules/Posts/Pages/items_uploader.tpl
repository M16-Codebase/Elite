<ul class="img-uploader-gallery itemsList">
	{include file="Modules/Posts/Pages/itemsList.tpl"}
</ul>
<div class="popup-window popup-window-addPostItem" title="Добавить товар">
	<form action="/pages/addItem/">
		<input type="hidden" name="id" class="post-id-input" value="{if !empty($post)}{$post.id}{/if}" />
		<table class="ribbed">
			<tr>
				<td><input type="text" name="item_id" /></td>
			</tr>
		</table>
		<div class="buttons">
			<button class="a-button-blue">Отправить</button>
		</div>
	</form>
</div>