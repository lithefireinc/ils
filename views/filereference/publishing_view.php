<script type="text/javascript">
 Ext.namespace("ils_publishing");
 ils_publishing.app = function()
 {
 	return{
 		init: function()
 		{
 			ExtCommon.util.init();
 			ExtCommon.util.quickTips();
 			this.getGrid();
 		},
 		getGrid: function()
 		{
 			ExtCommon.util.renderSearchField('searchby');

 			var Objstore = new Ext.data.Store({
 						proxy: new Ext.data.HttpProxy({
 							url: "<?=site_url("filereference/getPublishing")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "PUBLCODE"},
 											{ name: "DESCRIPTION"},
 											{ name: "ACRONYM"},
 											{ name: "ADDR_01"},
 											{ name: "ADDR_02"},
 											{ name: "PUBLIDNO"},
 											{ name: "COUNTRY"},
 											{ name: "CONTPERSON"},
 											{ name: "CONTPHONE"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ils_publishinggrid',
 				height: 300,
 				width: '100%',
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    { header: "Id", width: 75, sortable: true, dataIndex: "PUBLIDNO" },
 						  { header: "Publisher", width: 300, sortable: true, dataIndex: "DESCRIPTION" },
 						  { header: "Acronym", width: 120, sortable: true, dataIndex: "ACRONYM" },
 						  { header: "Address 01", width: 120, sortable: true, dataIndex: "ADDR_01" },
 						  { header: "Address 02", width: 120, sortable: true, dataIndex: "ADDR_02" },
 						  { header: "Country", width: 120, sortable: true, dataIndex: "COUNTRY" },
 						  { header: "Contact Person", width: 120, sortable: true, dataIndex: "CONTPERSON" },
 						  { header: "Contact Phone", width: 120, sortable: true, dataIndex: "CONTPHONE" }
 						]
 				),
 				sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
 	        	loadMask: true,
 	        	bbar:
 	        		new Ext.PagingToolbar({
 		        		autoShow: true,
 				        pageSize: 25,
 				        store: Objstore,
 				        displayInfo: true,
 				        displayMsg: 'Displaying Results {0} - {1} of {2}',
 				        emptyMsg: "No Data Found."
 				    }),
 				tbar: [new Ext.form.ComboBox({
                    fieldLabel: 'Search',
                    hiddenName:'searchby-form',
                    id: 'searchby',
					//store: Objstore,
                    typeAhead: true,
                    triggerAction: 'all',
                    emptyText:'Search By...',
                    selectOnFocus:true,

                    store: new Ext.data.SimpleStore({
				         id:0
				        ,fields:
				            [
				             'myId',   //numeric value is the key
				             'myText' //the text value is the value

				            ]


				         , data: [['id', 'ID'], ['sd', 'Short Description'], ['ld', 'Long Description']]

			        }),
				    valueField:'myId',
				    displayField:'myText',
				    mode:'local',
                    width:100,
                    hidden: true

                }), {
					xtype:'tbtext',
					text:'Search:'
				},'   ', new Ext.app.SearchField({ store: Objstore, width:250}),
 					    {
 					     	xtype: 'tbfill'
 					 	},{
 					     	xtype: 'tbbutton',
 					     	text: 'ADD',
							icon: '/images/icons/application_add.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_publishing.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '/images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_publishing.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_publishing.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ils_publishing.app.Grid = grid;
 			ils_publishing.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Publisher',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ils_publishing.app.Grid],
 		        resizable: false

 			    /*listeners : {
 				    	  close: function(p){
 					    	  window.location="../"
 					      }
 			       	} */
 	        });

 	        _window.render();


 		},
 			setForm: function(){
 		    var form = new Ext.form.FormPanel({
 		        labelWidth: 150,
 		        url:"<?=site_url("filereference/addPublishing")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,

 		        items: [ {
 					xtype:'fieldset',
 					title:'Fields w/ Asterisks are required.',
 					width:'auto',
 					height:'auto',
 					items:[
                        {

                            xtype:'textfield',
 		            fieldLabel: 'Publisher*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "128"},
 		            name: 'DESCRIPTION',
 		            allowBlank:false,
 		            anchor:'95%',  // anchor width by percentage
 		            id: 'DESCRIPTION'
 		        },
 		        {

                            xtype:'textfield',
 		            fieldLabel: 'Acronym*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "128"},
 		            name: 'ACRONYM',
 		            allowBlank:false,
 		            anchor:'95%',  // anchor width by percentage
 		            id: 'ACRONYM'
 		        },
 		        {

                            xtype:'textarea',
 		            fieldLabel: 'Address 01*',
                   
 		            name: 'ADDR_01',
 		            allowBlank:false,
 		            anchor:'95%',  // anchor width by percentage
 		            id: 'ADDR_01'
 		        },
 		        {

                            xtype:'textarea',
 		            fieldLabel: 'Address 02',
                            
 		            name: 'ADDR_02',
 		            allowBlank:true,
 		            anchor:'95%',  // anchor width by percentage
 		            id: 'ADDR_02'
 		        },
 		        ils_publishing.app.countryCombo(),
 		        {

                            xtype:'textfield',
 		            fieldLabel: 'Contact Person',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "128"},
 		            name: 'CONTPERSON',
 		            allowBlank:true,
 		            anchor:'95%',  // anchor width by percentage
 		            id: 'CONTPERSON'
 		        },
 		        {

                            xtype:'textfield',
 		            fieldLabel: 'Contact Phone',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "128"},
 		            name: 'CONTPHONE',
 		            allowBlank:true,
 		            anchor:'95%',  // anchor width by percentage
 		            id: 'CONTPHONE'
 		        }

 		        ]
 					}
 		        ]
 		    });

 		    ils_publishing.app.Form = form;
 		},
 		Add: function(){

 			ils_publishing.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Publisher',
 		        width: 510,
 		        height:400,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_publishing.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_publishing.app.Form)){//check if all forms are filled up

 		                ils_publishing.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ils_publishing.app.Grid.getId());
 				                _window.destroy();
 			                },
 			                failure: function(f,a){
 								Ext.Msg.show({
 									title: 'Error Alert',
 									msg: a.result.data,
 									icon: Ext.Msg.ERROR,
 									buttons: Ext.Msg.OK
 								});
 			                },
 			                waitMsg: 'Saving Data...'
 		                });
 	                }else return;
 	                }
 	            },{
 		            text: 'Cancel',
                            icon: '/images/icons/cancel.png', cls:'x-btn-text-icon',

 		            handler: function(){
 			            _window.destroy();
 		            }
 		        }]
 		    });
 		  	_window.show();
 		},
 		Edit: function(){


 			if(ExtCommon.util.validateSelectionGrid(ils_publishing.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ils_publishing.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.PUBLIDNO;

 			ils_publishing.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Publisher',
 		        width: 510,
 		        height:400,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_publishing.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_publishing.app.Form)){//check if all forms are filled up
 		                ils_publishing.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updatePublishing")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ils_publishing.app.Grid.getId());
 				                _window.destroy();
 			                },
 			                failure: function(f,a){
 								Ext.Msg.show({
 									title: 'Error Alert',
 									msg: a.result.data,
 									icon: Ext.Msg.ERROR,
 									buttons: Ext.Msg.OK
 								});
 			                },
 			                waitMsg: 'Updating Data...'
 		                });
 	                }else return;
 		            }
 		        },{
 		            text: 'Cancel',
                            icon: '/images/icons/cancel.png', cls:'x-btn-text-icon',

 		            handler: function(){
 			            _window.destroy();
 		            }
 		        }]
 		    });

 		  	ils_publishing.app.Form.getForm().load({
 				url: "<?=site_url("filereference/loadPublishing")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
                                    _window.show();
                                    Ext.get("COUNIDNO").dom.value = action.result.data.COUNIDNO;
 				},
 				failure: function(form, action) {
         					Ext.Msg.show({
 									title: 'Error Alert',
 									msg: "A connection to the server could not be established",
 									icon: Ext.Msg.ERROR,
 									buttons: Ext.Msg.OK,
 									fn: function(){ _window.destroy(); }
 								});
     			}
 			});
 			}else return;
 		},
		Delete: function(){


			if(ExtCommon.util.validateSelectionGrid(ils_publishing.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ils_publishing.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.PUBLIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("filereference/deletePublishing")?>",
							params:{ id: id},
							method: "POST",
							timeout:300000000,
			                success: function(responseObj){
                		    	var response = Ext.decode(responseObj.responseText);
						if(response.success == true)
						{
							Ext.Msg.show({
								title: 'Status',
								msg: "Record deleted successfully",
								icon: Ext.Msg.INFO,
								buttons: Ext.Msg.OK
							});
							ils_publishing.app.Grid.getStore().load({params:{start:0, limit: 25}});

							return;

						}
						else if(response.success == false)
						{
							Ext.Msg.show({
								title: 'Error!',
								msg: "There was an error encountered in deleting the record. Please try again",
								icon: Ext.Msg.ERROR,
								buttons: Ext.Msg.OK
							});

							return;
						}
							},
			                failure: function(f,a){
								Ext.Msg.show({
									title: 'Error Alert',
									msg: "There was an error encountered in deleting the record. Please try again",
									icon: Ext.Msg.ERROR,
									buttons: Ext.Msg.OK
								});
			                },
			                waitMsg: 'Deleting Data...'
						});
   			}
   			},

   			icon: Ext.MessageBox.QUESTION
			});

	                }else return;


		},
		countryCombo: function(){

 			return {
 				xtype:'combo',
 				id:'COUNTRY',
 				hiddenName: 'COUNIDNO',
                hiddenId: 'COUNIDNO',
 				name: 'COUNTRY',
 				valueField: 'id',
 				displayField: 'name',
 				//width: 100,
 				anchor: '95%',
 				triggerAction: 'all',
 				minChars: 2,
 				forceSelection: true,
 				enableKeyEvents: true,
 				pageSize: 10,
 				resizable: true,
 				//readOnly: true,
 				minListWidth: 300,
 				allowBlank:true,
 				store: new Ext.data.JsonStore({
 				id: 'idsocombo',
 				root: 'data',
 				totalProperty: 'totalCount',
 				fields:[{name: 'id'}, {name: 'name'}],
 				url: "<?=site_url("filereference/getCountryCombo")?>",
 				baseParams: {start: 0, limit: 10}

 				}),
 				listeners: {
 				beforequery: function(qe)
				{
					delete qe.combo.lastQuery;
				},
 				select: function (combo, record, index){
 				this.setRawValue(record.get('name'));
 				Ext.get(this.hiddenName).dom.value  = record.get('id');




 				},
 				blur: function(){
 				var val = this.getRawValue();
 				this.setRawValue.defer(1, this, [val]);
 				this.validate();
 				},
 				render: function() {
 				this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a classification'});

 				},
 				keypress: {buffer: 100, fn: function() {
 				Ext.get(this.hiddenName).dom.value  = '';
 				if(!this.getRawValue()){
 				this.doQuery('', true);
 				}
 				}}
 				},
 				fieldLabel: 'Country*'

 				}
 		}//end of functions
 	}

 }();

 Ext.onReady(ils_publishing.app.init, ils_publishing.app);

</script>
<div id="mainBody"></div>
