<script type="text/javascript">
Ext.namespace("classification_counter");
classification_counter.app = function(){
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
					url: "<?=site_url("reports/getClassificationCounter")?>",
					method: "POST"
				}),																
				reader: new Ext.data.JsonReader({
					root: "data",
					id: "id",	
					totalProperty: "totalCount",	
					fields:[													
						{ name: "DESCRIPTION" },
						{ name: "NUMBER_TITLES" },
						{ name: "NUMBER_VOLUMES" },
						{ name: "COPYRIGHT" }
					]		
				}),
				remoteSort:true,							 							
				baseParams: {start: 0, limit: 25}							
			});
 			
 			var grid = new Ext.grid.GridPanel({										
 				id: 'classification_countergrid',	
 				height: 300,
 				width: 900,
 				border: true,         		        		
 				store: store,
 				cm: new Ext.grid.ColumnModel([								  
 				 	{ header: "Classification", dataIndex: "DESCRIPTION", width: 250, sortable: true },
 					{ header: "Number of Titles", dataIndex: "NUMBER_TITLES", width: 150, sortable: true},
 				  	{ header: "Number of Volume", dataIndex: "NUMBER_VOLUMES", width: 150, sortable: true},
 				  	{ header: "Copyright within 5 Years", dataIndex: "COPYRIGHT", width: 150, sortable: true}
				]),
 				sm: new Ext.grid.RowSelectionModel({singleSelect:true}),		        
 	        		loadMask: true,
 	        		bbar: 			        	
 	        			new Ext.PagingToolbar({
 		        			autoShow: true,
 				        	pageSize: 25,
	 				        store: store,
	 				        displayInfo: true,						        
	 				        displayMsg: 'Displaying Results {0} - {1} of {2}',
	 				        emptyMsg: "No Data Found."					        
	 				}),			        	
 				//tbar:[]
 	    		});	

 			classification_counter.app.Grid = grid;
 			classification_counter.app.Grid.getStore().load({params:{start: 0, limit: 25}});

 			var _window = new Ext.Panel({
	 		        title: 'Book Classification Counter',
	 		        width: '100%',
	 		        height:420,
	 		        renderTo: 'mainBody',
	 		        draggable: false,
	 		        layout: 'fit',
	 		        items: [classification_counter.app.Grid],
	 		        resizable: false
 	        	}).render();
		}
			 
	 }
 }();
 Ext.onReady(classification_counter.app.init, classification_counter.app);
 
 </script>
<div id = "mainBody"></div>
