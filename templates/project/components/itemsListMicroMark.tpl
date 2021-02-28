
{? $priceIndex = 1}
{? $ic = 0}
{if $request_segment.key == 'ru'}
    {? $priceCurrency = 'RUB'}
    {? $priceIndex = 1000000}
{/if}
{if $request_segment.key =='en'}
    {? $priceCurrency = 'USD'}
    {? $priceIndex = 1000}
{/if}
{if strpos($page_url,'arenda')}
	{? $priceIndex = 1000}
{/if}
{if $count<=20}
	{? $frc = $count}
{/if}
{if $count>20}
	{? $frc = 20}
{/if} 

{literal}
<script type="application/ld+json">
{
  "@context": "http://schema.org",
  "@type": "Product",
  "name": "Аренда > элитная недвижимость",
  "offers": {
    "@type": "AggregateOffer",{/literal}
    {if !empty($priceVals)}
{literal}"highPrice": "{/literal}{$priceVals.max * $priceIndex}{literal}",{/literal}
{literal}"lowPrice": "{/literal}{$priceVals.min * $priceIndex}{literal}",{/literal}
{literal}"priceCurrency": "{/literal}{$priceCurrency}{literal}",{/literal}
    {/if}
{literal}
    "offerCount": "20",
    "offers": [
	{/literal}
	{foreach from=$items item=item name=mm}
    {? $ic += 1;}
    {?$url = !empty($item->getUrl()) ? $item->getUrl() : null}
{literal}
		{
			"@type": "Offer",
			"url": "{/literal}{$url}{literal}",{/literal}
    {if !empty($item.price)}
{literal}"price": "{/literal}{$item.properties.price.value * $priceIndex}{literal}",{/literal}
{literal}"priceCurrency": "{/literal}{$priceCurrency}{literal}"{/literal}
    {/if}
     	}{if $ic < $frc},{/if}
	{/foreach}
	{literal}
    ]
  }
}
</script>

{/literal}