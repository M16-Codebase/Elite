{?$pageTitle = 'Дополнительные правила sitemap — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="actions-cont">
	<div class="content-top">
		<h1>Дополнительные правила sitemap</h1>
		<div class="content-options">
			{include file="Admin/components/actions_panel.tpl" 
				multiple = true
				buttons = array(
					'back' => '/site/',
					'add' => '#',
					'delete' => array(
						'inactive' => true
					),
			)}
		</div>
	</div>
	<div class="content-scroll">
		<div class="viewport">
			<form action="/seo-sitemap/deleteRule/" class="sitemap-list white-blocks overview">
				<div class="white-blocks">
					<div class="wblock white-block-row white-header">
						<div class="w05"><input type="checkbox" class="check-all" /></div>
						<div class="w6">Ссылка</div>
						<div class="w5">Правило</div>
						<div class="w05"></div>
					</div>
					<div class="white-body" data-url="">
					{include file="Modules/Seo/SiteMapAdmin/rulesList.tpl"}
					</div>
				</div>
			</form>
		</div>
	</div>
</div>
{include file="/Modules/Seo/SiteMapAdmin/addAdditionalRules.tpl" assign=add_rule}
{capture assign=editBlock name=editBlock}
	{$add_rule|html}
{/capture}