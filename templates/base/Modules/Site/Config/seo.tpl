{?$pageTitle = 'Настройки счетчиков'}
{?$includeJS.site_config_main = 'Modules/Site/Config/index.js'}
{*include file='Modules/Site/Config/index.tpl'*}
<div class="content-top">
	<h1>Изменение параметров SEO</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
			'add' => ($account->isPermission('site-config', 'create')? '#' : ''),
			'save' => ((!empty($site_params) && $account->isPermission('site-config'))? '#' : '')
			)}
	</div>
	{?$custom_seo_params = array()}
</div>
<form id="site_config_form" class="content-scroll">
	<div class="white-blocks viewport">
		{if !empty($site_params)}
			<div class="wblock white-block-row white-header">
				{if $accountType == 'SuperAdmin'}
					<div class="w3">
						Ключ
					</div>
				{/if}
				<div class="{if $accountType == 'SuperAdmin'}w4{else}w6{/if}">
					Описание
				</div>
				<div class="{if $accountType == 'SuperAdmin'}w4{else}w5{/if}">
					Значение
				</div>
				{if $account->isPermission('site-config', 'del')}
					<div class="w1"></div>
				{/if}
			</div>
			<div class="white-body">	
				{foreach from=$site_params item=param}
					{if !in_array($param.key, array('sitemap_useragent', 'changefreq', 'sitemap_root', 'sitemap_www'))}
						<div class="wblock white-block-row">
							{if $accountType == 'SuperAdmin'}
								<div class="w3">{$param.key}</div>
							{/if}
							<div class="{if $accountType == 'SuperAdmin'}w4{else}w6{/if}">
								{if $accountType == 'SuperAdmin'}
									<input type="text" class="key-descr" name="param[{$param.key}][description]" />
								{else}
									<input type="hidden" class="key-descr" name="param[{$param.key}][description]" />
									{$param.description}
								{/if}
							</div>
							<div class="{if $accountType == 'SuperAdmin'}w4{else}w5{/if}">
								<input type="hidden" name="param[{$param.key}][type]" value="{$param.type}">
								<input type="hidden" name="param[{$param.key}][data_type]" value="{$param.data_type}">
								{if $param.data_type == 'checkbox'}
									<input type="hidden" name="param[{$param.key}][value]" value="0" />
									<input type="checkbox" name="param[{$param.key}][value]" value="1" />
								{elseif $param.data_type == 'textarea'}
									<textarea name="param[{$param.key}][value]" rows="7" cols="40"></textarea>
								{else}
									<input type="text" name="param[{$param.key}][value]" class="value_input" />
								{/if}
							</div>
							{if $account->isPermission('site-config', 'del')}
								{if $param.key != 'broken' && $param.key != 'test_mode'}
									<a class="w1 action-button action-delete" href="/site-config/del/?key={$param.key}" onclick="if (!confirm('Удалить параметр?')) return false;" title="Удалить"><i></i></a>
								{/if}
							{/if}
						</div>
					{else}
						{?$custom_seo_params[$param.key] = $param}
					{/if}
				{/foreach}
			</div>
			{else}
			<div class="no-seo-params">
				SEO параметры еще не созданы
			</div>	
		{/if}
	</div>
</form>
{if $accountType == 'SuperAdmin'}
	{capture assign=editBlock name=editBlock}
		{include assign=sitepropForm file="Admin/popups/create_siteprop.tpl"}
		<div class="content-top">
			<h1>Новый параметр</h1>
			<div class="content-options">
				{?$buttons = array(
					'back' => array('text' => 'Отмена'),
					'save' => array(
						'text' => 'Добавить',
						'url' => '#'
					)
				)}
				{include file="Admin/components/actions_panel.tpl"
					assign = seoHandlers
					buttons = $buttons
				}
				{$seoHandlers|html}
			</div>
		</div>
		<div class="content-scroll">
			<div class="viewport">
				{$sitepropForm|html}
			</div>
		</div>
	{/capture}
{/if}
{if !empty($errors)}
    <div class="popup-window popup-errors">
        <h2 class="error-title">Неверно заполнены поля:</h2>
        <ul class="error-fields">
            {foreach from=$errors item=err key=key}
                <li>{$key}: {$err}</li>
            {/foreach}
        </ul>
        <div class="buttons">
            <div class="button close-popup">Закрыть</div>
        </div>
    </div>
{/if}