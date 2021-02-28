{?$log_type = $log->getType()}
<div class="content-top">
	<h1>{$log_type.title} {$log.full_number}</h1>
	<div class="content-options">
		{?$buttons = array(
			'back' => '#'
		)}
		{include file="Admin/components/actions_panel.tpl"
			assign = addFormButtons
			buttons = $buttons}
		{$addFormButtons|html}
	</div>
</div>
<div class="content-scroll">
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
            {if !empty($log.referrer_url)}
                <div class="wblock white-block-row">
                    <div class="w4"><strong>Страница, с которой обратились</strong></div>
                    <div class="w8"><a href="{$log.referrer_url}" target="_blank">{if !empty($log.referrer_uri)}{$log.referrer_uri}{else}Перейти{/if}</a></div>
                </div>
            {/if}
			{if $log_type.key == "feedback" || $log_type.key == "callback" || $log_type.key == "vacancy"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Имя</strong></div>
					<div class="w8">{if !empty($log.author)}{$log.author}{else}—{/if}</div>
				</div>	
				{if $log_type.key == "feedback"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Вопрос</strong></div>
					<div class="w8">{if !empty($log.message)}{$log.message}{else}—{/if}</div>
				</div>	
				{/if}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Телефон</strong></div>
					<div class="w8">{if !empty($log.phone)}{$log.phone}{else}—{/if}</div>
				</div>
				{if $log_type.key == "callback"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Удобное время для звонка</strong></div>
					<div class="w8">{if !empty($log.time_from)}c {$log.time_from} {/if}{if !empty($log.time_to)}до {$log.time_to}{/if}</div>
				</div>
				{/if}
				{if $log_type.key == "feedback" || $log_type.key == "vacancy"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Электронная почта</strong></div>
					<div class="w8">{if !empty($log.email)}{$log.email}{else}—{/if}</div>
				</div>	
				{/if}
				{if $log_type.key == "vacancy"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Вакансия</strong></div>
					<div class="w8">{if !empty($log.vacancy)} <a href="{$log.vacancy->geturl()}">{$log.vacancy.title}</a>{else}—{/if}</div>
				</div>
				{/if}
				{if $log_type.key != "feedback"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>{if $log_type.key == "callback"}Комментарий{elseif $log_type.key == "vacancy"}Комментарий{/if}</strong></div>
					<div class="w8">{if !empty($log.message)}{$log.message}{else}—{/if}</div>
				</div>
				{/if}
				{if  $log_type.key == "vacancy"}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Резюме</strong></div>
					<div class="w8">
						{if !empty($log.summary)}
							<a href="{$log.summary.link}" class='a-block'>{$log.summary.title}</a>
						{else}
							<a href="{$log.summary_link}" target="_blank" class='a-block'>Просмотреть</a>
						{/if}
					</div>
				</div>	
				{/if}
			{/if}
                {if $log_type.key == 'review'}
                    {$log.review|var_dump}
                {/if}
                {if $log_type.key == 'question'}
                    {$log.question|var_dump}
                {/if}

			
			{*{if $log_type.key != 'simple' &&  $log_type.key != 'rent' &&  $log_type.key != 'sale'}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Адрес</strong></div>
					<div class="w8">{if !empty($log.address)}{$log.address}{else}—{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Предложение</strong></div>
					<div class="w8">{if !empty($log.offer)}{$log.offer}{else}—{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Готовность</strong></div>
					<div class="w8">{if !empty($log.readiness)}{$log.readiness}{else}—{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Коммерческие условия</strong></div>
					<div class="w8">{if !empty($log.condition)}{$log.condition}{else}—{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Описание</strong></div>
					<div class="w8">{if !empty($log.description)}{$log.description}{else}—{/if}</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Статус заявителя</strong></div>
					<div class="w8">{if !empty($log.status)}{$log.status}{elseif !empty($log.issue)}{$log.issue}{else}—{/if}</div>
				</div>
			{/if}
			{if $log_type.key =='simple'}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Тема обращения</strong></div>
					<div>{if !empty($log.issue)}{$log.issue}{else}—{/if}</div>
				</div>
			{/if}
			{if $log_type.key =='rent' || $log_type.key =='sale'}
				<div class="wblock white-block-row">
					<div class="w4"><strong>Предмет обращения</strong></div>
					<div class="w8">
						{if !empty($log.objects)}
							<ul class="log-items">
								{foreach from=$log.objects item=item_data}
									{?$item = $item_data.item}
									{?$item_type = $item->getType()}
									<li>
										<a href="{$item->getUrl()}"><strong>
											{if $item_type.id=='63' || $item_type.id=='64'}Офис
											{elseif $item_type.id=='65' || $item_type.id=='67'}Индустриальная недвижимость
											{elseif $item_type.id=='59'}Земельный участок
											{elseif $item_type.id=='66' || $item_type.id=='68'}Торговая недвижимость
											{elseif $item_type.id=='62'}Жилая недвижимость{/if}
											{$item.title}
										</strong></a>
										{if !empty($item_data.offers)}
											<ul class="log-offers">
												{foreach from=$item_data.offers item=offer}
													<li><a href="{$offer->getUrl()}">{{$offer.variant_title}}</a></li>
												{/foreach}
											</ul>
										{/if}
									</li>
								{/foreach}
							</ul>
						{else}
							—
						{/if}			
					</div>
				</div>
				<div class="wblock white-block-row">
					<div class="w4"><strong>Интересующая площадь, м²</strong></div>
					<div class="w8">{if !empty($log.interest)}{$log.interest}{else}—{/if}</div>
				</div>
			{/if}
			<div class="wblock white-block-row">
				<div class="w4"><strong>Имя</strong></div>
				<div class="w8">{if !empty($log.name)}{$log.name}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Организация</strong></div>
				<div class="w8">{if !empty($log.organisation)}{$log.organisation}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Должность</strong></div>
				<div class="w8">{if !empty($log.position)}{$log.position}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Телефон</strong></div>
				<div class="w8">{if !empty($log.phone)}{$log.phone}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>E-mail</strong></div>
				<div class="w8">{if !empty($log.email)}<a href="mailto:{$log.email}">{$log.email}</a>{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Дата</strong></div>
				<div class="w8">{if !empty($log.timestamp)}{$log.timestamp|date_format:'%d.%m.%Y %H:%M:%S'}{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Удобное время для звонка</strong></div>
				<div class="w8">{if !empty($log.time_from) && !empty($log.time_to)}{$log.time_from}:00 — {$log.time_to}:00{else}—{/if}</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w4"><strong>Текст обращения</strong></div>
				<div class="w8">{if !empty($log.message)}{$log.message}{else}—{/if}</div>
			</div>*}
		</div>
	</div>
</div>
