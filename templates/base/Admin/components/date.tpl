{strip}
	{if !empty($date)}
		{assign var="tmp_date" value=$date|getdate}
		{assign var="tmp_now" value=$smarty.now|getdate}
		{if $tmp_date.year == $tmp_now.year && $tmp_date.mon == $tmp_now.mon}
			{if $tmp_date.mday == $tmp_now.mday}
				Cегодня, {$date|date_format:"%H:%M"}
			{elseif ($tmp_date.mday-$tmp_now.mday)==-1}
				Вчера, {$date|date_format:"%H:%M"}
			{else}
				{$date|date_format:"%d.%m.%y, %H:%M"}
			{/if}
		{else}
			{$date|date_format:"%d.%m.%y, %H:%M"}
		{/if}
	{/if}
{/strip}