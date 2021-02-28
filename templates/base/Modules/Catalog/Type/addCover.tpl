{if !empty($type_cover)}
	<div class="w11">
		<a href="{$type_cover->getUrl()}" class="fancybox row-image m-cover">
			<img src="{$type_cover->getUrl(200, 100, true)}">
		</a>
	</div>
	<a href="/users-edit/deletePhoto/" class="action-button w1 delete-cover">
		<i class="icon-prop-delete"></i>
	</a>
{/if}
