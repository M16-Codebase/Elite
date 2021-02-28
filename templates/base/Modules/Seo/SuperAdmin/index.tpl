{?$pageTitle = 'META-теги — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>META-теги</h1>
	<div class="content-options">
		{include file='Admin/components/actions_panel.tpl'
			buttons = array(
				'add' => 1)
			)
		}
	</div>
	<ul class="seo-header">
	{*	<li class="a-left">
			<form>
				<input type="text" name="page_uid"><input type="submit" value="Создать" />
			</form>
		</li>*}
		{if $seoItemsCount > 0}
			<li class="a-right">
				<form method="GET">
					<input type="text" name="page_uid_find"><input type="submit" value="Найти" />
				</form>
			</li>
		{/if}
	</ul>
</div>
<div class="content-scroll">
	<div class="viewport">
		{?$sort_url = $smarty.server.REQUEST_URI}
		{?$sort_url = $sort_url|regex_replace:'~.sort.[a-z ]*.=(0|1)~':''}
		<div class="white-blocks">
			<div class="wblock white-header white-block-row">
				<div class="w1 th-num"><a href="/seo/?sort[id]={if isset($sort.id)}{1 - $sort.id}{else}1{/if}"{if isset($sort.id)} class="with_arrow{if $sort.id==1} down{else} up{/if}"{/if}>ID</a></div>
				<div class="w1 rh-name"><a href="/seo/?sort[page_uid]={if isset($sort.page_uid)}{1 - $sort.page_uid}{else}0{/if}"{if isset($sort.page_uid)} class="with_arrow{if $sort.page_uid==1} down{else} up{/if}"{/if}>UID</a></div>
				<div class="w2 th-title">Title</div>
				<div class="w2 th-descr">Descr</div>
				<div class="w2 th-keyword">Keyword</div>
				<div class="w2 th-canonical">Canonical</div>
				<div class="w1 th-visibility"></div>
				<div class="w1 th-delete"></div>
			</div>
			<div class="white-body">	
				{include file="Modules/Seo/SuperAdmin/metatagList.tpl"}
			</div>	
			{include file="components/paging.tpl" count=$seoItemsCount pageSize=$pageSize pageNum=$page url=$smarty.server.REQUEST_URI}
		</div>
	</div>	
</div>
{include file="/Modules/Seo/SuperAdmin/addMeta.tpl" assign=add_meta}
{capture assign=editBlock name=editBlock}
	{$add_meta|html}
{/capture}