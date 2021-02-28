{?$pageTitle = 'Тексты к страницам — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Тексты к страницам</h1>
	<div class="content-options">
		{include file='Admin/components/actions_panel.tpl'
			buttons = array(
				'add' => 1)
			)
		}
	</div>
</div>
<div class="content-scroll">
	<div class="white-blocks viewport">
		<div class="wblock white-block-row white-header">
			<div class="w05"></div>
			<div class="w6">Группа текстов</div>
			<div class="w5">URL</div>
			<div class="w05"></div>
		</div>
		<div class="white-body sortable" data-url="/segment-text/movePageUrl/" data-newpositionname="position">
			{include file="Modules/Segment/Text/urlList.tpl"}
		</div>
	</div>
</div>
{include file="/Modules/Segment/Text/addUrl.tpl" assign=add_url}
{capture assign=editBlock name=editBlock}
	{$add_url|html}
{/capture}