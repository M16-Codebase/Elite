{?$currentModuleUrl = $moduleUrl}{* костыль для подмены урла модуля, не затрагивая основную переменную *}
{if !empty($current_type) && empty($currentCatalog)}
	{?$currentCatalog = $current_type->getCatalog()}
{/if}
{if $currentModuleUrl == 'catalog-item'}
	{?$item_type_id = (!empty($smarty.get.id)) ? $smarty.get.id : (!empty($smarty.get.item_type_id) ? $smarty.get.item_type_id : 0)}
	{?$currentModuleUrl = $currentCatalog.key . '-item'}
{/if}
{if $currentModuleUrl == 'catalog-view'}
	{?$currentModuleUrl = $currentCatalog.key . '-item'}
{/if}
{if $currentModuleUrl == 'catalog-type' && $action == 'catalog'}
	{if $currentCatalog.key == 'config' && $current_type.key != $currentCatalog.key}
		{?$currentModuleUrl = 'config-' . $current_type.key}
	{elseif $currentCatalog.key == 'reviews_question' && $current_type.key != $currentCatalog.key}
        {?$currentModuleUrl = 'product-' . $current_type.key}
	{else}
		{?$currentModuleUrl = $currentCatalog.key . '-item'}
	{/if}
{/if}
<div class="aside-closed a-link">
	<strong>Показать меню</strong>
</div>
<ul class="aside-menu">
	<li class="am-submenu-contains{if $currentModuleUrl == 'catalog-type' || $currentModuleUrl == 'catalog' || $currentModuleUrl == 'catalog-admin'
									|| $currentModuleUrl == 'real-estate-item' || $currentModuleUrl == 'resale-item' || $currentModuleUrl == 'infrastructure-item'} m-current m-open{/if}">
		<a href="/catalog-admin/" class="am-item-title">Объекты</a>
		<ul class="aside-submenu">
			{if $account->isPermission('catalog-type') && $accountType != 'Admin'}
				<li{if $currentModuleUrl == 'catalog-type' && $action != 'cities'} class="m-current"{/if}>
					<a href="/catalog-type/" class="am-submenu-title">Настройки каталога</a>
				</li>
			{/if}
			{if $account->isPermission('catalog-item')}
				<li{if $currentModuleUrl == 'real-estate-item'} class="m-current"{/if}>
					<a href="/catalog-type/catalogIndex/?key=real-estate" class="am-submenu-title">Первичная недвижимость</a>
				</li>
				<li{if $currentModuleUrl == 'resale-item'} class="m-current"{/if}>
					<a href="/catalog-type/catalogIndex/?key=resale" class="am-submenu-title">Вторичная недвижимость</a>
				</li>
                <li{if $currentModuleUrl == 'residential-item'} class="m-current"{/if}>
                    <a href="/catalog-type/catalogIndex/?key=residential" class="am-submenu-title">Загородная</a>
                </li>
				<li{if $currentModuleUrl == 'infrastructure-item'} class="m-current"{/if}>
					<a href="/catalog-type/catalogIndex/?key=infrastructure" class="am-submenu-title">Объекты инфраструктуры</a>
				</li>
			{/if}
		</ul>
	</li>
	<li class="am-submenu-contains">
		<a href="/catalog-type/catalogIndex/?key=staff_list" class="am-item-title">Справочник сотрудников</a>
	</li>
	{if $account->isPermission('feedback')}
		<li class="am-submenu-contains">
			<a href="/feedback/" class="am-item-title">База обращений</a>
		</li>
	{/if}
	<li class="am-submenu-contains{if $currentModuleUrl == 'site-banner' || $currentModuleUrl == 'tests-admin' || $currentModuleUrl == 'site-teaser' || $currentModuleUrl == 'offers-item' || $currentModuleUrl == 'vacancy-item' || $currentModuleUrl == 'video-item' || $currentModuleUrl == 'photo-item' || $currentModuleUrl == 'segment-text' || $currentModuleUrl == 'jobs-admin' || $currentModuleUrl == 'deals-admin' || $currentModuleUrl == 'blog-admin' || $currentModuleUrl == 'news-admin' || $currentModuleUrl == 'district-item' || $currentModuleUrl == 'article-admin' || $currentModuleUrl == 'product-reviews' || $currentModuleUrl == 'product-questions'} m-current m-open{/if}">
		<a href="#" class="am-item-title">Контент</a>
		<ul class="aside-submenu">
            {if $account->isPermission('site-banner')}
                <li{if $currentModuleUrl == 'site-banner'} class="m-current"{/if}>
                    <a href="/site-banner/" class="am-submenu-title">Баннеры</a>
                </li>
            {/if}
            {*{if $account->isPermission('site-teaser')}*}
                {*<li{if $currentModuleUrl == 'site-teaser'} class="m-current"{/if}>*}
                    {*<a href="/site-teaser/" class="am-submenu-title">Тизеры</a>*}
                {*</li>*}
            {*{/if}*}
			{if $account->isPermission('segment-text')}
				<li{if $currentModuleUrl == 'segment-text'} class="m-current"{/if}>
					<a href="/segment-text/" class="am-submenu-title">Тексты к страницам</a>
				</li>
			{/if}
			{*{if $account->isPermission('article-admin')}*}
				{*<li{if $currentModuleUrl == 'article-admin'} class="m-current"{/if}>*}
					{*<a href="/article-admin/" class="am-submenu-title">Статьи</a>*}
				{*</li>*}
			{*{/if}*}
			{if $account->isPermission('catalog-item')}
				<li{if $currentModuleUrl == 'district-item'} class="m-current"{/if}>
					<a href="/catalog-type/catalogIndex/?key=district" class="am-submenu-title">Районы</a>
				</li>
			{/if}
			{*{if $account->isPermission('news-admin')}*}
				{*<li{if $currentModuleUrl == 'news-admin'} class="m-current"{/if}>*}
					{*<a href="/news-admin/{if $constants.segment_mode == 'lang'}?s=1{/if}" class="am-submenu-title">Новости</a>*}
				{*</li>*}
			{*{/if}*}
			{*{if $account->isPermission('blog-admin')}*}
				{*<li{if $currentModuleUrl == 'blog-admin'} class="m-current"{/if}>*}
					{*<a href="/blog-admin/{if $constants.segment_mode == 'lang'}?s=1{/if}" class="am-submenu-title">Блог</a>*}
				{*</li>*}
			{*{/if}*}
			{*{if $account->isPermission('catalog-item')}*}
				{*<li{if $currentModuleUrl == 'offers-item'} class="m-current"{/if}>*}
					{*<a href="/catalog-type/catalogIndex/?key=offers" class="am-submenu-title">Акции</a>*}
				{*</li>*}
				{*<li{if $currentModuleUrl == 'vacancy-item'} class="m-current"{/if}>*}
					{*<a href="/catalog-type/catalogIndex/?key=vacancy" class="am-submenu-title">Вакансии</a>*}
				{*</li>*}
				{*<li{if $currentModuleUrl == 'photo-item'} class="m-current"{/if}>*}
					{*<a href="/catalog-type/catalogIndex/?key=photo" class="am-submenu-title">Фотогалереи</a>*}
				{*</li>*}
				{*<li{if $currentModuleUrl == 'video-item'} class="m-current"{/if}>*}
					{*<a href="/catalog-type/catalogIndex/?key=video" class="am-submenu-title">Видеогалереи</a>*}
				{*</li>*}
                {*<li{if $currentModuleUrl == 'product-reviews'} class="m-current"{/if}>*}
                    {*<a href="/catalog-type/catalogIndex/?key=reviews_question&type=reviews" class="am-submenu-title">Отзывы</a>*}
                {*</li>*}
                {*<li{if $currentModuleUrl == 'product-questions'} class="m-current"{/if}>*}
                    {*<a href="/catalog-type/catalogIndex/?key=reviews_question&type=questions" class="am-submenu-title">Вопросы</a>*}
                {*</li>*}
			{*{/if}*}
		</ul>
	</li>
	{if $account->isPermission('lists') || $account->isPermission('site-config')}
		<li class="am-submenu-contains{if $currentModuleUrl == 'exchange' || $currentModuleUrl == 'config-global' || $currentModuleUrl == 'config-contacts' || $currentModuleUrl == 'config-notification' || $currentModuleUrl == 'logs-cron' || $currentModuleUrl == 'lists' || ($currentModuleUrl == 'site-config' && empty($seoPage)) || $action == 'cities' || $currentModuleUrl == 'menu-editor'} m-current m-open{/if}">
			<a href="#" class="am-item-title">Настройки</a>
			<ul class="aside-submenu">
				<li{if $currentModuleUrl == 'config-global'} class="m-current"{/if}>
					<a href="/catalog-type/settingsIndex/?key=global" class="am-submenu-title">Параметры сайта</a>
				</li>
				<li{if $currentModuleUrl == 'config-contacts'} class="m-current"{/if}>
					<a href="/catalog-type/settingsIndex/?key=contacts" class="am-submenu-title">Контактная информация</a>
				</li>
				<li{if $currentModuleUrl == 'config-notification'} class="m-current"{/if}>
					<a href="/catalog-type/settingsIndex/?key=notification" class="am-submenu-title">Получатели уведомлений</a>
				</li>
				{if $accountType == 'SuperAdmin' && $constants.segment_mode != 'none' && $account->isPermission('lists', 'segment')}
					<li{if $currentModuleUrl == 'lists' && $action = 'segment'} class="m-current"{/if}>
						<a href="/lists/segment/" class="am-submenu-title">Сегменты сайта</a>
					</li>
				{/if}
                {if $account->isPermission('cron-shedule')}
                    <li{if $currentModuleUrl == 'cron-shedule'} class="m-current"{/if}>
						<a href="/cron-shedule/" class="am-submenu-title" >Расписание крон задач</a>
					</li>
                {/if}
				{if $account->isPermission('logs-cron')}
					<li{if $currentModuleUrl == 'logs-cron'} class="m-current"{/if}>
						<a href="/logs-cron/" class="am-submenu-title" >Логи крон задач</a>
					</li>
				{/if}
			</ul>
		</li>
	{/if}
	{if $account->isPermission('users-edit')}
		<li class="am-submenu-contains{if $currentModuleUrl == 'users-edit'} m-current m-open{/if}">
			<a href="/users-edit/?type=man" class="am-item-title">Пользователи</a>
		</li>
	{/if}

	{if $account->isPermission('subscribe')}
		<li class="am-submenu-contains{if $currentModuleUrl == 'subscribe'} m-current m-open{/if}">
			<a href="#" class="am-item-title">Рассылки</a>
			<ul class="aside-submenu">
				<li{if $currentModuleUrl == 'subscribe' && $action != 'index'} class="m-current"{/if}><a href="/subscribe/subscribersLists/" class="am-submenu-title">Списки рассылки</a></li>
				<li{if $currentModuleUrl == 'subscribe' && $action == 'index'} class="m-current"{/if}><a href="/subscribe/" class="am-submenu-title">Сервис рассылки</a></li>
			</ul>
		</li>
	{/if}
	<li class="am-submenu-contains">
		<a href="/logs-view/" class="am-item-title">Логи</a>
	</li>
	{if $account->isPermission('site-search')}
		<li class="am-submenu-contains{if $currentModuleUrl == 'site-search'} m-current m-open{/if}">
			<a href="#" class="am-item-title">Поиск</a>
			<ul class="aside-submenu">
				<li{if $currentModuleUrl == 'site-search' && $action == 'index'} class="m-current"{/if}><a href="/site-search/" class="am-submenu-title">Фразы</a></li>
				<li{if $currentModuleUrl == 'site-search' && $action == 'logs'} class="m-current"{/if}><a href="/site-search/logs/" class="am-submenu-title">Логи</a></li>
			</ul>
		</li>
	{/if}
	{if $account->isPermission('seo')}
		<li class="am-submenu-contains{if $currentModuleUrl == 'seo' || $currentModuleUrl == 'config-seo' || !empty($seoPage)}  m-current m-open{/if}">
			<a href="/seo/" class="am-item-title">SEO</a>
			<ul class="aside-submenu">
				<li{if $currentModuleUrl == 'seo' && ($action == 'index' || $action == 'edit')} class="m-current"{/if}><a href="/seo/" class="am-submenu-title">Mета-теги</a></li>
				<li{if $currentModuleUrl == 'seo' && $action == 'viewRedirects'} class="m-current"{/if}><a href="/seo/viewRedirects/" class="am-submenu-title">Редиректы</a></li>
				<li{if !empty($seoPage) || ($currentModuleUrl == 'seo' && $action == 'counters')} class="m-current"{/if}><a href="/seo/counters/" class="am-submenu-title">Настройка счетчиков</a></li>
				<li{if $currentModuleUrl == 'config-seo'} class="m-current"{/if}><a href="/catalog-type/settingsIndex/?key=seo" class="am-submenu-title">Основные настройки</a></li>
				<li{if $currentModuleUrl == 'seo' && $action == 'editRobots'} class="m-current"{/if}><a href="/seo/editRobots/" class="am-submenu-title">Robots.txt</a></li>
				<li{if $currentModuleUrl == 'seo' && $action == 'editSitemap'} class="m-current"{/if}><a href="/seo/editSitemap/" class="am-submenu-title">Sitemap.xml</a></li>
				<li{if $currentModuleUrl == 'seo' && $action == 'links'} class="m-current"{/if}><a href="/seo/links/" class="am-submenu-title">Перелинковка</a></li>
			</ul>
		</li>
	{/if}
	{if $account->isPermission('seo-sitemap')}
		<li class="am-submenu-contains{if $currentModuleUrl == 'seo-sitemap' || !empty($seoPage)}  m-current m-open{/if}">
			<a href="/seo-sitemap/" class="am-item-title">Sitemap.xml</a>
			<ul class="aside-submenu">
				<li{if $currentModuleUrl == 'seo-sitemap' && $action == 'additionalRules'} class="m-current"{/if}><a href="/seo-sitemap/additionalRules/" class="am-submenu-title">Дополнительные правила для урлов</a></li>
				<li{if $currentModuleUrl == 'seo-sitemap' && $action == 'allowUrls'} class="m-current"{/if}><a href="/seo-sitemap/allowUrls/" class="am-submenu-title">Урлы для добавления</a></li>
			</ul>
		</li>
	{/if}
	{if $account->isPermission('sphinx-wordforms')}
		<li class="am-submenu-contains{if $currentModuleUrl == 'sphinx-wordforms' || !empty($seoPage)}  m-current m-open{/if}">
			<a href="/sphinx-wordforms/" class="am-item-title">Поиск Sphinx</a>
			<ul class="aside-submenu">
				<li{if $action == 'index'} class="m-current"{/if}><a class="am-submenu-title" href="/sphinx-wordforms/">Синонимы</a></li>
				<li{if $action == 'stopwords'} class="m-current"{/if}><a class="am-submenu-title" href="/sphinx-wordforms/stopwords/">Стоп-слова</a></li>
				<li{if $action == 'rebuild'} class="m-current"{/if}><a class="am-submenu-title" href="/sphinx-wordforms/rebuild/">Перегенерация индекса</a></li>
			</ul>
		</li>
	{/if}
	{if $accountType == 'SuperAdmin'}
		<li class="am-submenu-contains">
			<a href="#" class="am-item-title">Develop</a>
			<ul class="aside-submenu">
				{if $account->isPermission('site-logs') && $account->isPermission('catalog-superadmin', 'clearItemsCache')}
					<li>
						<a href="/catalog-superadmin/clearItemsCache/" class="am-submenu-title">Очистить кэш</a>
					</li>
					<li>
						<a href="/site-logs/get/" class="am-submenu-title">Скачать логи ошибок</a>
					</li>
					<li>
						<a href="/site-logs/clear/" class="am-submenu-title">Очистить логи ошибок</a>
					</li>
				{/if}
				<li>
					<a href="/permissions/roles/" class="am-submenu-title">Роли</a>
				</li>
				<li>
					<a href="/permissions/" class="am-submenu-title">Допуск</a>
				</li>
				<li>
					<a href="/db-migrations/" class="am-submenu-title">Миграции БД</a>
				</li>
                <li>
					<a href="/shared-memory/" class="am-submenu-title">Разделяемая память</a>
				</li>
			</ul>
		</li>
	{/if}
</ul>
<div class="aside-menu-button"></div>