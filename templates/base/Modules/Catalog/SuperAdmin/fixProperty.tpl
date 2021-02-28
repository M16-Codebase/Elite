{?$currentCatalog = $property->getType()->getCatalog()}
<form action="/catalog-superadmin/updatePropertyFixed/">
	<input type="hidden" name="id" />
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
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<label class="w12">
					<input type="radio" name="fix" value="0" /> Без ограничений
				</label>
			</div>
			<div class="wblock white-block-row">
				<label class="w12">
					<input type="radio" name="fix" value="2" /> Спрятать
				</label>
			</div>
			<div class="wblock white-block-row">
				<label class="w12">
					<input type="radio" name="fix" value="1" /> Запретить редактирование
				</label>
			</div>
			<div class="wblock">
				<div class="white-block-row">
					<label class="w12">
						<input type="radio" name="fix" value="3" /> Запретить редактирование полей:
					</label>
				</div>
				<div class="white-inner-cont enum-props-cont">
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[title]" value="1" /> Название
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[key]" value="1" /> Ключ
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[description]" value="1" /> Пояснение
						</label>
					</div>
                    {if !$currentCatalog.only_items}
                        <div class="white-block-row">
                            <label class="w12">
                                <input type="checkbox" name="fields[multiple]" value="1" /> Для {if empty($currentCatalog) || $currentCatalog['id'] == 1}вариантов{else}{$currentCatalog.word_cases['v']['2']['r']}{/if}
                            </label>
                        </div>
                    {/if}
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[unique]" value="1" /> Уникальное
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[data_type]" value="1" /> Тип свойства
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[set]" value="1" /> Множественное
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[search_type]" value="1" /> Возможности фильтрации
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[filter_title]" value="1" /> Название в фильтре
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[group_id]" value="1" /> Группа
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[necessary]" value="1" /> Необходимое
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[mask]" value="1" /> Шаблон вывода
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[visible]" value="1" /> Показывать
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[read_only]" value="1" /> Не редактируемое
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[major]" value="1" /> Похожее
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[major_count]" value="1" /> Диапазон похожести
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[values]" value="1" /> Значения
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[export]" value="1" /> Экспорт
						</label>
					</div>
					<div class="white-block-row">
						<label class="w12">
							<input type="checkbox" name="fields[image]" value="1" /> Картинка
						</label>
					</div>
				</div>
			</div>
		</div>
	</div>
</form>