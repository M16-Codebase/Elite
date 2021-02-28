{if !empty($content)}
	{?$color = !empty($color)? $color : 'blue'}
	<div class="tip a-inline-block">
		<i class="icon i-ask-{$color}"></i>
		<div class="content">
			<i class="icon i-ask-yellow"></i>
			{if !empty($title)}
				<div class="title">{$title}</div>
			{/if}
			<p>{$content|html}</p>		
		</div>
	</div>
{/if}