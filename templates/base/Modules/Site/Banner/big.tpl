{?$includeJS.edit_banners = 'Modules/Site/Banner/index.js'}
<h1>Баннеры на главной</h1>
{include file="Admin/components/actions_panel.tpl"
	add_url = '#'
}
{if !empty($banners)}
    <table class="ribbed">
        <tr>
            <th class="th-short">Изображение</th>
            <th>Url</th>
            <th class="th-short">Активировать</th>
			<th class="th-short">Ред.</th>
            <th class="th-delete"></th>
        </tr>
    {foreach from=$banners item=banner}
        {if !empty($banner.image)}
        <tr data-image_id="{$banner.image.id}">
            <td><img src="{$banner.image->getUrl(80, 40)}" /></td>
            <td>{$banner.image.info.url}</td>
            <td class="td-center"><input type="checkbox" value="1" name="active[{$banner.image.id}]" class="activate"{if $banner.active == 1} checked{/if} /></td>
            <td><a href="#" class="editBanner editBigBanner">Ред</a></td>
            <td class="td-center"><div class="delBanner delete"></div></td>
        </tr>
        {/if}
    {/foreach}
    </table>
{/if}

<div class="popup-window popup-window-createBanner">
	<form enctype="multipart/form-data" action="/site-banner/create/">
		<input type="hidden" name="big" value="1" />
		<table class="ribbed">
			<tr>
				<td class="td-title">Выберите изображение</td>
				<td>
					<input type="file" name="image" />
				</td>
			</tr>
		</table>
		<div class="buttons">
			<div class="submit a-button-blue">Загрузить</div>
		</div>
    </form>	
</div>

<div class="popup-window popup-window-editBanner">
    <form enctype="multipart/form-data" action="/site-banner/edit/">
        <table class="ribbed">
            {include file="Modules/Site/Banner/big_edit.tpl"}
        </table>
        <div class="buttons">
            <div class="button submit">Сохранить</div>
        </div>
    </form>
</div>

{*<div class="popup-window popup-window-createBanner">
    <form enctype="multipart/form-data" action="/site-banner/create/">
        <div><input type="file" name="image" /><input type="hidden" name="big" value="1" /></div>
    </form>
</div>

<div class="popup-window popup-window-editBanner">
    <form enctype="multipart/form-data" action="/site-banner/edit/">
        <table class="ribbed">
            
        </table>
        <div class="buttons">
            <div class="button submit-edit">Сохранить</div>
        </div>
    </form>
</div>*}