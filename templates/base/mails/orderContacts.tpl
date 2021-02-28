{if !empty($order.company_name)}
<tr>
	<td align="center" style="padding-top:31px;  background-color:#f3f3f3;">
		<table  width='841' cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td style="padding-left:50px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td colspan="2" width='' style="font-size:18px; font-weight:bold; line-height:34px; color:#000; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #e1e1e1;">
					Реквизиты организации
				</td>
				<td style="padding-left:53px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{if !empty($order.inn)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px; padding-right:15px; border-bottom:1px solid #e1e1e1;">
					ИНН
				</td>
				<td width="496" style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					{$order.inn}
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
			{if !empty($order.company_name)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left;  padding-bottom:7px; padding-top:7px;  padding-right:15px; border-bottom:1px solid #e1e1e1;">
					Наименование
				</td>
				<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					{$order.company_name}
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
			{if !empty($order.ogrn)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px;  padding-right:15px; border-bottom:1px solid #e1e1e1;">
					ОГРН
				</td>
				<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					{$order.ogrn}
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
			{if !empty($order.kpp)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left;  padding-bottom:7px; padding-top:7px;  padding-right:15px; border-bottom:1px solid #e1e1e1;">
					КПП
				</td>
				<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					{$order.kpp}
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
			{if !empty($order.company_city)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left;  padding-bottom:7px; padding-top:7px;  padding-right:15px; border-bottom:1px solid #e1e1e1;">
					Город
				</td>
				<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					{$order.company_city}
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
		</table>
	</td>
</tr>
{/if}
{if !empty($order.surname) || !empty($order.name) || !empty($order.patronymic || !empty($order.phone)) || !empty($order.email)}
<tr>
	<td align="center" style="padding-top:31px;  background-color:#f3f3f3;">
		<table  width='841' cellspacing="0" cellpadding="0" border="0">
			<tr>
				<td style="padding-left:50px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td colspan="2" style="font-size:18px; font-weight:bold; line-height:34px; color:#000; text-align:left; padding-bottom:7px; padding-top:4px; padding-right:15px; border-bottom:1px solid #e1e1e1;">
					Контактная информация
				</td>
				<td style="padding-left:53px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{if !empty($order.surname) || !empty($order.name) || !empty($order.patronymic)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px;  padding-right:15px; border-bottom:1px solid #e1e1e1;">
					ФИО {if !empty($order.company_name)}представителя{else}клиента{/if}
				</td>
				<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					{if !empty($order.surname)}{$order.surname} {/if}{if !empty($order.name)}{$order.name} {/if}{if !empty($order.patronymic)}{$order.patronymic} {/if}
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
			{if !empty($order.phone)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left;  padding-bottom:7px; padding-top:7px;  padding-right:15px; border-bottom:1px solid #e1e1e1;">
					Телефон
				</td>
				<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					{$order.phone}
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
			{if !empty($order.email)}
			<tr>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
				<td width="225" style="font-size:15px; color:#4a596e; text-align:left; padding-bottom:7px; padding-top:7px; padding-right:15px; border-bottom:1px solid #e1e1e1;">
					E-mail
				</td>
				<td style="font-size:15px; color:#000000; text-align:left; border-bottom:1px solid #e1e1e1;">
					<a href="mailto:{$order.email}" style="font-weight:bold;color:#059fdb;text-decoration:none;">
					{$order.email}
					</a>
				</td>
				<td style="padding-left:22px; line-height:0; width:1px;"><img src="http://{$site_url}/templates/base/img/mails/space.gif" alt=""></td>
			</tr>
			{/if}
		</table>
	</td>
</tr>
{/if}