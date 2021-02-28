{if !empty($entity)}
	{if !empty($current_entity['property_comments'][$item_prop.id][0])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
		{$current_entity['property_comments'][$item_prop.id][0]}</div>
	{/if}
	{if !empty($current_entity['property_comments'][$item_prop.id][$request_segment.id])}
		<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
		{$current_entity['property_comments'][$item_prop.id][$request_segment.id]}</div>
	{/if}
{else}
	{if $moduleName ==	'Modules\Catalog\Viewer'}
		{if !empty($catalog_item['property_comments'][$item_prop.id][0])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
			{$catalog_item['property_comments'][$item_prop.id][0]}</div>
		{/if}
	{/if}
	{if !empty($catalog_item['property_comments'][$item_prop.id][$request_segment.id])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
		{$catalog_item['property_comments'][$item_prop.id][$request_segment.id]}</div>
	{/if}
{/if}