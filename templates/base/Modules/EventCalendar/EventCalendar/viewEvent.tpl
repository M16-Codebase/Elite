{?$g_status = $event.guestStatus}
<a class="close-evt-popup" href="#"></a>
<h4><span style="background: {$event_colors[$event.properties.type_id]};">{$event.title}</span></h4>
<div class="time">
	<i></i> {$start_date} — {$end_date}
</div>
{?$ext_params = $event.properties}
<table class="cal-table">
	{if !empty($ext_params.type_title)}
		<tr>
			<td>Тип</td>
			<td>{$ext_params.type_title}</td>
		</tr>
	{/if}
	{if !empty($ext_params.organizer)}
		<tr>
			<td>Организатор</td>
			<td>{$ext_params.organizer}</td>
		</tr>
	{/if}
	{if !empty($event.location)}
		<tr>
			<td>Место</td>
			<td>{$event.location}</td>
		</tr>
	{/if}
	{if !empty($ext_params.price)}
		<tr>
			<td>Стоимость</td>
			<td>{$ext_params.price}</td>
		</tr>
	{/if}
	{if !empty($ext_params.htmlLink)}
		<tr>
			<td>Ссылка</td>
			<td><a href="{$ext_params.htmlLink}" target="_blank">{$ext_params.htmlLink}</a></td>
		</tr>
	{/if}
	{if $event.properties.inv_company || !empty($inv_dept) || !empty($inv_pers)}
		{?$text_status = array("needsAction" => "Ожидается подтверждение", "accepted" => "Приглашение принято", "declined" => "Приглашение отклонено", "tentative" => "Возможно пойду")}
		<tr>
			<td>Приглашенные</td>
			<td class="inv-list">
				{?$first_inv = true}
				{if $event.properties.inv_company}
					{if $first_inv}{?$first_inv = false}{else}, {/if}
					<span class="dept">Вся компания</span>
				{/if}
				{if !empty($inv_dept)}
					{foreach from=$inv_dept item=dept}
						{?$persons = $pers_by_dept[$dept.id]}
						<span class="dept">{if $first_inv}{?$first_inv = false}{else}, {/if}{$dept.name}{if !empty($persons)}: {/if}</span>
						{if !empty($persons)}
							{?$first_in_dept = true}
							{foreach from=$persons item=pers}
								{if $first_in_dept}{?$first_in_dept = false}{else}, {/if}
								<span{if !empty($g_status[$pers.email])} class="{$g_status[$pers.email]}" title="{$text_status[$g_status[$pers.email]]}"{/if}>{$pers.name} {$pers.surname}</span>
							{/foreach}
						{/if}
					{/foreach}
				{/if}
			</td>
		</tr>
	{/if}
</table>
{if !empty($event.description)}
    <p class="description">
		{$event.description}
	</p>
{/if}

<a class="edit-evt-btn" href="/eventcal/edit/?id={$event.id}"></a>