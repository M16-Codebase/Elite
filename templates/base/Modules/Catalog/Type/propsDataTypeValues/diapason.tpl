<div class="white-block-row">
	<div class="w3"></div>
	{foreach from=$segments item=$s}
		<div class="w4">{$s.title}</div>
	{/foreach}
	<div class="w1"></div>
</div>
<div class="white-block-row">
	<div class="w3">
		<span class="text-icon">И мин и макс</span>
		{include file="Admin/components/tip.tpl" content="Отображение значения, когда установлены и минимальное и максимальное значения"}
	</div>
	{if $constants.segment_mode == 'lang'}
		{foreach from=$segments item=$s}
			<div class="w4">
				<input type="text" name="values[{$s['id']}][min_max]"{if is_array($property)} data-default="{literal}{min}—{max}{/literal}"{/if} />
			</div>
		{/foreach}
		<div class="w1"></div>
	{else}
		<div class="w9">
			<input type="text" name="values[min_max]"{if is_array($property)} data-default="{literal}{min}—{max}{/literal}"{/if} />
		</div>
	{/if}
</div>
<div class="white-block-row">
	<div class="w3">
		<span class="text-icon">Только мин</span>
		{include file="Admin/components/tip.tpl" content="Отображение значения, когда установлено только минимальное значение"}
	</div>
	{if $constants.segment_mode == 'lang'}
		{foreach from=$segments item=$s}
			<div class="w4">
				<input type="text" name="values[{$s['id']}][min]"{if is_array($property)} data-default="от {literal}{min}{/literal}"{/if} />
			</div>
		{/foreach}
		<div class="w1"></div>
	{else}
		<div class="w9">
			<input type="text" name="values[min]"{if is_array($property)} data-default="от {literal}{min}{/literal}"{/if} />
		</div>
	{/if}
</div>
<div class="white-block-row">
	<div class="w3">
		<span class="text-icon">Только макс</span>
		{include file="Admin/components/tip.tpl" content="Отображение значения, когда установлено только максимальное значение"}
	</div>
	{if $constants.segment_mode == 'lang'}
		{foreach from=$segments item=$s}
			<div class="w4">
				<input type="text" name="values[{$s['id']}][max]"{if is_array($property)} data-default="до {literal}{max}{/literal}"{/if} />
			</div>
		{/foreach}
		<div class="w1"></div>
	{else}
		<div class="w9">
			<input type="text" name="values[max]"{if is_array($property)} data-default="до {literal}{max}{/literal}"{/if} />
		</div>
	{/if}
</div>