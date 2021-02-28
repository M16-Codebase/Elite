{?$pageTitle = (!empty($confTitle) ? $confTitle : '')}
{if $accountType == 'Guest'}
    <form class="auth-form" method="POST" action="/login/">
        <div class="header-yellow">
            <a href="/" class="logo">
                <img src="/img/icons/header-logo.png" alt="Logo Master-santehnik" />
            </a>
        </div>
        <div class="auth-body">
            <h3>Авторизация администратора</h3>
            <div class="field">
                <div class="f-title">Логин</div>
                <div class="f-input"><input type="text" name="email" /></div>
            </div>
            <div class="field">
                <div class="f-title">Пароль</div>
                <div class="f-input"><input type="password" name="pass" /></div>
            </div>
            {if !empty($error)}
                <div class="errors" data-error="{$error}">
                    <strong>Неверный логин или пароль</strong>
                </div>
            {/if}
            <div class="buttons">
                <button class="btn btn-sq-blue">Войти</button>
            </div>
        </div>
    </form>
{else}
    {?$admin_page = 1}
{/if}