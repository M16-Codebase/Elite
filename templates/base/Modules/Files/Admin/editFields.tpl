<input type="hidden" value="{$file.id}" name="id" />
<table class="ribbed">
	{if !empty($types)}
		<tr>
			<td class="td-title">Тип файла</td>
			<td>
				<select class="chosen fullwidth" name="type">
					<option value="">Без типа</option>
					{foreach from=$types key=id item=title}
						<option value="{$id}"{if $file.type == $id} selected{/if}>{$title}</option>
					{/foreach}
				</select>
			</td>
		</tr>
	{/if}
	<tr>
		<td class="td-title">Сертифицирующий орган</td>
		<td class="ords-list">
			<div class="org-item origin">
				<input type="text" data-url="/files-edit/getOrgList/" />
				<div class="table-btn delete"></div>
			</div>
			<div class="org-values">
                {?$file_orgs = $file.orgs}
				{if !empty($file_orgs)}
					{foreach from=$file_orgs item=org}
						<div class="org-item">
							<input type="text" name="orgs[]" class="autocomplete" data-url="/files-edit/getOrgList/" value="{$org}" />
							<div class="table-btn delete"></div>
						</div>
					{/foreach}
				{/if}
			</div>
			<div class="add-value">
				<input type="text" class="autocomplete" placeholder="Добавить сертифицирующий орган" data-url="/files-edit/getOrgList/" />
				<div class="table-btn add"></div>
			</div>
		</td>
	</tr>
	<tr>
		<td class="td-title">Дата окончания действия</td>
		<td>
			<input type="text" name="date" class="datepicker short" value="{if !empty($file.date)}{$file.timestamp|date_format:'%d.%m.%Y'}{/if}" />
		</td>
	</tr>
	<tr>
		<td class="td-title">Файл</td>
		<td class="v-center">
			<a href="{$file.link}" target="_blank">{$file.full_name}</a>
		</td>
	</tr>
	<tr>
		<td class="td-title">Заменить на файл</td>
		<td class="v-center">
			<input type="file" name="rfile" />
		</td>
	</tr>
	<tr>
		<tr>
			<td colspan="2">
                {?$variants = $file->searchVariants()}
				<div class="td-title">Номенклатурные номера товаров, разделенные запятыми</div>
				<ul class="num-area">
					{if !empty($variants)}
						{foreach from=$variants item=var}
							<li class="tagit-choice" title="{$var.variant_title}" tagvalue="{$var.id}">{$var.code}</li>
						{/foreach}
					{/if}
				</ul>
				<input type="hidden" name="variants" />
			</td>
		</tr>
	</tr>
</table>
<div class="buttons ">
	<button class="a-button-blue">Применить</button>
	<div class="close">или <span class="close-popup a-link">Отменить</span></div>
</div>