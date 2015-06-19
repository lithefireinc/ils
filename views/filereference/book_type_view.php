<script type="text/javascript">
 Ext.namespace("ils_book_type");
 ils_book_type.app = function()
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
 							url: "<?=site_url("filereference/getBookType")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [	
 											{ name: "BOTYCODE"},
 											{ name: "BOTYIDNO"},
 											{ name: "DESCRIPTION"},
 											{ name: "CLASSIFICATION"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ils_book_typegrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    { header: "Id", width: 75, sortable: true, dataIndex: "BOTYIDNO" },
 						  { header: "Book Type", width: 200, sortable: true, dataIndex: "DESCRIPTION" },
 						  { header: "Classification", width: 200, sortable: true, dataIndex: "CLASSIFICATION" }
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

 					     	handler: ils_book_type.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '/images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_type.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_type.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ils_book_type.app.Grid = grid;
 			ils_book_type.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Book Type',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ils_book_type.app.Grid],
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
 		        url:"<?=site_url("filereference/addBookType")?>",
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
 		            fieldLabel: 'Book Type*',
                    autoCreate : {tag: "input", type: "text", size: "20", autocomplete: "off", maxlength: "128"},
 		            name: 'DESCRIPTION',
 		            allowBlank:false,
 		            anchor:'95%',  // anchor width by percentage
 		            id: 'DESCRIPTION'
 		        },
 		        ils_book_type.app.classificationCombo()

 		        ]
 					}
 		        ]
 		    });

 		    ils_book_type.app.Form = form;
 		},
 		Add: function(){

 			ils_book_type.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Book Type',
 		        width: 510,
 		        height:200,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_book_type.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_book_type.app.Form)){//check if all forms are filled up

 		                ils_book_type.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ils_book_type.app.Grid.getId());
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


 			if(ExtCommon.util.validateSelectionGrid(ils_book_type.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ils_book_type.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.BOTYIDNO;

 			ils_book_type.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Book Type',
 		        width: 510,
 		        height:200,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_book_type.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_book_type.app.Form)){//check if all forms are filled up
 		                ils_book_type.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updateBookType")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ils_book_type.app.Grid.getId());
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

 		  	ils_book_type.app.Form.getForm().load({
 				url: "<?=site_url("filereference/loadBookType")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
 									
                                    _window.show();
                                    Ext.get('CLASIDNO').dom.value = action.result.data.CLASIDNO;
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


			if(ExtCommon.util.validateSelectionGrid(ils_book_type.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ils_book_type.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.BOTYIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("filereference/deleteBookType")?>",
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
							ils_book_type.app.Grid.getStore().load({params:{start:0, limit: 25}});

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
		classificationCombo: function(){

 			return {
 				xtype:'combo',
 				id:'CLASSIFICATION',
 				hiddenName: 'CLASIDNO',
                hiddenId: 'CLASIDNO',
 				name: 'CLASSIFICATION',
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
 				fields:[{name: 'id'}, {name: 'name', type:'string', mapping: 'name'}, {name: 'firstname'}, {name: 'middlename'}, {name: 'lastname'}, {name: 'cellphone'}, {name: 'homephone'}, {name: 'memberId'}],
 				url: "<?=site_url("filereference/getClassificationCombo")?>",
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
 				fieldLabel: 'Classification*'

 				}
 		}//end of functions
 	}

 }();

 Ext.onReady(ils_book_type.app.init, ils_book_type.app);

</script>
<div id="mainBody"></div>
