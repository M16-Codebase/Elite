{include file="Admin/components/header.tpl"}
<div class="page-main page-center a-justify">
	<aside class="page-aside aside-col{if ($moduleUrl == 'catalog-admin' && $action == 'index') || ($moduleUrl == 'site' && $action == 'index')} m-mainpage{/if}">
		{if !empty($aside_filter)}
			{?$aside_filter = $aside_filter|trim}
		{/if}					
		<div class="aside-menu-wrap{if !empty($empty_filter) || !empty($aside_filter)} m-closed{/if}">
			{include file="Admin/components/aside_menu.tpl"}
		</div>					
		{if !empty($aside_filter)}
			{$aside_filter|html}
		{else}
			<section class="aside-filter a-hidden"></section>
		{/if}
	</aside>
	{if !($moduleUrl == 'catalog-admin' && $action == 'index') && !($moduleUrl == 'site' && $action == 'index')}
		<section class="main-content main-col{if !empty($border_bottom)} m-border-bottom{/if}">
			{include file="Admin/components/breadcrumbs.tpl"}
			<div class="main-content-inner{if !empty($currentEditBlock)} m-edit{/if}">
				<div class="view-content">
					{$moduleResult|html}
				</div>
				{if !empty($editBlock)}
					<div class="edit-content-forms a-hidden">
						{$editBlock|html}
					</div>
				{/if}
				{if !empty($currentEditBlock)}
					<div class="edit-content m-edit-open m-current{if !empty($currentEditBlock_class)} {$currentEditBlock_class}{/if}">
						{$currentEditBlock|html}
					</div>
				{/if}
			</div>
		</section>
	{/if}
	<div class="mobile-detect"></div>
	<div class="aside-lock"></div>
</div>