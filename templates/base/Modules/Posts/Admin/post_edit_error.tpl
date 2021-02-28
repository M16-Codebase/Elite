{if isset($save_error_field)}
	<div class="error"><b>Ошибка: </b>
		{if $save_error_field=='title'}Слишком короткий заголовок
		{elseif $save_error_field=='text'}Мало текста (менее 10 символов)
		{else}Неправильно заполнено поле "{$save_error_field}"
		{/if}
	</div>
{/if}
