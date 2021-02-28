{?$type_office=false}
{?$type_manufacture=false}
{?$type_stead=false}
{?$type_business=false}
{?$type_investment=false}
{?$type_apartments=false}
{?$type_rent=false}
{?$type_sale=false}
{if $current_type.id=='63' || $current_type.id=='64'}
	{?$type_office=true}
{elseif $current_type.id=='65' || $current_type.id=='67'}
	{?$type_manufacture=true}
{elseif $current_type.id=='59'}
	{?$type_stead=true}
{elseif $current_type.id=='66' || $current_type.id=='68'}
	{?$type_business=true}
{elseif $current_type.id=='61'}
	{?$type_investment=true}
{elseif $current_type.id=='62'}
	{?$type_apartments=true}
{/if}
{if $current_type.id=='63' || $current_type.id=='65' || $current_type.id=='66'}
	{?$type_rent=true}
{elseif $current_type.id=='61' || $current_type.id=='64' || $current_type.id=='67' || $current_type.id=='68'}
	{?$type_sale=true}
{/if}