{?$subject = "Бонусные баллы в магазине «Мастер Сантехник»"}
<div style="font-size: 18px; color: #000000; margin-top: 30px; margin-bottom: 2px;">
	<font color="black">Здравствуйте{if !empty($user.name)}, {$user.name}{/if}!</font>
</div>
<div style="margin-top: 20px;">
	<div style="color: #666666; font-size: 12px; font-style: italic; line-height: 14px; font-family: Arial; margin-top: 9px;">
		У вас {$user.bonus|plural_form:'балл':'балла':'баллов'}.
	</div>
</div>
{include file="mails/mail_bottom.tpl" bottom=1}