{if $property.segment != 1 && $property.data_type != 'enum'}
	<td class="td-val" colspan="2">
		{if $property.set == 1 || is_array($l.additional_data.complete_value)}
			<ul>
				{foreach from=$l.additional_data.complete_value item=val}
					<li>{$val}</li>
				{/foreach}
			</ul>
		{else}
			{$l.additional_data.complete_value}
		{/if}
	</td>
{else}
	<td class="td-val">
		{if $l.segment_id == $rus_segment.id || $l.segment_id == 0}
			{if $property.set == 1 || is_array($l.additional_data.complete_value)}
				<ul>
					{foreach from=$l.additional_data.complete_value item=val}
						<li>{$val}</li>
					{/foreach}
				</ul>
			{else}
				{$l.additional_data.complete_value}
			{/if}
		{else}
			{if $property.data_type == 'enum'}
				{if $property.set == 1}
					<ul>
						{foreach from=$l.additional_data.value item=enum_id}
							<li>{$property['segment_enum'][$enum_id][$rus_segment.id]}</li>
						{/foreach}
					</ul>
				{else}
					{$property['segment_enum'][$l.additional_data.value][$rus_segment.id]}
				{/if}
			{else}
				&mdash;
			{/if}
		{/if}
	</td>
	<td class="en-col td-val">
		{if $l.segment_id != $rus_segment.id}
			{if $property.data_type == 'enum'}
				{if $property.set == 1}
					<ul>
					{foreach from=$l.additional_data.value item=enum_id}
						<li>{$property['segment_enum'][$enum_id][$english_segment.id]}</li>
					{/foreach}
					</ul>
				{else}
					{$property['segment_enum'][$l.additional_data.value][$english_segment.id]}
				{/if}
			{else}
				{if $property.set == 1 || is_array($l.additional_data.complete_value)}
					<ul>
						{foreach from=$l.additional_data.complete_value item=val}
							<li>{$val}</li>
						{/foreach}
					</ul>
				{else}
					{$l.additional_data.complete_value}
				{/if}				
			{/if}
		{else}
			&mdash;
		{/if}
	</td>
{/if}