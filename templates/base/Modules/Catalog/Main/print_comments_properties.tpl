{if !empty($entity)}	
	{if !empty($current_entity['property_comments'][$item_prop.id][$request_segment.id])}
		<div class="comment">{if !empty($print)}<img class="icon i-comment" src="{$tmp_path}/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
		{$current_entity['property_comments'][$item_prop.id][$request_segment.id]}</div>
	{/if}
{else}
	{if !empty($catalog_item['property_comments'][$item_prop.id][$request_segment.id])}<div class="comment">{if !empty($print)}<img class="icon i-comment" src="{$tmp_path}/img/print/comment.png" />{else}<i class="i-comment"></i>{/if}
		{$catalog_item['property_comments'][$item_prop.id][$request_segment.id]}</div>
	{/if}
{/if}