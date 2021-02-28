{if !empty($value_min) && !empty($value_max)}
	{if $value_min!=$value_max}
		{if !empty($value_range)}{$value_range}{else}{$value_min} — {$value_max}{/if}
	{else}
		{$value_min}
	{/if}
{elseif !empty($value_min) && empty($value_max)}
	{$value_min}
{elseif empty($value_min) && !empty($value_max)}
	{$value_max}
{/if}