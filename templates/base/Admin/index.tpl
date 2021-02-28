<!DOCTYPE html>
<html lang="ru">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
		<meta name="viewport" content="width=device-width, initial-scale=0.8, user-scalable=no" />
		<title>{$pageTitle}</title>
		<link rel="shortcut icon" href="/{$template_dir}/Admin/img/favicon.ico" />
		{include file="Admin/js_main_includes.tpl"}
        {include file="Admin/js_includes.tpl"}
		<link href="/{$template_dir}/Admin/style.css?{$temp_param_url}" rel="stylesheet" type="text/css" />
		{if !empty($includeCss)}
			{foreach from=$includeCss item="css"}
				{if $css != "style.css"}
					<link href="/{$template_dir}/{$css}?{$temp_param_url}" rel="stylesheet" type="text/css" />
				{/if}
			{/foreach}
		{/if}
		<script src="/{$template_dir}/Admin/script.js?{$temp_param_url}" type="text/javascript"></script>
        {if !empty($includeJS)}
			{foreach from=$includeJS item="jsfile"}
				{if $jsfile != "script.js"}
					<script src="/{$template_dir}/{$jsfile|html}?{$temp_param_url}" type="text/javascript"></script>
				{/if}
			{/foreach}
		{/if}

		{if !empty($customCss)}
            {foreach from=$customCss item="file"}
				<link href="/{$template_dir}/Admin/{$file|html}?{$temp_param_url}" rel="stylesheet" type="text/css" />
            {/foreach}
        {/if}
	</head>
	<body{if $accountType == 'SuperAdmin'} data-admin="1"{/if}>
		{include file="Admin/mainLayout.tpl"}
		{if !empty($debug_mode)}
			{debug charset="utf-8"}
		{/if}
		{if $accountType == 'SuperAdmin'}
			<div class="generate-time"><!--GenerateTime--></div>
		{/if}
		{if !empty($customJs)}
            {foreach from=$customJs item="file"}
				<script src="/{$template_dir}/Admin/{$file|html}?{$temp_param_url}" type="text/javascript"></script>
            {/foreach}
		{/if}
	</body>
</html>
