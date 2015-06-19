<script type="text/javascript">
 Ext.namespace("usermatrix");
 usermatrix.app = function()
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
 							url: "<?php echo site_url("userMatrix/getModuleGroup");?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "id", mapping: "id" },
 											{ name: "description" }
 										]
 						}),
 						remoteSort: true,
 						params: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'usermatrixgrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
 						  { header: "Id", dataIndex: "id", width: 100, sortable: true},
 						  { header: "Group", width: 300, sortable: true, dataIndex: "description" }
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
 				tbar: [
 					    {
 					     	xtype: 'tbfill'
 					 	},{
					     	xtype: 'tbbutton',
							icon: '/images/icons/application_add.png',
							cls:'x-btn-text-icon',
					     	text: 'ADD',
					     	handler: usermatrix.app.Add

					 	}, '-', {
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '/images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: usermatrix.app.Edit

 					 	}, '-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: usermatrix.app.Delete

 					 	}, '-', {
					     	xtype: 'tbbutton',

					     	text: 'USERS',
					     	icon: '/images/icons/user.png',
					     	cls:'x-btn-text-icon',

					     	handler: usermatrix.app.UserGrid

					 	}, '-', {
					     	xtype: 'tbbutton',
					     	icon: '/images/icons/application_key.png',
					     		cls:'x-btn-text-icon',

					     	text: 'MODULE ACCESS',
					     	handler: usermatrix.app.ModuleGrid

					 	}
 	    			 ]
 	    	});

 			usermatrix.app.Grid = grid;
 			usermatrix.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			//var msgbx = Ext.MessageBox.wait("Redirecting to main page. . .","Status");


 			var _window = new Ext.Panel({
 		        title: 'User Matrix',
 		        width: '100%',
 		        height:480,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [usermatrix.app.Grid],
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
 		        url:"<?php echo site_url("userMatrix/insertModuleGroup"); ?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,
 		        height: 100,

 		        items: [ {
 					xtype:'fieldset',
 					title:'Fields w/ Asterisks are required.',
 					width:370,
 					height:80,
 					items:[{
 					xtype:'textfield',
 		            fieldLabel: 'Group*',
                     maxLength:100,
 		            name: 'description',
 		            allowBlank:false,
 		            anchor:'90%',  // anchor width by percentage
 		            id: 'description'
 		        }

 		        ]
 					}
 		        ]
 		    });

 		    usermatrix.app.Form = form;
 		},
 		Add: function(){

 			usermatrix.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Group',
 		        width: 410,
 		        height:180,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: usermatrix.app.Form,
 		        buttons: [{
 		         	text: 'Save',
 		         	icon: '/images/icons/disk.png',
					cls:'x-btn-text-icon',
 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(usermatrix.app.Form)){//check if all forms are filled up

 		                usermatrix.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: Ext.Msg.INFO
  								 });
 				                ExtCommon.util.refreshGrid(usermatrix.app.Grid.getId());
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


 			if(ExtCommon.util.validateSelectionGrid(usermatrix.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = usermatrix.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.id;

 			usermatrix.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Group',
 		        width: 410,
 		        height:180,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: usermatrix.app.Form,
 		        buttons: [{
 		         	text: 'Save',
 		         	icon: '/images/icons/disk.png',
					cls:'x-btn-text-icon',
 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(usermatrix.app.Form)){//check if all forms are filled up
 		                usermatrix.app.Form.getForm().submit({
 			                url: "<?php echo site_url("userMatrix/updateModuleGroup"); ?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(usermatrix.app.Grid.getId());
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


 		  	usermatrix.app.Form.form.load({
							url:"<?php echo site_url("userMatrix/loadModuleGroup"); ?>",
							waitMsg:'Loading...',
                                                        params:{id: id},
							success: function(f, action){
                                                           // alert(action.result.data.firstName);

                                                            _window.show();
                                                            //Ext.getCmp('emp_photo').getEl().dom.src = action.result.data.filename;
							}

						});
 			}else return;
 		},
		Delete: function(){


			if(ExtCommon.util.validateSelectionGrid(usermatrix.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = usermatrix.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.id;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
							url: "<?php echo site_url("userMatrix/deleteModuleGroup"); ?>",
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
							usermatrix.app.Grid.getStore().load({params:{start:0, limit: 25}});

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
		UserGrid: function(){

			if(ExtCommon.util.validateSelectionGrid(usermatrix.app.Grid.getId())){//check if user has selected an item in the grid
	 			var sm = usermatrix.app.Grid.getSelectionModel();
	 			var id = sm.getSelected().data.id;


			var Userstore = new Ext.data.Store({
					proxy: new Ext.data.HttpProxy({
						url: "<?php echo site_url("userMatrix/getModuleGroupUsers"); ?>",
						method: "POST"
						}),
					reader: new Ext.data.JsonReader({
							root: "data",
							id: "id",
							totalProperty: "totalCount",
							fields: [
										{ name: "id"},
										{ name: "username" }
									]
					}),
					remoteSort: true,
					baseParams: {start: 0, limit: 25, id: id}
				});

			var UserGrid = new Ext.grid.GridPanel({
 				id: 'userGrid',
 				height: 300,
 				width: 400,
 				border: true,
 				ds: Userstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
 						  { header: "Id", dataIndex: "id", width: 50, sortable: true},
 						  { header: "Username", width: 250, sortable: true, dataIndex: "username" }
 						]
 				),
 				sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
 	        	loadMask: true,
 	        	bbar:
 	        		new Ext.PagingToolbar({
 		        		autoShow: true,
 				        pageSize: 25,
 				        store: Userstore,
 				        displayInfo: true,
 				        displayMsg: 'Displaying Results {0} - {1} of {2}',
 				        emptyMsg: "No Data Found."
 				    }),
 				tbar: [
 					    {
 					     	xtype: 'tbfill'
 					 	},{
					     	xtype: 'tbbutton',
							icon: '/images/icons/application_add.png',
							cls:'x-btn-text-icon',
					     	text: 'ADD',
					     	handler: usermatrix.app.AddUser

					 	}, '-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: usermatrix.app.DeleteUser

 					 	}
 	    			 ]
 	    	});

 	    	usermatrix.app.UserGrid = UserGrid;
 	    	usermatrix.app.UserGrid.getStore().load({params:{start: 0, limit: 25}});

			var UserWndow = new Ext.Window({
	 		        title: 'USERS',
	 		        width: 410,
	 		        height:310,
	 		        layout: 'fit',
	 		        plain:true,
	 		        modal: true,
	 		        bodyStyle:'padding:5px;',
	 		        buttonAlign:'center',
	 		        items: UserGrid
	 		    }).show();
			}

		},
		AddUser: function(){

 		//	usermatrix.app.setForm();

 		var sm = usermatrix.app.Grid.getSelectionModel();
	 	var id = sm.getSelected().data.id;

 		var form = new Ext.form.FormPanel({
 		        labelWidth: 100,
 		        url:"<?php echo site_url("userMatrix/insertModuleGroupUsers"); ?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,
 		        height: 100,


 		        items: [ {
 					xtype:'fieldset',
 					title:'Fields w/ Asterisks are required.',
 					width:370,
 					height:80,
 					items:[usermatrix.app.userCombo()

 		        ]
 					}
 		        ]
 		    });

 		  	var _AddUserWindow;

 		    _AddUserWindow = new Ext.Window({
 		        title: 'New Group User',
 		        width: 410,
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
 		                	params: {group_id: id},
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(usermatrix.app.UserGrid.getId());
 				                _AddUserWindow.destroy();
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
 			            _AddUserWindow.destroy();
 		            }
 		        }]
 		    }).show();

 		},
 		userCombo: function(){
 			var sm = usermatrix.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.id;
			return {
				xtype:'combo',
				id:'usercombo',
				hiddenName: 'username',
				hiddenId: 'username',
				name: 'usercombo',
				valueField: 'id',
				displayField: 'name',
				anchor: '90%',
				triggerAction: 'all',
				minChars: 2,
				
				enableKeyEvents: true,
				forceSelection: true,
				pageSize: 10,
				resizable: true,
				readOnly: false,
				minListWidth: 150,
				allowBlank: false,
				store: new Ext.data.JsonStore({
					id: 'idpurposecombo',
					root: 'data',
					totalProperty: 'totalCount',
					fields:[{name: 'id', type:'int', mapping:'id'}, {name: 'name', type:'string', mapping: 'username'}],
					url: "<?php echo site_url("userMatrix/getUserName"); ?>",
					baseParams: {start: 0, limit: 10, id: id}

				}),
				listeners: {
					select: function (combo, record, index){
						this.setRawValue(record.get('name'));
						//Ext.getCmp(this.id).setValue(record.get('name'));
						Ext.get(this.hiddenName).dom.value = record.get('name');
						var id = record.get('id');
					},
					blur: function(){
						var val = this.getRawValue();
						this.setRawValue.defer(1, this, [val]);
						
						if(this.validate())
						Ext.get(this.hiddenName).dom.value = val;
						else
						Ext.get(this.hiddenName).dom.value = '';
					},
					render: function() {
						this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a user'});

					},
					keypress: {buffer: 100, fn: function() {
						Ext.get(this.hiddenId).dom.value  = '';
						if(!this.getRawValue()){
							this.doQuery('', true);
						}
					}}
				},
				fieldLabel: 'Username*'

			}
			},
			DeleteUser: function(){


				if(ExtCommon.util.validateSelectionGrid(usermatrix.app.UserGrid.getId())){//check if user has selected an item in the grid
				var sm = usermatrix.app.UserGrid.getSelectionModel();
				var id = sm.getSelected().data.id;
				Ext.Msg.show({
	   			title:'Delete',
	  			msg: 'Are you sure you want to delete this record?',
	   			buttons: Ext.Msg.OKCANCEL,
	   			fn: function(btn, text){
	   			if (btn == 'ok'){

	   			Ext.Ajax.request({
								url: "<?php echo site_url("userMatrix/deleteModuleGroupUsers"); ?>",
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
								usermatrix.app.UserGrid.getStore().load({params:{start:0, limit: 25}});

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


			},moduleCategoryCombo: function(){

				return {
					xtype:'combo',
					id:'modulecategorycombo',
					hiddenName: 'modulecategorycombo2',
					name: 'modulecategorycombo3',
					valueField: 'id',
					displayField: 'name',
					anchor: '90%',
					triggerAction: 'all',
					minChars: 2,
					forceSelection: true,
					enableKeyEvents: true,
					pageSize: 10,
					resizable: true,
					readOnly: false,
					minListWidth: 150,
					allowBlank: false,
					store: new Ext.data.JsonStore({
						id: 'idpurposecombo',
						root: 'data',
						totalProperty: 'totalCount',
						fields:[{name: 'id', type:'int', mapping:'id'}, {name: 'name', type:'string', mapping: 'name'}],
						url: "functions/getModuleCategory.php",
						baseParams: {start: 0, limit: 10}

					}),
					listeners: {
					beforequery: function()
					{
						Ext.get('modulecombo').dom.value = '';
						Ext.get('modulecombo2').dom.value = '';
					},
						select: function (combo, record, index){
							this.setRawValue(record.get('name'));
							//Ext.get(this.hiddenName).dom.value  = record.get('id');
							var id = record.get('id');
						},
						blur: function(){
							var val = this.getRawValue();
							this.setRawValue.defer(1, this, [val]);
							this.validate();
						},
						render: function() {
							this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a Purpose'});

						},
						keypress: {buffer: 100, fn: function() {
							//Ext.get(this.hiddenName).dom.value  = '';
							if(!this.getRawValue()){
								this.doQuery('', true);
							}
						}}
					},
					fieldLabel: 'Category*'

				}
				},
				moduleCombo: function(){
					var sm = usermatrix.app.Grid.getSelectionModel();
		 			var id = sm.getSelected().data.id;

					return {
						xtype:'combo',
						id:'modulecombo',
						hiddenName: 'modulecombo2',
						name: 'modulecombo3',
						valueField: 'id',
						displayField: 'name',
						anchor: '90%',
						triggerAction: 'all',
						minChars: 2,
						forceSelection: true,
						enableKeyEvents: true,
						pageSize: 10,
						resizable: true,
						readOnly: false,
						minListWidth: 150,
						allowBlank: false,
						store: new Ext.data.JsonStore({
							id: 'idmodulecombo',
							root: 'data',
							totalProperty: 'totalCount',
							fields:[{name: 'id', type:'int', mapping:'id'}, {name: 'name', type:'string', mapping: 'name'}],
							url: "functions/getModule.php",
							baseParams: {start: 0, limit: 10}

						}),
						listeners: {
						beforequery: function()
						{
							var sm = usermatrix.app.Grid.getSelectionModel();
							var groupid = sm.getSelected().data.id;
							if (Ext.get('modulecategorycombo2').dom.value == "")
								return false;

							this.store.baseParams = {id: Ext.get('modulecategorycombo2').dom.value, group: groupid};

				            var o = {start: 0, limit: 10};
				            this.store.baseParams = this.store.baseParams || {};
				            this.store.baseParams[this.paramName] = '';
				            this.store.reload({params:o, timeout: 300000});
						},
							select: function (combo, record, index){
								this.setRawValue(record.get('name'));
								//Ext.get(this.hiddenName).dom.value  = record.get('id');

							},
							blur: function(){
								var val = this.getRawValue();
								this.setRawValue.defer(1, this, [val]);
								this.validate();
							},
							render: function() {
								this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a Purpose'});

							},
							keypress: {buffer: 100, fn: function() {
								//Ext.get(this.hiddenName).dom.value  = '';
								if(!this.getRawValue()){
									this.doQuery('', true);
								}
							}}
						},
						fieldLabel: 'Module*'

					}
					},
				ModuleGrid: function(){

					if(ExtCommon.util.validateSelectionGrid(usermatrix.app.Grid.getId())){//check if user has selected an item in the grid
			 			var sm = usermatrix.app.Grid.getSelectionModel();
			 			var id = sm.getSelected().data.id;


					var moduleStore = new Ext.data.Store({
							proxy: new Ext.data.HttpProxy({
								url: "<?php echo site_url("userMatrix/getModuleGroupAccess"); ?>",
								method: "POST"
								}),
							reader: new Ext.data.JsonReader({
									root: "data",
									id: "id",
									totalProperty: "totalCount",
									fields: [
												{ name: "id"},
												{ name: "module" },
												{name: "category"}
											]
							}),
							remoteSort: true,
							baseParams: {start: 0, limit: 25, id: id}
						});

					var moduleGrid = new Ext.grid.GridPanel({
		 				id: 'moduleGrid',
		 				height: 300,
		 				width: 450,
		 				border: true,
		 				ds: moduleStore,
		 				cm:  new Ext.grid.ColumnModel(
		 						[
		 						  { header: "Id", dataIndex: "id", width: 50, sortable: true},
		 						 { header: "Category", width: 200, sortable: true, dataIndex: "category" },
		 						  { header: "Module Name", width: 200, sortable: true, dataIndex: "module" }
		 						]
		 				),
		 				sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
		 	        	loadMask: true,
		 	        	bbar:
		 	        		new Ext.PagingToolbar({
		 		        		autoShow: true,
		 				        pageSize: 25,
		 				        store: moduleStore,
		 				        displayInfo: true,
		 				        displayMsg: 'Displaying Results {0} - {1} of {2}',
		 				        emptyMsg: "No Data Found."
		 				    }),
		 				tbar: [
		 					    {
		 					     	xtype: 'tbfill'
		 					 	},{
							     	xtype: 'tbbutton',
									icon: '/images/icons/application_add.png',
									cls:'x-btn-text-icon',
							     	text: 'ADD',
							     	handler: usermatrix.app.selectModules

							 	}, '-',{
		 					     	xtype: 'tbbutton',
		 					     	text: 'DELETE',
									icon: '/images/icons/application_delete.png',
		 							cls:'x-btn-text-icon',

		 					     	handler: usermatrix.app.DeleteModule

		 					 	}
		 	    			 ]
		 	    	});

		 	    	usermatrix.app.moduleGrid = moduleGrid;
		 	    	usermatrix.app.moduleGrid.getStore().load({params:{start: 0, limit: 25}});

					var moduleWindow = new Ext.Window({
			 		        title: 'Module Access',
			 		        width: 500,
			 		        height:310,
			 		        layout: 'fit',
			 		        plain:true,
			 		        modal: true,
			 		        bodyStyle:'padding:5px;',
			 		        buttonAlign:'center',
			 		        items: usermatrix.app.moduleGrid
			 		    }).show();
					}

				},
				AddModule: function(){

			 		//	usermatrix.app.setForm();

			 		var sm = usermatrix.app.Grid.getSelectionModel();
				 	var id = sm.getSelected().data.id;

			 		var form = new Ext.form.FormPanel({
			 		        labelWidth: 100,
			 		        url:"functions/createModuleAccess.php",
			 		        method: 'POST',
			 		        defaultType: 'textfield',
			 		        frame: true,
			 		        height: 200,


			 		        items: [ {
			 					xtype:'fieldset',
			 					title:'Fields w/ Asterisks are required.',
			 					width:370,
			 					height:180,
			 					items:[usermatrix.app.moduleCategoryCombo(), usermatrix.app.moduleCombo()

			 		        ]
			 					}
			 		        ]
			 		    });

			 		  	var _AddModuleAccessWindow;

			 		    _AddModuleAccessWindow = new Ext.Window({
			 		        title: 'New Group Module',
			 		        width: 410,
			 		        height:280,
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
			  								     icon: 'icon'
			  								 });
			 				                ExtCommon.util.refreshGrid(usermatrix.app.moduleGrid.getId());
			 				                _AddModuleAccessWindow.destroy();
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
			 			            _AddModuleAccessWindow.destroy();
			 		            }
			 		        }]
			 		    }).show();

			 		},
					DeleteModule: function(){


						if(ExtCommon.util.validateSelectionGrid(usermatrix.app.moduleGrid.getId())){//check if user has selected an item in the grid
						var sm = usermatrix.app.moduleGrid.getSelectionModel();
						var id = sm.getSelected().data.id;
						Ext.Msg.show({
			   			title:'Delete',
			  			msg: 'Are you sure you want to delete this record?',
			   			buttons: Ext.Msg.OKCANCEL,
			   			fn: function(btn, text){
			   			if (btn == 'ok'){

			   			Ext.Ajax.request({
                                                    url: "<?=site_url("userMatrix/deleteModule")?>",
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
										usermatrix.app.moduleGrid.getStore().load({params:{start:0, limit: 25}});

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
                                        selectModules: function(){


                                        var groupsm = usermatrix.app.Grid.getSelectionModel();
                                        var groupid = groupsm.getSelected().data.id;

                                        var store = new Ext.data.JsonStore({

			 				url: "<?php echo site_url("userMatrix/getModule"); ?>",

							root: 'data',

							totalProperty: 'totalCount',

							fields: [

										{ name: "id", mapping: "id" },

										{ name: "description", mapping: "module" },
                                                                                {name: "category"},
                                                                                {name: "link"}

									],
                                                         baseParams: {id: groupid}

                                         });

                                         var sm1 = new Ext.grid.CheckboxSelectionModel({checkOnly: true,dataIndex: 'id'});



				var gridMrfItemList = new Ext.grid.GridPanel({

					title: "Select Modules",

					id: 'mrfItemList',

        				height: 300,

					width: 'auto',

					stripeRows: true,

        				border: true,

					layout: "absolute",

					ds: store,

					cm:  new Ext.grid.ColumnModel(

							[

							  sm1,
                                                          { header: "Category", width: 150, sortable: true, locked:true, dataIndex: "category" },
							  { header: "Module", width: 150, sortable: true, locked:true, dataIndex: "description" },
                                                          { header: "Link", width: 150, sortable: true, locked:true, dataIndex: "link" }

							]

					),

					selModel: sm1,

		        		loadMask: true,

					//tbar: [ 'Search: ', ' ', new Ext.app.SearchField({ store: store, width:320}) ],

					bbar: new Ext.PagingToolbar({

				        		autoShow: true,

						        pageSize: 10,

						        store: store,

						        displayInfo: true,

						        displayMsg: 'Displaying Records {0} - {1} of {2}',

						        emptyMsg: "No Data Found."

						    })



	        	});



				var fpanel = new Ext.form.FormPanel({

					id: "mrfItemListForm",

					method: "POST",

					height: 300,

					width: 520,

					border: false,

					items: [gridMrfItemList]

				});



			var addItemListWin = new Ext.Window({

					title: 'Add Module Acess',

			        	width: 550,

			        	height: 380,

					modal: true,

					autoScroll: true,

					buttonAlign:'right',

					bodyStyle:'padding:5px;',

					resizable: false,

					items: [ fpanel ],

			        buttons: [

						{

								text: "Save",
								icon: '/images/icons/disk.png',
								cls:'x-btn-text-icon',

								handler: function(){

								if(!ExtCommon.util.validateSelectionGrid(gridMrfItemList.getId()))

									return;

								var selectedItemsJson = Ext.getCmp("mrfItemList").getSelectionModel().getSelections();



								var objectJson = { data: new Array() };



									for (var key in selectedItemsJson){

							    		tmpJson = Ext.util.JSON.encode(selectedItemsJson[key].data);

										if(tmpJson != null && typeof(tmpJson) != "undefined" && tmpJson != "null"){

											objectJson.data.push(selectedItemsJson[key].data.id);

										}

						    		}





						    		Ext.getCmp("mrfItemListForm").getForm().submit({

									 	url:"<?php echo site_url("userMatrix/insertModuleGroupAccess"); ?>",

									 	method: 'POST',

									 	params: { selected_items: Ext.util.JSON.encode(objectJson), groupid: groupid },

									 	waitMsg:'Loading...',

										success: function(form, action){

										    try{

										    	Ext.MessageBox.alert('Status', action.result.msg);

										    	Ext.getCmp('grandtotal').setValue(action.result.grandtotal);

										    }catch(e){

										    	Ext.MessageBox.alert('Status', "Successfully saved.");

									    	}

										usermatrix.app.moduleGrid.getStore().reload({params:{start:0, limit: 10}});

							                	addItemListWin.destroy();

										},

										failure: function(form, action){

											Ext.Msg.show({title: 'Error Alert',	msg: action.result.msg, icon: Ext.Msg.ERROR,buttons: Ext.Msg.OK});

										}

									 });





								}

						},

						{ 
						text: "Close",
						icon: '/images/icons/cancel.png',
						cls:'x-btn-text-icon', 
						handler: function(){

									addItemListWin.destroy();

							}

						}

					],

					listeners:{

						show: function(){

							store.load({params:{start:0, limit:10}});

						}

					}

				});



			addItemListWin.show();





		}//end of functions
 	}

 }();

 Ext.onReady(usermatrix.app.init, usermatrix.app);

</script>

<div id="mainBody">
</div>