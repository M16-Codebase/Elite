{*<form class="auth-form" method="POST" action="/login/">
	<div class="auth-body">
		<h3>Авторизация</h3>
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
</form>*}
{?$no_header=1}
{?$no_footer=1}
{?$no_header=1}
<form class="auth-form" method="POST" action="/login/">
    <input type="hidden" name="cms_login" value="1" />
	<div class="auth-body">
		<i class='icon-enter'></i>
		<h3 class="from-title">вход для администратора</h3>
		<h4>{if $constants.segment_mode == 'lang' && !empty($site_config[0].project_name)}{$site_config[0].project_name}{elseif !empty($site_config.project_name)}{$site_config.project_name}{/if}</h4>
		<div class="field">
			<input type="text" name="email" placeholder='Логин'/>
		</div>
		<div class="field">
			<input type="password" name="pass" placeholder='Пароль'/>
		</div>
		<div class="form-errors">
			{if !empty($error)}
			<p class="errors{* error-*}" data-error="{if !empty($error)}{$error}{/if}">
				Неверный логин или пароль
			</p>
			{/if}
		</div>
		<div class="buttons">
			<button class="btn btn-sq-blue{if !empty($error)} error{/if}"{if !empty($error)} disabled{/if}>Войти</button>
		</div>
	</div>
</form>
{*}<a href="https://oauth.vk.com/authorize?client_id=4533625&scope=email&redirect_uri=http%3A%2F%2Flps.loc%2Flogin%2F?auth_type=vk&response_type=code&v=5.24">авторизация вк</a>
<a href="https://www.facebook.com/dialog/oauth?client_id=1495257077397598&redirect_uri=http%3A%2F%2Flps.loc%2Flogin%2F?auth_type=facebook&response_type=code&scope=public_profile,email">авторизация фб</a>
<a href="http://www.odnoklassniki.ru/oauth/authorize?client_id=1100337664&response_type=code&redirect_uri=http://lps.loc/login/?auth_type=odnoklassniki">одноклассники</a>
*}
{*{include file="components/social-auth.tpl"}*}
{*literal}
<script>
    // This is called with the results from from FB.getLoginStatus().
    function statusChangeCallback(response) {
        console.log('statusChangeCallback');
        console.log(response);
        // The response object is returned with a status field that lets the
        // app know the current login status of the person.
        // Full docs on the response object can be found in the documentation
        // for FB.getLoginStatus().
        if (response.status === 'connected') {
            // Logged into your app and Facebook.
            testAPI();
        } else if (response.status === 'not_authorized') {
            // The person is logged into Facebook, but not your app.
            document.getElementById('status').innerHTML = 'Please log ' +
                    'into this app.';
        } else {
            // The person is not logged into Facebook, so we're not sure if
            // they are logged into this app or not.
            document.getElementById('status').innerHTML = 'Please log ' +
                    'into Facebook.';
        }
    }

    // This function is called when someone finishes with the Login
    // Button.  See the onlogin handler attached to it in the sample
    // code below.
    function checkLoginState() {
        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });
    }

    window.fbAsyncInit = function() {
        FB.init({
            appId      : '1458800681051559',
            cookie     : true,
            xfbml      : true,
            version    : 'v2.1'
        });

        // Now that we've initialized the JavaScript SDK, we call
        // FB.getLoginStatus().  This function gets the state of the
        // person visiting this page and can return one of three states to
        // the callback you provide.  They can be:
        //
        // 1. Logged into your app ('connected')
        // 2. Logged into Facebook, but not your app ('not_authorized')
        // 3. Not logged into Facebook and can't tell if they are logged into
        //    your app or not.
        //
        // These three cases are handled in the callback function.

        FB.getLoginStatus(function(response) {
            statusChangeCallback(response);
        });

    };

    // Load the SDK asynchronously
    (function(d, s, id) {
        var js, fjs = d.getElementsByTagName(s)[0];
        if (d.getElementById(id)) return;
        js = d.createElement(s); js.id = id;
        js.src = "//connect.facebook.net/en_US/sdk.js";
        fjs.parentNode.insertBefore(js, fjs);
    }(document, 'script', 'facebook-jssdk'));

    // Here we run a very simple test of the Graph API after login is
    // successful.  See statusChangeCallback() for when this call is made.
    function testAPI() {
        console.log('Welcome!  Fetching your information.... ');
        FB.api('/me', function(response) {
            console.log('Successful login for: ' + response.name);
            document.getElementById('status').innerHTML =
                    'Thanks for logging in, ' + response.name + '!';
        });
    }
</script>
{/literal}
<!--
  Below we include the Login Button social plugin. This button uses
  the JavaScript SDK to present a graphical Login button that triggers
  the FB.login() function when clicked.
-->

<fb:login-button scope="public_profile,email" response_type="code" redirect_uri="http://lps.loc/login/?auth_type=fb" onlogin="checkLoginState();">
</fb:login-button>*}