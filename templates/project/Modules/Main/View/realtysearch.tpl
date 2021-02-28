<div class="top-bg" id="site-top">
	<div class='bg-img'></div>
	<div class="site-top">
		<h1 class="title" title="Поиск по каталогу">
			<span>Поиск</span><br>по каталогу
		</h1>
	</div>
</div>
<div class="request-wrap row">
	{?$checkString = time()}
	{?$checkStringSalt = $checkString . $hash_salt_string}
	<form action="/realtysearch/" method="GET" class="request-form" data-checkstring="{$checkString}" data-hashstring="{md5($checkStringSalt)}">
		<label class="field">
			<div class="f-row">
				<div class="f-title">
					<span>Вы ищите</span>
					<span class="slash"></span>
				</div>
				<div class="f-input">
					<input type="text" name="phrase" />
				</div>
			</div>
		</label>
		<div class="buttons">
			<button class="btn m-magenta-fill js-search">{fetch file=$path . "search.svg"}Искать</button>
		</div>
	</form>
</div>

{if !empty($real_estate) || !empty($resale)}
	<div class="list-result tabs-cont">
		<div class="tabs-wrap">
			{if !empty($real_estate)}<a href=".m-realestate" class="tab-title"><span>Строящиеся объекты</span> {if !empty($real_estate_count)}<i>{$real_estate_count}</i>{/if}</a>{/if}
			{if !empty($resale)}<a href=".m-resale" class="tab-title"><span>На вторичном рынке</span> {if !empty($resale_count)}<i>{$resale_count}</i>{/if}</a>{/if}
		</div>
		<div class="bg-wrap m-white">
			<div class="list-wrap m-realestate tab-page">
				{if !empty($real_estate)}
					{include file='Modules/Main/View/realestatesearch.tpl' items=$real_estate count=$real_estate_count}
				{/if}
			</div>
				<div class="resale m-center">
					<div class="descr">{$lang->get('А почему бы не изучить предложения в строящихся домах?', 'Why not check some offers in buildings under construction?')}</div>
					<a href="{$url_prefix}/real-estate/" class="btn m-light-magenta">смотреть квартиры в новых домах</a>
				</div>
			<div class="list-wrap m-resale tab-page">
				{if !empty($resale)}
					{include file='Modules/Main/View/resalesearch.tpl' items=$resale count=$resale_count}
				{/if}
			</div>
		</div>
	</div>
{else}
	<div class="list-result m-empty">
		<div class="items-list">
			<div class="resale m-gray">
				<div class="main">К нашему великому сожалению</div>
				<div class="title"><span>Вариантов по вашему запросу не найдено</span></div>
				<div class="descr">Приглашаем изучить наш каталог</div>
				<a href="{$url_prefix}/real-estate/" class="btn m-light-magenta">Строящиеся объекты</a>
				<a href="{$url_prefix}/resale/" class="btn m-light-magenta">Вторичная недвижимость</a>
			</div>
		</div>
	</div>
{/if}

{if !empty($site_config.real_estate_consultant)}
	<div class="consultant row a-justify">
		<div class="info w2">
			<div class="title">Трудно <b>найти</b><br>квартиру вашей мечты?</div>
			<div class="small-descr">Позвоните по телефону</div>
			{if !empty($contacts.phone)}<div class="phone">{$contacts.phone}</div>{/if}
			<a href="{$url_prefix}/contacts/#form" class="btn m-magenta-fill">Оставить заявку</a>
			<div class="slash"></div>
		</div>
		{foreach from=$site_config.real_estate_consultant item=consultant name=cons}
			<div class="person w1">
				{if !empty($consultant.photo)}<div class="photo"><div><img src="{$consultant.photo->getUrl()}" alt=""></div></div>{/if}
				{if !empty($consultant.title)}<div class="name">{$consultant.title}</div>{/if}
				{if !empty($consultant.email)}<a class="email" href="mailto:{$consultant.email}">{$consultant.email}</a>{/if}
				{if !empty($consultant.appointment)}<div class="function">{$consultant.appointment}</div>{/if}
			</div>
		{/foreach}
	</div>
{/if}