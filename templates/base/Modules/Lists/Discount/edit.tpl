{?$admin_page = 1}
{?$includeJS.editor = "Modules/Posts/Pages/edit.js"}
{?$bc_location = empty($bc_location)? array('url'=> '/' . $moduleUrl . '/', 'title' => 'Акции') : $bc_location}
<h1>Редактирование акции</h1>
{include file="Admin/components/actions_panel.tpl"
	buttons = array(
		'back' => ('/' . $moduleUrl . '/listEdit/'),
		'save' => 1
	)
}
<form class="saveDiscount" action="/{$moduleUrl}/save/" id="edit_post_form">
    <input type="hidden" name="id" />
    <table class="ribbed">
        <tr class="td-row">
            <td>Название</td>
            <td><input type="text" name="title" /></td>
        </tr>
        <tr class="td-row">
            <td>Регионы</td>
            <td>
                <select name="segment_id">
                    <option value="0">Все</option>
                    {foreach from=$segments item=region}
                        <option value="{$region.id}">{$region.title}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr class="td-row">
            <td>Статус</td>
            <td>
                <select name="status">
                    {foreach from=$statuses item=rus key=k}
                        <option value="{$k}">{$rus}</option>
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr class="td-row">
            <td>Дата начала</td>
            <td><input type="text" name="date_start" class="datepicker" /></td>
        </tr>
        <tr class="td-row">
            <td>Дата окончания</td>
            <td><input type="text" name="date_end" class="datepicker" /></td>
        </tr>
        <tr class="td-row">
            <td>Показывать на главной</td>
            <td>
                <input type="hidden" name="on_page" value="0" />
                <input type="checkbox" name="on_page" value="1" />
            </td>
        </tr>
        <tr class="td-row">
            <td>Номера (ID) карточек товаров</td>
            <td>
				<ul class="num-area">
					{if !empty($item_titles)}
						{foreach from=$item_titles key=i_id item=i_title}
							<li tagvalue="{$i_id}" class="tagit-choice" title="{$i_title}"><div class="tagit-label">{$i_id}</div><a class="tagit-close"></a></li>
						{/foreach}
					{/if}
				</ul>
				<input type="hidden" name="ids" />
			</td>
        </tr>
        <tr>
			<td colspan="2">
				<textarea name="text" class="redactor"></textarea>
			</td>
		</tr>
</table>
</form>
{include file="Modules/Posts/Admin/post_uploader.tpl" is_gallery=0 select_preview=1}