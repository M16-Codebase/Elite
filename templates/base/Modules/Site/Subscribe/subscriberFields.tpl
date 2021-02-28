 <form class="subscribers-form" action="/subscribe/{if !empty($smarty.post.email)}edit{else}add{/if}Subscriber/">
	{if !empty($group)}<input type="hidden" name="group_id" value="{$group.id}">{/if}
	<div class="content-top">
		<h1>{if !empty($smarty.post.email)}Редактирование {else}Добавление {/if}подписчика</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'url' => '#',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = createTeaserHandlers
				buttons = $buttons}	
			{$createTeaserHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3"><strong>Электронная почта</strong></div>
				<div class="w9">
					<input type="text" name="email" {if !empty($smarty.post.email)}disabled data-disabled='1'{/if}/>
				</div>
			</div>
				<div class="wblock white-block-row">
				<div class="w3"><strong>Фамилия</strong></div>
				<div class="w9">
					<input type="text" name="surname"{if empty($smarty.post.email)} disabled="disabled" data-disabled="1"{/if}/>
				</div>
			</div>
				<div class="wblock white-block-row">
				<div class="w3"><strong>Имя Отчество</strong></div>
				<div class="w9">
					<input type="text" name="name"{if empty($smarty.post.email)} disabled="disabled" data-disabled="1"{/if}/>
				</div>
			</div>
				<div class="wblock white-block-row">
				<div class="w3"><strong>Название компании</strong></div>
				<div class="w9">
					<input type="text" name="company_name"{if empty($smarty.post.email)} disabled="disabled" data-disabled="1"{/if}/>
				</div>
			</div>
		</div>
	</div>
</form>
		
{*<form action="/subscribe/addSubscriber/">*}
	{*<input type="hidden" name="group_id"{if !empty($group)} value="{$group.id}"{/if}>*}
	{*<table class="ribbed">*}
		{*<tr>*}
			{*<td class="td-title" style="width: 180px;">*}
				{*<label for="email">Электронная почта</label>*}
			{*</td>*}
			{*<td>*}
				{*<input type="text" name="email" />*}
			{*</td>*}
		{*</tr>*}
		{*<tr>*}
			{*<td class="td-title">*}
				{*<label for="name">Фамилия</label>*}
			{*</td>*}
			{*<td>*}
				{*<input type="text" name="surname" />*}
			{*</td>*}
		{*</tr>*}
		{*<tr>*}
			{*<td class="td-title">*}
				{*<label for="name">Имя Отчество</label>*}
			{*</td>*}
			{*<td>*}
				{*<input type="text" name="name" />*}
			{*</td>*}
		{*</tr>*}
		{*<tr>*}
			{*<td class="td-title">*}
				{*<label for="name">Название компании</label>*}
			{*</td>*}
			{*<td>*}
				{*<input type="text" name="company_name" />*}
			{*</td>*}
		{*</tr>*}
	{*</table>*}
	{*<div class="buttons">*}
		{*<div class="submit a-button-blue">Добавить</div>*}
	{*</div>*}
{*</form>*}