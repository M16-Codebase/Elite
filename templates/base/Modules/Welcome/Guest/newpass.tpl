<form class="auth-form" method="POST" action="/login/">
	<div class="header-yellow">
	</div>
	<div class="auth-body">
		<h3>Восстановление пароля</h3>
		<p>Новый пароль отправлен Вам на e-mail.</p>
		<div class="link">
			Вы сможете изменить пароль в <a href="/profile/">личном кабинете</a>
		</div>
		{if !empty($error)}
			<div class="errors" data-error="{$error}">
				<strong>{$error}</strong>
			</div>
		{/if}
	</div>
</form>