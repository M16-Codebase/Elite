{?$current_user = $account->getUser()}
<p class="pr-main"><span class="user-info name">{$current_user.name}</span> <span class="user-info surname">{$current_user.surname}</span></p>
<p>Телефон: <span class="user-info phone">{$current_user.phone}</span></p>
<p>Электронная почта: <span class="user-info email">{$current_user.email}</span></p>