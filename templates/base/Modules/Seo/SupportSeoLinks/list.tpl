<div class="content-scroll">
    <div class="aside-panel">

        <div class="actions-panel a-clearbox multiple">
            <div class="actions-panel-inner">
                <a href="#" class="action-button action-add" title="Добавить">
                    <i class="action-icon icon-add"></i>
                    <div class="action-text">Добавить</div>
                </a>
                <a href="#" class="action-button action-delete m-inactive multi-action" title="Удалить">
                    <i class="action-icon icon-delete"></i>
                    <div class="action-text">Удалить</div>
                </a>
            </div>
        </div>
    </div>

    <form class="actions-cont items-edit viewport">
        <div class="items-list">
            <div class="white-blocks">
                <!-- header -->
                <div class="wblock white-block-row white-header">
                    <div class="w05" style="height: 48px;">
                        <input type="hidden" name="page" value="1">
                        <input type="hidden" name="type_id" value="17">
                    </div>
                    <label class="w05" style="height: 48px;"><input type="checkbox" class="check-all"></label>
                    <div class="w8" style="height: 48px;">Текст</div>
                    <div class="w3" style="height: 48px;"></div>
                </div>
                <!-- end header -->

                <!-- items
                <div id="lsnks_list">-->
                {foreach from=$seo_links item=link}
                    <div class="wblock white-block-row" data-item_id="{$link['id']}" data-href="{$link['href']}" data-text="{$link['text']}"  data-work="{$link['work']}">
                        <div class="w05 drag-drop ui-sortable-handle" style="height: 48px;"></div>
                        <label class="w05" style="height: 48px;">
                            <input type="checkbox" name="check[]" value="{$link['id']}" class="check-item">
                        </label>
                        <div class="w7" style="height: 48px;">
                            <input type="hidden" name="id" value="{$link['id']}">
                            <span class="item-title 321">{$link['text']}</span>
                        </div>
                        <div class="w1" style="height: 48px;"></div>
                        <div class="action-button action-visibility w1 m-active action-show" title="Отображается" style="height: 48px;">
                            <i class="icon-show"></i>
                        </div>
                        <a href="/supportSeoLinks/edit/?id={$link['id']}" class="action-button action-edit w1 m-border" title="Редактировать" style="height: 48px;">
                            <i class="icon-edit"></i>
                        </a>
                        <div class="action-button action-delete w1 m-border" title="Удалить" style="height: 48px;">
                            <i class="icon-delete"></i>
                        </div>
                    </div>
                {/foreach}
                <!--</div>
                 end items -->

            </div>
        </div>
    </form>
</div>