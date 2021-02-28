{if empty($pt_included)}
    {?$paytype_text = array(
        'shop'=>'Оплата наличными / картой в магазине', 
        'receipt'=>'Оплата наличными при получении', 
        'nal'=>'Наличными', 
        'beznal'=>'Безналичный расчет'
    )}
    {?$pt_included = true}
{/if}