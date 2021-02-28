<div class="comments-cont-title">
    {?$comment_count = count($comments)}
	{if $request_segment.id==1}
		{$comment_count|plural_form:'комментарий':'комментария':'комментариев'}
	{else}
		{$comment_count|plural_form:'comment':'comments':'comments'}
	{/if}
</div>
{if $post.status != 'close'}
	<a href="#.popup-add-comment" class="comment-add" data-toggle="popup" data-action="open"><i></i>
		Добавить комментарий
	</a>
{/if}
<ul class="comments-list">
	{foreach from=$comments item=comment}
		<li>
			{if !empty($delete_btn)}
				<a class="remove-comment" href="#" data-id="{$comment.id}" title="Удалить"><i></i></a>
			{/if}
			<div class="comment-top">
				<i></i>
				<span class="author">{$comment.author}</span>&nbsp;•&nbsp;
				{if !empty($post.pub_date)}
					{?$post_date = $post.pub_date|strtotime}
					<span class="news-date">
                        {$comment.dt|date_format_lang:'%e %B %Y, %H:%M', 'ru'}
					</span>
				{/if}
			</div>
			<div class="comment-text a-pre">{$comment.text}</div>
		</li>
	{/foreach}
</ul>