{?$pageTitle = 'Настройки генератора sitemap'}
{?$includeJS.site_config_main = 'Modules/Site/Config/index.js'}
<div class="content-top">
	<h1>Настройки генератора sitemap.xls</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
			'save' => ($account->isPermission('seo-config'))? '#' : '')
			)}
		{?$custom_seo_params = array()}
	</div>
</div>
<div class="content-scroll">
	<form id="site_config_form" class="white-blocks viewport">
		<div class="wblock white-block-row white-header">
			{if $accountType == 'SuperAdmin'}
				<div class="w4 th-short">Ключ</div>
			{/if}
			<div class="w4">Описание</div>
			<div class="w4">Значение</div>
		</div>
		<div class="wblock white-block-row">
			{if $accountType == 'SuperAdmin'}
				<div class="w4">changefreq</div>
			{/if}
			<div class="w4">
				changefreq, используемый для автоматических ссылок в sitemap.xml
			</div>
			<div class="w4">
				<input type="hidden" name="param[changefreq][type]" value="seo">
				<input type="hidden" name="param[changefreq][data_type]" value="text">
				<select name="param[changefreq][value]">
					{foreach from=$allow_changefreq item=changefreq}
						<option value="{$changefreq}">{$changefreq}</option>
					{/foreach}
				</select>
			</div>
		</div>
		<div class="wblock white-block-row">
			{if $accountType == 'SuperAdmin'}
				<div class="w4">sitemap_useragent</div>
			{/if}
			<div class="w4">
				Юзерагент, используемый для построения sitemap.xml
			</div>
			<div class="w4">
				<input type="hidden" name="param[sitemap_useragent][type]" value="seo">
				<input type="hidden" name="param[sitemap_useragent][data_type]" value="text">
				<select name="param[sitemap_useragent][value]">
					<option value="*">*</option>
					<option value="Yandex">Yandex</option>
					<option value="Google">Google</option>
				</select>
			</div>
		</div>
		<div class="wblock white-block-row">
			{if $accountType == 'SuperAdmin'}
				<div class="w4">sitemap_root</div>
			{/if}
			<div class="w4">
				сохранять sitemap в /sitemap.xml
			</div>
			<div class="w4 text-right">
				<input type="hidden" name="param[sitemap_root][type]" value="seo">
				<input type="hidden" name="param[sitemap_root][data_type]" value="checkbox">
				<input type="hidden" value="0" name="param[sitemap_root][value]">
				<input type="checkbox" value="1" name="param[sitemap_root][value]">
			</div>
		</div>
	</form>
</div>