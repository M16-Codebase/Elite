{?$pageTitle = 'Дополнительные урлы в sitemap — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="actions-cont">
	<div class="content-top">
		<h1>Дополнительные URLs в sitemap</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl"
				multiple = true
				buttons = array(
					'back' => '/site/',
					'add' => "#",
					'delete' => array(
						'inactive' => true
					),
			)}
		</div>
	</div>
	<div class="content-scroll">
		<form class="viewport link-list" action="/seo-sitemap/deleteAllowUrls/">
			<div class="white-blocks">
				<div class="white-header white-block-row wblock">
					<div class="w1 small"><input type="checkbox" class="check-all"></div>
					<div class="w3">URL</div>
					<div class="w3">Приоритет</div>
					<div class="w3">Последнее изменение</div>
					<div class="w1"></div>
					<div class="w1"></div>
				</div>
				<div class="white-body">
					{include file="Modules/Seo/SiteMapAdmin/allowUrlsList.tpl"}
				</div>
			</div>
		</form>
	</div>
</div>
{include file="/Modules/Seo/SiteMapAdmin/addAllowUrls.tpl" assign=add_urls}
{capture assign=editBlock name=editBlock}
	{$add_urls|html}
{/capture}