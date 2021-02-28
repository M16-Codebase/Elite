{?$log_type = $log->getType()}
<div class="content-top">
	<h1>{$log_type.title} {$log.full_number}</h1>
	<div class="content-options">
		{?$buttons = array(
			'back' => '#',
            'delete' => '#'
		)}
		{include file="Admin/components/actions_panel.tpl"
			assign = addFormButtons
			buttons = $buttons}
		{$addFormButtons|html}
	</div>
</div>
<div class="content-scroll feedback-body" data-id="{$log_type.id}">
	<div class="viewport">
		<div class="white-blocks overview">
			<div class="wblock white-block-row">
				<div class="w4"><strong>Номер обращения</strong></div>
				<div class="w8">{if !empty($log.full_number)}{$log.full_number}{else}—{/if}</div>
			</div>
			{if !empty($types)}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Тип обращения</strong></div>
					<div class="w8">{$log_type.title}</div>
				</div>
			{/if}
			<div class="wblock white-block-row">
				<div class="w4"><strong>Дата обращения</strong></div>
				<div class="w8">{if !empty($log.timestamp)}{$log.timestamp|date_format:'%d.%m.%Y %H:%M:%S'}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Имя</strong></div>
				<div class="w8">{if !empty($log.author)}{$log.author}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Телефон</strong></div>
				<div class="w8">{if !empty($log.phone)}{$log.phone}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Электронная почта</strong></div>
				<div class="w8">{if !empty($log.email)}{$log.email}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Сообщение</strong></div>
				<div class="w8">{if !empty($log.message)}{$log.message}{else}—{/if}</div>
			</div>
			{*{if !empty($log.referrer_url)}*}
				{*<div class="wblock white-block-row">*}
					{*<div class="w4"><strong>Страница, с которой обратились</strong></div>*}
					{*<div class="w8"><a href="{$log.referrer_url}" target="_blank">{if !empty($log.referrer_uri)}{$log.referrer_uri}{else}Перейти{/if}</a></div>*}
				{*</div>*}
			{*{/if}*}
			{if $log_type.key == "callback"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Удобное время для звонка</strong></div>
					<div class="w8">{if !empty($log.time_from)}c {$log.time_from} {/if}{if !empty($log.time_to)}до {$log.time_to}{/if}</div>
				</div>
			{/if}
			{if $log_type.key == 'view_apartments'}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Интересующая квартира</strong></div>
					<div class="w8"><a href="{$log.apartment->getUrl()}" target="_blank"}>{$log.apartment.title}</a></div>
				</div>
			{/if}
			{if $log_type.key == "apart_request"}
				{if !empty($log.complex)}
					<div class="wblock white-block-row">
						<div class="w4"><strong>Интересующий жилой комплекс</strong></div>
						<div class="w8"><a href="{$log.complex->getUrl()}" target="_blank"}>{$log.complex.title}</a></div>
					</div>
				{/if}
				{if !empty($log.apartments)}
					<div class="wblock white-block-row">
						<div class="w4"><strong>Интересующие квартиры (первичка)</strong></div>
						<div class="w8">
							<ul>
								{foreach from=$log.apartments item=apartment}
									<li>
										<a href="{$apartment->getUrl()}" target="_blank">
											{if !empty($apartment.title)}{$apartment.title}{else}—{/if}
										</a>
									</li>
								{/foreach}
							</ul>
						</div>
					</div>
				{/if}
				{if !empty($log.apartments_resale)}
					<div class="wblock white-block-row">
						<div class="w4"><strong>Интересующие квартиры (вторичка)</strong></div>
						<div class="w8">
							<ul>
								{foreach from=$log.apartments_resale item=apartment}
									<li>
										<a href="{$apartment->getUrl()}" target="_blank">
											{if !empty($apartment.title)}{$apartment.title}{else}—{/if}
										</a>
									</li>
								{/foreach}
							</ul>
						</div>
					</div>
				{/if}
			{/if}
			{if $log_type.key == 'flat_selection'}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Район</strong></div>
					<div class="w8">
						{if empty($log.district)}
							—
						{else}
							<ul>
								{foreach from=$log.district item=district}
									<li>{$district.title}</li>
								{/foreach}
							</ul>
						{/if}
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Площадь, м²</strong></div>
					<div class="w8">
						{if empty($log.area)}—{else}{$log.area}{/if}
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Цена, млн руб.</strong></div>
					<div class="w8">
						{if empty($log.price)}—{else}{$log.price}{/if}
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Число спален</strong></div>
					<div class="w8">
						{if empty($log.bed_number)}
							—
						{else}
							<ul>
								{foreach from=$log.bed_number item=bed_number}
									<li>{$bed_number}</li>
								{/foreach}
							</ul>
						{/if}
					</div>
				</div>
				{if !empty($log.primary)}
					<div class="wblock white-block-row">
						<div class="w4"><strong>На первичном рынке</strong></div>
						<div class="w8">Да</div>
					</div>
				{/if}
				{if !empty($log.resale)}
					<div class="wblock white-block-row">
						<div class="w4"><strong>На вторичном рынке</strong></div>
						<div class="w8">Да</div>
					</div>
				{/if}
				{if !empty($log.species)}
					<div class="wblock white-block-row">
						<div class="w4"><strong>Видовая квартира</strong></div>
						<div class="w8">Да</div>
					</div>
				{/if}
			{/if}
			{if $log_type.key == 'owner'}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Адрес</strong></div>
					<div class="w8">{if empty($log.address)}—{else}{$log.address}{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Число спален</strong></div>
					<div class="w8">{if empty($log.bed_number)}—{else}{$log.bed_number}{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Площадь, м²</strong></div>
					<div class="w8">
						{if empty($log.area)}—{else}{$log.area}{/if}
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Цена, млн руб.</strong></div>
					<div class="w8">
						{if empty($log.price)}—{else}{$log.price}{/if}
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Тип недвижимости</strong></div>
					<div class="w8">{if empty($log.estate_type)}—{else}{$log.estate_type}{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Видовая квартира</strong></div>
					<div class="w8">{if empty($log.species)}—{else}{$log.species}{/if}</div>
				</div>
			{/if}
		</div>
	</div>
</div>
