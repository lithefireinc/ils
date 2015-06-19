<script type="text/javascript">
 Ext.namespace("hrisv2_user_admin");
 hrisv2_user_admin.app = function()
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
 							url: "<?=site_url("userMatrix/getUsers")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "id"},
 											{ name: "username" },
 											{ name: "user_type" },
 											{name: "name"},
                                                                                        {name: "code"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'hrisv2_user_admingrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
 						  { header: "Id", dataIndex: "id", width: 100, sortable: true},
 						  { header: "User Name", width: 200, sortable: true, dataIndex: "username" },
 						 //{ header: "Name", width: 300, sortable: true, dataIndex: "name" },
                                                 { header: "User Type", width: 200, sortable: true, dataIndex: "user_type" }

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
 					     	text: 'EDIT USER NAME',
							icon: '/images/icons/user_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: hrisv2_user_admin.app.Edit

 					 	},{
 					     	xtype: 'tbbutton',
 					     	text: 'CHANGE PASSWORD',
							icon: '/images/icons/lock_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: hrisv2_user_admin.app.changePassword

 					 	}
 	    			 ]
 	    	});

 			hrisv2_user_admin.app.Grid = grid;
 			hrisv2_user_admin.app.Grid.getStore().load({params:{start: 0, limit: 25}});


 			var _window = new Ext.Panel({
 		        title: 'User Administration',
 		        width: '100%',
 		        height:400,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [hrisv2_user_admin.app.Grid],
 		        resizable: false
 	        });

 	        _window.render();


 		},
 			setForm: function(){

 		    var form = new Ext.form.FormPanel({
 		        labelWidth: 150,
 		        url:"<?=site_url("userMatrix/updateUserName")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,
 		        height: 100,

 		        items: [ {
 					xtype:'fieldset',
 					title:'Fields w/ Asterisks are required.',
 					width:'auto',
 					height:'auto',
 					defaults: {

 				        anchor: '95%',
 				        allowBlank: false
 				      },
 				      defaultType: 'textfield',

 					items:[
 					      {
 				        fieldLabel: 'User Name*',
 				        name: 'username',
 				        id: 'username'
 					      },
                     /*                         {
 				        fieldLabel: 'Name',
 				        name: 'name',
 				        id: 'name',
                                        readOnly: true,
                                        allowBlank: true
 					      },*/
                                              hrisv2_user_admin.app.userTypeCombo()
                                              /*,
                                              ,
                                              hrisv2_user_admin.app.studentCombo(),
                                              hrisv2_user_admin.app.teacherCombo()*/

 		        ]
 					}
 		        ]
 		    });

 		    hrisv2_user_admin.app.Form = form;
 		},
 		Add: function(){

 			hrisv2_user_admin.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'Edit User Information',
 		        width: 410,
 		        height:280,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: hrisv2_user_admin.app.Form,
 		        buttons: [{
 		         	text: 'Save',
 		         	icon: '/images/icons/disk.png',
					cls:'x-btn-text-icon',
 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(hrisv2_user_admin.app.Form)){//check if all forms are filled up

 		                hrisv2_user_admin.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(hrisv2_user_admin.app.Grid.getId());
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
 		            icon: '/images/icons/cancel.png',
					cls:'x-btn-text-icon',
 		            handler: function(){
 			            _window.destroy();
 		            }
 		        }]
 		    });
 		  	_window.show();
 		},
 		Edit: function(){


 			if(ExtCommon.util.validateSelectionGrid(hrisv2_user_admin.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = hrisv2_user_admin.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.id;
                        var user_type = sm.getSelected().data.code;
                        
 			hrisv2_user_admin.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update User Type',
 		        width: 510,
 		        height:190,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: hrisv2_user_admin.app.Form,
 		        buttons: [{
 		         	text: 'Save',
 		         	icon: '/images/icons/disk.png',
					cls:'x-btn-text-icon',
 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(hrisv2_user_admin.app.Form)){//check if all forms are filled up
 		                hrisv2_user_admin.app.Form.getForm().submit({
 			                url: "<?=site_url("userMatrix/updateUserName")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(hrisv2_user_admin.app.Grid.getId());
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
 		            icon: '/images/icons/cancel.png',
					cls:'x-btn-text-icon',
 		            handler: function(){
 			            _window.destroy();
 		            }
 		        }]
 		    });

 		  
 		  	


 		  	hrisv2_user_admin.app.Form.getForm().load({
 				url: "<?=site_url("userMatrix/loadUser")?>",
 				method: 'POST',
 				params: {id: id, user_type: user_type},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
 			 	 _window.show();
                                 Ext.get('USERTYPE').dom.value = action.result.data.code;
                                 /*
                                 switch(action.result.data.code){
                                     case 'STUD': Ext.getCmp("student").enable();
                                                  Ext.get('STUDIDNO').dom.value=action.result.data.STUDIDNO;
                                                  Ext.getCmp('student').setRawValue(action.result.data.name);
                                                  break;
                                     case 'FACU': Ext.getCmp("ADVISER").enable();
                                                  Ext.get('ADVIIDNO').dom.value=action.result.data.STUDIDNO;
                                                  Ext.getCmp('ADVISER').setRawValue(action.result.data.name);
                                                  break;
                                 }*/
                                 
 				},
 				failure: function(form, action) {
         					Ext.Msg.show({
 									title: 'Error Alert',
 									msg: "A connection to the server could not be established",
 									icon: Ext.Msg.ERROR,
 									buttons: Ext.Msg.OK,
 									fn: function(){ _window.close(); }
 								});
     			}
 			});
 			}else return;
 		},
		Delete: function(){


			if(ExtCommon.util.validateSelectionGrid(hrisv2_user_admin.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = hrisv2_user_admin.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.id;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
							url: "functions/deletehrisv2_user_admin.php",
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
							hrisv2_user_admin.app.Grid.getStore().load({params:{start:0, limit: 25}});

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
		userTypeCombo: function(){
			return {
				xtype:'combo',
				id:'user_type',
				hiddenName: 'USERTYPE',
                hiddenId: 'USERTYPE',
				name: 'user_type',
				valueField: 'id',
				displayField: 'name',
				anchor: '95%',
				triggerAction: 'all',
				minChars: 2,
				forceSelection: true,
				enableKeyEvents: true,
				pageSize: 10,
				resizable: true,
				//readOnly: true,
				minListWidth: 100,
				allowBlank: false,
				store: new Ext.data.JsonStore({
					id: 'idpurposecombo',
					root: 'data',
					totalProperty: 'totalCount',
					fields:[{name: 'id'}, {name: 'name'}],
					url: "<?=site_url("userMatrix/getUserTypeCombo")?>",
					params: {start: 0, limit: 10}

				}),
				listeners: {
					select: function (combo, record, index){
						this.setRawValue(record.get('name'));
						Ext.get(this.hiddenName).dom.value  = record.get('id');
                                               
                                           /* switch(record.get('id')){
                                             case 'STUD': Ext.getCmp("student").enable();
                                                          Ext.getCmp("ADVISER").disable();
                                                          //Ext.get('STUDIDNO').dom.value=action.result.data.STUDIDNO;
                                                          //Ext.getCmp('student').setRawValue(action.result.data.name);
                                                          break;
                                             case 'FACU': Ext.getCmp("ADVISER").enable();
                                                          Ext.getCmp("student").disable();
                                                         // Ext.get('ADVIIDNO').dom.value=action.result.data.STUDIDNO;
                                                         // Ext.getCmp('ADVISER').setRawValue(action.result.data.name);
                                                          break;
                                             case 'ADMIN': Ext.getCmp("ADVISER").disable();
                                                          Ext.getCmp("student").disable();
                                                         // Ext.get('ADVIIDNO').dom.value=action.result.data.STUDIDNO;
                                                         // Ext.getCmp('ADVISER').setRawValue(action.result.data.name);
                                                          break;
                                        }*/
                                        
					},
					blur: function(){
						var val = this.getRawValue();
						this.setRawValue.defer(1, this, [val]);
						this.validate();
					},
					render: function() {
						this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a User Type'});

					},
					keypress: {buffer: 100, fn: function() {
						Ext.get(this.hiddenName).dom.value  = '';
						if(!this.getRawValue()){
							this.doQuery('', true);
						}
					}}
				},
				fieldLabel: 'User Type*'

			}
			},
                        studentCombo: function(){

		return {
			xtype:'combo',
			id:'student',
			hiddenName: 'STUDIDNO',
                        hiddenId: 'STUDIDNO',
			name: 'student',
			valueField: 'id',
			displayField: 'name',
			//width: 100,
			anchor: '95%',
                        disabled: true,
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
			fields:[{name: 'id'}, {name: 'name'}, {name: 'STUDIDNO'}],
			url: "<?php echo site_url("studentProfile/getStudent"); ?>",
			baseParams: {start: 0, limit: 10}

			}),
			listeners: {
			select: function (combo, record, index){

                       // gradeView.app.Grid.getStore().setBaseParam("STUDIDNO", record.get("STUDIDNO"));

                            Ext.getCmp('studentForm').getForm().load({
				url: '<?php echo site_url("studentProfile/loadStudent"); ?>',
                                params:{id: record.get('id')},
                                method: 'POST',
				success: function(f,a){
                                    Ext.getCmp("student").setRawValue(a.result.data.NAME);
				},
				failure: function(f,a){

				},
				waitMsg: 'Loading data...'
				});
                        this.setRawValue(record.get('name'));
			Ext.get(this.hiddenName).dom.value  = record.get('id');

			},
			blur: function(){
			var val = this.getRawValue();
			this.setRawValue.defer(1, this, [val]);
			this.validate();
			},
			render: function() {
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a student'});

			},
			keypress: {buffer: 100, fn: function() {
			//Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Student*'

			}
	},
        changePassword: function(){
                            if(ExtCommon.util.validateSelectionGrid(hrisv2_user_admin.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = hrisv2_user_admin.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.id;
                        var user_type = sm.getSelected().data.code;

							var form = new Ext.form.FormPanel({
				 		        labelWidth: 150,
				 		        url:'<?=site_url("userMatrix/changePassword")?>',
				 		        method: 'POST',
				 		        defaultType: 'textfield',
				 		        frame: true,
				 		        height: 100,

				 		        items: [ {
				 					xtype:'fieldset',
				 					title:'Please Confirm',
				 					width:'auto',
				 					height:'auto',
				 					defaults: {

				 				        anchor: '95%',
				 				        allowBlank: false
				 				      },
				 				      defaultType: 'textfield',

				 					items:[
						 			  {
				 				        fieldLabel: 'New Password*',
				 				        inputType: 'password',
				 				        name: 'new_pass',
				 				        allowBlank: false,
				 				        id: 'new_pass'
				 				      },{
				 				        fieldLabel: 'Confirm Password*',
				 				        inputType: 'password',
                                                                        id: 'confirm_pass',
				 				        name: 'confirm_pass',
				 				        vtype: 'password',
                                                                        enableKeyEvents: true,
				 				        allowBlank: false,
				 				        initialPassField: 'new_pass', // id of the initial password field
                                                                        listeners: {
                                                                            specialkey: function(f, e){
                                                                            if(e.getKey() == e.ENTER){
                                                                                if(ExtCommon.util.validateFormFields(form)){//check if all forms are filled up

				 		                form.getForm().submit({
					 		                params: {id: id, admin: 1},
				 			                success: function(f,action){
				                 		    	Ext.MessageBox.alert('Status', action.result.data);
				                  		    	 Ext.Msg.show({
				  								     title: 'Status',
				 								     msg: action.result.data,
				  								     buttons: Ext.Msg.OK,
				  								     icon: Ext.Msg.INFO
				  								 });

				 				                _pwwindow.destroy();
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
                                                                            }
                                                                        }
				 				      }

				 		        ]
				 					}
				 		        ]
				 		    });

				 		    var _pwwindow = new Ext.Window({
				 		        title: 'Change Password',
				 		        width: 510,
				 		        height:180,
				 		        layout: 'fit',
				 		        plain:true,
				 		        modal: true,
				 		        bodyStyle:'padding:5px;',
				 		        buttonAlign:'center',
				 		        items: form,
				 		        buttons: [{
				 		         	text: 'Save',
				 		         	icon: '/images/icons/disk.png',
					cls:'x-btn-text-icon',
				 	                handler: function () {
				 			            if(ExtCommon.util.validateFormFields(form)){//check if all forms are filled up

				 		                form.getForm().submit({
					 		                params: {id: id},
				 			                success: function(f,action){
				                 		    	Ext.MessageBox.alert('Status', action.result.data);
				                  		    	 Ext.Msg.show({
				  								     title: 'Status',
				 								     msg: action.result.data,
				  								     buttons: Ext.Msg.OK,
				  								     icon: Ext.Msg.INFO
				  								 });

				 				                _pwwindow.destroy();
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
				 		            icon: '/images/icons/cancel.png',
									cls:'x-btn-text-icon',
				 		            handler: function(){
				 			            _pwwindow.destroy();
				 		            }
				 		        }]
				 		    });
				 		  	_pwwindow.show();
                                                           }else return;
  						}//end of functions
 	}

 }();

 Ext.onReady(hrisv2_user_admin.app.init, hrisv2_user_admin.app);

</script>
<div id="mainBody">
</div>