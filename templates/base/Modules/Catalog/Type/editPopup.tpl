{?$currentCatalog = $current_type->getCatalog()}

<form action="/catalog-type/{if $current_type.id == $constants.default_type_id}{if empty($smarty.post.id)}createCatalog{else}updateCatalog{/if}{else}{if empty($smarty.post.id)}create{else}update{/if}{/if}/">
	<input type="hidden" name="parent_id" value="{$current_type.id}" />
	<input type="hidden" name="id" />
	<div class="content-top">
		<h1>{if empty($smarty.post.id)}Добавление{else}Редактирование{/if} {if $current_type.id == $constants.default_type_id}каталога{else}категории{/if}{if !empty($type.title)} «{$type.title}»{/if}</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => empty($smarty.post.id)? 'Создать' : 'Сохранить',
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
			{if $constants.segment_mode == 'lang'}
				<div class="wblock">
					<div class="white-block-row">
						<div class="w12">
							<strong class="text-icon">{$field_list.title}</strong>
						</div>
					</div>
					<div class="white-inner-cont">
						{foreach from = $segments item=$s}
							<div class="white-block-row">
								<div class="w3">
									<span>{$s.title}</span>
								</div>
								<div class="w9">
									<input type="text" name="title[{$s.id}]"/>
								</div>
							</div>	
						{/foreach}	
					</div>
				</div>
			{else}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>{$field_list.title}</strong>
					</div>
					<div class="w9">
						<input type="text" name="title" />
					</div>
				</div>
			{/if}
			<div class="wblock white-block-row">
				<div class="w3">
					<strong>{$field_list.key}</strong>
				</div>
				<div class="w9">
					<input type="text" name="key" />
				</div>
			</div>
			{if $current_type.id == $constants.default_type_id}
				<div class="wblock white-block-row">
					<label class="w12">
						<input type="checkbox" name="nested_in" value="1" />
						<span>Каталог c наследуемыми айтемами</span>
					</label>
				</div>
			{/if}
			{if $currentCatalog.nested_in}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>{$field_list.nested_in}</strong>
					</div>
					<div class="w9">
						 <select name="nested_in">
							<option value="">Выберите...</option>
							{if !empty($typesByLevels)}
								{foreach from = $typesByLevels key=type_id item=l_type}
									{if empty($l_type.data.allow_children) && (empty($type) || $type['id'] != $type_id)}
										<option value="{$type_id}" class="level{$l_type.level+1}"{if !empty($current_type.id) && $current_type.id == $type_id} selected{/if}
												{if $l_type.level} data-before="<span class='tree a-inline-block' style='width: {$l_type.level * 14}px'></span>"{/if}>
											{$l_type.data.title}
										</option>
									{/if}
								{/foreach}
							{/if}
						</select>
					</div>
				</div>
				<div class="wblock">
					<div class="white-block-row">
						<div class="w4">
							<strong>Название айтема</strong>
						</div>
						<div class="w7">
							<input type="text" name="send_name" />
						</div>
						<div class="apply-object action-button w1" data-type="i">
							<i class="icon-prop-apply"></i>
						</div>
					</div>
					<div class="white-inner-cont">
						{include file="Modules/Catalog/Type/cases.tpl" type='i'}
					</div>
				</div>
			{else}
				{if $current_type.id != $constants.default_type_id}
					{if !$current_type->isChildrenCanHasChildren()}
						<input type="hidden" name="allow_children" value="0" />
					{else}
						<div class="wblock white-block-row">
							<label class="w12">
								<input type="hidden" name="allow_children" value="0" />
								<input type="checkbox" name="allow_children" value="1" />
								<span>{$field_list.allow_children}</span>
							</label>
						</div>
					{/if}
				{/if}
			{/if}
			{if $current_type.id == $constants.default_type_id}
				<div class="wblock white-block-row">
					<div class="w4">
						<strong>Максимальная глубина вложенности</strong>
					</div>
					<div class="w8">
						<select name="allow_children">
							<option value="0">0</option>
							<option value="1">1</option>
							<option value="2">2</option>
							<option value="3">3</option>
							<option value="4">4</option>
							<option value="5">5</option>
						</select>
					</div>
				</div>
				<div class="wblock">
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="enable_view_mode" value="0" />
							<input type="checkbox" name="enable_view_mode" value="1" />
							<span>{$field_list.enable_view_mode}</span>
						</label>
					</div>
				</div>
				<div class="wblock">
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="allow_item_url" value="0" />
							<input type="checkbox" name="allow_item_url" value="1" />
							<span>{$field_list.allow_item_url}</span>
						</label>
					</div>
				</div>
				{if !empty($constants.enable_sphinx)}
					<div class="wblock">
						<div class="white-block-row">
							<label class="w12">
								<input type="hidden" name="search_by_sphinx" value="0" />
								<input type="checkbox" name="search_by_sphinx" value="1" />
								<span>{$field_list.search_by_sphinx}</span>
							</label>
						</div>
					</div>
				{/if}
				<div class="wblock">
					<div class="white-block-row">
						<label class="w12">
                            <input type="hidden" name="dynamic_category" value="0" />
							<input type="checkbox" name="dynamic_category" value="1" />
							<span>{$field_list.dynamic_category}</span>
						</label>
					</div>
                    <div class="white-inner-cont">
						<div class="white-block-row">
							<div class="w3">
								<strong>Каталог для динамических категорий</strong>
							</div>
							<div class="w9">
								<select name="dynamic_for">
									{?$disallow_keys = array('config')}
									{if !empty($type)}
										{?$disallow_keys[] = $type.key}
									{/if}
									{foreach from=$current_type->getChildren() item=cat}
										{if !in_array($cat.key, $disallow_keys) && !$cat.dynamic_for}
											<option value="{$cat.key}">{$cat.title}</option>
										{/if}
									{/foreach}
								</select>
							</div>
						</div>
                    </div>
				</div>
				<div class="wblock">
					<div class="white-block-row">
						<div class="w12">
							<strong>Показывать администраторам вкладки:</strong>
						</div>
					</div>
					<div class="white-inner-cont">
						<div class="white-block-row">
							<label class="w12">
								<input type="hidden" name="show_props_tab" value="0" />
								<input type="checkbox" name="show_props_tab" value="1" />
								<span>Свойства</span>
							</label>
						</div>
						<div class="white-block-row">
							<label class="w12">
								<input type="hidden" name="show_groups_tab" value="0" />
								<input type="checkbox" name="show_groups_tab" value="1" />
								<span>Группы</span>
							</label>
						</div>
						<div class="white-block-row">
							<label class="w12">
								<input type="hidden" name="show_text_tab" value="0" />
								<input type="checkbox" name="show_text_tab" value="1" />
								<span>Текст</span>
							</label>
						</div>
						<div class="white-block-row">
							<label class="w12">
								<input type="hidden" name="show_cover_tab" value="0" />
								<input type="checkbox" name="show_cover_tab" value="1" />
								<span>Обложка</span>
							</label>
						</div>
						<div class="white-block-row">
							<label class="w12">
								<input type="hidden" name="f" value="0" />
								<input type="checkbox" name="show_banner_tab" value="1" />
								<span>Баннеры</span>
							</label>
						</div>
						<div class="white-block-row">
							<label class="w12">
								<input type="hidden" name="show_metatags_tab" value="0" />
								<input type="checkbox" name="show_metatags_tab" value="1" />
								<span>Мета-теги</span>
							</label>
						</div>
					</div>
				</div>
				<div class="wblock">
					<div class="white-block-row">
						<div class="w4">
							<strong>Название айтема</strong>
						</div>
						<div class="w7">
							<input type="text" name="send_name" />
						</div>
						<div class="apply-object action-button w1" data-type="i">
							<i class="icon-prop-apply"></i>
						</div>
					</div>
					<div class="white-inner-cont">
						{include file="Modules/Catalog/Type/cases.tpl" type='i'}
					</div>
				</div>
				<div class="wblock">
					<div class="white-block-row">
						<div class="w4">
							<strong>Обложка айтема</strong>
						</div>
						<div class="w8">
							<select name="item_cover_name">
								<option value=""></option>
								{foreach from=$item_covers_list key=icon_name item=icon_url}
									<option value="{$icon_name}">{$icon_url}</option>
								{/foreach}
							</select>
						</div>
					</div>
				</div>
				<div class="wblock">
					<div class="white-block-row">
						<label class="w12">
							<input type="hidden" name="only_items" value="1" />
							<input type="checkbox" name="only_items" value="0" class="allow-variants" /> 
							<span>В данной категории будут использоваться варианты</span>
						</label>
					</div>
					<div class="white-inner-cont {$type.only_items} {if !empty($type) && $type.only_items == 0}{else} a-hidden{/if}">
						<div class="white-block-row">
							<div class="w4">
								<span>Название варианта</span>
							</div>
							<div class="w7">
								<input type="text" name="send_name" />
							</div>
							<div class="apply-object action-button w1" data-type="v">
								<i class="icon-prop-apply"></i>
							</div>
						</div>
						{include file="Modules/Catalog/Type/cases.tpl" type='v'}
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>{$field_list.item_prefix}</strong>
					</div>
					<div class="w9">
						<input type="text" name="item_prefix" />
					</div>
				</div>
				<div class="wblock white-block-row">
					<label class="w12">
						<input type="hidden" name="allow_item_property" value="0" />
						<input type="checkbox" name="allow_item_property" value="1" class="items-to-properties" data-text="айтемов" /> 
						<span>{$field_list.allow_item_property}</span>
					</label>
				</div>
				<div class="wblock white-block-row">
					<label class="w12">
						<input type="hidden" name="allow_variant_property" value="0" />
						<input type="checkbox" name="allow_variant_property" value="1" class="items-to-properties" data-text="вариантов" />
						<span>{$field_list.allow_variant_property}</span>
					</label>
				</div>
				{if $constants.segment_mode != 'none'}
					<div class="wblock white-block-row">
						<label class="w12">
							<input type="hidden" name="allow_segment_properties" value="0" />
							<input type="checkbox" name="allow_segment_properties" value="1" class="items-to-properties" data-text="вариантов" />
							<span>{$field_list.allow_segment_properties}</span>
						</label>
					</div>
				{/if}
			{elseif $current_type.key == 'treat'}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong>{$field_list.number_prefix}</strong>
					</div>
					<div class="w9">
						<input type="text" name="number_prefix" />
					</div>
				</div>
			{/if}
		</div>
	</div>

</form>