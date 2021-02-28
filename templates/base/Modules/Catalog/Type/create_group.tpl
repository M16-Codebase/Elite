{capture assign=prop_group_fields}
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<input type="hidden" name="type_id" value="{$current_type.id}" />
			<input type="hidden" name="group" value="0" />
			<input type="hidden" name="group_id" />
			{if $constants.segment_mode == 'lang'}
				{foreach from=$segments item=seg}
					<div class="wblock white-block-row">
						<div class="w3">
							<strong>Название ({$seg.title})</strong>
						</div>
						<div class="w9">
							<input type="text" name="title[{$seg.id}]" />
						</div>
					</div>
				{/foreach}
			{else}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>Название</strong>
					</div>
					<div class="w9">
						<input type="text" name="title" />
					</div>
				</div>
			{/if}
			{if $account->getRole() == 'SuperAdmin'}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>Ключ</strong>
					</div>
					<div class="w9">
						<input type="text" name="key" />
					</div>
				</div>
			{/if}
		</div>
	</div>
{/capture}

<form action="/catalog-type/addPropGroup/" class="group-create-form">
	<div class="content-top">
		<h1>Создание группы свойств</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Создать',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	{$prop_group_fields|html}
</form>

<form action="/catalog-type/editPropGroup/" class="group-edit-form">
	<div class="content-top">
		<h1>Редактирование группы свойств</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Созранить',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	{$prop_group_fields|html}
</form>