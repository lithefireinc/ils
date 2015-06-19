 <script type="text/javascript" src="/js/ext34/examples/ux/Spinner.js"></script>
 <script type="text/javascript" src="/js/ext34/examples/ux/SpinnerField.js"></script>
 <link rel="stylesheet" type="text/css" href="/js/ext34/examples/ux/css/Spinner.css" />
<script type="text/javascript">
 Ext.namespace("ils_book_search");
 ils_book_search.app = function()
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
 							url: "<?=site_url("book/getBookSearch")?>",
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
 						baseParams: {start: 0, limit: 25},
 						listeners: {
 							load: function(qe, rec, o){
 								this.setBaseParam("TITLE", Ext.getCmp('FILTERTITLE').getValue());
 		
 							}
 						}
 					});


 			var grid = new Ext.grid.GridPanel({
 				id: 'ils_book_searchgrid',
 				height: 333,
 				width: '100%',
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
 						  { header: "Location", width: 100, sortable: true, dataIndex: "LOCATION" },
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
 					     	text: 'VIEW',
							icon: '/images/icons/application_edit.png',
 							cls:'x-btn-text-icon',

 					     	handler: ils_book_search.app.Edit

 					 	}
 	    			 ]
 	    	});

 			ils_book_search.app.Grid = grid;
 			ils_book_search.app.Grid.getStore().load({params:{start: 0, limit: 25}});
 			
 			var filter_form = new Ext.form.FormPanel({
 		        labelWidth: 80,
 		        url:"<?php echo site_url("hr/insertEmployee"); ?>",
 		        method: 'POST',
 		        defaultType: 'textfield',
 		        frame: true,
                       // height: 100,
                        id: 'teacherForm',
                       // autoScroll: true,
                       // width: 900,
 		        items: [ {
 					xtype:'fieldset',

 					width:'auto',
 					height:'auto',
 					items:[

                                            {
			            layout:'column',
			            width: 'auto',
			            items: [
                                        {
	 	 			          columnWidth:.33,
	 	 			          layout: 'form',
	 	 			          items: [
	 	 			          {
	 	 			          	xtype: 'textfield',
	 	 			          	id: 'FILTERTITLE',
	 	 			          	name: 'FILTERTITLE',
	 	 			          	allowBlank : true,
	 	 			          	anchor: '93%',
	 	 			          	fieldLabel: 'Title'
	 	 			          },
	 	 			          ExtCommon.util.createCombo('FILTERSUBJECT', 'FILTERBOSUIDNO', '93%', '<?php echo site_url('filereference/getBookSubjectCombo'); ?>', 'Subject', true, false)
                                              ]
                                              },
                                              {
	 	 			          columnWidth:.33,
	 	 			          layout: 'form',
	 	 			          items: [{
	 	 			          	xtype: 'textfield',
	 	 			          	id: 'FILTERCALLNO',
	 	 			          	name: 'FILTERCALLNO',
	 	 			          	allowBlank : true,
	 	 			          	anchor: '93%',
	 	 			          	fieldLabel: 'Call No.'
	 	 			          },
	 	 			          ExtCommon.util.createCombo('FILTERAUTHOR', 'FILTERAUTHIDNO', '93%', '<?php echo site_url('filereference/getAuthorCombo'); ?>', 'Author', true, false)
	 	 			          ]
                                              },
                                              {
	 	 			          columnWidth:.33,
	 	 			          layout: 'form',
	 	 			          items: [{
	 	 			          	xtype: 'textfield',
	 	 			          	id: 'FILTERISBN',
	 	 			          	name: 'FILTERISBN',
	 	 			          	allowBlank : true,
	 	 			          	anchor: '93%',
	 	 			          	fieldLabel: 'ISBN'
	 	 			          }]
                                              }
                                    ]
                                }


 		        ]
 					}
 		        ],
                        buttons: [{
 		         	text: 'Refresh List',
                                icon: '/images/icons/arrow_rotate_clockwise.png',
 	                handler: function () {
                            if(ExtCommon.util.validateFormFields(filter_form)){//check if all forms are filled up
                           
                            ils_book_search.app.Grid.getStore().setBaseParam('TITLE', Ext.getCmp('FILTERTITLE').getValue());
                            ils_book_search.app.Grid.getStore().setBaseParam("BOSUIDNO", Ext.get('FILTERBOSUIDNO').dom.value);
                            ils_book_search.app.Grid.getStore().setBaseParam("AUTHIDNO", Ext.get('FILTERAUTHIDNO').dom.value);
                            ils_book_search.app.Grid.getStore().setBaseParam("CALLNO", Ext.get('FILTERCALLNO').dom.value);
                            ils_book_search.app.Grid.getStore().setBaseParam("ISBN", Ext.get('FILTERISBN').dom.value);
                            ils_book_search.app.Grid.getStore().load();
                            }else return;





 	                }
 	            },
 	            {
 		         	text: 'Clear All',
                                icon: '/images/icons/delete.png',
 	                handler: function () {
						filter_form.form.reset()

 	                }
 	            }]
 		    });
            ils_book_search.app.filter_form = filter_form;

 			var _window = new Ext.Panel({
 		        title: 'Books',
 		        width: '100%',
 		        height:490,
 		        renderTo: 'mainBody',
 		        draggable: false,
 		        layout: 'form',
 		        items: [ils_book_search.app.filter_form, ils_book_search.app.Grid],
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
 					 	}
 	    			 ]
 	    	});

 			ils_book_search.app.authorGrid = author_grid;
 			
 			
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
 					 	}
 	    			 ]
 	    	});

 			ils_book_search.app.subjectGrid = subject_grid;
 			
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
                        					readOnly: true,
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
                        					readOnly: true,
                        					fieldLabel: 'Call No*',
                        					allowBlank: false,
                        					maxLength: 128,
                        					anchor: '88%'
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
                        					readOnly: true,
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
                        				ExtCommon.util.createCombo('LOCATION', 'LOCAIDNO', '90%', '<?php echo site_url('filereference/getLocationCombo'); ?>', 'Location*', true, true)
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
                        					readOnly: true,
                        					fieldLabel: 'Series Title',
                        					allowBlank: true,
                        					maxLength: 35,
                        					anchor: '93%'
                        				},
                        				{
                        					xtype: 'textfield',
                        					name: 'EDITION',
                        					id: 'EDITION',
                        					readOnly: true,
                        					fieldLabel: 'Edition',
                        					allowBlank: true,
                        					maxLength: 35,
                        					anchor: '93%'
                        				},
                        				{
                        					xtype: 'textfield',
                        					name: 'VOLUME',
                        					id: 'VOLUME',
                        					readOnly: true,
                        					fieldLabel: 'Volume',
                        					allowBlank: true,
                        					maxLength: 15,
                        					anchor: '93%'
                        				},
                        				{
                        					xtype: 'textfield',
                        					name: 'ISBN',
                        					id: 'ISBN',
                        					readOnly: true,
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
                        				ExtCommon.util.createCombo('PUBLISHER', 'PUBLIDNO', '93%', '<?php echo site_url('filereference/getPublisherCombo'); ?>', 'Publisher*', true, true),
                        				{
                        					xtype: 'textfield',
                        					name: 'PLACE',
                        					id: 'PLACE',
                        					readOnly: true,
                        					fieldLabel: 'Place (Publication)*',
                        					allowBlank: false,
                        					maxLength: 35,
                        					anchor: '93%'
                        				},
                        				ExtCommon.util.createCombo('COUNTRY', 'COUNIDNO', '93%', '<?php echo site_url('filereference/getCountryCombo'); ?>', 'Country*', true, true),
                        				new Ext.ux.form.SpinnerField({
							                fieldLabel: 'Copyright Date',
							                name: 'COPYRIGHT',
							                id: 'COPYRIGHT',
							                readOnly: true,
							                anchor: '93%',
							                minValue: 1970
							            }),
							            {
                        					xtype: 'textfield',
                        					name: 'PHYSDESC',
                        					id: 'PHYSDESC',
                        					readOnly: true,
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
			                        					readOnly: true,
			                        					id: 'PAGES',
			                        					fieldLabel: 'Pages*',
			                        					allowBlank: false,
			                        					anchor: '90%'
		                        					},
		                        					{
			                        					xtype: 'datefield',
			                        					name: 'PURCDATE',
			                        					id: 'PURCDATE',
			                        					readOnly: true,
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
				                        					readOnly: true,
				                        					labelWidth: 100,
				                        					fieldLabel: 'Copies*',
				                        					allowBlank: false,
				                        					anchor: '86.5%'
				                        				},
				                        				{
				                        					xtype: 'numberfield',
				                        					name: 'AMOUNT',
				                        					id: 'AMOUNT',
				                        					readOnly: true,
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
                        				ils_book_search.app.authorGrid,
                        				ils_book_search.app.subjectGrid,
                        				{
				                        					xtype: 'textarea',
				                        					name: 'BIBLIO',
				                        					id: 'BIBLIO',
				                        					readOnly: true,
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
		        title: 'Miscellaneous', 
		        height: 'auto',
		        frame: true, 
		        layout: 'form', 
		        autoScroll: true,
		        labelWidth: 120, 
		        items:[
		        	ExtCommon.util.createCombo('ITEMSTATUS', 'ITSTIDNO', '50%', '<?php echo site_url('filereference/getItemStatusCombo'); ?>', 'Book/Item Status*', true, true),
		        	{
			                        					xtype: 'datefield',
			                        					name: 'D_INVENTORY',
			                        					id: 'D_INVENTORY',
			                        					readOnly: true,
			                        					fieldLabel: 'Inventory Date*',
			                        					allowBlank: true,
			                        					anchor: '50%',
			                        					format: 'Y-m-d',
			                        					maxValue: new Date()
			                        			},
			        ExtCommon.util.createCombo('COURSE', 'COURIDNO', '50%', '<?php echo site_url('filereference/getCourseCombo'); ?>', 'Course*', true, true),
		        	ExtCommon.util.createCombo('SEMESTER', 'SEMEIDNO', '50%', '<?php echo site_url('book/getSemesterCombo'); ?>', 'Semester*', true, true),
		        	{
				                        					xtype: 'textarea',
				                        					name: 'NOTES',
				                        					id: 'NOTES',
				                        					readOnly: true,
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

 		    ils_book_search.app.Form = form;
 		    
 		},
 		Edit: function(){


 			if(ExtCommon.util.validateSelectionGrid(ils_book_search.app.Grid.getId())){//check if user has selected an item in the grid
 			var sm = ils_book_search.app.Grid.getSelectionModel();
 			var id = sm.getSelected().data.ACCESSNO;
 			
 			ils_book_search.app.edit = true;

 			ils_book_search.app.setForm();
 		    _window = new Ext.Window({
 		        title: 'Update Book',
 		        width: 1000,
 		        height:680,
 		        layout: 'fit',
 		        plain:true,
 		        modal: true,
 		        bodyStyle:'padding:5px;',
 		        buttonAlign:'center',
 		        items: ils_book_search.app.Form,
 		        buttons: [{
 		            text: 'Close',
                            icon: '/images/icons/cancel.png', cls:'x-btn-text-icon',

 		            handler: function(){
 			            _window.destroy();
 		            }
 		        }]
 		    });

 		  	ils_book_search.app.Form.getForm().load({
 				url: "<?=site_url("book/loadBook")?>",
 				method: 'POST',
 				params: {id: id},
 				timeout: 300000,
 				waitMsg:'Loading...',
 				success: function(form, action){
 									
                                    _window.show();
                                    ils_book_search.app.authorGrid.getStore().setBaseParam("ACCESSNO", Ext.getCmp("ACCESSNO").getValue()); 
 				                	ExtCommon.util.refreshGrid(ils_book_search.app.authorGrid.getId());
 				                	ils_book_search.app.subjectGrid.getStore().setBaseParam("ACCESSNO", Ext.getCmp("ACCESSNO").getValue()); 
 				                	ExtCommon.util.refreshGrid(ils_book_search.app.subjectGrid.getId());
 				                	Ext.get('LOCAIDNO').dom.value = action.result.data.LOCAIDNO;
 				                	Ext.get('PUBLIDNO').dom.value = action.result.data.PUBLIDNO;
 				                	Ext.get('COUNIDNO').dom.value = action.result.data.COUNIDNO;
 				                	Ext.get('ITSTIDNO').dom.value = action.result.data.ITSTIDNO;
 				                	Ext.get('COURIDNO').dom.value = action.result.data.COURIDNO;
 				                	Ext.get('SEMEIDNO').dom.value = action.result.data.SEMEIDNO;
                                    
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
	}//end of functions
 	}

 }();

 Ext.onReady(ils_book_search.app.init, ils_book_search.app);

</script>
<div id="mainBody"></div>
