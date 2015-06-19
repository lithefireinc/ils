<script type="text/javascript">
 Ext.namespace("ogs_subject");
 ogs_subject.app = function()
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
 							url: "<?=site_url("filereference/getSubject")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "SUBJIDNO"},
 											{ name: "SUBJCODE"},
                                                                                        { name: "COURSEDESC"},
                                                                                        { name: "UNITS_LEC"},
                                                                                        { name: "UNITS_LAB"},
                                                                                        { name: "FEE_TUI"},
                                                                                        { name: "FEE_LAB"},
                                                                                        { name: "FEE_TUT"},
                                                                                        { name: "FEE02_TUI"},
                                                                                        { name: "FEE02_LAB"},
                                                                                        { name: "FEE02_TUT"},
                                                                                        { name: "REMARKS"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ogs_subjectgrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    { header: "Id", width: 75, sortable: true, dataIndex: "SUBJIDNO" },
 						  { header: "Subject", width: 180, sortable: true, dataIndex: "SUBJCODE" },
                                                  { header: "Description", width: 300, sortable: true, dataIndex: "COURSEDESC" },
                                                  { header: "No. of Units (Lec)", width: 100, sortable: true, dataIndex: "UNITS_LEC" },
                                                  { header: "No. of Units (Lab)", width: 100, sortable: true, dataIndex: "UNITS_LAB" },
                                                  { header: "Tuition", width: 100, sortable: true, dataIndex: "FEE_TUI" },
                                                  { header: "Lab Fee", width: 100, sortable: true, dataIndex: "FEE_LAB" },
                                                  { header: "Tutorial Fee", width: 100, sortable: true, dataIndex: "FEE_TUT" },
                                                  { header: "Tuition 2", width: 100, sortable: true, dataIndex: "FEE02_TUI" },
                                                  { header: "Lab Fee 2", width: 100, sortable: true, dataIndex: "FEE02_LAB" },
                                                  { header: "Tutorial Fee 2", width: 100, sortable: true, dataIndex: "FEE02_TUT" },
                                                  { header: "Remarks", width: 100, sortable: true, dataIndex: "REMARKS" },
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

 					     	handler: ogs_subject.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '<?=base_url()?>images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_subject.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '<?=base_url()?>images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_subject.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ogs_subject.app.Grid = grid;
 			ogs_subject.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Subject',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ogs_subject.app.Grid],
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
 		        url:"<?=site_url("filereference/addSubject")?>",
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
 		            fieldLabel: 'Subject Code*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "25"},
 		            name: 'SUBJCODE',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'SUBJCODE'
 		        },
                        {
                            xtype:'textarea',
 		            fieldLabel: 'Description*',
                            maxLength: 100,
 		            name: 'COURSEDESC',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'COURSEDESC'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'No. of Units (Lec)*',
                            maxLength: 5,
 		            name: 'UNITS_LEC',
 		            allowBlank:false,
                            value: 0,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'UNITS_LEC',
                            listeners: {
                                change: function(){
                                   // Ext.getCmp("UNITS_TTL").setValue(this.getValue()+Ext.getCmp("UNITS_LAB").getValue());
                                },
                                blur: function(){
                                  //  Ext.getCmp("UNITS_TTL").setValue(this.getValue()+Ext.getCmp("UNITS_LAB").getValue());
                                }
                            }
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'No. of Units (Lab)*',
                            maxLength: 5,
 		            name: 'UNITS_LAB',
                            value: 0,
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'UNITS_LAB',
                            listeners: {
                                change: function(){
                                  //  Ext.getCmp("UNITS_TTL").setValue(this.getValue()+Ext.getCmp("UNITS_LEC").getValue());
                                },
                                blur: function(){
                                  //  Ext.getCmp("UNITS_TTL").setValue(this.getValue()+Ext.getCmp("UNITS_LEC").getValue());
                                }
                            }
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'No. of Units (Total)*',
                            maxLength: 5,
                           // readOnly: true,
                            value: 0,
 		            name: 'UNITS_TTL',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'UNITS_TTL'
 		        },
                        {
                            xtype:'numberfield',
 		            fieldLabel: 'Tuition Fee*',
                            maxLength: 11,
 		            name: 'FEE_TUI',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FEE_TUI'
 		        },
                        {
                            xtype:'numberfield',
 		            fieldLabel: 'Lab. Fee',
                            maxLength: 11,
 		            name: 'FEE_LAB',
                            value: 0,
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FEE_LAB'
 		        },
                        {
                            xtype:'numberfield',
 		            fieldLabel: 'Tutorial Fee',
                            maxLength: 11,
 		            name: 'FEE_TUT',
                            value: 0,
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FEE_TUT'
 		        },
                        {xtype: 'label', html: '<span style="font-style: italic; font-weight: bold">If applicable: </span>'},
                        {
                            xtype:'numberfield',
 		            fieldLabel: 'Tuition Fee 2',
                            maxLength: 11,
 		            name: 'FEE02_TUI',
 		            allowBlank:true,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FEE02_TUI'
 		        },
                        {
                            xtype:'numberfield',
 		            fieldLabel: 'Lab. Fee 2',
                            maxLength: 11,
 		            name: 'FEE02_LAB',
 		            allowBlank:true,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FEE02_LAB'
 		        },
                        {
                            xtype:'numberfield',
 		            fieldLabel: 'Tutorial Fee 2*',
                            maxLength: 11,
 		            name: 'FEE02_TUT',
 		            allowBlank:true,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FEE02_TUT'
 		        },
                        {
                            xtype:'textarea',
 		            fieldLabel: 'Remarks*',
                            maxLength: 42,
 		            name: 'REMARKS',
 		            allowBlank:true,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'REMARKS'
 		        }

 		        ]
 					}
 		        ]
 		    });

 		    ogs_subject.app.Form = form;
 		},
 		Add: function(){

 			ogs_subject.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Subject',
 		        width: 510,
 		        height:530,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_subject.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_subject.app.Form)){//check if all forms are filled up

 		                ogs_subject.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ogs_subject.app.Grid.getId());
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


 			if(ExtCommon.util.validateSelectionGrid(ogs_subject.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ogs_subject.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.SUBJIDNO;

 			ogs_subject.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Subject',
 		        width: 510,
 		        height:530,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_subject.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_subject.app.Form)){//check if all forms are filled up
 		                ogs_subject.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updateSubject")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ogs_subject.app.Grid.getId());
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

 		  	ogs_subject.app.Form.getForm().load({
 				url: "<?=site_url("filereference/loadSubject")?>",
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


			if(ExtCommon.util.validateSelectionGrid(ogs_subject.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ogs_subject.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.SUBJIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("filereference/deleteSubject")?>",
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
							ogs_subject.app.Grid.getStore().load({params:{start:0, limit: 25}});

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

 Ext.onReady(ogs_subject.app.init, ogs_subject.app);

</script>
<div id="mainBody"></div>