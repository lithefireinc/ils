<script type="text/javascript">
Ext.namespace("bibliography");
bibliography.app = function(){
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
					url: "<?=site_url("reports/getBibliography")?>",
					method: "POST"
				}),																
				reader: new Ext.data.JsonReader({
					root: "data",
					id: "id",	
					totalProperty: "totalCount",	
					fields:[													
						{ name: "ACCESSNO" },
						{ name: "CALLNO" },
						{ name: "BIBLIO" }
					]		
				}),
				remoteSort:true,							 							
				baseParams: {start: 0, limit: 50}							
			});
 			
 			var grid = new Ext.grid.GridPanel({										
 				id: 'bibliographygrid',	
 				height: 300,
 				width: '100%',
 				border: true,         		        		
 				store: store,
 				cm: new Ext.grid.ColumnModel([								  
 				 	{ header: "Access Number", dataIndex: "ACCESSNO", width: 100, sortable: true },
 					{ header: "Call Number", dataIndex: "CALLNO", width: 150, sortable: true },
 					{ header: "Bibliography", dataIndex: "BIBLIO", width: 900, sortable: true }
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

 			bibliography.app.Grid = grid;
 			bibliography.app.Grid.getStore().load({params:{start: 0, limit: 50}});

 			var _window = new Ext.Panel({
	 		        title: 'Bibliography',
	 		        height: 420,
	 		        width: '100%',
	 		        renderTo: 'mainBody',
	 		        draggable: false,
	 		        layout: 'fit',
	 		        items: [bibliography.app.Grid],
	 		        resizable: false
 	        	}).render();
		}
			 
	 }
 }();
 Ext.onReady(bibliography.app.init, bibliography.app);
 
 </script>
<div id = "mainBody"></div>