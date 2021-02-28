{?$current_user = $account->getUser()}
{?$current_region = $current_user->getRegion()}
<p>Регион:</p>
<p class="pr-main">{$current_region.title} <a href=".popup-profile-region" class="edit-link" data-toggle="popup" data-action="open">— Изменить</a></p>