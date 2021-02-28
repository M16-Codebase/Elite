<form>
    {if empty($email)}
        Введите email <input type="text" name="email">
    {else}
        email <input type="text" name="email">
        Пользователь с электронной почтой {$email} уже зарегистрирован, введите пароль чтобы подтвердить что это вы<br>
        <input type="password" name="pass">
        {if !empty($pass)}<br>неправильный пароль{/if}
    {/if}
    <input type="submit">
</form>

{$errors|var_dump}
