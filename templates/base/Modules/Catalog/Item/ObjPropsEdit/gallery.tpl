<div class="content-top">
	<h1>{if !empty($object)}Редактирование{else}Создание{/if} галереи{if !empty($property.title)} для свойства «{$property.title}»{/if}</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
				'back' => '#',
				'save' => array(
					'class' => 'submit'
				)
			)}
	</div>
</div>

<div class="content-scroll">
	<div class="viewport">
		<input type="hidden" name="object_id" />
		<input type="hidden" name="entity_id" />
		<input type="hidden" name="segment_id" />
		<input type="hidden" name="property_id" />
		{if !empty($object)}
			{include file="Modules/Images/Admin/files_uploader.tpl" gallery=$object no_paste=1}
		{/if}
	</div>
</div>