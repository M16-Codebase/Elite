<form action="/catalog-type/setPropertyAvailable/">
	<input type="hidden" name="type_id" value="{$type['id']}" />
	<input type="hidden" name="prop_id" value="{$property['id']}" />
	
	<div class="content-top">
		<h1>{$property.title}</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = addFormButtons
				buttons = $buttons}
			{$addFormButtons|html}
		</div>
	</div>
	
	<div class="content-scroll">
		<div class="prop-avail-cont white-blocks viewport">
			<div class="wblock white-block-row">
				<label class="w12">
					<input type="checkbox" name="available" value="1" /> <span>Свойство используется в данном типе</span>
				</label>
			</div>
			{if $property['data_type'] == 'enum' && !empty($property['values'])}
				<div class="wblock">
					<div class="white-block-row">
						<div class="w10">
							<input type="hidden" class="input-prop-id" value="{$property.id}" />
							<strong>В перечислении использовать значения:</strong>
						</div>
						<div class="unset-all  a-link w2">
							<span class="small-descr">Снять все</span>
						</div>
					</div>
					<div class="enum-props white-inner-cont">
						<div class="origin used-prop white-block-row a-hidden">
							<label class="w12">
								<input type="checkbox" checked="checked" /> <span></span>
							</label>
						</div>
						{foreach from=$property['values'] item=enum}
							<div class="used-prop white-block-row">
								<label class="w12">
									<input type="checkbox" name="ids[{$enum['id']}]" value="{$enum['id']}" /> <span>{$enum['value']}</span>
								</label>
							</div>
						{/foreach}
						<div class="white-block-row add-value">
							<div class="w11">
								<input type="text" placeholder="Добавить значение" />
							</div>
							<div class="action-button action-add w1" title="Добавить">
								<i class="icon-add"></i>
							</div>
						</div>
					</div>
				</div>
			{/if}
		</div>
	</div>
</form>