{?$pageTitle = 'Баннеры — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Баннеры</h1>
	<div class="content-options" id="banners">
		{include file="Admin/components/actions_panel.tpl" 
			multiple = true
			buttons = array(
				'back' => '/site/',
				'add' => array(
					'class' => 'show-create'
				)
			)
		)}	
	</div>
</div>
{if empty($catalog_banner)}
<div class="select-variant choose-url tab-top">
	<select name="url">
		{if $constants.segment_mode == 'lang'}
			{foreach from=$pageUrls key=url_item item=url_title}
				{foreach from=$segments item=s}
					<option value="{$url_item}" data-segment_id="{$s.id}" {if !empty($smarty.get.url) && $smarty.get.url == $url_item && (!empty($smarty.get.segment_id) && $smarty.get.segment_id == $s['id'] || empty($smarty.get.segment_id) && $s['id'] == 1) || empty($smarty.get.url) && $url_item == '/' && $s['id'] == 1}selected="selected" {/if}>{$url_title} ({$s.title})</option>
				{/foreach}
			{/foreach}
		{else}
			{foreach from=$pageUrls key=url_item item=url_title}
				<option value="{$url_item}" {if !empty($smarty.get.url) && $smarty.get.url == $url_item || empty($smarty.get.url) && $url_item == '/'}selected="selected" {/if}>{$url_title}</option>
			{/foreach}
		{/if}
	</select>
</div>
{/if}
<div class="content-scroll">
	<div class="viewport">
		{include file="Modules/Site/Banner/banners.tpl"}
	</div>
</div>