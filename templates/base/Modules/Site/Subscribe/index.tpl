{?$pageTitle = 'Сервис рассылки — ' . (!empty($confTitle) ? $confTitle : '')}
{?$admin_page = 1}
<div class="content-top">
	<h1>Сервис рассылки</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" 
			multiple = true
			buttons = array(
				'back' => '/site/',
				'save' => array(
					'class' => 'submit',
					'data' => array(
						'group_id' => !empty($group.id) ? $group.id : '',
					)	
				)
			)
		)}	
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		{include file="Modules/Site/Subscribe/subscribeList.tpl"}
	</div>
</div>

{*{?$admin_page = 1}
{?$pageTitle = 'Сервис рассылки — Управление сайтом | Сантехкомплект'}
<h1>Сервис рассылки</h1>
{include file="Admin/components/actions_panel.tpl" 
    multiple = true
    buttons = array(
        'back' => '/subscribe/subscribersLists/',
        'save' => '#'
)}
<div>
    <a href="https://sendsay.ru"><img src="/templates/Admin/img/sendsay.png"></a>
</div>
<a href="https://sendsay.ru">sendsay.ru</a>
{if !empty($smarty.get.error)}
    <h4>Произошла ошибка аутентификации</h4>
    {if $smarty.get.error == 'wrong_credentials'}
        <p>
            Указаны неверные учетные данные, укажите правильные данные и попробуйте войти снова.
        </p>
    {elseif $smarty.get.error == 'force_change_password'}
        <p>
            Пароль устарел. Смените пароль в личном кабинете на сайте <a href="pro.subscribe.ru" target="_blank">pro.subscribe.ru</a>, укажите новый пароль в форме ниже и попробуйте снова.
        </p>
    {/if}
{/if}
<form id="auth-data-form" action="/subscribe/saveAuthData/">
    <table class="ribbed">
        <tbody>
            <tr>
                <td style="width:140px;">Общий логин</td>
                <td><input type="text" name="login"></td>
            </tr>
            <tr>
                <td style="width:140px;">Личный логин</td>
                <td><input type="text" name="sublogin"></td>
            </tr>
            <tr>
                <td style="width:140px;">Пароль</td>
                <td><input type="text" name="password"></td>
            </tr>
        </tbody>
    </table>
</form>*}