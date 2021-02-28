<form class="edit-banner" enctype="multipart/form-data" action="/site-banner/edit/">
	<input type="hidden" name="id" />
	<input type="hidden" name="segment_id" />
	{if !empty($smarty.post.caturl)}
		<input type="hidden" name="cat" value="1" />
	{/if}
	{if !empty($smarty.post.caturl)}
		<input type="hidden" name="url[]" value="{$smarty.post.caturl}" />
	{elseif	!empty($url)}
		{if is_array($url)}
			{foreach from=$url item=u}
				<input type="hidden" name="url[]" value="{$u}" />
			{/foreach}
		{else}
			<input type="hidden" name="url[]" value="{$url}" />
		{/if}
	{else}
		<input type="hidden" name="url[]"{if $smarty.post.url} value="{$smarty.post.url}"{/if} />
	{/if}
	<div class="content-top">
		<h1>{if !empty($smarty.post.id)}Редактирование {else}Добавление {/if}баннера</h1>
		<div class="content-options">
			{?$buttons = array(
				'back' => array('text' => 'Отмена'),
				'save' => array(
					'text' => 'Сохранить',
					'url' => '#',
					'class' => 'submit'
				)
			)}
			{include file="Admin/components/actions_panel.tpl"
				assign = createBannerHandlers
				buttons = $buttons}	
			{$createBannerHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="prop-item w12 img-preview">
					<a href="" class="fancybox row-image m-banner origin">
						<div class="preloader"><div></div></div>
						<img src="" width="100">
					</a>
					<div class="prop-title h4">Изображение</div>
					<div class="row{if empty($banner.image)} a-hidden{/if}">
						<div class="w12 img-preview-body">
							{if !empty($banner.image)}
							<a href="{$banner.image->getUrl()}" class="fancybox row-image m-banner">
								<img src="{$banner.image->getUrl(100)}">
							</a>
							{/if}
						</div>
					</div>
					<div class="add-row row m-fullwidth">
						<label for="change-image" class="change-img">
						<div class="add-object add-btn w3">
							<i class="icon-{if !empty($banner.image)}replace{else}add{/if}"></i><span class="small-descr">{if !empty($banner.image)}Заменить{else}Добавить{/if} изображение</span>
						</div>
						</label>
						<input type="file" name="image" class="hidden-input" id="change-image" accept="image/*"/>
					</div>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3"><strong>Ссылка на баннере</strong></div>
				<div class="w9">
					<input type="text" name="destination" />
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3"><strong>Статус</strong></div>
				<div class="w2 banner-show-dropdown dropdown m-status">
					<input type="hidden" name='active' >
					<div class="dropdown-toggle action-button m-status-icon" title="{if !empty($banner.url)}{if $banner.active}Показан{else}Скрыт{/if}{else}Скрыт{/if}">
						<i class="icon-{if !empty($banner.url)}{if $banner.active}show{else}hide{/if}{else}hide{/if}"></i>
						<span>{if !empty($banner.url)}{if $banner.active}Показан{else}Скрыт{/if}{else}Скрыт{/if}</span>
					</div>
					<ul class='dropdown-menu a-hidden'>
						<li data-type='1'><span>Показан</span></li>
						<li data-type='0'><span>Скрыт</span></li>
					</ul>
				</div>
				<div class="w7"></div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3"><strong>Период отображения</strong></div>
				<div class="w9">
					<input id='from' type="text" name="date_start" class="short" value="{if !empty($banner.date_start)}{$banner.date_start|date_format:'%d.%m.%Y'}{/if}"/>
					<span>—</span>
					<input id='to' type="text" name="date_end" class="short" value="{if !empty($banner.date_end)}{$banner.date_end|date_format:'%d.%m.%Y'}{/if}"/>
				</div>
			</div>
			<div class="wblock white-block-row">
				<div class="w3"><strong>Задержка</strong></div>
				<div class="w9">
					<input type="text" name="seconds" class="short" /> сек.
				</div>
			</div>
		</div>
	</div>
</form>