<?php
/* Smarty version 3.1.33, created on 2020-10-15 12:52:00
  from '/var/www/sell_your_apart/data/www/m16__core/components/migx/templates/mgr/fields.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5f881bc04b2ea9_66208923',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    '7a26a43b3b8ed288124a029f78d5b188019ce951' => 
    array (
      0 => '/var/www/sell_your_apart/data/www/m16__core/components/migx/templates/mgr/fields.tpl',
      1 => 1589378117,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f881bc04b2ea9_66208923 (Smarty_Internal_Template $_smarty_tpl) {
$_smarty_tpl->_checkPlugins(array(0=>array('file'=>'/var/www/sell_your_apart/data/www/m16__core/model/smarty/plugins/function.cycle.php','function'=>'smarty_function_cycle',),));
echo (($tmp = @$_smarty_tpl->tpl_vars['OnResourceTVFormPrerender']->value)===null||$tmp==='' ? '' : $tmp);?>

<?php echo (($tmp = @$_smarty_tpl->tpl_vars['error']->value)===null||$tmp==='' ? '' : $tmp);?>

<?php if ($_smarty_tpl->tpl_vars['formcaption']->value != '') {?>
    <h2><?php echo $_smarty_tpl->tpl_vars['formcaption']->value;?>
</h2>
<?php }?> 

<input type="hidden" class="mulititems_grid_item_fields" name="mulititems_grid_item_fields" value='<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['fields']->value, ENT_QUOTES, 'UTF-8', true);?>
' />
<input type="hidden" class="tvmigxid" name="tvmigxid" value='<?php echo (($tmp = @$_smarty_tpl->tpl_vars['migxid']->value)===null||$tmp==='' ? '' : $tmp);?>
' />


    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['categories']->value, 'category', false, NULL, 'cat', array (
  'first' => true,
  'last' => true,
  'index' => true,
  'iteration' => true,
  'total' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['category']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['index']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['first'] = !$_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['index'];
$_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['total'];
?>
        <?php if (count($_smarty_tpl->tpl_vars['category']->value['layouts']) > 0) {?>
            <?php if (isset($_smarty_tpl->tpl_vars['formnames']->value) && count($_smarty_tpl->tpl_vars['formnames']->value) > 0) {?>
                <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['first'] : null)) {?>
                    <div class="x-form-item x-tab-item <?php echo smarty_function_cycle(array('values'=>",alt"),$_smarty_tpl);?>
 modx-tv" id="tvFormname-tr">
                        <label for="tvFormname" class="modx-tv-label">
                            <span class="modx-tv-caption" id="tvFormname-caption"><?php echo $_smarty_tpl->tpl_vars['multiple_formtabs_label']->value;?>
</span>
                            <span class="modx-tv-reset" ></span> 
                            <?php if ($_smarty_tpl->tpl_vars['tv']->value->descriptionX) {?><span class="modx-tv-description"><?php echo $_smarty_tpl->tpl_vars['tv']->value->descriptionX;?>
</span><?php }?>
                        </label>
                        <div class="x-form-element modx-tv-form-element" style="padding-left: 200px;">
                            <select id="tvFormname" name="tvFormname">
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['formnames']->value, 'item');
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['item']->value) {
?>
	                            <option value="<?php echo $_smarty_tpl->tpl_vars['item']->value['value'];?>
" <?php if ($_smarty_tpl->tpl_vars['item']->value['selected']) {?> selected="selected"<?php }?>><?php echo $_smarty_tpl->tpl_vars['item']->value['text'];?>
</option>
                            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?>
                            </select>
                        </div>
                        <br class="clear" />
                    </div>
                    <?php echo '<script'; ?>
 type="text/javascript">
                    // <![CDATA[
                    

                    MODx.combo.FormnameDropdown = function(config) {
                        config = config || {};
                        Ext.applyIf(config,{
                            transform: 'tvFormname'
                            ,id: 'tvFormname'
                            ,triggerAction: 'all'
                            ,width: 350
                            ,allowBlank: true
                            ,maxHeight: 300
                            ,editable: false
                            ,typeAhead: false
                            ,forceSelection: false
                            ,msgTarget: 'under'
                            ,listeners: { 
		                    'select': {fn:this.selectForm,scope:this}
		                }});
                        MODx.combo.FormnameDropdown.superclass.constructor.call(this,config);
                        //this.config = config;
                        //return this;
                    };
                    Ext.extend(MODx.combo.FormnameDropdown,Ext.form.ComboBox,{
	                    selectForm: function() {
		                    var win = Ext.getCmp('modx-window-mi-grid-update-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
');
                            //win.fp.autoLoad.params.record_json=this.baseParams.record_json;
                            win.switchForm();
		                    //panel.autoLoad.params['context']=this.getValue();
		                    //panel.doAutoLoad();
		                    //MODx.fireResourceFormChange();
	                    }
                    }); 
                    Ext.reg('modx-combo-formnamedropdown',MODx.combo.FormnameDropdown);
                    MODx.load({
                        xtype: 'modx-combo-formnamedropdown'
                    });
                    
                    // ]]>
                    <?php echo '</script'; ?>
>    
                <?php }?>
            <?php }?>
            <?php if (count($_smarty_tpl->tpl_vars['categories']->value) > 1 && !$_smarty_tpl->tpl_vars['category']->value['print_before_tabs']) {?>
            <div id="modx-window-mi-grid-update-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-tabs">    
            <?php }?>
            <?php if (count($_smarty_tpl->tpl_vars['categories']->value) < 2 || ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['first'] : null) && $_smarty_tpl->tpl_vars['category']->value['print_before_tabs'])) {?>
            <div id="modx-tv-tab<?php echo $_smarty_tpl->tpl_vars['category']->value['id'];?>
" >
            <?php } else { ?>
            <div id="modx-tv-tab<?php echo $_smarty_tpl->tpl_vars['category']->value['id'];?>
" class="x-tab" title="<?php echo ucfirst((($tmp = @$_smarty_tpl->tpl_vars['category']->value['category'])===null||$tmp==='' ? $_smarty_tpl->tpl_vars['_lang']->value['uncategorized'] : $tmp));?>
">
            <?php }?>
            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['category']->value['layouts'], 'layout', false, NULL, 'layout', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['layout']->value) {
?>
                <div class="layout" style="width:100%;clear:both;float:left;<?php echo (($tmp = @$_smarty_tpl->tpl_vars['layout']->value['style'])===null||$tmp==='' ? '' : $tmp);?>
">
                    <?php if ($_smarty_tpl->tpl_vars['layout']->value['caption'] != '') {?>
                        <h3><?php echo $_smarty_tpl->tpl_vars['layout']->value['caption'];?>
</h3>
                    <?php }?>                    
                    <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layout']->value['columns'], 'layoutcolumn', false, NULL, 'layoutcolumn', array (
  'last' => true,
  'iteration' => true,
  'total' => true,
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['layoutcolumn']->value) {
$_smarty_tpl->tpl_vars['__smarty_foreach_layoutcolumn']->value['iteration']++;
$_smarty_tpl->tpl_vars['__smarty_foreach_layoutcolumn']->value['last'] = $_smarty_tpl->tpl_vars['__smarty_foreach_layoutcolumn']->value['iteration'] === $_smarty_tpl->tpl_vars['__smarty_foreach_layoutcolumn']->value['total'];
?>
                        <div class="column" style="<?php if (!(isset($_smarty_tpl->tpl_vars['__smarty_foreach_layoutcolumn']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_layoutcolumn']->value['last'] : null)) {?>padding-right: 10px;<?php }?>width:<?php echo $_smarty_tpl->tpl_vars['layoutcolumn']->value['width'];?>
;min-width:<?php echo $_smarty_tpl->tpl_vars['layoutcolumn']->value['minwidth'];?>
;float:left;<?php echo $_smarty_tpl->tpl_vars['layoutcolumn']->value['style'];?>
">
                            <?php if ($_smarty_tpl->tpl_vars['layoutcolumn']->value['caption'] != '') {?>
                                <h4><?php echo $_smarty_tpl->tpl_vars['layoutcolumn']->value['caption'];?>
</h4>
                            <?php }?>                              
                            <?php
$_from = $_smarty_tpl->smarty->ext->_foreach->init($_smarty_tpl, $_smarty_tpl->tpl_vars['layoutcolumn']->value['tvs'], 'tv', false, NULL, 'tv', array (
));
if ($_from !== null) {
foreach ($_from as $_smarty_tpl->tpl_vars['tv']->value) {
?>
                                <?php if ($_smarty_tpl->tpl_vars['tv']->value->type == "description_is_code") {?>
                                    <?php echo $_smarty_tpl->tpl_vars['tv']->value->get('formElement');?>

                                <?php } elseif ($_smarty_tpl->tpl_vars['tv']->value->type != "hidden") {?>
                                    <div class="x-form-item x-tab-item <?php echo smarty_function_cycle(array('values'=>",alt"),$_smarty_tpl);?>
 modx-tv" id="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
-tr" style="width: calc(100% - 5px); padding:0 !important;<?php if ($_smarty_tpl->tpl_vars['tv']->value->display == "none") {?>display:none;<?php }?> ">
                                        <label for="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" class="x-form-item-label modx-tv-label" style="width: auto;">
                                            <div class="modx-tv-label-title"> 
                                                <?php if ((($tmp = @$_smarty_tpl->tpl_vars['showCheckbox']->value)===null||$tmp==='' ? '' : $tmp)) {?><input type="checkbox" name="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
-checkbox" class="modx-tv-checkbox" value="1" /><?php }?>
                                                <span class="modx-tv-caption" id="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
-caption"><?php echo $_smarty_tpl->tpl_vars['tv']->value->caption;?>
</span>
                                            </div>    
                                            <a class="modx-tv-reset" id="modx-tv-reset-<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" onclick="MODx.resetTV(<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
);" title="<?php echo $_smarty_tpl->tpl_vars['_lang']->value['set_to_default'];?>
"></a>
                                            <?php if ($_smarty_tpl->tpl_vars['tv']->value->description) {?><span class="modx-tv-label-description"><?php echo $_smarty_tpl->tpl_vars['tv']->value->description;?>
</span><?php }?>
                                        </label>
                                        <?php if ($_smarty_tpl->tpl_vars['tv']->value->inherited) {?><br /><span class="modx-tv-inherited"><?php echo $_smarty_tpl->tpl_vars['_lang']->value['tv_value_inherited'];?>
</span><?php }?>
                                        <div class="x-form-clear-left"></div>
                                        <div class="x-form-element modx-tv-form-element" style="padding-left: 0px;">
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
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><!-- $layoutcolumn.tvs  -->
                        </div> <!-- column  -->
                    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><!-- $layout.columns  -->    
                </div> <!-- layout  -->
            <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><!-- $category.layouts  -->
            </div>
            <?php if ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['first']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['first'] : null) && $_smarty_tpl->tpl_vars['category']->value['print_before_tabs']) {?>
                <br class="clear" />
            <?php }?>            
            <?php if (count($_smarty_tpl->tpl_vars['categories']->value) > 1 && ((isset($_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['last']) ? $_smarty_tpl->tpl_vars['__smarty_foreach_cat']->value['last'] : null))) {?>
            </div>    
            <?php }?>         
        <?php }?>
       
    <?php
}
}
$_smarty_tpl->smarty->ext->_foreach->restore($_smarty_tpl, 1);?><!-- $categories  -->


<?php if (count($_smarty_tpl->tpl_vars['categories']->value) > 1) {?>

<?php echo '<script'; ?>
 type="text/javascript">
// <![CDATA[
Ext.onReady(function() {    
    var tabs = MODx.load({
        xtype: 'modx-tabs'
        ,applyTo: 'modx-window-mi-grid-update-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
-tabs'
        ,activeTab: 0
        ,autoTabs: true
        ,border: false
        ,plain: true
        ,hideMode: 'offsets'
        ,defaults: {
            bodyStyle: 'padding: 5px;'
            ,autoHeight: true
        }
        ,deferredRender: false
    });
    var win = Ext.getCmp('modx-window-mi-grid-update-<?php echo $_smarty_tpl->tpl_vars['win_id']->value;?>
');    
    win.tabs = tabs;
    
	<?php if ($_smarty_tpl->tpl_vars['tvcount']->value > 0) {?>
    <?php }?>
});    
// ]]>
<?php echo '</script'; ?>
>


<?php }
echo (($tmp = @$_smarty_tpl->tpl_vars['scripts']->value)===null||$tmp==='' ? '' : $tmp);?>

<?php echo (($tmp = @$_smarty_tpl->tpl_vars['OnResourceTVFormRender']->value)===null||$tmp==='' ? '' : $tmp);?>

<br class="clear" /><?php }
}
