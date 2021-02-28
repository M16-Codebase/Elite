<?php
/* Smarty version 3.1.33, created on 2020-10-15 12:37:59
  from '/var/www/sell_your_apart/data/www/sell-your-apartment.m16-estate.ru/adminpanel/templates/default/element/tv/renders/input/richtext.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5f8818770f8c30_47615042',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'eceaee0511ced1d836be79e4b15337b500aeb67f' => 
    array (
      0 => '/var/www/sell_your_apart/data/www/sell-your-apartment.m16-estate.ru/adminpanel/templates/default/element/tv/renders/input/richtext.tpl',
      1 => 1589378094,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f8818770f8c30_47615042 (Smarty_Internal_Template $_smarty_tpl) {
?><textarea id="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" name="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" class="modx-richtext" onchange="MODx.fireResourceFormChange();"><?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tv']->value->get('value'), ENT_QUOTES, 'UTF-8', true);?>
</textarea>

<?php echo '<script'; ?>
 type="text/javascript">

Ext.onReady(function() {
    
    MODx.makeDroppable(Ext.get('tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
'));
    
});
<?php echo '</script'; ?>
><?php }
}
