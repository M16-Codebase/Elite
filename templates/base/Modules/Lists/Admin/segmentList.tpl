{if !empty($segments)}
	{?$current_segment = $account->getUser()->getSegment()}
	{if !empty($current_segment)}
		<tr data-id="{$current_segment.id}" class="current-region">
			<td class="descr small">{$current_segment.id}</td>
            <td>
				<a class="data data-title region-marker" href="/lists/store/?segment_id={$current_segment.id}">
					{$current_segment.title}
				</a>
			</td>
			<td class="descr">
                {$current_segment.key}	
            </td>
			<td class="small">
				<div class="dropdown">
					<div class="table-btn dropdown-toggle more"></div>
					<ul class="dropdown-menu dd-list a-hidden">
						<li class="edit-region"><a href="#">Редактировать</a></li>
						<li class="delete-region"><a href="#">Удалить</a></li>
					</ul>
				</div>
			</td>
        </tr>
	{/if}
    {foreach from=$segments item=r}
		{if $r.id != $current_segment.id}
			<tr data-id="{$r.id}">
				<td class="descr small">{$r.id}</td>
				<td>
					<a class="data data-title region-marker" href="/lists/store/?segment_id={$r.id}">
						{$r.title}
					</a>
				</td>
				<td class="descr time-cml">
					{$r.key}
				</td>
				<td class="small">
					<div class="dropdown">
						<div class="table-btn dropdown-toggle more"></div>
						<ul class="dropdown-menu dd-list a-hidden">
							<li class="edit-region"><a href="#">Редактировать</a></li>
							<li class="delete-region"><a href="#">Удалить</a></li>
						</ul>
					</div>
				</td>
			</tr>
		{/if}
    {/foreach}
{/if}