{*{capture assign=aside_filter name=aside_filter}
	<section class="aside-filter">
		<form class="user-form items-filter" method="GET" action="/catalog-type/catalog/?id=59">
			<input type="hidden" name="order[surname]" class="input-sort" />
			<div class="field">
				<div class="f-title">Фамилия</div>
				<div class="f-input">
					<input type="text" name="surname" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Имя</div>
				<div class="f-input">
					<input type="text" name="name" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Отчество</div>
				<div class="f-input">
					<input type="text" name="patronymic" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Должность</div>
				<div class="f-input">
					<input type="text" name="appointment" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">Телефон</div>
				<div class="f-input">
					<input type="text" name="phone" />
				</div>
			</div>
			<div class="field">
				<div class="f-title">E-mail</div>
				<div class="f-input">
					<input type="text" name="email" />
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
{/capture}*}
<div class="wblock white-block-row white-header">
	<label class="w05"><input type="checkbox" class="check-all" /></label>
	<div class="w15"></div>
	<div class="w3">ФИО</div>
	<div class="w2">Телефон</div>
	<div class="w2">E-mail</div>
	<div class="w3">
		<input type="hidden" name="page" value="{!empty($smarty.get.page) ? $smarty.get.page : 1}" />
		<input type="hidden" name="type_id" value="{$current_type.id}" />
	</div>
</div>
<div class="white-body">
	{foreach from=$catalog_items item=item}
		{?$item_title = ''}
		{if !empty($item.surname)}{?$item_title = $item.surname}{/if}
		{if !empty($item.name)}{?$item_title .= ' '.$item.name}{/if}
{*		{if !empty($item.patronymic)}{?$item_title .= ' '.$item.patronymic}{/if}*}
		<div class="wblock white-block-row" data-item_id="{$item.id}" data-position="{$item.position}" data-item-text="{$currentCatalog.word_cases['i']['1']['p']}"{if !empty($currentCatalog.word_cases['v'])} data-variant-text="{$currentCatalog.word_cases['v']['2']['r']}"{/if}>
			<label class="w05">
				<input type="checkbox" name="check[]" value="{$item.id}" class="check-item" />
			</label>
			<a href="{if !empty($item.photo)}{$item.photo->getUrl()}{/if}" class="w15 fancybox">
				{if !empty($item.photo)}<img src="{$item.photo->getUrl(80,80)}">{/if}
			</a>
			<div class="w3">
				<input type="hidden" name="item_id" value="{$item.id}" />
				<span class="item-title">{if !empty($item_title)}{$item_title}{/if}</span><br>
				<span class="small-descr">{if !empty($item.appointment)}{$item.appointment}{/if}</span>
			</div>
			<div class="w2">{if !empty($item.phone)}{$item.phone}{/if}</div>
			<div class="w2">{if !empty($item.email)}{$item.email}{/if}</div>
			<div class="action-button action-visibility w1
				{if $account->isPermission('catalog-item', 'changeVisible')} m-active{else} m-inactive{/if} 
				action-{if $item['status'] == 3}show{else}hide{/if}"
				title="{if $item['status'] == 3}Отображается{else}Не отображается{/if}">
				<i class="icon-{if $item['status'] == 3}show{else}hide{/if}"></i>
			</div>
			{if $account->isPermission('catalog-item', 'edit')}
				<a href="/catalog-item/edit/?id={$item.id}" class="action-button action-edit w1 m-border" title="Редактировать">
					<i class="icon-edit"></i>
				</a>
			{else}
				<div class="action-button action-edit m-inactive w1 m-border" title="Редактировать">
					<i class="icon-edit"></i>
				</div>
			{/if}
			<div class="action-button action-delete w1 m-border" title="Удалить">
				<i class="icon-delete"></i>
			</div>
		</div>
	{/foreach}
</div>