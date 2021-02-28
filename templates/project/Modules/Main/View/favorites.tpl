{if $request_segment.key == 'ru'}
	{?$pageTitle = 'Избранные предложения | М16-Недвижимость'}
	{?$pageDescription = 'Ваш личный ТОП-лист элитных квартир и объектов Санкт-Петербурга'}
{else}
	{?$pageTitle = 'My Favorites List | M16 Real Estate Agency'}
	{?$pageDescription = 'Your personal top list of luxury apartments and buildings in St.Petersburg'}
{/if}
<div class="top-bg m-white">
	<div class="site-top">
		<h1 class="title" title="{$lang->get('Избранные предложения', 'Favorite offers collection')}">{$lang->get('<span>Избранные</span><br>предложения', '<span>Favorite</span><br>offers collection')|html}</h1>
	</div>
</div>
<div class="favorites">
	{include file='Modules/Main/View/favoritesInner.tpl'}
</div>