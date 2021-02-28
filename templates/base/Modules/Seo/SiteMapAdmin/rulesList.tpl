{if !empty($rules)}
		{foreach from=$rules item=rule}
			<div class="wblock m-{$rule.type}" data-id='{$rule.id}'>
				<div class="white-block-row">
					<div class="w05">
						<input type="checkbox" name="ids[]" class="check-item" value="{$rule.id}" />
					</div>
					<div class="url-text w6">
						{$rule.url}
					</div>
					<div class="w3">
						<div class="allow-text">{$rule.type}</div>
					</div>
					<div class="w05"></div>
					<div class="action-button action-edit w1" title="Редактировать">
						<i class="icon-edit"></i>
					</div>
					<div class="action-button action-delete w1 m-border" title="Удалить">
						<i class="icon-delete"></i>
					</div>
				</div>
				{*<div class="white-block-row open-head">
					<div class="w05 drag-drop"></div>
					<div class="w05">
						<input type="checkbox" name="ids[]" value="{$rule.id}" />
					</div>
					<div class="w6">
						<input type="text" value="{$rule.url}" class="url-input input" />
					</div>
					<div class="w3">
						<div class="dropdown allow-menu" data-val="{$rule.type}">
							<div class="dropdown-toggle a-link">{$rule.type}</div>
							<ul class="dropdown-menu">
								<li data-val="allow" class="a-link">Allow</li>
								<li data-val="disallow" class="a-link">Disallow</li>
							</ul>
						</div>
					</div>
					<a href="" class="action-button action-ok w1" title="Сохранить"><i></i></a>
					<a href="" class="action-button action-cancel w1 m-border" title="Отмена"><i></i></a>
				</div>*}
			</div>
		{/foreach}
{else}
	<div class="white-body">
		<div class="wblock white-block-row">
			<div class="w12"> Параметры выборки не созданы</div>
		</div>
	</div>
{/if}