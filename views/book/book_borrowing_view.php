 <script type="text/javascript" src="/js/ext34/examples/ux/Spinner.js"></script>
 <script type="text/javascript" src="/js/ext34/examples/ux/SpinnerField.js"></script>
 <link rel="stylesheet" type="text/css" href="/js/ext34/examples/ux/css/Spinner.css" />
<style type="text/css">
.unfunded{
   background-color: #f18282 !important;
   color: white;
}
.returned{
   background-color: #95e797 !important;
   color: white;
}
.borrowed{
   background-color: #5d79f6 !important;
   color: white;
}
</style>
<script type="text/javascript">
 Ext.namespace("ils_book_borrowing");
 ils_book_borrowing.app = function()
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
 			ExtCommon.util.renderSearchField('bookgrid');
//			Ext.QuickTips.init();
 			var Objstore = new Ext.data.Store({
 						proxy: new Ext.data.HttpProxy({
 							url: "<?=site_url("book/getBorrowedBooks")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [	
 											{name: 'id'},
 											{ name: "ACCESSNO"},
 											{ name: "TITLE"},
 											{ name: "STUDIDNO"},
 											{ name: "NAME"},
 											{ name: "D_BORROWED"},
 											{ name: "D_RETURNED"},
 											{ name: "D_DUE"},
 											{ name: "FINE_DUE"},
 											{ name: "FINE"},
 											{ name: "PAID"},
 											{ name: "BOOKSTAT"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});

			var gridView = new Ext.grid.GridView({ 
	                getRowClass : function (row, index) { 
	                    var cls = ''; 
	                    var data = row.data;
	                    if(isSet(data.UNFUNDED) && isSet(data.PAID)){
	                        if(data.BOOSTAT == "OVERDUE"){
	                        cls = 'unfunded'; 
	                        }
	                        if(data.BOOSTAT == "RETURNED"){
	                        cls = 'returned'; 
	                        }
	                        if(data.BOOSTAT == "BORROWED"){
	                        cls = 'borrowed'; 
	                        }
	                    }
	                    
	                    return cls; 
	                } 
	        });
 			var grid = new Ext.grid.GridPanel({
 				id: 'ils_book_borrowinggrid',
 				height: 300,
 				width: 900,
 				border: true,
 				view: gridView,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    //{ header: "Id", width: 75, sortable: true, dataIndex: "BOTYIDNO" },
 						  { header: "Access No.", width: 100, sortable: true, dataIndex: "ACCESSNO" },
 						  { header: "Title", width: 450, sortable: true, dataIndex: "TITLE" },
 						  { header: "Student Id", width: 100, sortable: true, dataIndex: "STUDIDNO" },
 						  { header: "Student Name", width: 250, sortable: true, dataIndex: "NAME" },
 						  { header: "Date Borrowed", width: 100, sortable: true, dataIndex: "D_BORROWED" },
 						  { header: "Date Due", width: 100, sortable: true, dataIndex: "D_DUE" },
 						  { header: "Date Returned", width: 100, sortable: true, dataIndex: "D_RETURNED" },
 						  { header: "Fine", width: 100, sortable: true, dataIndex: "FINE_DUE" },
 						  { header: "Paid", width: 100, sortable: true, dataIndex: "PAID" },
 						  { header: "Status", width: 150, sortable: true, dataIndex: "BOOKSTAT", renderer: this.statusFormat }
 						 
 						  
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
                    hiddenName:'book_grid',
                    id: 'bookgrid',
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

 					     	handler: ils_book_borrowing.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'RETURN',
							icon: '/images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_borrowing.app.Update

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'VIEW',
							icon: '/images/icons/application_view_detail.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_borrowing.app.Edit

 					 	}
 	    			 ]
 	    	});

 			ils_book_borrowing.app.Grid = grid;
 			ils_book_borrowing.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Books',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ils_book_borrowing.app.Grid],
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
 				
 			var Objstore = new Ext.data.Store({
 						proxy: new Ext.data.HttpProxy({
 							url: "<?=site_url("book/getStudentBorrowingHistory")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [	
 											{ name: "ACCESSNO"},
 											{ name: "TITLE"},
 											{ name: "STUDIDNO"},
 											{ name: "NAME"},
 											{ name: "D_BORROWED"},
 											{ name: "D_RETURNED"},
 											{ name: "D_DUE"},
 											{ name: "FINE_DUE"},
 											{ name: "PAID"},
 											{ name: "BOOKSTAT"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'student_book_grid',
 				height: 200,
 				//width: 900,
 				border: true,
 				title: 'Student Borrowing History',
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
 						  { header: "Access No.", width: 100, sortable: true, dataIndex: "ACCESSNO" },
 						  { header: "Title", width: 450, sortable: true, dataIndex: "TITLE" },
 						  { header: "Date Borrowed", width: 100, sortable: true, dataIndex: "D_BORROWED" },
 						  { header: "Date Due", width: 100, sortable: true, dataIndex: "D_DUE" },
 						  { header: "Date Returned", width: 100, sortable: true, dataIndex: "D_RETURNED" },
 						  { header: "Fine", width: 100, sortable: true, dataIndex: "FINE_DUE" },
 						  { header: "Paid", width: 100, sortable: true, dataIndex: "PAID" },
 						  { header: "Status", width: 150, sortable: true, dataIndex: "BOOKSTAT", renderer: this.statusFormat }
 						 
 						  
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
                    hiddenName:'book_grid',
                    id: 'bookgrid',
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

                })
 	    			 ]
 	    	});
 	    	
 	    	ils_book_borrowing.app.studentGrid = grid;
 	    	
 	    	var Objstore = new Ext.data.Store({
 						proxy: new Ext.data.HttpProxy({
 							url: "<?=site_url("book/getBookBorrowingHistory")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [	
 											{ name: "ACCESSNO"},
 											{ name: "TITLE"},
 											{ name: "STUDIDNO"},
 											{ name: "NAME"},
 											{ name: "D_BORROWED"},
 											{ name: "D_RETURNED"},
 											{ name: "D_DUE"},
 											{ name: "FINE_DUE"},
 											{ name: "PAID"},
 											{ name: "BOOKSTAT"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'book_history_grid',
 				height: 255,
 				//width: 900,
 				border: true,
 				title: 'Book Borrowing History',
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
 						  { header: "Student Id", width: 100, sortable: true, dataIndex: "STUDIDNO" },
 						  { header: "Student Name", width: 250, sortable: true, dataIndex: "NAME" },
 						  { header: "Date Borrowed", width: 100, sortable: true, dataIndex: "D_BORROWED" },
 						  { header: "Date Due", width: 100, sortable: true, dataIndex: "D_DUE" },
 						  { header: "Date Returned", width: 100, sortable: true, dataIndex: "D_RETURNED" },
 						  { header: "Fine", width: 100, sortable: true, dataIndex: "FINE_DUE" },
 						  { header: "Paid", width: 100, sortable: true, dataIndex: "PAID" },
 						  { header: "Status", width: 150, sortable: true, dataIndex: "BOOKSTAT", renderer: this.statusFormat }
 						 
 						  
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
                    hiddenName:'book_grid',
                    id: 'bookgrid',
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

                })
 	    			 ]
 	    	});
 	    	
 	    	ils_book_borrowing.app.bookGrid = grid;
 	    		
 		    var form = new Ext.form.FormPanel({
 		        labelWidth: 100,
 		        url:"<?=site_url("book/borrowBook")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,
 		        items: [ {
 					xtype:'fieldset',
 					//title:'Fields w/ Asterisks are required.',
 					width:'auto',
 					height:'auto',
 					items:[
                       {
                       		layout: 'column',
                       		width: 'auto',
                       		items: [
                       			{
                       				columnWidth: .4,
                       				width: 'auto',
                       				layout: 'form',
                       				items: [
                       					ils_book_borrowing.app.studentCombo(),
                       					{
                        					xtype: 'textfield',
                        					name: 'YEAR',
                        					id: 'YEAR',
                        					fieldLabel: 'Year Level',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'COURSE',
                        					id: 'COURSE',
                        					fieldLabel: 'Course',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				}
                       				]
                       			},
                       			{
                       				columnWidth: .6,
                       				width: 'auto',
                       				layout: 'fit',
                       				items: [
                       					ils_book_borrowing.app.studentGrid
                       				]
                       			}
                       		]
                       }

 		        ]
 					},
 					{
 					xtype:'fieldset',
 					//title:'Fields w/ Asterisks are required.',
 					width:'auto',
 					height:'auto',
 					items:[
                       {
                       		layout: 'column',
                       		width: 'auto',
                       		items: [
                       			{
                       				columnWidth: .4,
                       				width: 'auto',
                       				layout: 'form',
                       				items: [
                       				ils_book_borrowing.app.bookCombo(),
                       				{
                        					xtype: 'textfield',
                        					name: 'ACCESSNO2',
                        					id: 'ACCESSNO2',
                        					fieldLabel: 'Access Number',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                       				{
                        					xtype: 'textfield',
                        					name: 'CALLNO',
                        					id: 'CALLNO',
                        					fieldLabel: 'Call Number',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'AVAIL',
                        					id: 'AVAIL',
                        					fieldLabel: 'Availability',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'LOCATION',
                        					id: 'LOCATION',
                        					fieldLabel: 'Location',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'CLASSIFICATION',
                        					id: 'CLASSIFICATION',
                        					fieldLabel: 'Classification',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'BOOKTYPE',
                        					id: 'BOOKTYPE',
                        					fieldLabel: 'Book Type',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'CATEGORY',
                        					id: 'CATEGORY',
                        					fieldLabel: 'Category',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'DAYSALLO',
                        					id: 'DAYSALLO',
                        					fieldLabel: 'Days Allowed',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'FINE',
                        					id: 'FINE',
                        					fieldLabel: 'Fine',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				}
                       				]
                       			},
                       			{
                       				columnWidth: .6,
                       				width: 'auto',
                       				layout: 'fit',
                       				items: [
                       					ils_book_borrowing.app.bookGrid
                       				]
                       			}
                       		]
                       }

 		        ]
 					}
 		        ]
 		    });

 		    ils_book_borrowing.app.Form = form;
 		},
 		Add: function(){

 			ils_book_borrowing.app.setForm();

 		  	var _window;

 		    _window = new Ext.Window({
 		        title: 'New Borrowing Entry',
 		        width: 1000,
 		        height:620,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_book_borrowing.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_book_borrowing.app.Form)){//check if all forms are filled up
						if(Ext.getCmp('AVAIL').getValue() == 'No Copies Available'){
							Ext.Msg.show({
 									title: 'Error Alert',
 									msg: 'No available copy for this book',
 									icon: Ext.Msg.ERROR,
 									buttons: Ext.Msg.OK
 								});
 							return;
						}
 		                ils_book_borrowing.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
 				                ExtCommon.util.refreshGrid(ils_book_borrowing.app.Grid.getId());
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


 			if(ExtCommon.util.validateSelectionGrid(ils_book_borrowing.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ils_book_borrowing.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.id;

 			ils_book_borrowing.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Book Type',
 		        width: 1000,
 		        height:620,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_book_borrowing.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_book_borrowing.app.Form)){//check if all forms are filled up
 		                ils_book_borrowing.app.Form.getForm().submit({
 			                url: "<?=site_url("filereference/updateBookType")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ils_book_borrowing.app.Grid.getId());
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

 		  	ils_book_borrowing.app.Form.getForm().load({
 				url: "<?=site_url("book/loadBorrowedBook")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
 									
                                    _window.show();
                                    ils_book_borrowing.app.studentGrid.getStore().setBaseParam("STUDIDNO", action.result.data.STUDIDNO);
                                    ils_book_borrowing.app.studentGrid.getStore().load();
                                    ils_book_borrowing.app.bookGrid.getStore().setBaseParam("ACCESSNO", action.result.data.ACCESSNO);
                                    ils_book_borrowing.app.bookGrid.getStore().load();
                                    Ext.getCmp("STUDENT").setReadOnly(true);
                                    Ext.getCmp("BOOK").setReadOnly(true);
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
 		Update: function(){


 			if(ExtCommon.util.validateSelectionGrid(ils_book_borrowing.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ils_book_borrowing.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.id;

 			var form = new Ext.form.FormPanel({
 		        labelWidth: 100,
 		        url:"<?=site_url("book/returnBook")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,

 		        items: [
 		        {xtype: 'fieldset',
 		        width: 'auto',
 		        title: 'Borrowers Information',
 		        items: [
 		        					{
                        					xtype: 'textfield',
                        					name: 'STUDENT',
                        					id: 'STUDENT',
                        					fieldLabel: 'Student',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    			},
 		       		{
                        					xtype: 'textfield',
                        					name: 'YEAR',
                        					id: 'YEAR',
                        					fieldLabel: 'Year Level',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				{
                        					xtype: 'textfield',
                        					name: 'COURSE',
                        					id: 'COURSE',
                        					fieldLabel: 'Course',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				}
 		        ]
 		        
 		       },
 		        {xtype: 'fieldset',
 		        width: 'auto',
 		        title: 'Book Information',
 		        items: [
 		        {
                        					xtype: 'textfield',
                        					name: 'BOOK',
                        					id: 'BOOK',
                        					fieldLabel: 'Book Title',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    			},
                       				{
                        					xtype: 'textfield',
                        					name: 'ACCESSNO',
                        					id: 'ACCESSNO',
                        					fieldLabel: 'Access Number',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                       				{
                        					xtype: 'textfield',
                        					name: 'CALLNO',
                        					id: 'CALLNO',
                        					fieldLabel: 'Call Number',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '93%'
                    				},
                    				ExtCommon.util.createCombo('BOOKSTAT', 'BOSTIDNO', '40%', '<?php echo site_url('filereference/getCombo/BOOKSTAT/BOSTIDNO/BOOKSTAT'); ?>', 'Status*', true, false),
                    				{
			                        					xtype: 'datefield',
			                        					name: 'D_BORROWED',
			                        					id: 'D_BORROWED',
			                        					fieldLabel: 'Date Borrowed',
			                        					allowBlank: false,
			                        					readOnly: true,
			                        					anchor: '40%',
			                        					format: 'Y-m-d'
			                       },
			                       {
			                        					xtype: 'datefield',
			                        					name: 'D_DUE',
			                        					id: 'D_DUE',
			                        					fieldLabel: 'Date Due',
			                        					allowBlank: false,
			                        					readOnly: true,
			                        					anchor: '40%',
			                        					format: 'Y-m-d'
			                       },
			                       {
			                        					xtype: 'datefield',
			                        					name: 'D_RETURNED',
			                        					id: 'D_RETURNED',
			                        					fieldLabel: 'Date Returned',
			                        					allowBlank: false,
			                        					anchor: '40%',
			                        					//disabledDays:  [0, 6],
			                        					format: 'Y-m-d',
			                        					//maxValue: new Date(),
			                        					listeners: {
			                        						select: function(fm, dt){
			                        							var one_day=1000*60*60*24;
			                        							var fine = ils_book_borrowing.app.Grid.getSelectionModel().getSelected().data.FINE;
			                        							//alert(fine);
			                        							d_due = Ext.getCmp("D_DUE").getValue();
			                        							days_due = (dt-d_due.getTime())/one_day;
			                        							if(days_due*fine >= 0){
			                        							Ext.getCmp("FINE_DUE").setValue(days_due*fine);
			                        							Ext.getCmp("PAID").setMaxValue(days_due*fine);
			                        							}
			                        							else{
			                        							Ext.getCmp("FINE_DUE").setValue(0);
			                        							Ext.getCmp("PAID").setMaxValue(0);
			                        							}
			                        						}
			                        					}
			                       },
			                       {
                        					xtype: 'numberfield',
                        					name: 'FINE_DUE',
                        					id: 'FINE_DUE',
                        					fieldLabel: 'Fine Due',
                        					allowBlank: true,
                        					readOnly: true,
                        					anchor: '40%'
                    				},
			                       {
                        					xtype: 'numberfield',
                        					name: 'PAID',
                        					id: 'PAID',
                        					fieldLabel: 'Amount Paid',
                        					allowBlank: true,
                        					anchor: '40%'
                    				}
 		        ]
 		        
 		        }
 		        					
 		        ]
 		        
 		    });
 		    
 		    _window = new Ext.Window({
 		        title: 'Return Book',
 		        width: 710,
 		        height:500,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(form)){//check if all forms are filled up
 		                form.getForm().submit({
 			                url: "<?=site_url("book/returnBook")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ils_book_borrowing.app.Grid.getId());
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

 		  	form.getForm().load({
 				url: "<?=site_url("book/loadBorrowedBook")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
 									
                                    _window.show();
                                    Ext.getCmp("D_RETURNED").setMinValue(action.result.data.D_BORROWED);
                                    Ext.getCmp("PAID").setMaxValue(action.result.data.FINE_DUE);
                                    Ext.get("BOSTIDNO").dom.value = action.result.data.BOSTIDNO;
                                    Ext.getCmp("BOOKSTAT").setReadOnly(true);
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


			if(ExtCommon.util.validateSelectionGrid(ils_book_borrowing.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ils_book_borrowing.app.Grid.getSelectionModel();
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
							ils_book_borrowing.app.Grid.getStore().load({params:{start:0, limit: 25}});

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
		studentCombo: function(){

 			return {
 				xtype:'combo',
 				id:'STUDENT',
 				hiddenName: 'STUDIDNO',
                hiddenId: 'STUDIDNO',
 				name: 'STUDENT',
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
 				//readOnly: true,
 				minListWidth: 300,
 				allowBlank:true,
 				store: new Ext.data.JsonStore({
 				id: 'idsocombo',
 				root: 'data',
 				totalProperty: 'totalCount',
 				fields:[{name: 'id'}, {name: 'name'}, {name: 'NAME'}, {name: 'YEAR'}, {name: 'COURSE'}],
 				url: "<?=site_url("book/getStudentCombo")?>",
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
 				Ext.getCmp("YEAR").setValue(record.get('YEAR'));
 				Ext.getCmp("COURSE").setValue(record.get('COURSE'));
				ils_book_borrowing.app.studentGrid.getStore().setBaseParam("STUDIDNO", record.get('id'));
				ils_book_borrowing.app.studentGrid.getStore().load();



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
 				Ext.get(this.hiddenName).dom.value  = '';
 				if(!this.getRawValue()){
 				this.doQuery('', true);
 				}
 				}}
 				},
 				fieldLabel: 'Student*'

 				}
 		},
		bookCombo: function(){

 			return {
 				xtype:'combo',
 				id:'BOOK',
 				hiddenName: 'ACCESSNO',
                hiddenId: 'ACCESSNO',
 				name: 'BOOK',
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
 				//readOnly: true,
 				minListWidth: 450,
 				allowBlank:true,
 				store: new Ext.data.JsonStore({
 				id: 'idsocombo',
 				root: 'data',
 				totalProperty: 'totalCount',
 				fields:[{name: 'id'}, {name: 'name'}, {name: 'LOCATION'}, {name: 'AVAIL'}, {name: 'CLASSIFICATION'}
 				, {name: 'BOOKTYPE'}, {name: 'CATEGORY'}, {name: 'FINE'}, {name: 'DAYSALLO'}, {name: 'CALLNO'}],
 				url: "<?=site_url("book/getBookCombo")?>",
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
 	
				ils_book_borrowing.app.bookGrid.getStore().setBaseParam("ACCESSNO", record.get('id'));
				ils_book_borrowing.app.bookGrid.getStore().load();
				Ext.getCmp("ACCESSNO2").setValue(record.get("id"));
				Ext.getCmp("CALLNO").setValue(record.get("CALLNO"));
				Ext.getCmp("LOCATION").setValue(record.get("LOCATION"));
				Ext.getCmp("AVAIL").setValue(record.get("AVAIL"));
				Ext.getCmp("CLASSIFICATION").setValue(record.get("CLASSIFICATION"));
				Ext.getCmp("BOOKTYPE").setValue(record.get("BOOKTYPE"));
				Ext.getCmp("CATEGORY").setValue(record.get("CATEGORY"));
				Ext.getCmp("FINE").setValue(record.get("FINE"));
				Ext.getCmp("DAYSALLO").setValue(record.get("DAYSALLO"));



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
 				Ext.get(this.hiddenName).dom.value  = '';
 				if(!this.getRawValue()){
 				this.doQuery('', true);
 				}
 				}}
 				},
 				fieldLabel: 'Book*'

 				}
 		},
 		statusFormat: function(val){

			var fmtVal;

			switch(val){
				case "BORROWED"	: 	fmtVal = '<span style="color: blue; font-weight: bold;">'+val+'</span>'; break;
			 	case "RETURNED"	:  	fmtVal = '<span style="color: green; font-weight: bold;">'+val+'</span>'; break;
			 	case "OVERDUE"	:  	fmtVal = '<span style="color: red; font-weight: bold;">'+val+'</span>'; break;
			 

			}

			return fmtVal;
		}//end of functions
 	}

 }();

 Ext.onReady(ils_book_borrowing.app.init, ils_book_borrowing.app);

</script>
<div id="mainBody"></div>
