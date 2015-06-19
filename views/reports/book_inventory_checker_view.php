<script type="text/javascript" src="/js/ext34/examples/ux/CheckColumn.js"></script>
<script type="text/javascript">
Ext.namespace("book_inventory_checker");
book_inventory_checker.app = function(){
	return{
		init: function(){
			ExtCommon.util.init();
			ExtCommon.util.quickTips();
			this.getgrid();
		},
		
		getgrid: function(){
			//var store = new Ext.data.Store({});
			var store = new Ext.data.Store({ 
				proxy: new Ext.data.HttpProxy({ 
					url: "<?=site_url("reports/getBookInventoryChecker")?>",
					method: "POST"
				}),																
				reader: new Ext.data.JsonReader({
					root: "data",
					id: "id",	
					totalProperty: "totalCount",	
					fields:[													
						{ name: "ACCESSNO" },
						{ name: "TITLE" },
						{ name: "AT_LIBRARY", type: "boolean"}
					]		
				}),
				remoteSort:true,							 							
				baseParams: {start: 0, limit: 50}							
			});
 			
 			var grid = new Ext.grid.GridPanel({										
 				id: 'book_inventory_checkergrid',	
 				height: 300,
 				width: 900,
 				border: true,         		        		
 				store: store,
 				cm: new Ext.grid.ColumnModel([								  
 					{ header: "Access Number", dataIndex: "ACCESSNO", width: 100, sortable: true },
 					{ header: "Title", dataIndex: "TITLE", width: 500, sortable: true },
 					{
                             			xtype: 'checkcolumn',
                                          	header: 'At Library',
                                          	dataIndex: 'AT_LIBRARY',
                                          	width: 75,
                                          	listeners: {
                                          		mousedown: function(col, e, record){
                                                      		Ext.Ajax.request({
                                                    			url: "<?php echo site_url("reports/updateBookInventoryChecker")?>",
                                                                  	params:{ id: record.get("ACCESSNO"), AT_LIBRARY: record.get("AT_LIBRARY")},
                                                                  	method: "POST",
                                                                  	timeout:300000000,
                                                          		success: function(responseObj){
                                                      				var response = Ext.decode(responseObj.responseText);
                                                      				if(response.success == true){
                      									return;
                      								}
                                                            			else if(response.success == false){
	                                                           			return;
                                                            			}
                                                                  	},
                                                          		failure: function(f,a){
                                                                        },
                                                          		waitMsg: 'Deleting Data...'
                                                            	});		
                       					}
                       				}
                       			}                 				
				]),
 				sm: new Ext.grid.RowSelectionModel({singleSelect:true}),		        
 	        		loadMask: true,
 	        		bbar: 			        	
 	        			new Ext.PagingToolbar({
 		        			autoShow: true,
 				        	pageSize: 50,
	 				        store: store,
	 				        displayInfo: true,						        
	 				        displayMsg: 'Displaying Results {0} - {1} of {2}',
	 				        emptyMsg: "No Data Found."					        
	 				}),			        	
 				//tbar:[]
 	    		});	

 			book_inventory_checker.app.Grid = grid;
 			book_inventory_checker.app.Grid.getStore().load({params:{start: 0, limit: 50}});

 			var _window = new Ext.Panel({
	 		        title: 'Book Inventory Checker',
	 		        height: 420,
	 		        width: '100%',
	 		        renderTo: 'mainBody',
	 		        draggable: false,
	 		        layout: 'fit',
	 		        items: [book_inventory_checker.app.Grid],
	 		        resizable: false
 	        	}).render();
		}
			 
	 }
 }();
 Ext.onReady(book_inventory_checker.app.init, book_inventory_checker.app);
 
 </script>
<div id = "mainBody"></div>