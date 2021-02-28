{?$pageTitle = 'Редиректы — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Редиректы</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" 
			multiple = true
			buttons = array(
				'add' => '#'
		)}
	</div>
	<div class="redirects-forms a-justify">
		<form actio="/seo/viewRedirects/" class="manage-filter" method="GET">
			<span>From:</span><input type="text" name="from" class="from-input short" />
			<span>To:</span><input type="text" name="to" class="to-input short" />
			<input type="submit" value="Фильтр" />
		</form>
		<form class="manage-files" action="/seo/uploadRedirects/">
			<input class="to-input" type="file" name="redirects" onchange="$(this).closest('form').submit();" size="5" />
		</form>
	</div>
</div>
<div class="content-scroll">
	<div class="viewport">
		<div class="white-blocks">
			<div class="wblock white-block-row white-header">
				<div class="w5">Откуда</div>
				<div class="w5">Куда</div>
				<div class="w2"></div>
			</div>
			<div class="white-body">
				{include file='Modules/Seo/SuperAdmin/redirectList.tpl'}
			</div>
		</div>
	</div>
</div>
{include file="/Modules/Seo/SuperAdmin/addRedirect.tpl" assign=add_redirect}
{capture assign=editBlock name=editBlock}
	{$add_redirect|html}
{/capture}