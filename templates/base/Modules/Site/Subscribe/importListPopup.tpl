 <form class="import-list-form" action="/subscribe/importSubscribersList/">
	<input type="hidden" name="group_id" value="">
	<div class="content-top">
		<h1>Экспорт подписчиков в другой список</h1>
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
				assign = createTeaserHandlers
				buttons = $buttons}	
			{$createTeaserHandlers|html}
		</div>
	</div>
	<div class="content-scroll">
		<div class="white-blocks viewport">
			<div class="wblock white-block-row">
				<div class="w3"><strong>Список для экспорта</strong></div>
				<div class="w9">
					 <select name="target_group">
						<option class="default-value" value="" selected="selected">Выберите...</option>
						<option value="add-new-list" >Новый список</option>
						{foreach from=$groups item=group}
							{if $group.type == 'list' && $group.id != $target_group && $group.id != 'main'}
								<option value="{$group.id}">{$group.name}</option>
							{/if}
						{/foreach}
					</select>
				</div>
			</div>
			<div class="wblock white-block-row a-hidden">
				<div class="w3"><strong>Название списка</strong></div>
				<div class="w9">
					<input type="text" name="target_group_name">
				</div>
			</div>
		</div>
	</div>
	<div class="selected-items"></div>
</form>

{*<form>
    <table class="ribbed">
        <tr>
            <td class="td-title">
                <label for="email">Список для экспорта</label>
            </td>
            <td>
                <select name="target_group">
                    <option class="default-value" value="" onclick="$('#new_group_name_row').hide();" selected="selected">Выберите...</option>
                    <option value="" onclick="alert('test');">Новый список</option>
                    {foreach from=$groups item=group}
                        {if $group.type == 'list' && $group.id != $target_group && $group.id != 'main'}
                            <option value="{$group.id}" onclick="$('#new_group_name_row').hide();">{$group.name}</option>
                        {/if}
                    {/foreach}
                </select>
            </td>
        </tr>
        <tr id="new_group_name_row" class="a-hidden">
            <td class="td-title">
                <label>Название списка</label>
            </td>
            <td>
                <input type="text" name="target_group_name">
            </td>
        </tr>
    </table>
    <div class="buttons">
        <button class="submit a-button-blue">Экспорт</button>
    </div>
</form>*}