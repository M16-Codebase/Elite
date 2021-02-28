{?$unchangeable = ($property['fixed'] == 1 || $property['fixed'] == 2) && $accountType != 'SuperAdmin'}
{?$currentCatalog = $current_type->getCatalog()}

<form class="property-form" action="/catalog-type/saveProp/{if !empty($property.id)}?id={$property.id}{/if}" data-property="{$property.id}">
	<div class="content-top">
		<h1>{if !empty($property.title)}Редактирование свойства «{$property.title}»{else}Новое свойство{/if}{if $accountType == 'SuperAdmin' && $property.fixed} (fixed){/if}</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl"
				buttons = array(
					'back' => 1,
					'save' => 1
				)}
		</div>
	</div>

	<div class="content-scroll">
		<div class="white-blocks edit-property viewport">
			<input type="hidden" name="type_id" />
			{if $constants.segment_mode == 'lang'}
				<div class="wblock">
					<div class="white-block-row">
						<div class="w12">
							<strong class="text-icon">{$field_list.title}</strong>
							{include file="Admin/components/tip.tpl" content="Наименование свойства для отображения его в списке свойств и таблице характеристик на сайте."}
						</div>
					</div>
					<div class="white-inner-cont">
						{foreach from = $segments item=$s}
							<div class="white-block-row">
								<div class="w3">
									<span>{$s.title}</span>
								</div>
								<div class="w9">
									<input type="text" name="title[{$s.id}]"{if $unchangeable || (in_array('title', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
								</div>
							</div>
						{/foreach}
					</div>
				</div>
			{else}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong class="text-icon">{$field_list.title}</strong>
						{include file="Admin/components/tip.tpl" content="Наименование свойства для отображения его в списке свойств и таблице характеристик на сайте."}
					</div>
					<div class="w9">
						<input type="text" name="title"{if $unchangeable || (in_array('title', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
					</div>
				</div>
			{/if}
			<div class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.key}</strong>
					{include file="Admin/components/tip.tpl" content="Уникальный идентификатор свойства. Используется при формировании URL и в шаблоне страницы. При создании специальных ключей не рекомендуется использовать знак дефиса."}
				</div>
				<div class="w9">
					<input type="text" name="key"{if $unchangeable || (in_array('key', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
				</div>
			</div>
            {*пока скроем, идея хорошая, но пока не требуется*}
			{*<div class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.external_key}</strong>
					{include file="Admin/components/tip.tpl" content="Уникальный идентификатор свойства в 1С."}
				</div>
				<div class="w9">
					<input type="text" name="external_key"{if $unchangeable || (in_array('external_key', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
				</div>
			</div>*}
			{if $current_type.allow_children}
				<label class="wblock white-block-row">
					<div class="w3">
						<strong>{$field_list.default_prop}</strong>
					</div>
					<div class="w9">
						<input type="hidden" name="default_prop" value="0">
						<input type="checkbox" name="default_prop" value="1">
					</div>
				</label>
			{/if}
			<div class="wblock type-tabs tabs-cont" data-speed="10">
				<div class="white-block-row">
					<div class="w3">
						<strong class="text-icon">{$field_list.data_type}</strong>
						{include file="Admin/components/tip.tpl" content="<strong>Целое число</strong> —  свойство описывается целым числом.<br /><br /><strong>Дробное число</strong> — свойство описывается дробным числом.<br /><br /><strong>Строка</strong> — свойство описывается произвольной строкой текста.<br /><br /><strong>Перечисление</strong> — свойству присваиваются значения из заранее созданного списка.<br /><br /><strong>Флаг</strong> — наличие или отсутствие какого-либо аспекта ".$currentCatalog.nested_in ? $current_type.word_cases['i']['1']['r'] : $currentCatalog.word_cases['i']['1']['r'].". Например, в продаже: есть или нет.<br /><br /><strong>Составной</strong> — свойство создается путем комбинации значений других свойств."}
					</div>
					<div class="w9">
						<select class="tab-title" name="data_type"{if ($unchangeable && (in_array('data_type', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')) || ($property.data_type == 'post' && $accountType != 'SuperAdmin')} data-disabled disabled{/if}>
							{foreach from=$properties_key.data_type key=type_key item=type_text}
								{if $type_key != 'post' || $accountType == 'SuperAdmin' || $property.data_type == 'post'}
									<option value="{$type_key}" data-target=".tab-{$type_key}">{$type_text}</option>
								{/if}
							{/foreach}
						</select>
					</div>
				</div>
				{include file="Modules/Catalog/Type/editProp_types.tpl"}
			</div>
			<div class="wblock white-block-row sort-type">
				<div class="w3">
					<strong>{$field_list.sort}</strong>
				</div>
				<div class="w9">
					<select name="sort"{if $unchangeable || (in_array('data_type', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if}>
						{foreach from=$properties_key.sort key=sort_key item=sort_text}
							<option value="{$sort_key}">{$sort_text}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.context}</strong>
					{include file="Admin/components/tip.tpl" content="[значение свойства]:[ключи свойств через запятую]:[действие: show (1) или hide (0)] | [...]"}
				</div>
				<div class="w9">
					<textarea rows="3" name="context" class="m-pre"{if $unchangeable || (in_array('context', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if}></textarea>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.description}</strong>
					{include file="Admin/components/tip.tpl" content="На некоторых страницах предусмотрена всплывающая подсказка, уточняющая значение свойства."}
				</div>
				<div class="w9">
					<textarea name="description"{if $unchangeable || (in_array('description', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} rows="3"></textarea>
				</div>
			</div>
			{if $constants.segment_mode == 'lang'}
				<div class="wblock">
					<div class="white-block-row">
						<div class="w12">
							<strong class="text-icon">{$field_list.public_description} сегмента</strong>
							{include file="Admin/components/tip.tpl" content="На некоторых страницах предусмотрена всплывающая подсказка, уточняющая значение свойства."}
						</div>
					</div>
					<div class="white-inner-cont">
						{foreach from = $segments item=$s}
							<div class="white-block-row">
								<div class="w3">
									<span>{$s.title}</span>
								</div>
								<div class="w9">
									<textarea type="text" name="public_description[{$s.id}]"{if $unchangeable && (in_array('public_description', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} ></textarea>
								</div>
							</div>	
						{/foreach}	
					</div>
				</div>
			{else}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong class="text-icon">{$field_list.public_description}</strong>
						{include file="Admin/components/tip.tpl" content="На некоторых страницах предусмотрена всплывающая подсказка, уточняющая значение свойства."}
					</div>
					<div class="w9">
						<textarea name="public_description"{if $unchangeable || (in_array('public_description', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} rows="3"></textarea>
					</div>
				</div>
			{/if}
			<div class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.group_id}</strong>
					{include file="Admin/components/tip.tpl" content="Все свойства типа ". $currentCatalog.nested_in ? $current_type.word_cases['i'][1]['i'] : $currentCatalog.word_cases['i']['1']['i']." могут быть разбиты на группы для удобства отображения."}
				</div>
				<div class="w9">
					<select name="group_id"{if $unchangeable || (in_array('group_id', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if}>
						<option value="0">Без группы</option>
						{if !empty($prop_groups)}
							{foreach from=$prop_groups item=group}
								{if $group.key != 'icon' || $accountType == 'SuperAdmin'}
									<option value="{$group.id}">{$group.title}</option>
								{/if}
							{/foreach}
						{/if}
					</select>
				</div>
			</div>
			<label class="wblock white-block-row prop-necessary">
				<div class="w3">
					<strong class="text-icon">{$field_list.necessary}</strong>
					{?$tip_title = 'При отметке данной опции система будет требовать заполнения данного свойства при создании и редактировании ' . ($currentCatalog.nested_in ? $current_type.word_cases['i'][2]['r'] : $currentCatalog.word_cases['i']['2']['r']) . (!empty($currentCatalog.word_cases['v']) ? (' или ' .$currentCatalog.word_cases['v']['2']['r']) : '') . '.'}
					{include file="Admin/components/tip.tpl" content=$tip_title}
				</div>
				<div class="w9">
					<input type="hidden" name="necessary" value="0"{if $unchangeable || (in_array('necessary', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
					<input type="checkbox" name="necessary" value="1"{if $unchangeable || (in_array('necessary', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
				</div>
			</label>
			<label class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.unique}</strong>
					{?$tip_title = 'При отметке данной опции система будет проверять уникальность данного свойства при создании и редактировании '. ($currentCatalog.nested_in ? $current_type.word_cases['i'][2]['r'] : $currentCatalog.word_cases['i']['2']['r']) . (!empty($currentCatalog.word_cases['v']) ? (' или ' . $currentCatalog.word_cases['v']['2']['r']) : '') . '.'}
					{include file="Admin/components/tip.tpl" content=$tip_title}
				</div>
				<div class="w9">
					<input type="hidden" name="unique" value="0" />
					<input type="checkbox" name="unique" value="1"{if $unchangeable || (in_array('unique', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
				</div>
			</label>
			{if !empty($currentCatalog.word_cases['v'])}
				<label class="wblock white-block-row">
					<div class="w3">
						<strong class="text-icon">Для {$currentCatalog.word_cases['v']['2']['r']}</strong>
						{include file="Admin/components/tip.tpl" content="Показывает, является ли данное свойство изменяемым для ". $currentCatalog.word_cases['v']['2']['r'] .' '. ($currentCatalog.nested_in ? $current_type.word_cases['i']['1']['r'] : $currentCatalog.word_cases['i']['1']['r']). "."}
					</div>
					<div class="w9">
						<input type="hidden" name="multiple" value="0" />
						<input type="checkbox" name="multiple" value="1"{if $unchangeable || (in_array('multiple', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
                        {if !is_array($property)}
                            <input type="hidden" name="default_mult_change" />
                        {/if}
					</div>
				</label>
			{/if}
			<div class="wblock">
				<label class="white-block-row">
					<div class="w3">
						<strong class="text-icon">{$field_list.major}</strong>
						{include file="Admin/components/tip.tpl" content="Использовать данное свойство для подбора похожих"}
					</div>
					<div class="w9">
						<input type="hidden" name="major" value="0" />
						<input type="checkbox" name="major" value="1"{if $unchangeable || (in_array('major', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
					</div>
				</label>
				<div class="white-inner-cont major-count{if !$property.major || $property.data_type == 'enum'} a-hidden{/if}">
					<div class="white-block-row">
						<div class="w3">
							<span class="text-icon">Разброс значений</span>
							{include file="Admin/components/tip.tpl" content="Данный параметр определяет диапазон значений свойства, внутри которого ищутся аналоги. Предположим, что N — значение свойства.<br /><strong>0</strong> — аналогами считаются. " ($currentCatalog.nested_in ? $current_type.word_cases['i'][2]['i'] : $currentCatalog.word_cases['i']['2']['i']) ." со значением свойства, равным N.<br /><strong>n</strong> — аналоги подбираются в диапазоне от N-n до N+n.<br /><strong>+n</strong> — аналоги подбираются в диапазоне от N до N+n.<br /><strong>-n</strong> — аналоги подбираются в диапазоне от N-n до N.<br /><strong>+n%</strong> — аналоги подбираются в диапазоне от N до N+N*n/100.<br /><strong>-n%</strong> — аналоги подбираются в диапазоне от N-N*n/100 до N.<br /><strong>-n+m</strong> — аналоги подбираются в диапазоне от N-n до N+m.<br /><strong>-n%+m%</strong> — аналоги подбираются в диапазоне от N-N*n/100 до N+N*m/100."}
						</div>
						<div class="w9">
							<input type="text" name="major_count" class="m-small"{if $unchangeable || (in_array('major_count', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
						</div>
					</div>
				</div>	
			</div>
			{if $constants.segment_mode == 'lang'}
				<div class="wblock">
					<div class="white-block-row">
						<div class="w12">
							<strong class="text-icon">{$field_list.mask}</strong>
							{include file="Admin/components/tip.tpl" content="Использование шаблона позволяет, дописывая что-либо перед или после, изменять выводимое значение."}
							<div class="small-descr">Используйте <a class="mask-val" href="#">{ldelim}!{rdelim}</a> в качестве значения свойства</div>
						</div>
					</div>
					<div class="white-inner-cont">
						{foreach from = $segments item=$s}
							<div class="white-block-row">
								<div class="w3">
									<span>{$s.title}</span>
								</div>
								<div class="w9">
									<input type="text" name="mask[{$s.id}]"{if $unchangeable && (in_array('mask', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
								</div>
							</div>	
						{/foreach}	
					</div>
				</div>
			{else}
				<div class="wblock white-block-row">
					<div class="w3">
						<strong class="text-icon">{$field_list.mask}</strong>
						{include file="Admin/components/tip.tpl" content="Использование шаблона позволяет, дописывая что-либо перед или после, изменять выводимое значение."}
					</div>
					<div class="w9">
						<input type="text" name="mask" id="prop_value_mask"{if $unchangeable || (in_array('mask', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
						<div class="small-descr">Используйте <a class="mask-val" href="#">{ldelim}!{rdelim}</a> в качестве значения свойства</div>
					</div>
				</div>
			{/if}
			<div class="wblock search-type">
				<div class="white-block-row">
					<div class="w3">
						<strong class="text-icon">{$field_list.search_type}</strong>
						{include file="Admin/components/tip.tpl" content="Параметр определяет участвует ли данное свойство в фильтре подбора ".($currentCatalog.nested_in ? $current_type.word_cases['i'][2]['r'] : $currentCatalog.word_cases['i'][2]['r']).".<br /><br /><b>Нет</b> — свойство не участвует в фильтре.<br /><br /><b>Диапазон</b> — свойство будет представлено в фильтре в виде инструмента выбора дипазона с парой бегунков для грубого задания граничных значений и полей ввода для уточнения.<br /><br /><b>Выбор</b> — свойство будет представлено в фильтре выпадающим списком  заданных значений для выбора одного из них.<br /><br /><b>Автозаполнение</b> — свойство будет представлено в фильтре в виде поля ввода, по ходу заполнения предлагающего варианты из заданных значений.<br /><br /><b>Мультиселект</b> — свойство будет представлено в фильтре списком возможных значений, снабженным флажками для выбора нескольких вариантов одновременно."}
					</div>
					<div class="w9">
						<select name="search_type"{if $unchangeable || (in_array('search_type', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if}>
							{foreach from=$properties_key.search_type key=st_key item=st_text}
								<option value="{$st_key}"
									{if $st_key == 'none' && empty($property.search_type)} selected="selected"{/if}
									{if ($st_key == 'between' && $property.data_type != 'int' && $property.data_type != 'float' && $property.data_type != 'diapasonInt' && $property.data_type != 'diapasonFloat') || ($st_key == 'autocomplete' && !in_array($property.data_type, array('string', 'address')))} data-disabled disabled{/if}>{$st_text}
								</option>
							{/foreach}
						</select>
					</div>
				</div>
				<div class="white-inner-cont filter-name{if $property.search_type == 'none'} a-hidden{/if}">
					{if $constants.segment_mode == 'lang'}
						<div class="white-block-row">
							<div class="w3">{$field_list.filter_title}</div>
							<div class="w9">
							{foreach from = $segments item=$s}
								<span>{$s.title}</span>&nbsp;<input type="text" class="m-small" name="filter_title[{$s.id}]"{if $unchangeable && (in_array('filter_title', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
								 &nbsp; 
							{/foreach}	
							</div>
						</div>
					{else}
						<div class="white-block-row">
							<div class="w3">
								<span class="text-icon">{$field_list.filter_title}</span>
								{include file="Admin/components/tip.tpl" content="Если поле оставить пустым в фильтре подбора ".($currentCatalog.nested_in ? $current_type.word_cases['i'][2]['r'] : $currentCatalog.word_cases['i'][2]['r'])." будет использоваться значение параметра «Название». Если необходимо изменить или дополнить его, в данное поле следует ввести желаемую формулировку.<br />Например, для свойства «Масса» название в фильтре имеет смысл задать как «Масса, кг»."}
							</div>
							<div class="w9">
							<input type="text" name="filter_title"{if $unchangeable || (in_array('filter_title', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if}/>
							</div>
						</div>
					{/if}
					<div class="white-block-row">
						<div class="w3">
							{$field_list.filter_visible}
						</div>
						<div class="w9">
							{foreach from=$properties_key.filter_visible item="fv" key="fv_val"}
								<div class="export-list">
									<label>
										<input type="checkbox" name="filter_visible[{$fv_val}]" value="{$fv_val}"{if $unchangeable || (in_array('filter_visible', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />&nbsp;&nbsp;{$fv}
									</label>
								</div>
							{/foreach}
						</div>
					</div>
				</div>
			</div>			
			<div class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.visible}</strong>
					{include file="Admin/components/tip.tpl" content="Определяет в каких контекстах отображается данное свойство."}
				</div>
				<div class="w9">
					{foreach from=$properties_key.visible key=vis_key item=vis_text}
						<div class="export-list">
							<label>
								<input type="checkbox" 
									   name="visible[{$vis_key}]" 
									   value="{$vis_key}"
									   {if $vis_key == 4} class="show-ms-general"{/if}
									   {if ($unchangeable && (in_array('visible', $unchangeableParamsByProps) && $accountType != 'SuperAdmin'))} data-disabled disabled{/if}		
									   />
								&nbsp;&nbsp;{$vis_text}
							</label>
						</div>
					{/foreach}
				</div>
			</div>
			<label class="wblock white-block-row">
				<div class="w3">
					<strong class="text-icon">{$field_list.read_only}</strong>
					{include file="Admin/components/tip.tpl" content="Параметр определяет является ли значение свойства изменяемым в режиме редактирования."}
				</div>
				<div class="w9">
					<input type="hidden" name="read_only" value="0"{if $unchangeable || (in_array('read_only', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
					<input type="checkbox" name="read_only" value="1"{if $unchangeable || (in_array('read_only', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
				</div>
			</label>
			{if $constants.segment_mode != 'none' && $currentCatalog.allow_segment_properties}
				<label class="wblock white-block-row">
					<div class="w3">
						<strong class="text-icon">{$field_list.segment}</strong>
						{include file="Admin/components/tip.tpl" content="Параметр определяет является ли значение свойства сегментированным."}
					</div>
					<div class="w9">
						<input type="hidden" name="segment" value="0"{if $unchangeable || (in_array('segment', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
						<input type="checkbox" name="segment" value="1"{if $unchangeable || (in_array('segment', $unchangeableParamsByProps) && $accountType != 'SuperAdmin')} data-disabled disabled{/if} />
					</div>
				</label>
			{/if}
		</div>
	</div>
</form>