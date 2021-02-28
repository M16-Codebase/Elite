<div class="popup-window popup-add-comment" data-class="popup-comment popup-green" data-title="Новый комментарий" data-width="464">
	<form method="post" action="/blog/addComment/">
		<input type="hidden" name="id" value="{$post.id}">
		<div class="fields-block">
			<div class="field">
				{if $user == NULL}
					<label class="field f-col required">
						<div class="f-title">Ваше имя</div>
						<div class="f-input"><input type="text" name="author" value="{$comment_author}"></div>
						<div class="f-error e-empty a-hidden">Пожалуйста, укажите ваше имя</div>
					</label>
				{else}
					<div class="f-title">
						<span>Имя: {$user['name'].' '.$user['surname']}</span>
					</div>
				{/if}
			</div>
			<label class="field f-col required">
				<div class="f-title">Ваш комментарий</div>
				<div class="f-input">
					<textarea name="comment" rows="14"></textarea>
				</div>
			</label>
		</div>
		<ul class="f-error general-err a-hidden">
			<li class="e-check_sum a-hidden">Ошибка при отправке формы. Перезагрузите страницу и попробуйте еще раз.</li>
		</ul>
		<div class="buttons">
			<button type="submit" class="a-btn-green more-arrow">Отправить</button>
		</div>
	</form>
</div>
				
{*
<div class="popup-window popup-add-comment" data-class="popup-comment" data-title="Ваш комментарий" data-width="540">
	<form method="post" action="/blog/addComment/">
		<input type="hidden" name="id" value="{$post.id}">
		<div class="field f-name">
			<div class="f-title">
				Напишите Ваш комментарий
			</div>
		</div>
		{if $user == NULL}
			Имя<input type="text" name="author"value="{$comment_author}"><br>
			E-mail<input type="text" name="email" value="{$email}"><br>
		{else}
			Имя: {$user['name'].' '.$user['surname']}<br>
			E-mail: {$user['email']}<br>
		{/if}
		Комментарий<textarea name="comment"></textarea>
		<input type="submit" value="Post comment">
	</form>
</div>
*}