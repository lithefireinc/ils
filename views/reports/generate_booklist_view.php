<script type="text/javascript" src="/js/ext34/examples/ux/Spinner.js"></script>
<script type="text/javascript" src="/js/ext34/examples/ux/SpinnerField.js"></script>
<link rel="stylesheet" type="text/css" href="/js/ext34/examples/ux/css/Spinner.css" />
<script type="text/javascript">
Ext.namespace("generate_booklist");
generate_booklist.app = function(){
	return{
		init: function(){
			ExtCommon.util.init();
			ExtCommon.util.quickTips();
			this.getgrid();
		},

		getgrid: function(){
			ExtCommon.util.renderSearchField('');
			//var store = new Ext.data.Store({});
			var store = new Ext.data.Store({ 
				proxy: new Ext.data.HttpProxy({ 
					url: "<?=site_url("reports/getGenerateBooklist")?>",
					method: "POST"
				}),																
				reader: new Ext.data.JsonReader({
					root: "data",
					id: "id",	
					totalProperty: "totalCount",	
					fields:[													
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
				remoteSort:true,							 							
				baseParams: {start: 0, limit: 50}							
			});
 			
 			var grid = new Ext.grid.GridPanel({										
 				id: 'generate_booklistgrid',	
 				height: 300,
 				width: 900,
 				border: true,         		        		
 				store: store,
 				cm: new Ext.grid.ColumnModel([								  
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
 				tbar:[
 					'Filter by Classification: ', ' ',
 				//	ExtCommon.util.createCombo('FILTERDESCRIPTION', 'FILTERCLASIDNO', '93%', '<?php echo site_url('reports/getClassificationCombo'); ?>', 'Classificatio', true, false),
 					generate_booklist.app.classificationCombo(),
 					{
 						xtype: 'tbfill'
 					}
 				]
 	    		});	

 			generate_booklist.app.Grid = grid;
 			generate_booklist.app.Grid.getStore().load({params:{start: 0, limit: 50}});

 			var _window = new Ext.Panel({
	 		        title: 'Generate Booklist',
	 		        height: 420,
	 		        width: '100%',
	 		        renderTo: 'mainBody',
	 		        draggable: false,
	 		        layout: 'fit',
	 		        items: [generate_booklist.app.Grid],
	 		        resizable: false
 	        	}).render();
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
				//	anchor: '90%',
					width: 250,
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
						url: "<?php echo site_url('reports/getClassificationCombo'); ?>",
						baseParams: {start: 0, limit: 10}

					}),
					listeners: {
					beforequery: function(qe)
					{
		                       delete qe.combo.lastQuery;
		                                
					},
						select: function (combo, record, index){
							this.setRawValue(record.get('name'));
							Ext.get(this.hiddenId).dom.value  = record.get('id');
							generate_booklist.app.Grid.getStore().setBaseParam('CLASIDNO', record.get("id"));
							generate_booklist.app.Grid.getStore().load();
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
							//Ext.get(this.hiddenName).dom.value  = '';
							if(!this.getRawValue()){
								this.doQuery('', true);
							}
						}}
					},
					fieldLabel: 'Category*'

				}
				}
			 
	 }
 }();
 Ext.onReady(generate_booklist.app.init, generate_booklist.app);
 
 </script>
<div id = "mainBody"></div>