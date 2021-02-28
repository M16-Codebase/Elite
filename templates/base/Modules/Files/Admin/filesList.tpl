<input type="hidden" name="current_type" value="{if !empty($current_type)}{$current_type}{elseif !empty($smarty.get.type)}{$smarty.get.type}{/if}" />
<table class="ribbed"> 
	{if !empty($files)}
		{foreach from=$files item=file}
			<tr data-id="{$file.id}">
				<td class="small">
					<input type="checkbox" name="check[{$file.id}]" class="check-item" />
				</td>
				<td><a href="{$file.link}" target="_blank">{$file.full_name}</a></td>
				<td class="td-title">
					{if !empty($file.orgs)}
						{foreach from=$file.orgs item=org name=file_orgs}
							{$org}{if !last}, {/if}
						{/foreach}
					{/if}
				</td>
				<td>
                    {if !empty($file.date)}
						{if $file.date <= date('Y-m-d')}
							<div class="small-descr red">Просрочен!</div>
						{elseif date('Y-m-d', strtotime('-30 day', $file.timestamp)) <= date('Y-m-d')}
							<div class="small-descr orange">до {$file.timestamp|date_format:'%d.%m.%Y'}</div>
						{else}
							<div class="small-descr">до {$file.timestamp|date_format:'%d.%m.%Y'}</div>
						{/if}
                    {/if}
				</td>
				<td class="small">
                    {?$count = $file.variants_count}
					{if !empty($count)}
						<a href="#" class="file-variants">{$count}</a>
					{else}
						0
					{/if}
				</td>
				<td class="small">
					<div class="table-btn reload"></div>
				</td>
				<td class="small">
					<div class="table-btn delete"></div>
				</td>
			</tr>
		{/foreach}
	{else}
		<tr>
			<td class="td-title" colspan="9">Пусто</td>
		</tr>
	{/if}	
</table>
	
{if !empty($error)}
    <div class="popup-window popup-errors" data-title="Ошибка">
		{$error}
	</div>
{/if}