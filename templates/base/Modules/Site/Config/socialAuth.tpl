{?$pageTitle = 'Параметры аутентификации через социальные сети — ' . (!empty($confTitle) ? $confTitle : '')}
<div class="content-top">
	<h1>Параметры аутентификации через социальные сети</h1>
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
			'back' => '/site/',
			'save' => 1
			)}
	</div>
</div>

<div class="content-scroll">
	<form class="social-auth-form viewport">

		<div class="white-blocks socials">
			{if !empty($networks_list)}
				{foreach from=$networks_list key=network item=network_data}
					<div class="wblock">
						<div class="white-block-row">
							<div class="w3 a-inline-cont">
								<i class="i-soc-icon i-{$network}"></i>
								<strong class="soc-name">{$network_data.name}</strong>
							</div>
							<div class="w9">
								<input type="hidden" name="{$network}[enable]" value="{$network_data.enable}" />
								<div class="dropdown soc-visible{if empty($network_data.enable)} m-hide{/if}">
									<div class="dropdown-toggle a-inline-cont">
										<i class="i-visible"></i>
										<span class="show">Использовать для аутентификации</span>
										<span class="hide">Не использовать для аутентификации</span>
									</div>
									<ul class="dropdown-menu a-hidden">
										<li data-val="1" class="a-link">Использовать</li>
										<li data-val="0" class="a-link">Не использовать</li>
									</ul>
								</div>
							</div>
						</div>
						<div class="white-inner-cont">
							{foreach from=$network_data.fields key=key item=title}
								<div class="white-block-row">
									<div class="w3">
										{$title}
									</div>
									<div class="w9">
										<input type="text" name="{$network}[{$key}]" />
									</div>
								</div>
							{/foreach}
						</div>
					</div>
				{/foreach}
			{else}
				<div class="wblock white-block-row empty-result">
					<div class="w12">Нет подключенных сетей</div>
				</div>
			{/if}
		</div>

	</form>
</div>