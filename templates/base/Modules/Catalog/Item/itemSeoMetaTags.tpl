<form class="content-scroll edit-meta" action="/catalog-item/itemSeoMetaTags/?item_id={$catalog_item.id}{if !empty($metatag_segment_id)}&segment_id={$metatag_segment_id}{/if}">
	<div class="aside-panel">
		{include file="Admin/components/actions_panel.tpl"
			buttons = array(
				'save' => 1
			)}
	</div>
	<div class="white-blocks viewport">
		<div class="wblock white-block-row">
			<div class="w3">
				Title:
			</div>
			<div class="w9">
				<input type="text" name="meta_tag_data[{if !empty($metatag_segment_id)}{$metatag_segment_id}{else}0{/if}][title]" />
			</div>
		</div>
		<div class="wblock white-block-row">
			<div class="w3">
				Description:
			</div>
			<div class="w9">
				<input type="text" name="meta_tag_data[{if !empty($metatag_segment_id)}{$metatag_segment_id}{else}0{/if}][description]" />
			</div>
		</div>
		<div class="wblock white-block-row">
			<div class="w3">
				Keywords:
			</div>
			<div class="w9">
				<input type="text" name="meta_tag_data[{if !empty($metatag_segment_id)}{$metatag_segment_id}{else}0{/if}][keywords]" />
			</div>
		</div>
		<div class="wblock white-block-row">
			<div class="w3">
				Canonical:
			</div>
			<div class="w9">
				<input type="text" name="meta_tag_data[{if !empty($metatag_segment_id)}{$metatag_segment_id}{else}0{/if}][canonical]" />
			</div>
		</div>
		<div class="wblock post-block">
			<textarea class="redactor" name="meta_tag_data[{if !empty($metatag_segment_id)}{$metatag_segment_id}{else}0{/if}][text]"></textarea>
		</div>
	</div>
</form>