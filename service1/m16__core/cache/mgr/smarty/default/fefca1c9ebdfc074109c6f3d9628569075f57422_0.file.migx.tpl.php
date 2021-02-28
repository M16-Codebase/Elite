<?php
/* Smarty version 3.1.33, created on 2020-10-15 12:37:59
  from '/var/www/sell_your_apart/data/www/m16__core/components/migx/elements/tv/migx.tpl' */

/* @var Smarty_Internal_Template $_smarty_tpl */
if ($_smarty_tpl->_decodeProperties($_smarty_tpl, array (
  'version' => '3.1.33',
  'unifunc' => 'content_5f881877045604_28608167',
  'has_nocache_code' => false,
  'file_dependency' => 
  array (
    'fefca1c9ebdfc074109c6f3d9628569075f57422' => 
    array (
      0 => '/var/www/sell_your_apart/data/www/m16__core/components/migx/elements/tv/migx.tpl',
      1 => 1589378117,
      2 => 'file',
    ),
  ),
  'includes' => 
  array (
  ),
),false)) {
function content_5f881877045604_28608167 (Smarty_Internal_Template $_smarty_tpl) {
?><input id="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" name="tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" type="hidden" class="textfield" value="<?php echo htmlspecialchars($_smarty_tpl->tpl_vars['tv_value']->value, ENT_QUOTES, 'UTF-8', true);?>
"<?php echo $_smarty_tpl->tpl_vars['style']->value;?>
 tvtype="<?php echo $_smarty_tpl->tpl_vars['tv']->value->type;?>
" />
<div id="tvpanel<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
" style="width:100%">
</div>

<br/>

<?php echo '<script'; ?>
 type="text/javascript">
    // <![CDATA[
    <?php echo $_smarty_tpl->tpl_vars['grid']->value;?>

    


MODx.window.UpdateTvItem = function(config) {
    config = config || {};
    
    Ext.applyIf(config,{
        title:'<?php echo $_smarty_tpl->tpl_vars['update_win_title']->value;?>
'
        ,id: 'modx-window-mi-grid-update-<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
' 
        ,width: '1000'
		,closeAction: 'hide'
        ,shadow: false
        ,resizable: true
        ,collapsible: true
        ,maximizable: true
        ,allowDrop: true
        ,height: '600'
        ,constrain: true
        //,saveBtnText: _('done')
        ,forceLayout: true
        ,boxMaxHeight: '700'
        ,autoScroll: true
        ,buttons: [{
            text: config.cancelBtnText || _('cancel')
            ,scope: this
            ,handler: this.cancel
        },{
            text: config.saveBtnText || _('done')
            ,scope: this
            ,handler: this.submit
        }]
        ,record: {}
		,grid: null
        ,action: 'u'
		,record_json: ''
        /*
        ,keys: [{
            key: Ext.EventObject.ENTER
            ,fn: this.submit
            ,scope: this
        }]
        */		
        ,fields: []
    });
    MODx.window.UpdateTvItem.superclass.constructor.call(this,config);
    this.options = config;
    this.config = config;

    //this.on('show',this.onShow,this);
    this.on('hide',this.onHideWindow,this);
    this.on('resize',this.onResizeWindow,this);
    this.addEvents({
        success: true
        ,failure: true
        ,beforeSubmit: true
		,hide:true
        ,resize:true
		//,show:true
    });
    this._loadForm();	
};
Ext.extend(MODx.window.UpdateTvItem,Ext.Window,{
    cancel: function(){

        this.hide();
    },         
    onResizeWindow: function(){
        if (typeof(this.tabs) != 'undefined'){
            this.tabs.doLayout();
            //workarround for tab-resizing-issue
            var activeTab = this.tabs.getActiveTab();
            var id = activeTab.getItemId();
            this.tabs.setActiveTab(100);
            this.tabs.setActiveTab(id);
        }
    },         
    onHideWindow: function(){
   
        var v = this.fp.getForm().getValues();
        var fields = Ext.util.JSON.decode(v['mulititems_grid_item_fields']);
        if (fields.length>0){
            for (var i = 0; i < fields.length; i++) {
                tvid = (fields[i].tv_id);
                field = Ext.get('tv'+tvid);
                if (field && typeof(field.onHide) != 'undefined'){
                    field.onHide();
                }                  
            }
        }
        this.destroy();
    },      
    submit: function() {
        var v = this.fp.getForm().getValues();
        var object_id = this.baseParams.object_id;
        var fields = Ext.util.JSON.decode(v['mulititems_grid_item_fields']);
        var item = {};
        var tvid = '';
        var tvids = {}; 
        var addNewItemAt = '';
        //we run onBeforeSubmit on each field, if this function exists. For example for richtext-fields.       
        if (fields.length>0){
            for (var i = 0; i < fields.length; i++) {
                tvid = (fields[i].tv_id);
                field = Ext.get('tv'+tvid);
                tvids[fields[i].field] = tvid;
                if (field && typeof(field.onBeforeSubmit) != 'undefined'){
                    field.onBeforeSubmit();
                }                         
            }
        }	
        
        v = this.fp.getForm().getValues();
        fields = Ext.util.JSON.decode(v['mulititems_grid_item_fields']);        

        if (this.fp.getForm().isValid()) {
            var s = this.grid.getStore();
            if (this.action == 'd'){
                MODx.fireResourceFormChange();     
            }
            if (this.action == 'e'){
                tvid = tvids['jsonexport'];
                //console.log(v['tv'+tvid]);
                Ext.get('tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
').dom.value = v['tv'+tvid];
                this.grid.loadData();

            }else{
                if (this.action == 'u'){
                    var idx = this.baseParams.itemid; 
                }else{
                    /*append record*/
                    var addNewItemAt = '<?php echo (($tmp = @$_smarty_tpl->tpl_vars['customconfigs']->value['addNewItemAt'])===null||$tmp==='' ? '' : $tmp);?>
';
                    var items=Ext.util.JSON.decode('<?php echo $_smarty_tpl->tpl_vars['newitem']->value;?>
');
		            s.loadData(items,true);
                
                    idx=s.getCount()-1;
                    this.grid.autoinc = v['tvmigxid'];               
                }
            
                var rec = s.getAt(idx);

                if (fields.length>0){
                    for (var i = 0; i < fields.length; i++) {
                        tvid = (fields[i].tv_id);
                        if (v['tv'+tvid+'_prefix']) v['tv'+tvid]=v['tv'+tvid+'_prefix']+v['tv'+tvid];//url-TV support
                        item[fields[i].field]=v['tv'+tvid+'[]'] || v['tv'+tvid] || '';							
                        //set defined record-fields to its new value
                        rec.set(fields[i].field,item[fields[i].field])
                    }
                    //we store the item.values to rec.json because perhaps sometimes we can have different fields for each record
                    rec.json=item;
                }
            
                if (addNewItemAt == 'top'){
                    s.insert(0,rec);
                }
            					
                this.grid.getView().refresh();
                this.grid.collectItems();
                //this.onDirty();                
                
            } 

            if (this.fireEvent('success',v)) {
                this.fp.getForm().reset();
                this.hide();
                return true;
            }
        }
        return false;
    },
    _loadForm: function() {
        //if (this.checkIfLoaded(this.config.record || null)) { return false; }
        this.fp = this.createForm({
            url: this.config.url
            ,baseParams: this.config.baseParams || { action: this.config.action || '' }
            //,items: this.config.fields || []
        });
		//console.log('renderForm');
        this.add(this.fp);
    }	
    ,createForm: function(config){
        config = config || {};
        Ext.applyIf(config,{
            labelAlign: this.config.labelAlign || 'right'
            ,labelWidth: this.config.labelWidth || 100
            ,frame: this.config.formFrame || true
            ,popwindow : this
			,border: false
            ,bodyBorder: false
            ,errorReader: MODx.util.JSONReader
            ,url: this.config.url
            ,baseParams: this.config.baseParams || {}
            ,fileUpload: this.config.fileUpload || false
        });
        return new MODx.panel.MiGridUpdate<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
(config);
    }
    ,switchForm: function() {
        var v = this.fp.getForm().getValues();
        //console.log(v);
        var fields = Ext.util.JSON.decode(v['mulititems_grid_item_fields']);
        var item = {};
        var tvs = {};        
        var tvid = '';
        var field;
        if (fields.length>0){
            for (var i = 0; i < fields.length; i++) {
                tvid = (fields[i].tv_id);
                field = Ext.get('tv'+tvid);
                if (field && typeof(field.onBeforeSubmit) != 'undefined'){
                    field.onBeforeSubmit();
                }                         
            }
        }	        
        v = this.fp.getForm().getValues();
        fields = Ext.util.JSON.decode(v['mulititems_grid_item_fields']);            
        if (fields.length>0){
            for (var i = 0; i < fields.length; i++) {
                tvid = (fields[i].tv_id);
                tvs['tv'+tvid] = true;
                item[fields[i].field]=v['tv'+tvid+'[]'] || v['tv'+tvid] || '';
                field = Ext.get('tv'+tvid);  
                if (field && typeof(field.onHide) != 'undefined'){
                    field.onHide();
                    field.remove();
                }                   							
            }
        }

        //console.log(item);			        
        this.fp.autoLoad.params.record_json=Ext.util.JSON.encode(item);
        this.fp.doAutoLoad();        
    }
    
    ,onShow: function() {
        //console.log('onshow');
        if (this.fp.isloading) return;
        this.fp.isloading=true;
        this.fp.autoLoad.params.record_json=this.baseParams.record_json;
        this.fp.doAutoLoad();
    }

});
Ext.reg('modx-window-tv-item-update-<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
',MODx.window.UpdateTvItem);

MODx.panel.MiGridUpdate<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
 = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'migxdb-panel-object-<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
'
		,title: ''
        ,url: config.url
        ,baseParams: config.baseParams	
        ,class_key: ''
        //,autoSize: true
        ,autoLoad: this.autoload(config)
        ,layout: 'anchor'
        , height:'98%'            
        ,anchorSize: {width:'98%', height:'98%'}   
        ,listeners: {
            //'beforeSubmit': {fn:this.beforeSubmit,scope:this},
            //'success': {fn:this.success,scope:this}
			'load': {fn:this.load,scope:this}
        }		
    });
 	MODx.panel.MiGridUpdate<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
.superclass.constructor.call(this,config);
	
	//this.addEvents({ load: true });
};
Ext.extend(MODx.panel.MiGridUpdate<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
,MODx.FormPanel,{
    autoload: function(config) {
		this.isloading=true;
		var a = {
            url: '<?php echo $_smarty_tpl->tpl_vars['config']->value['connectorUrl'];?>
'
            //url: config.url
			,method: 'POST'
            ,params: config.baseParams
            ,scripts: true
            ,callback: function() {
				this.isloading=false;
				this.isloaded=true;
				this.fireEvent('load');
                //MODx.fireEvent('ready');
            }
            ,scope: this
        };
        return a;        	
    },scope: this
    
    ,
    setup: function() {

    }
    ,beforeSubmit: function(o) {
        //tinyMCE.triggerSave(); 
    }
	 ,load: function() {

        var v = this.getForm().getValues();
        //console.log(v);
        var fields = Ext.util.JSON.decode(v['mulititems_grid_item_fields']);
        var item = {};
        var tvs = {};        
        var tvid = '';
        var field = null;
        if (fields.length>0){
            for (var i = 0; i < fields.length; i++) {
                
                tvid = (fields[i].tv_id);
                field = Ext.get('tv'+tvid);
                if (field && typeof(field.onLoad) != 'undefined'){
                    field.onLoad();
                }                
			
            }
        } 
        
        //this.popwindow.width='1000px';
		//this.width='1000px';
		//this.syncSize();
		//this.popwindow.syncSize();
		return '';
	 }
});
Ext.reg('migxdb-panel-object',MODx.panel.MiGridUpdate<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
);


<?php echo $_smarty_tpl->tpl_vars['iframewindow']->value;?>



/*
Ext.ux.IFrameComponent = Ext.extend(Ext.BoxComponent, {
     onRender : function(ct, position){
          this.el = ct.createChild({tag: 'iframe', id: 'iframe-'+ this.id, frameBorder: 0, src: this.url});
     }
});
*/
/*
var MiPreviewPanel = new Ext.Panel({
     id: 'MiPreviewPanel',
     title: 'MIGX - Preview',
     closable:true,
     // layout to fit child component
     layout:'fit', 
     // add iframe as the child component
     items: [ new Ext.ux.IFrameComponent({ id: id, url: 'http://www.gitrevo.webcmsolutions.de/manager' }) ]
});
*/
/*
Ext.ux.IFrameComponent = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        layout:'fit'
        ,id: 'modx-iframe-mi-preview'
        ,url: 'http://www.gitrevo.webcmsolutions.de/preview1.html' 
    });
    Ext.ux.IFrameComponent.superclass.constructor.call(this,config);
};
Ext.extend(Ext.ux.IFrameComponent,Ext.BoxComponent,{
     onRender : function(ct, position){
          this.el = ct.createChild({tag: 'iframe', id: 'iframe-'+ this.id, frameBorder: 0, src: this.url});
     }
});
Ext.reg('modx-iframe-mi-preview',Ext.ux.IFrameComponent);
*/     

MODx.window.MiPreview = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        title: _('migx.preview')
        ,id: 'modx-window-mi-preview'
        ,width: '1050'
        ,height: '700'
		,closeAction: 'hide'
        ,shadow: true
        ,resizable: true
        ,collapsible: true
        ,maximizable: true
        ,autoScroll: true
        ,items: [
           {
            xtype: 'form'
            ,id:'migx_preview_form'
            ,target: 'preview_iframe'
            ,standardSubmit: true
            ,url: config.src
            ,items:[{
                xtype:'hidden'
                ,name:'migx_outputvalue'
                ,id:'migx_preview_json'
            }
            
            ]
        },
        
        {
            xtype: 'container'
            ,width: '980'
            ,height: '620'
            ,autoEl: {
            tag: 'iframe'
            ,name: 'migx_preview_iframe'
            ,src: config.src
            }
         }]
        //,saveBtnText: _('done')
        ,forceLayout: true
        ,buttons: [{
            text: config.cancelBtnText || _('close')
            ,scope: this
            ,handler: function() { this.hide(); }
        }]
        ,action: 'u'
		,record_json: ''
        ,keys: [{
            key: Ext.EventObject.ENTER
            ,fn: this.submit
            ,scope: this
        }]		
    });
    MODx.window.MiPreview.superclass.constructor.call(this,config);
    this.options = config;
    this.config = config;

    //this.on('show',this.onShow,this);
    this.addEvents({
        success: true
        ,failure: true
		//,hide:true
		//,show:true
    });
    //this.renderIframe();	
};
Ext.extend(MODx.window.MiPreview,Ext.Window,{

    renderIframe: function() {
		this.add(this.iframe);
		
    }
    ,onShow: function() {
     var input = Ext.getCmp('migx_preview_json');
     input.setValue(this.json);
     input.getEl().dom.name = this.jsonvarkey;
     var formpanel = Ext.getCmp('migx_preview_form');
     var form = Ext.getCmp('migx_preview_form').getForm();
     form.getEl().dom.action=this.src;
     form.getEl().dom.target='migx_preview_iframe';
     form.submit();  
    }

});
Ext.reg('modx-window-mi-preview',MODx.window.MiPreview);


Ext.onReady(function() {
var lang = <?php echo $_smarty_tpl->tpl_vars['migx_lang']->value;?>
;
  for (var name in lang) {
    MODx.lang[name] = lang[name];
  }
  
});

        MODx.load({
            xtype: 'modx-grid-multitvgrid-<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
'
            ,renderTo: 'tvpanel<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
'
            ,tv: '<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
'
            ,cls: 'tv_modx-grid-multitvgrid_items'
            ,id:'tv<?php echo $_smarty_tpl->tpl_vars['tv']->value->id;?>
_items'
			,columns:Ext.util.JSON.decode('<?php echo $_smarty_tpl->tpl_vars['columns']->value;?>
')
            ,configs: '<?php echo $_smarty_tpl->tpl_vars['properties']->value['configs'];?>
'
			,pathconfigs:Ext.util.JSON.decode('<?php echo $_smarty_tpl->tpl_vars['pathconfigs']->value;?>
')
            ,fields:Ext.util.JSON.decode('<?php echo $_smarty_tpl->tpl_vars['fields']->value;?>
')
            ,wctx: '<?php echo $_smarty_tpl->tpl_vars['myctx']->value;?>
'
            ,tv_type: '<?php echo $_smarty_tpl->tpl_vars['tv_type']->value;?>
'
            //,url: MODx.config.assets_url+'components/migx/connector.php' 
            ,url: '<?php echo $_smarty_tpl->tpl_vars['config']->value['connectorUrl'];?>
'
        });

var MIGx = MIGx || {};
 
MIGx.updateGrid = function() {
    var grids = this.el.select('.tv_modx-grid-multitvgrid_items');
    grids.each(function(grid){
        Ext.getCmp(grid.id).getView().refresh();
    });  
};
 
Ext.ComponentMgr.onAvailable('modx-resource-tabs', function() {
    Ext.apply(this, {
        listeners: {
            tabchange: MIGx.updateGrid,
            afterlayout: MIGx.updateGrid,
            resize: MIGx.updateGrid
        }
    });
     
});



<?php echo '</script'; ?>
><?php }
}
