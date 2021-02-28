<div class="field sortable m-object object-user" data-notsend="1" data-items=".object-prop">

    {if empty($object) && empty($objects)}

        <div class="row">
            <div class="w11">
                <input type="text" class="input-values" name="{$property.key}" placeholder="ID пользователя" />
            </div>
            <div class="apply-object action-button w1" title="Добавить пользователя" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                <i class="icon-prop-apply"></i>
            </div>
        </div>

    {else}

        {if !empty($objects)}
            {?$prop_val_id = !empty($entity['properties'][$property.key]['val_id']) ? $entity['properties'][$property.key]['val_id'] : null}
            {if !empty($prop_val_id)}
                {?$prop_obj = $entity['properties'][$property.key]['complete_value']}
                {?$prop_val = $entity['properties'][$property.key]['value']}
                {foreach from=$prop_val_id item=val_id key=val_i}
                    {if !empty($object) && empty($objects[$prop_val[$val_i]])}
                        {?$obj_id = $prop_val[$val_i]}
                        {?$object = $prop_obj[$obj_id]}
                        <div class="row object-prop row-cont m-fullwidth" data-id="{$object.id}" data-type="{$object.person_type}" 
							 data-userdata='"name":"{$object.name}", "surname":"{$object.surname}", "email":"{$object.email}", "company_name":"{$object.company_name}", "inn":"{$object.inn}"'>
                            <div class="w12">
                                <div class="row m-fullwidth a-hidden">
                                    <div class="w11">
                                        <input type="text" class="input-values" name="{$property.key}" placeholder="ID {$catalogs[$property['values']]['word_cases']['i']['1']['r']}" value="{$obj_id}" />
                                    </div>
                                    <div class="apply-object action-button w1" title="Добавить пользователя" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                                        <i class="icon-prop-apply"></i>
                                    </div>
                                </div>
                                <div class="row m-saved m-fullwidth">
                                    <div class="drag-drop w05">
                                        <input type="hidden" class="input-object" name="{$property.key}" data-val-id="{$val_id}" value="{$obj_id}" />
                                    </div>
                                    <div class="w7 title">
                                        {$object.name} {$object.surname} <span class="descr">— {$object.id}</span>
                                    </div>
                                    <div class="w3">
                                        <a href="mailto:{$object.email}" class="small-descr">{$object.email}</a>
                                    </div>
                                    <div class="w05"></div>
                                    <div class="edit-item-object action-button w05" data-object_id="{$obj_id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                                        <i class="icon-prop-edit"></i>
                                    </div>
                                    <div class="delete-object action-button w05">
                                        <i class="icon-prop-delete"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    {/if}
                {/foreach}
            {/if}
            {foreach from=$objects item=object key=obj_id}
                {if !empty($object)}
                    <div class="row object-prop row-cont m-fullwidth" data-id="{$object.id}" data-type="{$object.person_type}" 
						 data-userdata='"name":"{$object.name}", "surname":"{$object.surname}", "email":"{$object.email}", "company_name":"{$object.company_name}", "inn":"{$object.inn}"'>
                        <div class="w12">
                            <div class="row m-fullwidth a-hidden">
                                <div class="w11">
                                    <input type="text" class="input-values" name="{$property.key}" placeholder="ID пользователя" value="{$obj_id}" />
                                </div>
                                <div class="apply-object action-button w1" title="Добавить пользователя" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                                    <i class="icon-prop-apply"></i>
                                </div>
                            </div>
                            <div class="row m-fullwidth">
                                <div class="drag-drop w05">
                                    <input type="hidden" class="input-object" name="{$property.key}" value="{$obj_id}" />
                                </div>
                                <div class="w7 title">
                                    {$object.name} {$object.surname} <span class="descr">— {$object.id}</span>
                                </div>
                                <div class="w3">
                                    <a href="mailto:{$object.email}" class="small-descr">{$object.email}</a>
                                </div>
                                <div class="w05"></div>
                                <div class="edit-item-object action-button w05" data-object_id="{$obj_id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                                    <i class="icon-prop-edit"></i>
                                </div>
                                <div class="delete-object action-button w05">
                                    <i class="icon-prop-delete"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                {/if}
            {/foreach}
            <div class="row add-row row-cont m-fullwidth">
                <div class="w12">
                    <div class="row add-row m-fullwidth a-hidden">
                        <div class="w11">
                            <input type="text" class="input-values" name="{$property.key}" placeholder="ID пользователя" />
                        </div>
                        <div class="apply-object action-button w1" title="Добавить пользователя" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                            <i class="icon-prop-apply"></i>
                        </div>
                    </div>
                    <div class="add-row row m-fullwidth">
                        <div class="edit-item-object add-btn w3">
                            <i class="icon-add"></i> <span class="small-descr">Добавить пользователя</span>
                        </div>
                        <div class="w8"></div>
                        <div class="w1">
                            <div class="prop-menu dropdown">
                                <div class="dropdown-toggle">
                                    <i class="icon-prop-more"></i>
                                </div>
                                <ul class="dropdown-menu a-hidden">
                                    <li><a href="#" class="delete-all">Удалить все</a></li>
                                    <li><a href="#" class="sort-alph">Отсортировать по алфавиту</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


        {elseif !empty($object)}

			{*{?$obj_id = $object.id}
            <div class="row add-row a-hidden">
                <div class="w11">
                    <input type="text" class="input-values" name="{$property.key}" placeholder="ID пользователя" value="{$obj_id}" />
                </div>
                <div class="apply-object action-button w1" title="Добавить товар" data-url="/catalog-item/getEntities/" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                    <i class="icon-prop-apply"></i>
                </div>
            </div>
            <div class="row object-prop" data-id="{$object.id}" data-type="{$object.person_type}" 
				 data-userdata='"name":"{$object.name}", "surname":"{$object.surname}", "email":"{$object.email}", "company_name":"{$object.company_name}", "inn":"{$object.inn}"'>
                <div class="w11">
                    <div class="title">{$object.name} {$object.surname} <span class="descr small-descr">— {$object.id}</span></div>
					<div><a href="mailto:{$object.email}" class="small-descr">{$object.email}</a></div>
                </div>
                <div class="edit-item-object action-button w05" data-object_id="{$obj_id}" data-entity_id="{$entity.id}" data-property_id="{$property.id}" data-segment_id="{$segment_id}">
                    <i class="icon-prop-edit"></i>
                </div>
                <div class="delete-object action-button w05">
                    <i class="icon-prop-delete"></i>
                </div>
            </div>*}
        {/if}

    {/if}
</div>