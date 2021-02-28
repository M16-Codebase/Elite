{?$pageTitle = 'Контент — Блог'}
{?$admin_page = 1}
{?$access_control = true}
{*ссылки в Modules/Posts/Pages/index.tpl неправильные, 
надо либо придумать как сделать правильные ссылки и для сервиса и для страниц, (пока сделала $moduleUrl, надо смотреть, везде ли так можно)
либо скопипастить оттуда и заменить что надо*}
{?$includeJS.alien_index = "Modules/Posts/Pages/index.js"}
{?$site_link='/blog/'}
{include file="Modules/Posts/Pages/index.tpl" url_on_site='/blog/' action_rus='Блог'}