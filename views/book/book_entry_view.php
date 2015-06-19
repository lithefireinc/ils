 <script type="text/javascript" src="/js/ext34/examples/ux/Spinner.js"></script>
 <script type="text/javascript" src="/js/ext34/examples/ux/SpinnerField.js"></script>
 <link rel="stylesheet" type="text/css" href="/js/ext34/examples/ux/css/Spinner.css" />
<script type="text/javascript">
 Ext.namespace("ils_book_entry");
 ils_book_entry.app = function()
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
 							url: "<?=site_url("book/getBooks")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [	
 											{ name: "ACCESSNO"},
 											{ name: "CALLNO"},
 											{ name: "TITLE"},
 											{ name: "LOCATION"},
 											{ name: "EDITION"},
 											{ name: "VOLUME"},
 											{ name: "ISBN"},
 											{ name: "PUBLISHER"},
 											{ name: "LOCATION"},
 											{ name: "PLACE"},
 											{ name: "COUNTRY"},
 											{ name: "COPYRIGHT"},
 											{ name: "PAGES"},
 											{ name: "COPIES"},
 											{ name: "PURCDATE"},
 											{ name: "PLACE"},
 											{ name: "AMOUNT"},
 											{ name: "PHYSDESC"},
 											{ name: "BOOKTYPE"},
 											{ name: "DDC"},
 											{ name: "DDCDECI"},
 											{ name: "CATEGORY"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ils_book_entrygrid',
 				height: 300,
 				width: 900,
 				border: true,
 				ds: Objstore,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    //{ header: "Id", width: 75, sortable: true, dataIndex: "BOTYIDNO" },
 						  { header: "Access No.", width: 100, sortable: true, dataIndex: "ACCESSNO" },
 						  { header: "Call No.", width: 150, sortable: true, dataIndex: "CALLNO" },
 						  { header: "Title", width: 450, sortable: true, dataIndex: "TITLE" },
 						  { header: "Location", width: 100, sortable: true, dataIndex: "LOCATION" },
 						  { header: "Edition", width: 60, sortable: true, dataIndex: "EDITION" },
 						  { header: "Volume", width: 60, sortable: true, dataIndex: "VOLUME" },
 						  { header: "ISBN", width: 100, sortable: true, dataIndex: "ISBN" },
 						  { header: "Publisher", width: 200, sortable: true, dataIndex: "PUBLISHER" },
 						  { header: "Place", width: 150, sortable: true, dataIndex: "PLACE" },
 						  { header: "Country", width: 150, sortable: true, dataIndex: "COUNTRY" },
 						  { header: "Copyright", width: 100, sortable: true, dataIndex: "COPYRIGHT" },
 						  { header: "No. of Pages", width: 100, sortable: true, dataIndex: "PAGES" },
 						  { header: "No. of Copies", width: 100, sortable: true, dataIndex: "COPIES" },
 						  { header: "Purchase Date", width: 100, sortable: true, dataIndex: "PURCDATE" },
 						  { header: "Amount", width: 200, sortable: true, dataIndex: "AMOUNT" },
 						  { header: "Physical Description", width: 250, sortable: true, dataIndex: "PHYSDESC" },
 						  { header: "Book Type", width: 200, sortable: true, dataIndex: "BOOKTYPE" },
 						  { header: "DDC", width: 200, sortable: true, dataIndex: "DDC" },
 						  { header: "DDCDECI", width: 200, sortable: true, dataIndex: "DDCDECI" },
 						  { header: "Category", width: 200, sortable: true, dataIndex: "CATEGORY" }
 						  
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

 					     	handler: ils_book_entry.app.Add

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'EDIT',
							icon: '/images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_entry.app.Edit

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_entry.app.Delete

 					 	}
 	    			 ]
 	    	});

 			ils_book_entry.app.Grid = grid;
 			ils_book_entry.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
 		        title: 'Books',
 		        width: '100%',
 		        height:420,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'fit',
 		        items: [ils_book_entry.app.Grid],
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
 				
 			var author_store = new Ext.data.Store({
 						proxy: new Ext.data.HttpProxy({
 							url: "<?=site_url("book/getTempBookAuthor")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "AUTHIDNO"},
 											{ name: "AUTHOR"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});	
 				
 			var author_grid = new Ext.grid.GridPanel({
 				id: 'author_grid',
 				height: 150,
 				width: '100%',
 				border: true,
 				style: {marginBottom: '10px'},
 				ds: author_store,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    
 						  { header: "Author", width: 300, sortable: true, dataIndex: "AUTHOR" }
 						]
 				),
 				sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
 	        	loadMask: true,
 	        	bbar:
 	        		new Ext.PagingToolbar({
 		        		autoShow: true,
 				        pageSize: 25,
 				        store: author_store,
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
				},'   ', new Ext.app.SearchField({ store: author_store, width:250}),
 					    {
 					     	xtype: 'tbfill'
 					 	},{
 					     	xtype: 'tbbutton',
 					     	text: 'ADD',
							icon: '/images/icons/application_add.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_entry.app.AddAuthor,
 					     	//disabled: true,
 					     	id: 'add_author_button'
 					     	

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_entry.app.DeleteAuthor,
 					     	//disabled: true,
 					     	id: 'delete_author_button'

 					 	}
 	    			 ]
 	    	});

 			ils_book_entry.app.authorGrid = author_grid;
 			
 			
 			var subject_store = new Ext.data.Store({
 						proxy: new Ext.data.HttpProxy({
 							url: "<?=site_url("book/getTempBookSubject")?>",
 							method: "POST"
 							}),
 						reader: new Ext.data.JsonReader({
 								root: "data",
 								id: "id",
 								totalProperty: "totalCount",
 								fields: [
 											{ name: "BOSUIDNO"},
 											{ name: "SUBJECT"}
 										]
 						}),
 						remoteSort: true,
 						baseParams: {start: 0, limit: 25}
 					});	
 				
 			var subject_grid = new Ext.grid.GridPanel({
 				id: 'subject_grid',
 				height: 150,
 				width: '100%',
 				border: true,
 				style: {marginBottom: '10px'},
 				ds: subject_store,
 				cm:  new Ext.grid.ColumnModel(
 						[
                                                    
 						  { header: "Subject", width: 300, sortable: true, dataIndex: "SUBJECT" }
 						]
 				),
 				sm: new Ext.grid.RowSelectionModel({singleSelect:true}),
 	        	loadMask: true,
 	        	bbar:
 	        		new Ext.PagingToolbar({
 		        		autoShow: true,
 				        pageSize: 25,
 				        store: subject_store,
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
				},'   ', new Ext.app.SearchField({ store: subject_store, width:250}),
 					    {
 					     	xtype: 'tbfill'
 					 	},{
 					     	xtype: 'tbbutton',
 					     	text: 'ADD',
							icon: '/images/icons/application_add.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_entry.app.AddSubject,
 					     	//disabled: true,
 					     	id: 'add_subject_button'

 					 	},'-',{
 					     	xtype: 'tbbutton',
 					     	text: 'DELETE',
							icon: '/images/icons/application_delete.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_entry.app.DeleteSubject,
 					     	//disabled: true,
 					     	id: 'delete_subject_button'

 					 	}
 	    			 ]
 	    	});

 			ils_book_entry.app.subjectGrid = subject_grid;
 			
 		    var form = new Ext.form.FormPanel({
 		        labelWidth: 75,
 		        url:"<?=site_url("book/addBook")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,

 		        items: [ {
 					xtype:'fieldset',
 					title:'Book details',
 					width:'auto',
 					height:'auto',
 					items:[
                        {
                        	layout: 'column',
                        	width: 'auto',
                        	items: [
                        		{
                        			columnWidth: .25,
                        			layout: 'form',
                        			items: [
                        				{
                        					xtype: 'textfield',
                        					name: 'ACCESSNO',
                        					id: 'ACCESSNO',
                        					fieldLabel: 'Access No.*',
                        					allowBlank: false,
                        					maxLength: 10,
                        					anchor: '88%',
                        					listeners: {
                        						change: function(qe){
                        							
                        						}
                        					}
                        				},
                        				
                        				{
                        					xtype: 'textfield',
                        					name: 'CALLNO',
                        					id: 'CALLNO',
                        					fieldLabel: 'Call No*',
                        					allowBlank: false,
                        					maxLength: 128,
                        					anchor: '88%',
                        					enableKeyEvents: true,
                        					listeners: {
                        						keydown: function(t, e){
                        							
                        							//Ext.getCmp('CALLNO2').setValue(t.getValue());
                        						},
                        						blur: function(){
                        							Ext.getCmp('CALLNO2').setValue(this.getValue());
                        						}
                        					}
                        				}/*,
                        				
                        				
                        				{
                        					xtype: 'textfield',
                        					name: 'DDC',
                        					id: 'DDC',
                        					fieldLabel: 'DDC*',
                        					allowBlank: false,
                        					maxLength: 3,
                        					anchor: '88%'
                        				}*/
                        			]
                        		},
                        		{
                        			columnWidth: .50,
                        			layout: 'form',
                        			items: [
                        				
                        				{
                        					xtype: 'textarea',
                        					name: 'TITLE',
                        					id: 'TITLE',
                        					fieldLabel: 'Book Title*',
                        					allowBlank: false,
                        					maxLength: 128,
                        					anchor: '88%',
                        					height: 50
                        				}/*,
                        				
                        				{
                        					xtype: 'textfield',
                        					name: 'DDCDECI',
                        					id: 'DDCDECI',
                        					fieldLabel: 'DDCDECI*',
                        					allowBlank: false,
                        					maxLength: 10,
                        					anchor: '88%'
                        				}*/
                        			]
                        		},
                        		{
                        			columnWidth: .25,
                        			layout: 'form',
                        			items: [
                        				ils_book_entry.app.locationCombo()/*,
                        				,
                        				
                        				,
                        				ils_book_entry.app.bookTypeCombo(),
                        				,
                        				,
                        				,
                        				ils_book_entry.app.categoryCombo()*/
                        			]
                        		}
                        	]
                        	
                        }
 		        ]
 					},
 					new Ext.TabPanel({

		        width:'auto',
		        activeTab: 0,
		        frame:true,
		        height: 490,
               // autoScroll: true,
                deferredRender: false,
		        //defaults:{autoHeight: true},
		        items:[
		        {
		        title: 'Book Entries', 
		        height: 'auto',
		        frame: true, 
		        layout: 'form', 
		        autoScroll: true, 
		        items:[
		        	{
		        		xtype: 'fieldset',
		        		width: 'auto',
		        		height: 'auto',
		        		labelWidth: 130,
		        		items: [
		        			{
                        	layout: 'column',
                        	width: 'auto',
                        	items: [
                        		{
                        			columnWidth: .5,
                        			layout: 'form',
                        			items: [
                        			{xtype: 'fieldset',
                        				anchor: '98%',
                        				height: 'auto',
                        				items: [
                        				{
                        					xtype: 'textfield',
                        					name: 'SERIES',
                        					id: 'SERIES',
                        					fieldLabel: 'Series Title',
                        					allowBlank: true,
                        					maxLength: 35,
                        					anchor: '93%'
                        				},
                        				{
                        					xtype: 'textfield',
                        					name: 'EDITION',
                        					id: 'EDITION',
                        					fieldLabel: 'Edition',
                        					allowBlank: true,
                        					maxLength: 35,
                        					anchor: '93%'
                        				},
                        				{
                        					xtype: 'textfield',
                        					name: 'VOLUME',
                        					id: 'VOLUME',
                        					fieldLabel: 'Volume',
                        					allowBlank: true,
                        					maxLength: 15,
                        					anchor: '93%'
                        				},
                        				{
                        					xtype: 'textfield',
                        					name: 'ISBN',
                        					id: 'ISBN',
                        					fieldLabel: 'ISBN*',
                        					allowBlank: false,
                        					maxLength: 35,
                        					anchor: '93%'
                        				}
                        				]
                        				},
                        				{xtype: 'fieldset',
                        				anchor: '98%',
                        				height: 'auto',
                        				items: [
                        				ils_book_entry.app.publisherCombo(),
                        				{
                        					xtype: 'textfield',
                        					name: 'PLACE',
                        					id: 'PLACE',
                        					fieldLabel: 'Place (Publication)*',
                        					allowBlank: false,
                        					maxLength: 35,
                        					anchor: '93%'
                        				},
                        				ils_book_entry.app.countryCombo(),
                        				new Ext.ux.form.SpinnerField({
							                fieldLabel: 'Copyright Date',
							                name: 'COPYRIGHT',
							                id: 'COPYRIGHT',
							                anchor: '93%',
							                minValue: 1970
							            }),
							            {
                        					xtype: 'textfield',
                        					name: 'PHYSDESC',
                        					id: 'PHYSDESC',
                        					fieldLabel: 'Physical Description*',
                        					allowBlank: true,
                        					maxLength: 128,
                        					anchor: '93%'
                        				}
                        				]
                        				},
                        				{
                        					xtype: 'fieldset',
                        					anchor: '98%',
                        					height: 100.25,
                        					
                        					labelWidth: 75,
                        					items: [
                        						{
	                        				layout: 'column',
	                        				width: 'auto',
	                        				height: 'auto',
	                        				items: [
	                        					{
	                        						columnWidth: .5,
	                        						layout: 'form',
	                        						//style: {marginTop: '25px'},
	                        						items: [
	                        						{
			                        					xtype: 'numberfield',
			                        					name: 'PAGES',
			                        					
			                        					id: 'PAGES',
			                        					fieldLabel: 'Pages*',
			                        					allowBlank: false,
			                        					anchor: '90%'
		                        					},
		                        					{
			                        					xtype: 'datefield',
			                        					name: 'PURCDATE',
			                        					id: 'PURCDATE',
			                        					fieldLabel: 'Purchase Date*',
			                        					allowBlank: false,
			                        					anchor: '90%',
			                        					format: 'Y-m-d',
			                        					maxValue: new Date()
			                        				}
	                        							
	                        						]
	                        					},
	                        					{
	                        						columnWidth: .5,
	                        						layout: 'form',
	                        						items: [
	                        							{
				                        					xtype: 'numberfield',
				                        					name: 'COPIES',
				                        					id: 'COPIES',
				                        					labelWidth: 100,
				                        					fieldLabel: 'Copies*',
				                        					allowBlank: false,
				                        					anchor: '86.5%'
				                        				},
				                        				{
				                        					xtype: 'numberfield',
				                        					name: 'AMOUNT',
				                        					id: 'AMOUNT',
				                        					labelWidth: 100,
				                        					fieldLabel: 'Amount*',
				                        					allowBlank: false,
				                        					anchor: '86.5%'
				                        				}
	                        							
	                        						]
	                        					}
                        					]
                        				}
                        					]
                        				}
                        				
                        				
                        			]
                        		},
                        		{
                        			columnWidth: .5,
                        			layout: 'form',
                        			labelAlign: 'top',
                        			items: [
                        				ils_book_entry.app.authorGrid,
                        				ils_book_entry.app.subjectGrid,
                        				{
				                        					xtype: 'textarea',
				                        					name: 'BIBLIO',
				                        					id: 'BIBLIO',
				                        					labelWidth: 100,
				                        					fieldLabel: 'Bibliography*',
				                        					allowBlank: false,
				                        					anchor: '100%',
				                        					msgTarget: 'qtip'
				                        					
				                        		}
				                        		
                        			]
                        		}
                        	]
                        }
		        		]
		        }
		        
		        ]
		       },
		        {
		        title: 'Cataloguing', 
		        height: 'auto',
		        frame: true, 
		        layout: 'form', 
		        autoScroll: true, 
		        items:[
		        	ExtCommon.util.createCombo('CLASSIFICATION', 'CLASIDNO', '50%', '<?php echo site_url('filereference/getCombo/FILECLAS/CLASIDNO/DESCRIPTION'); ?>', 'Classification*', false, false),
		        	ExtCommon.util.createCombo('BOOKTYPE', 'BOTYIDNO', '50%', '<?php echo site_url('filereference/getCombo/BOOKTYPE/BOTYIDNO/DESCRIPTION'); ?>', 'Book Type*', false, false),
		        	{
		        		layout: 'column',
		        		width: 'auto',
		        		labelWidth: 120,
		        		items: [
		        			{
		        				columnWidth: .35,
		        				layout: 'form',
		        				width: 'auto',
		        				items: [
		        					{
                        					xtype: 'textfield',
                        					name: 'DDC',
                        					id: 'DDC',
                        					fieldLabel: 'DDC (With Decimal)',
                        					allowBlank: true,
                        					maxLength: 128,
                        					anchor: '93%'
                    				}
		        				]
		        			},
		        			{
		        				columnWidth: .15,
		        				layout: 'fit',
		        				width: 'auto',
		        				items: [
		        					{
                        					xtype: 'textfield',
                        					name: 'DDCDECI',
                        					id: 'DDCDECI',
                        					fieldLabel: '',
                        					allowBlank: true,
                        					maxLength: 128,
                        					anchor: '93%'
                    				}
		        				]
		        			}
		        		]
		        	},
		        	ExtCommon.util.createCombo('CATEGORY', 'CATEIDNO', '50%', '<?php echo site_url('filereference/getCombo/FILECATE/CATEIDNO/DESCRIPTION'); ?>', 'Category*', false, false),
		        	{
                        					xtype: 'textfield',
                        					name: 'CALLNO2',
                        					id: 'CALLNO2',
                        					fieldLabel: 'Call Number',
                        					allowBlank: true,
                        					maxLength: 128,
                        					anchor: '50%',
                        					readOnly: true
                   },
                   {
                        					xtype: 'textarea',
                        					name: 'C_NOTES',
                        					id: 'C_NOTES',
                        					fieldLabel: '',
                        					allowBlank: true,
                        					maxLength: 128,
                        					anchor: '50%'
                    }
		        	
		        ]
		        
		       },
		       {
		        title: 'Miscellaneous', 
		        height: 'auto',
		        frame: true, 
		        layout: 'form', 
		        autoScroll: true,
		        labelWidth: 120, 
		        items:[
		        	ExtCommon.util.createCombo('ITEMSTATUS', 'ITSTIDNO', '50%', '<?php echo site_url('filereference/getItemStatusCombo'); ?>', 'Book/Item Status*', true, false),
		        	{
			                        					xtype: 'datefield',
			                        					name: 'D_INVENTORY',
			                        					id: 'D_INVENTORY',
			                        					fieldLabel: 'Inventory Date*',
			                        					allowBlank: true,
			                        					anchor: '50%',
			                        					format: 'Y-m-d',
			                        					maxValue: new Date()
			                        			},
			        ExtCommon.util.createCombo('COURSE', 'COURIDNO', '50%', '<?php echo site_url('filereference/getCourseCombo'); ?>', 'Course*', true, false),
		        	ExtCommon.util.createCombo('SEMESTER', 'SEMEIDNO', '50%', '<?php echo site_url('book/getSemesterCombo'); ?>', 'Semester*', true, false),
		        	{
				                        					xtype: 'textarea',
				                        					name: 'NOTES',
				                        					id: 'NOTES',
				                        					fieldLabel: 'Notes*',
				                        					allowBlank: true,
				                        					anchor: '50%'
				                        					
				                        		}
		        ]
		        
		        }
		        ],
		        listeners: {
		        	tabchange: function(panel, tab){
		        		
		        			if(panel.items.indexOf(panel.getActiveTab()) == 1){
		        				
		        			}
		        		
		        	}
		        }
		        })
		        
 		        ]
 		    });

 		    ils_book_entry.app.Form = form;
 		    
 		},
 		Add: function(){

 			ils_book_entry.app.setForm();

 		  	var _window;
 		  	
 		  	ils_book_entry.app.edit = false;

 		    _window = new Ext.Window({
 		        title: 'New Book',
 		        width: 1000,
 		        height:680,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_book_entry.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 	                handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_book_entry.app.Form)){//check if all forms are filled up

 		                ils_book_entry.app.Form.getForm().submit({
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
  								 ils_book_entry.app.edit = true;
 				                ExtCommon.util.refreshGrid(ils_book_entry.app.Grid.getId());
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
 		        }],
 		        listeners: {
 		        	beforedestroy: function(){
 		        		if(!ils_book_entry.app.edit){
 		        						Ext.Ajax.request({
						                            url: "<?=site_url("book/deleteTempData")?>",
													params:{ ACCESSNO: Ext.getCmp("ACCESSNO").getValue()},
													method: "POST",
													timeout:300000000,
									                success: function(responseObj){
						                		    	var response = Ext.decode(responseObj.responseText);
												if(response.success == true)
												{
													return;
						
												}
												else if(response.success == false)
												{
													return;
												}
													},
									                failure: function(f,a){
														Ext.Msg.show({
															title: 'Error Alert',
															msg: "There was an error encountered. Please contact the administrator",
															icon: Ext.Msg.ERROR,
															buttons: Ext.Msg.OK
														});
									                },
									                waitMsg: 'Please wait...'
												});
												}
 		        	}
 		        }
 		    });
 		  	_window.show();
 		},
 		Edit: function(){


 			if(ExtCommon.util.validateSelectionGrid(ils_book_entry.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ils_book_entry.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.ACCESSNO;
 			
 			ils_book_entry.app.edit = true;

 			ils_book_entry.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Book',
 		        width: 1000,
 		        height:680,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_book_entry.app.Form,
 		        buttons: [{
 		         	text: 'Save',
                                icon: '/images/icons/disk.png',  cls:'x-btn-text-icon',

 		            handler: function () {
 			            if(ExtCommon.util.validateFormFields(ils_book_entry.app.Form)){//check if all forms are filled up
 		                ils_book_entry.app.Form.getForm().submit({
 			                url: "<?=site_url("book/updateBook")?>",
 			                params: {id: id},
 			                method: 'POST',
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
 				                ExtCommon.util.refreshGrid(ils_book_entry.app.Grid.getId());
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

 		  	ils_book_entry.app.Form.getForm().load({
 				url: "<?=site_url("book/loadBook")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
 									
                                    _window.show();
                                    ils_book_entry.app.authorGrid.getStore().setBaseParam("ACCESSNO", Ext.getCmp("ACCESSNO").getValue()); 
 				                	ExtCommon.util.refreshGrid(ils_book_entry.app.authorGrid.getId());
 				                	ils_book_entry.app.subjectGrid.getStore().setBaseParam("ACCESSNO", Ext.getCmp("ACCESSNO").getValue()); 
 				                	ExtCommon.util.refreshGrid(ils_book_entry.app.subjectGrid.getId());
 				                	Ext.get('LOCAIDNO').dom.value = action.result.data.LOCAIDNO;
 				                	Ext.get('PUBLIDNO').dom.value = action.result.data.PUBLIDNO;
 				                	Ext.get('COUNIDNO').dom.value = action.result.data.COUNIDNO;
 				                	Ext.get('ITSTIDNO').dom.value = action.result.data.ITSTIDNO;
 				                	Ext.get('COURIDNO').dom.value = action.result.data.COURIDNO;
 				                	Ext.get('SEMEIDNO').dom.value = action.result.data.SEMEIDNO;
 				                	Ext.get('CLASIDNO').dom.value = action.result.data.CLASIDNO;
 				                	Ext.get('BOTYIDNO').dom.value = action.result.data.BOTYIDNO;
 				                	Ext.get('CATEIDNO').dom.value = action.result.data.CATEIDNO;
                                    
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


			if(ExtCommon.util.validateSelectionGrid(ils_book_entry.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ils_book_entry.app.Grid.getSelectionModel();
			var id = sm.getSelected().data.ACCESSNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("book/deleteBook")?>",
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
							ils_book_entry.app.Grid.getStore().load({params:{start:0, limit: 25}});

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
 				hiddenName: 'CLASCODE',
                hiddenId: 'CLASCODE',
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
 				beforequery: function()
				{
					/*if (Ext.getCmp('SERIALNUM').getValue() == "")
						return false;

					this.store.baseParams = {id: Ext.getCmp('SERIALNUM').getValue()};

		            var o = {start: 0, limit:10};
		            this.store.baseParams = this.store.baseParams || {};
		            this.store.baseParams[this.paramName] = '';
		            this.store.load({params:o, timeout: 300000});*/
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
 		},
 		locationCombo: function(){

		return {
			xtype:'combo',
			id:'LOCATION',
			hiddenName: 'LOCAIDNO',
                        hiddenId: 'LOCAIDNO',
			name: 'LOCATION',
			valueField: 'id',
			displayField: 'name',
			//width: 100,
			anchor: '90%',
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
			url: "<?php echo site_url("filereference/getLocationCombo"); ?>",
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a location'});

			},
			keypress: {buffer: 100, fn: function() {
			Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Location*'

			}
	},
	publisherCombo: function(){

		return {
			xtype:'combo',
			id:'PUBLISHER',
			hiddenName: 'PUBLIDNO',
                        hiddenId: 'PUBLIDNO',
			name: 'PUBLISHER',
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
			url: "<?php echo site_url("filereference/getPublishingCombo"); ?>",
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a publisher'});

			},
			keypress: {buffer: 100, fn: function() {
			Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Publisher*'

			}
	},
	semesterCombo: function(){

		return {
			xtype:'combo',
			id:'SEMESTER',
			hiddenName: 'SEMEIDNO',
            hiddenId: 'SEMEIDNO',
			name: 'SEMESTER',
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
			url: "<?php echo site_url("filereference/getSemesterCombo"); ?>",
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a'+this.fieldLabel});

			},
			keypress: {buffer: 100, fn: function() {
			Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Semester*'

			}
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
			url: "<?php echo site_url("filereference/getCountryCombo"); ?>",
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a country'});

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
	},
	bookTypeCombo: function(){

		return {
			xtype:'combo',
			id:'BOOKTYPE',
			hiddenName: 'BOTYIDNO',
            hiddenId: 'BOTYIDNO',
			name: 'COUNTRY',
			valueField: 'id',
			displayField: 'name',
			//width: 100,
			anchor: '88%',
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
			url: "<?php echo site_url("filereference/getBookTypeCombo"); ?>",
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a book type'});

			},
			keypress: {buffer: 100, fn: function() {
			Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Book Type*'

			}
	},
	categoryCombo: function(){

		return {
			xtype:'combo',
			id:'CATEGORY',
			hiddenName: 'CATEIDNO',
            hiddenId: 'CATEIDNO',
			name: 'CATEGORY',
			valueField: 'id',
			displayField: 'name',
			//width: 100,
			anchor: '88%',
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
			url: "<?php echo site_url("filereference/getCategoryCombo"); ?>",
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a '+this.fieldLabel});

			},
			keypress: {buffer: 100, fn: function() {
			Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Category*'

			}
	},
	authorCombo: function(){

		return {
			xtype:'combo',
			id:'AUTHOR',
			hiddenName: 'AUTHIDNO',
            hiddenId: 'AUTHIDNO',
			name: 'AUTHOR',
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
			url: "<?php echo site_url("filereference/getAuthorCombo"); ?>",
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
			this.el.set({qtip: 'Type at least ' + this.minChars + ' characters to search for a '+this.fieldLabel});

			},
			keypress: {buffer: 100, fn: function() {
			Ext.get(this.hiddenName).dom.value  = '';
			if(!this.getRawValue()){
			this.doQuery('', true);
			}
			}}
			},
			fieldLabel: 'Author*'

			}
	},
	AddAuthor: function(){
		if(!Ext.getCmp('ACCESSNO').validate()){
			Ext.Msg.show({
  								     title: 'Status',
 								     msg: "Access Number required before adding authors",
  								     buttons: Ext.Msg.OK,
  								     icon: Ext.Msg.ERROR
  								 });
			return;
		}
							
 			var form = new Ext.form.FormPanel({
 		        labelWidth: 150,
 		        url:"<?=site_url("book/addBookAuthor")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,

 		        items: [ {
 					xtype:'fieldset',
 					title:'Fields w/ Asterisks are required.',
 					width:'auto',
 					height:'auto',
 					items:[
                        ils_book_entry.app.authorCombo()

 		        ]
 					}
 		        ]
 		    });

 		  	var author_window;

 		    author_window = new Ext.Window({
 		        title: 'Add Author',
 		        width: 510,
 		        height:170,
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
 		                	params: {ACCESSNO: Ext.getCmp("ACCESSNO").getValue()},
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
  								Ext.getCmp("ACCESSNO").setReadOnly(true);
  								ils_book_entry.app.authorGrid.getStore().setBaseParam("ACCESSNO", Ext.getCmp("ACCESSNO").getValue()); 
 				                ExtCommon.util.refreshGrid(ils_book_entry.app.authorGrid.getId());
 				                author_window.destroy();
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
 			            author_window.destroy();
 		            }
 		        }]
 		    });
 		    
 		    if(!ils_book_entry.app.edit){	
       			Ext.Ajax.request({
						                            url: "<?=site_url("book/checkAccessNumber")?>",
													params:{ ACCESSNO: Ext.getCmp("ACCESSNO").getValue()},
													method: "POST",
													timeout:300000000,
									                success: function(responseObj){
						                		    	var response = Ext.decode(responseObj.responseText);
												if(response.success == true)
												{
													//Ext.getCmp("ACCESSNO").setReadOnly(true);
													author_window.show();
													return;
						
												}
												else if(response.success == false)
												{
													
													Ext.Msg.show({
														title: 'Error!',
														msg: response.data,
														icon: Ext.Msg.ERROR,
														buttons: Ext.Msg.OK,
											   			fn: function(btn, text){
											   			if (btn == 'ok'){
											   				Ext.getCmp("ACCESSNO").focus(true);
											   			}
											   			}
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
									                waitMsg: 'Please wait...'
												});
					}else{
						author_window.show();
					}		
 		  	
 		},
	AddSubject: function(){
		if(!Ext.getCmp('ACCESSNO').validate()){
			Ext.Msg.show({
  								     title: 'Status',
 								     msg: "Access Number required before adding subjects",
  								     buttons: Ext.Msg.OK,
  								     icon: Ext.Msg.ERROR
  								 });
			return;
		}
		
								
 			var form = new Ext.form.FormPanel({
 		        labelWidth: 150,
 		        url:"<?=site_url("book/addBookSubject")?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,

 		        items: [ {
 					xtype:'fieldset',
 					title:'Fields w/ Asterisks are required.',
 					width:'auto',
 					height:'auto',
 					items:[
 					ExtCommon.util.createCombo('BOOKSUBJECT', 'BOSUIDNO', '93%', '<?php echo site_url('filereference/getBookSubjectCombo'); ?>', 'Subject*', false, false)
 		        ]
 					}
 		        ]
 		    });

 		  	var subject_window;

 		    subject_window = new Ext.Window({
 		        title: 'Add Subject',
 		        width: 510,
 		        height:170,
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
 		                	params: {ACCESSNO: Ext.getCmp("ACCESSNO").getValue()},
 			                success: function(f,action){
                 		    	Ext.MessageBox.alert('Status', action.result.data);
                  		    	 Ext.Msg.show({
  								     title: 'Status',
 								     msg: action.result.data,
  								     buttons: Ext.Msg.OK,
  								     icon: 'icon'
  								 });
  								Ext.getCmp("ACCESSNO").setReadOnly(true);
  								ils_book_entry.app.subjectGrid.getStore().setBaseParam("ACCESSNO", Ext.getCmp("ACCESSNO").getValue()); 
 				                ExtCommon.util.refreshGrid(ils_book_entry.app.subjectGrid.getId());
 				                subject_window.destroy();
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
 			            subject_window.destroy();
 		            }
 		        }]
 		    });
 		    
 		    if(!ils_book_entry.app.edit){
       			Ext.Ajax.request({
						                            url: "<?=site_url("book/checkAccessNumber")?>",
													params:{ ACCESSNO: Ext.getCmp("ACCESSNO").getValue()},
													method: "POST",
													timeout:300000000,
									                success: function(responseObj){
						                		    	var response = Ext.decode(responseObj.responseText);
												if(response.success == true)
												{
													//Ext.getCmp("ACCESSNO").setReadOnly(true);
													subject_window.show();
													return;
						
												}
												else if(response.success == false)
												{
													
													Ext.Msg.show({
														title: 'Error!',
														msg: response.data,
														icon: Ext.Msg.ERROR,
														buttons: Ext.Msg.OK,
											   			fn: function(btn, text){
											   			if (btn == 'ok'){
											   				Ext.getCmp("ACCESSNO").focus(true);
											   			}
											   			}
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
									                waitMsg: 'Please wait...'
												});
						}else{
							subject_window.show();
						}
 		  	
 		},
 		DeleteAuthor: function(){


			if(ExtCommon.util.validateSelectionGrid(ils_book_entry.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ils_book_entry.app.authorGrid.getSelectionModel();
			var id = sm.getSelected().data.AUTHIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("book/deleteBookAuthor")?>",
							params:{ id: id, ACCESSNO: Ext.getCmp('ACCESSNO').getValue()},
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
							ils_book_entry.app.authorGrid.getStore().load();

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
 		DeleteSubject: function(){


			if(ExtCommon.util.validateSelectionGrid(ils_book_entry.app.Grid.getId())){//check if user has selected an item in the grid
			var sm = ils_book_entry.app.subjectGrid.getSelectionModel();
			var id = sm.getSelected().data.BOSUIDNO;
			Ext.Msg.show({
   			title:'Delete',
  			msg: 'Are you sure you want to delete this record?',
   			buttons: Ext.Msg.OKCANCEL,
   			fn: function(btn, text){
   			if (btn == 'ok'){

   			Ext.Ajax.request({
                            url: "<?=  site_url("book/deleteBookSubject")?>",
							params:{ id: id, ACCESSNO: Ext.getCmp('ACCESSNO').getValue()},
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
							ils_book_entry.app.subjectGrid.getStore().load();

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


		}//end of functions
 	}

 }();

 Ext.onReady(ils_book_entry.app.init, ils_book_entry.app);

</script>
<div id="mainBody"></div>
