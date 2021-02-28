{?$current_user = $account->getUser()}
<div class="cabinet-page justify">
	<aside class="aside-catalog-menu aside-col m-scrollable">
		<div class="white-block-inner content-white-block vert-menu">
			<div class="aside-catalog-select">
				{*{include file="components/catalog-menu.tpl"}*}
			</div>
			{include file="Modules/Profile/My/cabinet-menu.tpl"}
		</div>
		<div class="white-block-inner benefits-cont content-white-block">
			{*{include file="components/benefits.tpl"}*}
		</div>
	</aside>
	<div class="main-col">
		<div class="content-white-block white-block-inner">
			{include file="components/breadcrumb.tpl" other_link=array('Личный кабинет' => array('link'=>'/profile/',  'Личные данные' => '/profile/', 'Заказы' => '/profile/orders/', 'Бонусный счет' => '/profile/bonus/', 'Отзывы' => '/profile/reviews/'))}
			<h1>ЛИЧНЫЕ ДАННЫЕ</h1>
			<div class="profile-security">
				Мы гарантируем сохранность информации.<br />
				Данные, вводимые вами, будут использоваться только для обработки ваших заказов.
			</div>
			<div class="justify">
				<div class="col2">
					<div class="profile-block">
						<div class="pr-title h3">Контактные данные <a href=".popup-profile-cont" class="edit-link" data-toggle="popup" data-action="open">— Изменить</a></div>
						<div class="user-data">
							{include file="Modules/Profile/My/userData.tpl"}
						</div>
						<div class="pr-buttons">
							<a href=".popup-profile-pass" class="change-pass edit-link" data-toggle="popup" data-action="open">— Изменить пароль</a>
						</div>
					</div>
					{if $current_user.person_type == 'org'}
						<div class="profile-block">
							<div class="pr-title h3">Организация <a href=".popup-profile-org" class="edit-link" data-toggle="popup" data-action="open">— Изменить</a></div>
							<div class="company-data">
								{include file="Modules/Profile/My/companyData.tpl"}
							</div>
						</div>
					{/if}	
				</div>
				<div class="col2">
					<div class="profile-block">
						<div class="pr-title h3">Адреса доставки <a href=".popup-profile-adr" class="edit-link" data-toggle="popup" data-action="open">— Добавить</a></div>
						<div class="address-list">
							{include file="Modules/Profile/My/addressList.tpl"}
						</div>
					</div>
					{*<div class="profile-block">
						<div class="pr-title h3">Настройки</div>
						<div class="region-data">
							{include file="Modules/Profile/My/regionData.tpl"}
						</div>					
						<label class="subscr-label"><input type="checkbox" name="subscribe" class="cbx subscr-cbx" value="1" /> Получать рассылку «Мастер-Сантехника»</label>
						<label class="subscr-label"><input type="checkbox" name="order_status" class="cbx order-status-cbx" value="1" /> Получать уведомления об изменениях статусов заказов</label>
					</div>*}
				</div>
			</div>
            <div class="justify">
                <div class="col2">
                    <div class="profile-block">
                        <div class="pr-title h3">Привязанные соцсети</div>
                        <ul class="social-auth-detach">
                            {foreach from=$current_user.socialAuth key=network item=identity}
                                <li>{$network} <a href="#" class="detach-btn" data-network="{$network}">Отвязать</a></li>
                            {/foreach}
                        </ul>
                    </div>

                </div>

            </div>
        </div>
	</div>
</div>
{*{include file="components/brands.tpl"}*}
{*{include file="components/news-block.tpl"}*}
		

<div class="popup-window profile-edit-popup popup-profile-cont" data-class="blue-form-popup" data-title="Контактные данные" data-width="270">
	<ul class="errors"></ul>
	<form action="/profile/userData/" data-cont=".user-data">
		<div class="field">
			<input type="text" name="name" placeholder="Имя" />
		</div>
		<div class="field">
			<input type="text" name="surname" placeholder="Фамилия" />
		</div>
		<div class="field">
			<input type="text" name="phone" placeholder="Телефон" />
		</div>
		<div class="field">
			<input type="text" name="email" placeholder="Электронная почта" />
		</div>
		<div class="buttons">
			<button class="btn btn-white-yellow-big">Сохранить</button>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>		
	</form>
</div>

<div class="popup-window profile-edit-popup popup-profile-pass" data-class="blue-form-popup" data-title="Изменение пароля" data-width="270">
	<ul class="errors"></ul>
	<form action="/profile/changePass/">
		<div class="field">
			<input type="password" name="pass" placeholder="Пароль" />
		</div>
		<div class="field">
			<input type="password" name="pass2" placeholder="Повторите пароль" />
		</div>
		<div class="buttons">
			<button class="btn btn-white-yellow-big">Сохранить</button>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>		
	</form>
</div>

<div class="popup-window profile-edit-popup popup-profile-org" data-class="blue-form-popup" data-title="Организация" data-width="270">
	<ul class="errors"></ul>
	<form action="/profile/companyData/" data-cont=".company-data">
		<div class="field">
			<input type="text" name="company_name" placeholder="Организация" />
		</div>
		<div class="field">
			<input type="text" name="inn" placeholder="ИНН" />
		</div>
		<div class="buttons">
			<button class="btn btn-white-yellow-big">Сохранить</button>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>		
	</form>
</div>

<div class="popup-window profile-edit-popup popup-profile-adr" data-class="blue-form-popup" data-title="Новый адрес" data-width="270">
	<ul class="errors"></ul>
	<form action="/profile/addAddress/" data-cont=".address-list">
		<div class="field">
			<textarea name="address" rows="4"></textarea>
		</div>
		<div class="buttons">
			<button class="btn btn-white-yellow-big">Добавить</button>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>		
	</form>
</div>

<div class="popup-window profile-edit-popup popup-profile-region" data-class="blue-form-popup" data-title="Регион" data-width="270">
	<ul class="errors"></ul>
	<form action="/profile/regionData/" data-cont=".region-data">
		<div class="field">
			<select name="region_id" class="chosen fullwidth">
				{foreach from=$segments item=$region}
					<option value="{$region.id}">{$region.title}</option>
				{/foreach}
			</select>
		</div>
		<div class="buttons">
			<button class="btn btn-white-yellow-big">Выбрать</button>
			<div class="cancel-btn" data-toggle="popup" data-action="close"></div>
		</div>		
	</form>
</div>



















{*if !empty($user)}
    <form method="POST">
        <table>
            <tr>
                <td>Компания</td>
                <td><input type="text" name="company_name" /></td>
            </tr>
            <tr>
                <td>Имя</td>
                <td><input type="text" name="name" /></td>
            </tr>
            <tr>
                <td>email</td>
                <td><input type="text" name="email" /></td>
            </tr>
            <tr>
                <td>дата регистрации</td>
                <td>{$user.reg_timestamp|date_format:'%d.%m.%Y %H:%I:%S'}</td>
            </tr>
        </table>
        <input type="submit" value="Сохранить" />
    </form>
{/if*}