<script type="text/javascript">
 Ext.namespace("ogs_school");
 ogs_school.app = function()
 {
 	return{
 		init: function()
 		{
 			ExtCommon.util.init();
 			ExtCommon.util.quickTips();
                        ExtCommon.util.validations();
 			this.getGrid();
 		},
 		getGrid: function()
 		{
 			ExtCommon.util.renderSearchField('searchby');

 			var Objstore = new Ext.data.Store({
 						proxy: new Ext.data.HttpProxy({
 							url: "<?=site_url("filereference/getSchool")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "SCHOIDNO"},
 											{ name: "SCHOOL"},
                                                                                        { name: "SCHOOL_INI"},
                                                                                        { name: "ADDR_01"},
                                                                                        { name: "ADDR_02"},
                                                                                        { name: "ADDR_03"},
                                                                                        { name: "PHONE_01"},
                                                                                        { name: "PHONE_02"},
                                                                                        { name: "PHONE_03"},
                                                                                        { name: "FAX_01"},
                                                                                        { name: "FAX_02"},
                                                                                        { name: "CON_NAME"},
                                                                                        { name: "CON_PHONE"},
                                                                                        { name: "WEBSITE"},
                                                                                        { name: "EMAIL"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ogs_schoolgrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                  { header: "Id", width: 75, sortable: true, dataIndex: "SCHOIDNO" },
 						  { header: "School", width: 300, sortable: true, dataIndex: "SCHOOL" },
                                                  { header: "School Initials", width: 100, sortable: true, dataIndex: "SCHOOL_INI" },
                                                  { header: "Address Line 1", width: 150, sortable: true, dataIndex: "ADDR_01" },
                                                  { header: "Address Line 2", width: 150, sortable: true, dataIndex: "ADDR_02" },
                                                  { header: "Address Line 3", width: 150, sortable: true, dataIndex: "ADDR_03" },
                                                  { header: "Phone No.", width: 150, sortable: true, dataIndex: "PHONE_01" },
                                                  { header: "Mobile No.", width: 150, sortable: true, dataIndex: "PHONE_02" },
                                                  { header: "Other Phone No.", width: 150, sortable: true, dataIndex: "PHONE_03" },
                                                  { header: "Fax No.", width: 150, sortable: true, dataIndex: "FAX_01" },
                                                  { header: "Other Fax No.", width: 150, sortable: true, dataIndex: "FAX_01" },
                                                  { header: "Contact Person", width: 150, sortable: true, dataIndex: "CON_NAME" },
                                                  { header: "Contact Number", width: 150, sortable: true, dataIndex: "CON_PHONE" },
                                                  { header: "Website", width: 150, sortable: true, dataIndex: "WEBSITE" },
                                                  { header: "Email", width: 150, sortable: true, dataIndex: "EMAIL" }
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

 					     	handler: ogs_school.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '<?=base_url()?>images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_school.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '<?=base_url()?>images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_school.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ogs_school.app.Grid = grid;
 			ogs_school.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'School',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ogs_school.app.Grid],
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
 		        url:"<?=site_url("filereference/addSchool")?>",
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
 		            fieldLabel: 'School Name*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'SCHOOL',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'SCHOOL'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Initials*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'SCHOOL_INI',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'SCHOOL_INI'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Address Line 01*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'ADDR_01',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'ADDR_01'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Address Line 02*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'ADDR_02',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'ADDR_02'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Address Line 03*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'ADDR_03',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'ADDR_03'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Telephone No.*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'PHONE_01',
 		            allowBlank:false,
                            maskRe: /[\d-]/,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'PHONE_01'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Mobile No.*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'PHONE_02',
                             maskRe: /[\d-]/,
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'PHONE_02'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Other Phone No.',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'PHONE_03',
                             maskRe: /[\d-]/,
 		            //allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'PHONE_03'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Fax No.*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'FAX_01',
 		            allowBlank:false,
                             maskRe: /[\d-]/,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FAX_01'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Other Fax No.',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'FAX_02',
                             maskRe: /[\d-]/,
 		            //allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FAX_02'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Contact Person*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'CON_NAME',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'CON_NAME'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Contact Phone No.*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'CON_PHONE',
 		            allowBlank:false,
                             maskRe: /[\d-]/,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'CON_PHONE'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Website',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'WEBSITE',
 		            //allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'WEBSITE'
 		        },
                        {
                            xtype:'textfield',
 		            fieldLabel: 'Email',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'EMAIL',
 		            //allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'EMAIL',
                            vtype: 'email'
 		        }

 		        ]
 					}
 		        ]
 		    });

 		    ogs_school.app.Form = form;
 		},
 		Add: function(){

 			ogs_school.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New School',
 		        width: 510,
 		        height:500,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_school.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_school.app.Form)){//check if all forms are filled up

 		                ogs_school.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ogs_school.app.Grid.getId());
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


 			if(ExtCommon.util.validateSelectionGrid(ogs_school.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ogs_school.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.SCHOIDNO;

 			ogs_school.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update School',
 		        width: 510,
 		        height:500,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_school.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_school.app.Form)){//check if all forms are filled up
 		                ogs_school.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updateSchool")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ogs_school.app.Grid.getId());
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

 		  	ogs_school.app.Form.getForm().load({
 				url: "<?=site_url("filereference/loadSchool")?>",
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


			if(ExtCommon.util.validateSelectionGrid(ogs_school.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ogs_school.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.SCHOIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("filereference/deleteSchool")?>",
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
							ogs_school.app.Grid.getStore().load({params:{start:0, limit: 25}});

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

 Ext.onReady(ogs_school.app.init, ogs_school.app);

</script>
<div id="mainBody"></div>
