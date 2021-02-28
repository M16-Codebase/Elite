{*ссылки в Modules/Posts/Pages/index.tpl неправильные, 
надо либо придумать как сделать правильные ссылки и для сервиса и для страниц, (пока сделала $moduleUrl, надо смотреть, везде ли так можно)
либо скопипастить оттуда и заменить что надо*}
{?$includeJS.alien_index = "Modules/Posts/Pages/index.js"}
{include file="Modules/Posts/Pages/index.tpl" url_on_sait='/news/' action_rus='Полезные советы'}