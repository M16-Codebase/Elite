<div class="content-top">
	<h1>{if !empty($create)}Добавление{else}Редактирование{/if} станции метро</h1>
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
		<div class="white-blocks">
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Станция</strong>
				</div>
				<div class="w9">
					<select name="metro_prop">
						{foreach from=$stations item=station}
							<option value="{$station.variant_title}">{$station.variant_title}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Время пешком</strong>
				</div>
				<div class="w9">
					<input type="text" name="walk_prop" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>Время на машине</strong>
				</div>
				<div class="w9">
					<input type="text" name="drive_prop" />
				</div>
			</div>
		</div>
	</div>
</div>