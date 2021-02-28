<div class="popup-window popup-select-file" data-width="1100">
	<form action="#" class="file-filter">
		<div class="a-inline-cont">
			<div class="field">
				<div class="title">Наименование</div>
				<div>
					<input type="text" />
				</div>
			</div>
			<div class="field">
				<div class="title">Название файла</div>
				<div>
					<input type="text" />
				</div>
			</div>
			<div class="field">
				<div class="title">Дата окончания действия</div>
				<div>
					<input type="text" class="small" /> — <input type="text" class="small" />
				</div>
			</div>
			<div class="field">
				<div class="title">Производитель</div>
				<div>
					<select class="chosen">
						<option>Выберите</option>
					</select>
				</div>
			</div>
			<div class="buttons">
				<button class="a-button-green">Показать</button>
			</div>
		</div>
		<div class="clear-form a-link-dotted">Сбросить фильтр</div>
	</form>
	<div class="aside-controls">
		<div class="aside-panel">
			{include file="Admin/components/actions_panel.tpl"
				buttons = array(
					'save' => 1
				)}
		</div>
		<div class="select-files-list">
			<table class="ribbed">
				<tr>
					<td class="small"><input type="radio" name="select_file" /></td>
					<td>Трубы и фитинги PP-R напорные STC</td>
					<td class="td-title">STC</td>
					<td><a href="#" class="small-descr">spf_stc_pp-r_pipies.pdf</a></td>
					<td><span class="small-descr">до 18.09.2013</span></td>
				</tr>
				<tr>
					<td class="small"><input type="radio" name="select_file" /></td>
					<td>Трубы и фитинги PP-R напорные STC</td>
					<td class="td-title">ПК Контур</td>
					<td><a href="#" class="small-descr">spf_stc_pp-r_pipies.pdf</a></td>
					<td><span class="small-descr orange">до 18.09.2013</span></td>
				</tr>
			</table>
		</div>
	</div>
</div>