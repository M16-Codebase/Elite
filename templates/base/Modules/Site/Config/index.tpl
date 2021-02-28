<div class="content-top">
	{if !empty($custom_title)}
		<h1>{$custom_title}</h1>
	{else}
		<h1>Изменение параметров сайта</h1>
	{/if}
	<div class="content-options">
		{include file="Admin/components/actions_panel.tpl" 
			buttons = array(
				'add' => ($account->isPermission('site-config', 'create')? '#' : ''),
				'save' => ((!empty($site_params) && $account->isPermission('site-config'))? '#' : '')
			)}
	</div>
</div>
<div class="content-scroll">
	<div class="change-params white-blocks viewport">
		<form id="site_config_form">
			{if !empty($site_params)}
				<div class="wblock white-block-row white-header">
					{if $accountType == 'SuperAdmin'}
						<div class="w3">Ключ</div>
					{/if}
					<div class="w4">Описание</div>
					<div class="w4">Значение</div>
					{if $account->isPermission('site-config', 'del')}<div class="w1"></div>{/if}
				</div>
				<div class="white-body">
					{foreach from=$site_params item=param}
						<div class="wblock white-block-row">
							{if ($param.key != 'broken' || $accountType == 'SuperAdmin') && $param.key != 'sendsay_auth_data'}
								<div class="w3">
									{if $accountType == 'SuperAdmin'}
										{$param.key}
									{/if}
								</div>
								<div class="w4"{if $accountType != 'SuperAdmin'} style="width: 250px;"{/if}>
									{if $accountType == 'SuperAdmin'}
										<input type="text" class="key-descr" name="param[{$param.key}][description]" />
									{else}
										<input type="hidden" class="key-descr" name="param[{$param.key}][description]" />
										{$param.description}
									{/if}
								</div>
								<div class="w4">
									<input type="hidden" name="param[{$param.key}][type]" value="{$param.type}">
									<input type="hidden" name="param[{$param.key}][data_type]" value="{$param.data_type}">
									{if $param.data_type == 'checkbox'}
										<input type="hidden" name="param[{$param.key}][value]" value="0" />
										<input type="checkbox" name="param[{$param.key}][value]" value="1" />
									{elseif $param.data_type == 'textarea'}
										<textarea name="param[{$param.key}][value]" rows="7" cols="40"></textarea>
									{else}
										<input type="text" name="param[{$param.key}][value]" class="value_input" />
									{/if}
								</div>
								<div class="ribbed w1">
								{if  $account->isPermission('site-config', 'del')}
									{if $param.key != 'broken' && $param.key != 'test_mode'}
										<a class="table-btn delete" href="/site-config/del/?key={$param.key}" onclick="if (!confirm('Удалить параметр?')) return false;" title="Удалить"></a>	
									{/if}	
								{/if}
								</div>
							{/if}								
						</div>	
					{/foreach}
				</div>
			{/if}

		</form>
	</div>
</div>

{if $accountType == 'SuperAdmin'}
	{capture assign="editBlock" name="editBlock"}
		<form action="/site-config/create/" class="add-form">
			<input type="hidden" name="type"{if !empty($param_type)} value="{$param_type}"{/if} />
			<div class="content-top">
				<h1>Новый параметр</h1>
				<div class="content-options">
					{?$buttons = array(
						'back' => array('text' => 'Отмена'),
						'save' => array(
							'text' => 'Создать'
						)
					)}
					{include file="Admin/components/actions_panel.tpl"
						assign = addFormButtons
						buttons = $buttons}
					{$addFormButtons|html}
				</div>
			</div>	
			<div class="content-scroll">
				<div class="white-blocks viewport">
					<div class="wblock white-block-row">
						<div class="w3">
							<span>
								Ключ
							</span>
						</div>
						<div class="w9">
							<input type="text" name="key" />
						</div>
					</div>
					<div class="wblock white-block-row">
						<div class="w3">
							<span>
								Значение
							</span>
						</div>
						<div class="w9">
							<input type="text" name="value" class="title_input bold" />
						</div>
					</div>
					<div class="wblock white-block-row">
						<div class="w3">
							<span>
								Описание
							</span>
						</div>
						<div class="w9">
							<input type="text" name="description" class="title_input bold" />
						</div>
					</div>
					<div class="wblock white-block-row">
						<div class="w3">
							<span>
								Тип данных
							</span>
						</div>
						<div class="w9">
							<select name="data_type">
								<option value="text">text</option>
								<option value="checkbox">checkbox</option>
								<option value="textarea">textarea</option>
							</select>
						</div>
					</div>
					<input type="hidden" name="add" value="1" />
				</div>
			</div>	
		</form>
	{/capture}
{/if}
{if !empty($errors)}
	<div class="popup-window popup-errors">
		<h2 class="error-title">Неверно заполнены поля:</h2>
		<ul class="error-fields">
			{foreach from=$errors item=err key=key}
				<li>{$key}: {$err}</li>
			{/foreach}
		</ul>
		<div class="buttons">
			<div class="button close-popup">Закрыть</div>
		</div>
	</div>
{/if}