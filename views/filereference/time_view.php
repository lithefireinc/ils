<script type="text/javascript">
 Ext.namespace("ogs_time");
 ogs_time.app = function()
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
 							url: "<?=site_url("filereference/getTime")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "TIMEIDNO"},
 											{ name: "TIME"},
                                                                                        { name: "DESCRIPTIO"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ogs_timegrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    { header: "Id", width: 75, sortable: true, dataIndex: "TIMEIDNO" },
 						  { header: "Time", width: 300, sortable: true, dataIndex: "TIME" },
                                                  { header: "Description", width: 300, sortable: true, dataIndex: "DESCRIPTIO" }
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
							icon: '<?=base_url()?>images/icons/application_add.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_time.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '<?=base_url()?>images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_time.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '<?=base_url()?>images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_time.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ogs_time.app.Grid = grid;
 			ogs_time.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Time',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ogs_time.app.Grid],
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
 		        url:"<?=site_url("filereference/addTime")?>",
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
 		            fieldLabel: 'Time*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'TIME',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'TIME'
 		        },
                        {
                            xtype:'textarea',
 		            fieldLabel: 'Description*',
                            maxLength: 47,
 		            name: 'DESCRIPTIO',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'DESCRIPTIO'
 		        }

 		        ]
 					}
 		        ]
 		    });

 		    ogs_time.app.Form = form;
 		},
 		Add: function(){

 			ogs_time.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Time',
 		        width: 510,
 		        height:230,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_time.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_time.app.Form)){//check if all forms are filled up

 		                ogs_time.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ogs_time.app.Grid.getId());
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
                            icon: '<?=base_url()?>images/icons/cancel.png', cls:'x-btn-text-icon',

 		            handler: function(){
 			            _window.destroy();
 		            }
 		        }]
 		    });
 		  	_window.show();
 		},
 		Edit: function(){


 			if(ExtCommon.util.validateSelectionGrid(ogs_time.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ogs_time.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.TIMEIDNO;

 			ogs_time.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Time',
 		        width: 510,
 		        height:230,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_time.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_time.app.Form)){//check if all forms are filled up
 		                ogs_time.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updateTime")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ogs_time.app.Grid.getId());
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
                            icon: '<?=base_url()?>images/icons/cancel.png', cls:'x-btn-text-icon',

 		            handler: function(){
 			            _window.destroy();
 		            }
 		        }]
 		    });

 		  	ogs_time.app.Form.getForm().load({
 				url: "<?=site_url("filereference/loadTime")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
                                    _window.show();
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


			if(ExtCommon.util.validateSelectionGrid(ogs_time.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ogs_time.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.TIMEIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("filereference/deleteTime")?>",
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
							ogs_time.app.Grid.getStore().load({params:{start:0, limit: 25}});

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


		}
 	}

 }();

 Ext.onReady(ogs_time.app.init, ogs_time.app);

</script>
<div id="mainBody"></div>
