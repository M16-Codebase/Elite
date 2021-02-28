{?$pageTitle = 'Ссылки для перелинковки — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Ссылки для перелинковки</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" buttons=array(
			'add' => '#'
		)}
	</div>
</div>
<div class="content-scroll">
	<div class="viewport">
		{include file="Modules/Seo/SuperAdmin/linksList.tpl"}
	</div>
</div>
{include file="/Modules/Seo/SuperAdmin/addLinks.tpl" assign=add_links}
{capture assign=editBlock name=editBlock}
	{$add_links|html}
{/capture}			

   