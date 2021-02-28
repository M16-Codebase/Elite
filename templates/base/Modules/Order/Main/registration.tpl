<form method="POST">
    <input type="radio" name="person_type" value="fiz" />Физ лицо
    <input type="radio" name="person_type" value="org" />Юр лицо<br />
    E-mail <input type="text" name="email" />{if !empty($errors['email'])}{$errors['email']}{/if}<br />
    Пароль <input type="password" name="pass" />{if !empty($errors['pass'])}{$errors['pass']}{/if}<br />
    Повтор пароля <input type="password" name="pass2" /><br />{if !empty($errors['pass2'])}{$errors['pass2']}{/if}
    <input type="submit" name="login_form" />
</form>