{if empty($comments_type)}{?$comments_type = 'property'}{/if}
<div class="prop-comments justify{if !empty($comments_type)} m-{$comments_type}{/if}{if $comments_type == 'value'} m-hidden{/if}" data-comments-type="{$comments_type}"{if !empty($comments_id)} data-comments-id="{$comments_id}"{/if}>
	<div class="comment-col">
		<div class="comment-title m-lock"><i></i></div>
		<div class="text-cont">
			<textarea name="private" class="private-text" data-segment="0">
				{if !empty($comments_list)}
					{if $comments_type == 'value'}
						{if !empty($comments_list['private'][$comments_id])}{$comments_list['private'][$comments_id]}{/if}
					{else}
						{if !empty($comments_list[0])}{$comments_list[0]}{/if}
					{/if}
				{/if}
			</textarea>
		</div>
	</div>
	<div class="comment-col">
		<div class="comment-title"><i></i> RU</div>
		<div class="text-cont">
			<textarea name="public" class="public-text s-ru" data-segment="{$lang.ru}">
				{if !empty($comments_list)}
					{if $comments_type == 'value'}
						{if !empty($comments_list['public'][$comments_id][$lang.ru])}{$comments_list['public'][$comments_id][$lang.ru]}{/if}
					{else}
						{if !empty($comments_list[$lang.ru])}{$comments_list[$lang.ru]}{/if}
					{/if}
				{/if}
			</textarea>
		</div>
	</div>
	<div class="comment-col">
		<div class="comment-title"><i></i> EN</div>
		<div class="text-cont">
			<textarea name="public" class="public-text s-en" data-segment="{$lang.en}">
				{if !empty($comments_list)}
					{if $comments_type == 'value'}
						{if !empty($comments_list['public'][$comments_id][$lang.en])}{$comments_list['public'][$comments_id][$lang.en]}{/if}
					{else}
						{if !empty($comments_list[$lang.en])}{$comments_list[$lang.en]}{/if}
					{/if}
				{/if}
			</textarea>
		</div>
	</div>
	<div class="close-comments"></div>
</div>