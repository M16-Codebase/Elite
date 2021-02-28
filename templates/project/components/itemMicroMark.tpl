{if ($item.typerk=='Квартира')}
    {? $type_ = $lang->get('Элитная квартира в Санкт-Петербурге', 'Luxury apartment in St.Petersburg') . ' ' . $item.title}
{else}
    {? $type_ = $lang->get('Элитный коттедж в Санкт-Петербурге', 'Luxury cottage in St.Petersburg') . ' ' . $item.title}
{/if}



{? $priceIndex = 1}


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


{literal}
<script type="application/ld+json">
{
  "@context": "http://schema.org",
     "@graph": [
 {
      "@type": "Place",
      "address": {
		"@type": "PostalAddress",{/literal}
		{if !empty($item.address)}
{literal}"streetAddress": "{/literal}{$item.address}{literal}",{/literal}
    {/if}
		{if !empty($item.district.title)}
{literal}"addressLocality": "{/literal}{$item.district.title}{literal}"{/literal}
    {/if}
		{literal}
	  },
      "name": "{/literal}{$type_}{literal}"
    },

    {
      "@type": "Offer",{/literal}
      {if !empty($item.properties.price)}
{literal}"price": "{/literal}{$item.properties.price.value * $priceIndex}{literal}",
		"priceCurrency": "{/literal}{$priceCurrency}{literal}",{/literal}
    {/if}
	{literal}
      "url": "{/literal}{$page_url}{literal}"
    }
]
}
</script>
{/literal}