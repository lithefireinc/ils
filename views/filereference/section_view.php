<script type="text/javascript">
 Ext.namespace("ogs_section");
 ogs_section.app = function()
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
 							url: "<?=site_url("filereference/getSection")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "SECTIDNO"},
 											{ name: "SECTION"},
                                                                                        { name: "DESCRIPTIO"},
                                                                                        { name: "YEAR"},
                                                                                        { name: "SECTORDER"},
                                                                                        { name: "MALE"},
                                                                                        { name: "FEMALE"},
                                                                                        { name: "STUDCOUNT"},
                                                                                        { name: "COURIDNO"},
                                                                                        { name: "COURSE"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ogs_sectiongrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    { header: "Id", width: 75, sortable: true, dataIndex: "SECTIDNO" },
 						  { header: "Section", width: 200, sortable: true, dataIndex: "SECTION" },
                                                  { header: "Description", width: 200, sortable: true, dataIndex: "DESCRIPTIO" },
                                                  { header: "Year", width: 75, sortable: true, dataIndex: "YEAR" },
                                                  { header: "Order", width: 75, sortable: true, dataIndex: "SECTORDER" },
                                                  { header: "Male Students", width: 100, sortable: true, dataIndex: "MALE" },
                                                  { header: "Female Students", width: 100, sortable: true, dataIndex: "FEMALE" },
                                                  { header: "Number of Students", width: 120, sortable: true, dataIndex: "STUDCOUNT" },
                                                  { header: "Course", width: 250, sortable: true, dataIndex: "COURSE" }
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

 					     	handler: ogs_section.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '<?=base_url()?>images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_section.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '<?=base_url()?>images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_section.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ogs_section.app.Grid = grid;
 			ogs_section.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Section',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ogs_section.app.Grid],
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
 		        url:"<?=site_url("filereference/addSection")?>",
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
 		            fieldLabel: 'Section*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'SECTION',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'SECTION'
 		        },
                        ogs_section.app.studentLevelCombo(),
                        {

                            xtype:'textfield',
 		            fieldLabel: 'Order*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "2"},
 		            name: 'SECTORDER',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'SECTORDER'
 		        },
                        {
                            xtype:'textarea',
 		            fieldLabel: 'Description*',
                            maxLength: 47,
 		            name: 'DESCRIPTIO',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'DESCRIPTIO'
 		        },{

                            xtype:'numberfield',
 		            fieldLabel: 'Male*',
 		            name: 'MALE',
                            readOnly: true,
                            value: 0,
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'MALE',
                            listeners: {
                                change: function(){
                                    Ext.getCmp("STUDCOUNT").setValue(this.getValue()+Ext.getCmp("FEMALE").getValue());
                                },
                                blur: function(){
                                    Ext.getCmp("STUDCOUNT").setValue(this.getValue()+Ext.getCmp("FEMALE").getValue());
                                }
                            }
 		        },{

                            xtype:'numberfield',
 		            fieldLabel: 'Female*',
 		            name: 'FEMALE',
                            readOnly: true,
                            value: 0,
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'FEMALE',
                            listeners: {
                                change: function(){
                                    Ext.getCmp("STUDCOUNT").setValue(this.getValue()+Ext.getCmp("MALE").getValue());
                                },
                                blur: function(){
                                    Ext.getCmp("STUDCOUNT").setValue(this.getValue()+Ext.getCmp("MALE").getValue());
                                }
                            }
 		        },
                        {
                            xtype:'numberfield',
 		            fieldLabel: 'Number of Students*',
                            readOnly: true,
                            value: 0,
 		            name: 'STUDCOUNT',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'STUDCOUNT'
 		        },
                        ogs_section.app.courseCombo()

 		        ]
 					}
 		        ]
 		    });

 		    ogs_section.app.Form = form;
 		},
 		Add: function(){

 			ogs_section.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Section',
 		        width: 510,
 		        height:400,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_section.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_section.app.Form)){//check if all forms are filled up

 		                ogs_section.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ogs_section.app.Grid.getId());
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


 			if(ExtCommon.util.validateSelectionGrid(ogs_section.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ogs_section.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.SECTIDNO;

 			ogs_section.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Section',
 		        width: 510,
 		        height:400,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_section.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '<?=base_url()?>images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_section.app.Form)){//check if all forms are filled up
 		                ogs_section.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updateSection")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ogs_section.app.Grid.getId());
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

 		  	ogs_section.app.Form.getForm().load({
 				url: "<?=site_url("filereference/loadSection")?>",
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


			if(ExtCommon.util.validateSelectionGrid(ogs_section.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ogs_section.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.SECTIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("filereference/deleteSection")?>",
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
							ogs_section.app.Grid.getStore().load({params:{start:0, limit: 25}});

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
        courseCombo: function(){

		return {
			xtype:'combo',
			id:'COURIDNO',
			//hiddenName: 'COURIDNO',
			name: 'COURIDNO',
			valueField: 'id',
			displayField: 'name',
			//width: 100,
			anchor: '93%',
			triggerAction: 'all',
			minChars: 2,
			forceSelection: true,
			enableKeyEvents: true,
			pageSize: 10,
			resizable: true,
			readOnly: false,
			minListWidth: 300,
			allowBlank: false,
			store: new Ext.data.JsonStore({
			id: 'idsocombo',
			root: 'data',
			totalProperty: 'totalCount',
			fields:[{name: 'id'}, {name: 'name'}],
			url: "<?php echo site_url("filereference/getCourseCombo"); ?>",
			baseParams: {start: 0, limit: 10}

			}),
			listeners: {
			beforequery: function(qe){
				delete qe.combo.lastQuery;
			},	
			select: function (combo, record, index){
			this.setRawValue(record.get('name'));
			Ext.getCmp(this.id).setValue(record.get('name'));

			},
			blur: function(){
			var val = this.getRawValue();
			this.setRawValue.defer(1, this, [val]);
			this.validate();
			},
			render: function() {
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a course'});

			},
			keypress: {buffer: 100, fn: function() {
			//Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Course*'

			}
	},
        studentLevelCombo: function(){

		return {
			xtype:'combo',
			id:'YEAR',
			//hiddenName: 'COURIDNO',
			name: 'YEAR',
			valueField: 'id',
			displayField: 'name',
			//width: 100,
			anchor: '93%',
			triggerAction: 'all',
			minChars: 2,
			forceSelection: true,
			enableKeyEvents: true,
			pageSize: 10,
			resizable: true,
			readOnly: false,
			minListWidth: 300,
			allowBlank: false,
			store: new Ext.data.JsonStore({
			id: 'idsocombo',
			root: 'data',
			totalProperty: 'totalCount',
			fields:[{name: 'id'}, {name: 'name'}],
			url: "<?php echo site_url("filereference/getStudentLevelCombo"); ?>",
			baseParams: {start: 0, limit: 10}

			}),
			listeners: {
			beforequery: function(qe){
				delete qe.combo.lastQuery;
			},	
			select: function (combo, record, index){
			this.setRawValue(record.get('name'));
			Ext.getCmp(this.id).setValue(record.get('name'));

			},
			blur: function(){
			var val = this.getRawValue();
			this.setRawValue.defer(1, this, [val]);
			this.validate();
			},
			render: function() {
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a course'});

			},
			keypress: {buffer: 100, fn: function() {
			//Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Year Level*'

			}
	}//end of functions
 	}

 }();

 Ext.onReady(ogs_section.app.init, ogs_section.app);

</script>
<div id="mainBody"></div>
