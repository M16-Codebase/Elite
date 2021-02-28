{?$currentCatalog = $current_type->getCatalog()}
<h1>Конвертация номеров JDE в номенклатурные номера</h1>
<form class="convert-form">
	<div class="convert-selects">
		Из <select name="convertFrom" class="chosen convert-from">
			<option value="1c">1C (синхр. EКБ)</option>
			<option value="jde">JDE</option>
			<option value="nn" selected>НН (ЕНС)</option>
			<option value="id">ID {$currentCatalog.word_cases['v']['2']['r']}</option>
		</select>
		&nbsp;&nbsp;&nbsp;
		В <select name="convertTo" class="chosen convert-to">
			<option value="1c">1C (синхр. EКБ)</option>
			<option value="jde" selected>JDE</option>
			<option value="nn">НН (ЕНС)</option>
			<option value="id">ID {$currentCatalog.word_cases['v']['2']['r']}</option>
		</select>
	</div>	
	<table class="ribbed convert-numbers">
		<tr>
			<td>
				<div class="td-title">Номера <span class="convertFrom">НН (ЕНС)</span>, через запятую</div>
				<textarea name="numbers" rows="4"></textarea>
			</td>
		</tr>
		<tr>
			<td class="convert-button td-center">
				<button class="a-button-blue">Конвертировать</button>
			</td>
		</tr>
		<tr>
			<td>
				<div class="td-title">Номера <span class="convertTo">JDE</span></div>
				<textarea name="ens_numbers" rows="4"></textarea>
			</td>
		</tr>
	</table>    
</form>