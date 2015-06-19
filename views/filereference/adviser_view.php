<script type="text/javascript">
 Ext.namespace("ogs_adviser");
 ogs_adviser.app = function()
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
 							url: "<?=site_url("filereference/getAdviser")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "ADVIIDNO"},
 											{ name: "ADVISER"},
                                                                                        { name: "SCHOOL"},
                                                                                        { name: "YEAR"},
                                                                                        { name: "SECTION"},
                                                                                        { name: "ROOM"},
                                                                                        {name: "IDNO"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ogs_advisergrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                  { header: "Id", width: 75, sortable: true, dataIndex: "ADVIIDNO" },
 						  { header: "Adviser", width: 250, sortable: true, dataIndex: "ADVISER" },
                                                  { header: "Id Number", width: 250, sortable: true, dataIndex: "IDNO" }
                                                 // { header: "School", width: 250, sortable: true, dataIndex: "SCHOOL" }
                                                /*,
                                                  { header: "Year Level", width: 100, sortable: true, dataIndex: "YEAR" },
                                                  { header: "Section", width: 150, sortable: true, dataIndex: "SECTION" },
                                                  { header: "Room", width: 150, sortable: true, dataIndex: "ROOM" }*/
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

 					     	handler: ogs_adviser.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '/images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_adviser.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ogs_adviser.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ogs_adviser.app.Grid = grid;
 			ogs_adviser.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Adviser',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ogs_adviser.app.Grid],
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
 		        labelWidth: 100,
 		        url:"<?=site_url("filereference/addAdviser")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,

 		        items: [ {
 					xtype:'fieldset',
 					title:'Fields w/ Asterisks are required.',
 					width:'auto',
 					height:'auto',
 					items:[{
          			            layout:'column',
          			            width: 'auto',
          			            items: [
                                                  {
          	 	 			          columnWidth:.66,
          	 	 			          layout: 'form',
          	 	 			          items: [
                        {

                            xtype:'textfield',
 		            fieldLabel: 'Adviser*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "47"},
 		            name: 'ADVISER',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'ADVISER'
 		        },
                        {

                            xtype:'textfield',
 		            fieldLabel: 'Id Number*',
                            autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "15"},
 		            name: 'IDNO',
 		            allowBlank:false,
 		            anchor:'93%',  // anchor width by percentage
 		            id: 'IDNO'
 		        },
 		        {xtype: 'textfield',
                                fieldLabel: 'First Name*',
                                name: 'FIRSTNAME',
                                id: 'FIRSTNAME',
                                allowBlank:false,
                                anchor: '93%'
                                },
                                {xtype: 'textfield',
                                fieldLabel: 'Middle Name*',
                                name: 'MIDDLENAME',
                                id: 'MIDDLENAME',
                                allowBlank:false,
                                anchor: '93%'
                                },
                                {xtype: 'textfield',
                                fieldLabel: 'Last Name*',
                                name: 'LASTNAME',
                                id: 'LASTNAME',
                                allowBlank:false,
                                anchor: '93%'
                                },
                                new Ext.form.ComboBox(
	 		 			      				   {

	 		 	 			       	   	         store: new Ext.data.SimpleStore(
	 		 	 			       		            {
	 		 	 			       		               fields: ['field', 'value'],
	 		 	 			       		               data : [['001', 'Male'],['002', 'Female']]
	 		 	 			          		         }),
	 		 	 			       	   	         	valueField:'field',
	 		 	 			       		            displayField:'value',
                                                                                    fieldLabel: 'Gender*',
	 		 	 			          		    name: 'GENDER_',
	 		 	 			       		            id: 'GENDER_',
                                                                                    hiddenName:'GENDER',
                                                                                    hiddenId:'GENDER',
	 		 	 			       		            editable: false,
	 		 	 			       		            mode: 'local',
	 		 	 			       		            anchor: '93%',
	 		 	 			       		            triggerAction: 'all',
	 		 	 			          		    selectOnFocus: true,
                                                                                    allowBlank: false,
	 		 	 			       		            forceSelection:true,
	 		 	 			       		            tabIndex: 0,
	 		 	 			       		            listeners: {
                                                                                    select: function(combo, record, index){

                                                                                    }
	 		 	 			       		            }
	 		 	 			          		    }),
                                                {xtype: 'datefield',
		 	 			        fieldLabel: 'Date of Birth*',
		 	 			        name: 'BIRTHDATE',
		 	 			        id: 'BIRTHDATE',
		 	 			        allowBlank:false,
		 	 			        format: 'Y-m-d',
		 	 			        anchor: '93%',
                                                        maxValue: new Date()

		 	 			    },
		 	 			    {xtype: 'hidden',
		 	 			    id: 'ADVIPICTURE',
		 	 			    name: 'ADVIPICTURE'
		 	 			    
		 	 			    }
		 	 			     ]
		 	 			     },
		 	 			     {
          	 	 			          columnWidth:.33,
          	 	 			          layout: 'form',
          	 	 			          items: [
		 	 			     new Ext.BoxComponent({

          						    width: 130,
                                                              //anchor: '95%',

          						    height: 130,

          						    id: 'emp_photo',

          						    name: 'emp_photo',

          						    autoEl: {tag: 'img', src: '/images/icon_pic.jpg'}

          					}),
          					{xtype: 'button', text: 'Add Picture',
          					id: "upload_btn",
          					disabled: true,
          					style: {marginTop: '16.5px'},
                                                    icon: '/images/icons/picture_add.png',
          	 	 			        	handler: function () {
                                                                    var attform = new Ext.form.FormPanel({
											id: 'atthform',
											name: 'atthform',
											method: 'POST',
											height: 110,
											width: 342,
											labelWidth: 150,
											frame: true,
											fileUpload: true,
											items: [

												{
													xtype: 'textfield',
											    	fieldLabel: 'Select file to upload',
											    	name: 'file',
											    	id: 'file',
											    	inputType: 'file',
											    	disableKeyFilter: true,
                                                                                                autoCreate: {tag: 'input', type: 'text', size: '200', autocomplete: 'off'}
												},
                                                                                                {
                                                                                                    xtype: 'hidden',
                                                                                                    name: 'ADVIIDNO',
                                                                                                    id: 'ADVIIDNO'
                                                                                                }
											]
										});

										var watth = new Ext.Window({
											title: "Add Picture",
											width: 360,
											height: 120,
											modal: true,
											plain: true,
											buttonAlign: 'right',
											bodyStyle: 'padding:5px;',
											resizable: false,
											layout: 'fit',
											items: [attform],
											buttons: [
												{
													text: "Upload",
													icon: '/images/icons/picture_save.png',
cls:'x-btn-text-icon',

													handler: function(){
														if (!attform.form.isValid()){
						        							Ext.Msg.show({
						        								title: "Error!",
						        								msg: "Please fill-in required fields (Marked Red)!",
						        								icon: Ext.Msg.ERROR,
						        								buttons: Ext.Msg.OK
						        							});
						        							return;
						        						}



						        						attform.form.submit({
						        							url: '<?php echo site_url("admin/uploadPhoto"); ?>',
						        							
						        							method: 'POST',
						        							success: function(f,a){
						        								Ext.Msg.show({
						        									title: 'Success',
						        									msg: a.result.data, //"An error has occured while trying to save the record!",
						        									icon: Ext.Msg.INFO,
						        									buttons: Ext.Msg.OK
						        								});
                                                                 Ext.getCmp('emp_photo').getEl().dom.src = a.result.filename;
																 Ext.getCmp('ADVIPICTURE').setValue(a.result.filename);
													            watth.close();
						        							},
						        							failure: function(f,a){
						        								Ext.Msg.show({
						        									title: 'Error Alert',
						        									msg: a.result.data, //"An error has occured while trying to save the record!",
						        									icon: Ext.Msg.ERROR,
						        									buttons: Ext.Msg.OK
						        								})
						        							},
						        							waitMsg: 'Saving data...'
						        						})
													}
												},{
													text: "Cancel",
													icon: '/images/icons/cancel.png',
                                                                                                        cls:'x-btn-text-icon',

													handler: function() {
														watth.close();
													}
												}
											]
										});
										watth.show();
          	 	  			            

                                                                }

              	 	  	                }
          					]
          					}
          					]
          				}
                        //ogs_adviser.app.schoolCombo()
                        /*,
                        ogs_adviser.app.studentLevelCombo(),
                        ogs_adviser.app.sectionCombo(),
                        ogs_adviser.app.roomCombo()*/

 		        ]
 					}
 		        ]
 		    });

 		    ogs_adviser.app.Form = form;
 		},
 		Add: function(){

 			ogs_adviser.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Adviser',
 		        width: 610,
 		        height:310,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_adviser.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_adviser.app.Form)){//check if all forms are filled up

 		                ogs_adviser.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ogs_adviser.app.Grid.getId());
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


 			if(ExtCommon.util.validateSelectionGrid(ogs_adviser.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ogs_adviser.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.ADVIIDNO;

 			ogs_adviser.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Adviser',
 		        width: 610,
 		        height:310,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ogs_adviser.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ogs_adviser.app.Form)){//check if all forms are filled up
 		                ogs_adviser.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updateAdviser")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ogs_adviser.app.Grid.getId());
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

 		  	ogs_adviser.app.Form.getForm().load({
 				url: "<?=site_url("filereference/loadAdviser")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
                                    _window.show();
                                    Ext.getCmp('upload_btn').enable();
                                     Ext.getCmp('emp_photo').getEl().dom.src = action.result.data.ADVIPICTURE;
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


			if(ExtCommon.util.validateSelectionGrid(ogs_adviser.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ogs_adviser.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.ADVIIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("filereference/deleteAdviser")?>",
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
							ogs_adviser.app.Grid.getStore().load({params:{start:0, limit: 25}});

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
			allowBlank: true,
			store: new Ext.data.JsonStore({
			id: 'idsocombo',
			root: 'data',
			totalProperty: 'totalCount',
			fields:[{name: 'id'}, {name: 'name'}],
			url: "<?php echo site_url("filereference/getStudentLevelCombo"); ?>",
			baseParams: {start: 0, limit: 10}

			}),
			listeners: {
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
			fieldLabel: 'Year Level'

			}
	},
        schoolCombo: function(){

		return {
			xtype:'combo',
			id:'IDNO',
			//hiddenName: 'COURIDNO',
			name: 'IDNO',
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
			url: "<?php echo site_url("filereference/getSchoolCombo"); ?>",
			baseParams: {start: 0, limit: 10}

			}),
			listeners: {
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a school'});

			},
			keypress: {buffer: 100, fn: function() {
			//Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'School*'

			}
	},
        sectionCombo: function(){

		return {
			xtype:'combo',
			id:'SECTION',
			//hiddenName: 'COURIDNO',
			name: 'SECTION',
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
			allowBlank: true,
			store: new Ext.data.JsonStore({
			id: 'idsocombo',
			root: 'data',
			totalProperty: 'totalCount',
			fields:[{name: 'id'}, {name: 'name'}],
			url: "<?php echo site_url("filereference/getSectionCombo"); ?>",
			baseParams: {start: 0, limit: 10}

			}),
			listeners: {
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a school'});

			},
			keypress: {buffer: 100, fn: function() {
			//Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Section'

			}
	},
        roomCombo: function(){

		return {
			xtype:'combo',
			id:'ROOM',
			//hiddenName: 'COURIDNO',
			name: 'ROOM',
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
			allowBlank: true,
			store: new Ext.data.JsonStore({
			id: 'idsocombo',
			root: 'data',
			totalProperty: 'totalCount',
			fields:[{name: 'id'}, {name: 'name'}],
			url: "<?php echo site_url("filereference/getRoomCombo"); ?>",
			baseParams: {start: 0, limit: 10}

			}),
			listeners: {
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a school'});

			},
			keypress: {buffer: 100, fn: function() {
			//Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Room'

			}
	}//end of functions
 	}

 }();

 Ext.onReady(ogs_adviser.app.init, ogs_adviser.app);

</script>
<div id="mainBody"></div>
