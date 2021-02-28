<?php
/* Smarty version 3.1.33, created on 2020-10-15 12:37:59
  from '/var/www/sell_your_apart/data/www/sell-your-apartment.m16-estate.ru/adminpanel/templates/default/resource/sections/tvs.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5f8818773335f4_39233038',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'e50b6fced2edbbabcd3cbc222ce7fc798b55cb7f' => 
    array (
      0 => '/var/www/sell_your_apart/data/www/sell-your-apartment.m16-estate.ru/adminpanel/templates/default/resource/sections/tvs.tpl',
      1 => 1589378095,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f8818773335f4_39233038 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/sell_your_apart/data/www/m16__core/model/smarty/plugins/function.cycle.php','function'=>'smarty_function_cycle',),));
echo $_smarty_tpl->tpl_vars['OnResourceTVFormPrerender']->value;?>


<input type="hidden" name="tvs" value="1" />
<div id="modx-tv-tabs" class="x-form-label-top">
<?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['categories']->value, 'category');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['category']->value) {
if (count($_smarty_tpl->tpl_vars['category']->value['tvs']) > 0) {?>

    <div id="modx-tv-tab<?php echo $_smarty_tpl->tpl_vars['category']->value['id'];?>
" class="x-tab<?php if ($_smarty_tpl->tpl_vars['category']->value['hidden']) {?>-hidden<?php }?>" title="<?php echo $_smarty_tpl->tpl_vars['category']->value['category'];?>
">
    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['category']->value['tvs'], 'tv', false, NULL, 'tv', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tv']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['total'];
if ($_smarty_tpl->tpl_vars['tv']->value->type != "hidden") {?>
    <div class="modx-tv-type-<?php echo $_smarty_tpl->tpl_vars['tv']->value->type;?>
 x-form-item x-tab-item <?php echo smarty_function_cycle(array('values'=>",alt"),$_smarty_tpl);?>
 modx-tv<?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['first'] : null)) {?> tv-first<?php }
if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_tv']->value['last'] : null)) {?> tv-last<?php }?>" id="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
-tr">
        <label for="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" class="x-form-item-label modx-tv-label">
            <div class="modx-tv-label-title">
                <?php if ((($tmp = @$_smarty_tpl->tpl_vars['showCheckbox']->value)===null||$tmp==='' ? '' : $tmp)) {?><input type="checkbox" name="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
-checkbox" class="modx-tv-checkbox" value="1" /><?php }?>
                <span class="modx-tv-caption" id="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
-caption"><?php if ($_smarty_tpl->tpl_vars['tv']->value->caption) {
echo $_smarty_tpl->tpl_vars['tv']->value->caption;
} else {
echo $_smarty_tpl->tpl_vars['tv']->value->name;
}?></span>
            </div>
            <a class="modx-tv-reset" id="modx-tv-reset-<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" title="<?php echo $_smarty_tpl->tpl_vars['_lang']->value['set_to_default'];?>
"></a>
            <?php if ($_smarty_tpl->tpl_vars['tv']->value->description) {?>
            <span class="modx-tv-label-description"><?php echo $_smarty_tpl->tpl_vars['tv']->value->description;?>
</span>
            <?php }?>
        </label>
        <?php if ($_smarty_tpl->tpl_vars['tv']->value->inherited) {?><span class="modx-tv-inherited"><?php echo $_smarty_tpl->tpl_vars['_lang']->value['tv_value_inherited'];?>
</span><?php }?>
        <div class="x-form-element modx-tv-form-element">
            <input type="hidden" id="tvdef<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tv']->value->default_text, ENT_QUOTES, 'UTF-8', true);?>
" />
            <?php echo $_smarty_tpl->tpl_vars['tv']->value->get('formElement');?>

        </div>
    </div>
    <?php echo '<script'; ?>
 type="text/javascript">Ext.onReady(function() { new Ext.ToolTip({target: 'tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
-caption',html: '[[*<?php echo $_smarty_tpl->tpl_vars['tv']->value->name;?>
]]'});});<?php echo '</script'; ?>
>
<?php } else { ?>
    <input type="hidden" id="tvdef<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tv']->value->default_text, ENT_QUOTES, 'UTF-8', true);?>
" />
    <?php echo $_smarty_tpl->tpl_vars['tv']->value->get('formElement');?>

<?php }?>
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>

    <div class="clear"></div>

    </div>
<?php }
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
</div>

<?php echo '<script'; ?>
 type="text/javascript">
// <![CDATA[
Ext.onReady(function() {
    MODx.resetTV = function(id) {
        var i = Ext.get('tv'+id);
        var d = Ext.get('tvdef'+id);

        if (i) {
            i.dom.value = d.dom.value;
            i.dom.checked = d.dom.value ? true : false;
        }
        var c = Ext.getCmp('tv'+id);
        if (c) {
            if (c.xtype == 'checkboxgroup' || c.xtype == 'radiogroup') {
                var cbs = d.dom.value.split(',');
                for (var i=0;i<c.items.length;i++) {
                    if (c.items.items[i]) {
                        c.items.items[i].setValue(cbs.indexOf(c.items.items[i].id) != -1);
                    }
                }
            } else {
                c.setValue(d.dom.value);
            }
        }
        var p = Ext.getCmp('modx-panel-resource');
        if (p) {
            p.markDirty();
            p.fireEvent('tv-reset',{id:id});
        }
    };

    Ext.select('.modx-tv-reset').on('click',function(e,t,o) {
        var id = t.id.split('-');
        id = id[3];
        MODx.resetTV(id);
    });
    MODx.refreshTVs = function() {
        if (MODx.unloadTVRTE) { MODx.unloadTVRTE(); }
        Ext.getCmp('modx-panel-resource-tv').refreshTVs();
    };
    <?php if ($_smarty_tpl->tpl_vars['tvcount']->value > 0) {?>
    MODx.load({
        xtype: 'modx-vtabs'
        ,applyTo: 'modx-tv-tabs'
        ,autoTabs: true
        ,border: false
        ,plain: true
        ,deferredRender: false
        ,id: 'modx-resource-vtabs'
        ,headerCfg: {
            tag: 'div'
            ,cls: 'x-tab-panel-header vertical-tabs-header'
            ,id: 'modx-resource-vtabs-header'
            ,html: MODx.config.show_tv_categories_header === true ? '<h4 id="modx-resource-vtabs-header-title">'+_('categories')+'</h4>' : ''
        }
        ,listeners: {
            beforeadd: function (tabpanel, comp) {
                if (comp.contentEl && (Ext.get(comp.contentEl).child('.modx-tv') === null)) {
                    return false;
                }
            }
            ,afterrender: function (tabpanel) {
                if (tabpanel.items.length === 0) {
                    Ext.getCmp('modx-resource-tabs').hideTabStripItem('modx-panel-resource-tv');
                }
            }
        }
    });
    <?php }?>

    MODx.tvCounts = <?php echo $_smarty_tpl->tpl_vars['tvCounts']->value;?>
;
    MODx.tvMap = <?php echo $_smarty_tpl->tpl_vars['tvMap']->value;?>
;
    
});
// ]]>
<?php echo '</script'; ?>
>


<?php echo $_smarty_tpl->tpl_vars['OnResourceTVFormRender']->value;?>


    <div class="clear"></div>
<?php }
}
