{if $account->isPermission('catalog-item', 'transferItem')}
	<form action="/catalog-item/transferItem/" class="transfer-item-form transfer-form">
		<div class="content-top">
			<h1>Перемещение</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Переместить',
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
			<div class="white-blocks viewport">
				<div class="wblock white-block-row">
					<div class="w3">В категорию</div>
					<div class="w9">
						<input type="hidden" name="item_id" class="input-item" />
						<select name="type_id">
							<option value="" class="level0" selected>Выберите...</option>
							{if !empty($all_types_by_levels)}
								{foreach from = $all_types_by_levels key=type_id item=type}
									<option class="level{$type.level+1}" value="{$type_id}">
										{section loop=$type.level name=level}-{/section}{$type.data.title}
									</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
			</div>
		</div>
	</form>
	
	<form action="/catalog-item/transferVariant/" class="transfer-variant-form transfer-form">
		<div class="content-top">
			<h1>Перемещение</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Переместить',
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
			<div class="white-blocks viewport">
				<div class="wblock white-block-row">
					<div class="w3">В существующий товар</div>
					<div class="w9">
						<input type="hidden" name="variant_id" class="input-variant" />
						<input type="text" name="item_id" placeholder="ID товара" />
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w3">Или создать новый товар в категорию</div>
					<div class="w9">
						<select name="type_id">
							<option value="" class="level0" selected>Выберите...</option>
							{if !empty($all_types_by_levels)}
								{foreach from = $all_types_by_levels key=type_id item=type}
									<option class="level{$type.level+1}" value="{$type_id}">
										{section loop=$type.level name=level}-{/section}{$type.data.title}
									</option>
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
			</div>
		</div>
	</form>
{/if}