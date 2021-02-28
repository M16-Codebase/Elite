<h1>{$catalog_type.title}</h1>

<ul>
    <li>
        {$catalog_type.title}
        Правило категории{if !empty($category_rules_by_id[$catalog_type.id])}+{else}-{/if}
        Правило дочерних категорий{if !empty($category_child_rules_by_id[$catalog_type.id])}+{else}-{/if}
        Правило айтемов{if !empty($item_common_rules_by_id[$catalog_type.id])}+{else}-{/if}
        {if !$catalog_type.allow_children}
            Переопределено у айтемов — {if !empty($item_rules_by_id[$catalog_type.id])}count($item_rules_by_id[catalog_type.id]){else}0{/if}
        {/if}
    </li>
    {foreach from=$category_children item=child_list}
        {foreach from=$child_list item=t}
            <li>
                {$t.title}
                Правило категории{if !empty($category_rules_by_id[$t.id])}+{else}-{/if}
                Правило дочерних категорий{if !empty($category_child_rules_by_id[$t.id])}+{else}-{/if}
                Правило айтемов{if !empty($item_common_rules_by_id[$t.id])}+{else}-{/if}
                {if !$t.allow_children}
                    Переопределено у айтемов — {if !empty($item_rules_by_id[$t.id])}{count($item_rules_by_id[$t.id])}{else}0{/if}
                {/if}
            </li>
        {/foreach}
    {/foreach}
</ul>