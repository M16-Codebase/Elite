<div class="log-table-header">
	<table class="log-table">
		<tr>
			<th class="td-date">Дата и время</th>
			<th>Инициатор</th>
			<th class="td-val">Значение RU</th>
			<th class="td-val th-en">Значение EN</th>
		</tr>
	</table>
</div>
<div class="log-table-body">
	{if !empty($logs)}
		<table class="log-table">
			<tr>
				<th class="td-date">Дата и время</th>
				<th>Инициатор</th>
				<th class="td-val">Значение RU</th>
				<th class="td-val th-en">Значение EN</th>
			</tr>
			{foreach from=$logs item=l name=logs_list}
				<tr{if iteration is odd} class="even"{/if}>
					<td class="td-date">{strtotime($l.time)|date_format:'%d.%m.%Y %H:%M'}</td>
					<td><a href="mailto:{$l.user.email}">{$l.user.email}</a></td>
					{include file='Modules/Logs/View/property_log_view.tpl'}
				</tr>
			{/foreach}
			{*<tr class="even">
				<td class="td-date">01.10.2013 12:05</td>
				<td><a href="#">nh@maris-spb.ru</a></td>
				<td class="td-val">
					При покупке труб следует отдавать предпочтение сертифицированной продукции известных компаний. Так как система водоснабжения предполагает длительное использование, нужно быть уверенным в их качестве и безопасности для здоровья.
				</td>
				<td class="en-col td-val">
					When purchasing pipe should favor certified well-known companies. Since the water system involves long-term use, you need to be sure of their quality and safety for human health.
				</td>
			</tr>
			<tr>
				<td class="td-date">01.10.2013 11:35</td>
				<td><a href="#">nh@maris-spb.ru</a></td>
				<td class="td-val">
					<ul>
						<li>Круглосуточная охрана</li>
						<li>Система контроля доступа</li>
					</ul>
				</td>
				<td class="en-col td-val">
					<ul>
						<li>24-hour Security Service</li>
						<li>Access Control System</li>
					</ul>
				</td>
			</tr>
			<tr class="even">
				<td class="td-date">01.10.2013 12:05</td>
				<td><a href="#">nh@maris-spb.ru</a></td>
				<td class="td-val">
					При покупке труб следует отдавать предпочтение сертифицированной продукции известных компаний. Так как система водоснабжения предполагает длительное использование, нужно быть уверенным в их качестве и безопасности для здоровья.
				</td>
				<td class="en-col td-val">
					When purchasing pipe should favor certified well-known companies. Since the water system involves long-term use, you need to be sure of their quality and safety for human health.
				</td>
			</tr>
			<tr>
				<td class="td-date">01.10.2013 11:55</td>
				<td><a href="#">nh@maris-spb.ru</a></td>
				<td class="td-val">Санкт-Петербург</td>
				<td class="en-col td-val">St. Petersburg</td>
			</tr>
			<tr class="even">
				<td class="td-date">01.10.2013 12:05</td>
				<td><a href="#">nh@maris-spb.ru</a></td>
				<td class="td-val">
					При покупке труб следует отдавать предпочтение сертифицированной продукции известных компаний. Так как система водоснабжения предполагает длительное использование, нужно быть уверенным в их качестве и безопасности для здоровья.
				</td>
				<td class="en-col td-val">
					When purchasing pipe should favor certified well-known companies. Since the water system involves long-term use, you need to be sure of their quality and safety for human health.
				</td>
			</tr>*}
		</table>
	{/if}
</div>
