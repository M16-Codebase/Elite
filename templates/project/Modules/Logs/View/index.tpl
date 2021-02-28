{?$pageTitle = 'Логи изменений — ' . (!empty($confTitle) ? $confTitle : '')}
{*include file="Modules/Logs/View/logged_fields.tpl"*}
{capture assign=aside_filter}
	<section class="aside-filter">
		<form method="GET" class="user-form items-filter" action="/logs-view/logsList/">			
			<div class="field">
				<div class="f-title">Тип события</div>
				<div class="f-input">
					<select name="type">
						<option value="">Выберите</option>
                        {if $accountType == 'SuperAdmin'}{*на данном проекте, смертным админам не нужны эти логи*}
                            <option value="item_type">Каталоги и категории</option>
                            <option value="property">Свойства</option>
                        {/if}
                        {foreach from=$catalogs item=catalog}
                            {if $accountType == 'SuperAdmin' || in_array($catalog.key, array('real-estate', 'resale', 'staff_list', 'infrastructure'))}
                                <option value="catalog_{$catalog.id}">{$catalog.title}</option>
                            {/if}
                        {/foreach}
                        {if $accountType == 'SuperAdmin'}{*на данном проекте, смертным админам не нужны эти логи*}
                            {*<option value="item">Товары</option>
                            <option value="variant">Варианты</option>*}
                            <option value="config">Внутренние настройки</option>
                        {/if}
                        <option value="user">Пользователи</option>
                        <option value="file">Файлы</option>
                        <option value="post">Посты</option>
                        <option value="image">Изображения</option>
                        <option value="collection">Галереи</option>
					</select>
				</div>
			</div>
			<div class="field">
				<div class="f-title">ID сущности</div>
				<div class="f-input">
					<input type="text" name="entity_id" />
				</div>
			</div>
			{*if !empty($logged_fields)}
				{foreach from=$logged_fields item=fields key=ent_type}
					<div class="field{if empty($params['entity_type']) || $params['entity_type'] != $ent_type} a-hidden{/if}">
						<div class="f-title">Параметр</div>
						<div class="f-input">
							<select name="attr_id">
								<option value="">Все</option>
								{foreach from=$fields item=f key=k}
									<option value="{$k}">{$f}</option>
								{/foreach}
							</select>
						</div>
					</div>
				{/foreach}
			{/if*}
			<div class="field">
				<div class="f-title">Инициатор события</div>
				<div class="f-input">
					<select name="user">
						<option value="">Все</option>
						{foreach from=$users item=usr key=usr_id}
							<option value="{$usr_id}">{$usr->getEmail()}</option>
						{/foreach}
					</select>
				</div>
			</div>
			<div class="field">
				<div class="f-title">Дата и время события</div>
				<div class="f-input">
					<div class="date-row a-justify">
						<span class="unit">С </span>
						<input type="text" name="date[min]" class="datepicker date-input" /> 
						<select name="time[min]" class="time-select">
							{?$cal_hour = 0}
							{section name=hours start=0 loop=24 step=1}
								{?$shown_hour = ($cal_hour < 10)? '0'.$cal_hour : $cal_hour}
									<option value="{$cal_hour}">{$shown_hour}:00</option>
								{?$cal_hour++}
							{/section}
						</select>
					</div>
					<div class="date-row a-justify">
						<span class="unit">По </span>
						<input type="text" name="date[max]" class="datepicker date-input" />
						<select name="time[max]" class="time-select">
							{?$cal_hour = 0}
							{section name=hours start=0 loop=24 step=1}
								{?$shown_hour = ($cal_hour < 10)? '0'.$cal_hour : $cal_hour}
									<option value="{$cal_hour}">{$shown_hour}:00</option>
								{?$cal_hour++}
							{/section}
						</select>
					</div>
				</div>
			</div>
			<div class="buttons">
				<button class="submit btn btn-main a-block">Показать</button>
				<div class="link-cont">
					<span class="clear-form a-link small-descr">Сбросить фильтр</span>
				</div>
			</div>
		</form>
	</section>
{/capture}

{include file="Admin/components/breadcrumbs.tpl"}
<div class="content-top">
	<h1>Лог изменений</h1>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		<div class="wblock white-block-row white-header">
			<div class="w3">Дата, время, инициатор</div>
			<div class="w9">Событие</div>
		</div>
		<div class="white-body logs-list">
		{include file="Modules/Logs/View/logsList.tpl"}
		</div>
	</div>
</div>