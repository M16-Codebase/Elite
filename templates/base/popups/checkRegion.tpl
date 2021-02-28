<div class="popup-check-inner">
    <form action="/region/change/" method="GET">
        {*cookie_id в куках*}
        {*url_id в урле*}
        {*def_id определился по умолчанию*}
		<div class="f-logo">
			<img src="/img/icons/header-logo.png" alt="Logo Master-santehnik">
		</div>
		<div class="errors">Интернет-магазин инженерной сантехники</div>
		<div class="field f-text">	
			<div class="f-title">
				<div class="ui-dialog-title">Ваш регион</div>
				<div class="field">
					Выберите региональную версию сайта,<br />которая лучше всего вам подходит.
				</div>
			</div>
			{if !empty($segments)}
				<select name="reg_id" class="chosen fullwidth" data-def_id="{$def_id}" data-url_id="{$url_id}" data-cookie_id="{$cookie_id}" data-city="{$def_city.city}">
					{foreach from=$segments item=reg}
						<option value="{$reg.id}">{$reg.title}</option>
					{/foreach}
				</select>
			{/if}
			<div class="field f-descr">Вы сможете изменить регион позже</div>
		</div>
        <button  class="btn btn-white-yellow-big" type="submit" value="OK">Продолжить</button>
    </form>
</div>