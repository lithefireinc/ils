<?php
class Reports extends MY_Controller{

    function Reports(){
        parent::__construct();

    }
	
	function getNextCharId($table, $id_field, $length = 5){
        $pdo = new PDO("mysql:host=localhost;port=3306;dbname=lithefzj_engine", "lithefzj_darryl", "LeyyeL03@!");
        //mysql_query("SELECT LPAD(MAX(SUBSTR($id_field, 2))+1, 5, '0') AS IDCHAR FROM $table");
        foreach($pdo->query("SELECT LPAD(MAX(SUBSTR($id_field, 2))+1, $length, '0') AS IDCHAR FROM $table") as $row):
           if($row['IDCHAR'] == null)
            return '00001';
           else
            return $row['IDCHAR'];
        endforeach;
    }
	
	function sort_by_key($arr,$key){
        global $key2sort;
        $key2sort = $key;
        uasort($arr,  array($this, 'sbk'));
        return ($arr);
    }

    function sbk($a, $b){global $key2sort; return (strcasecmp ($a[$key2sort],$b[$key2sort]));}
    
    
    function bibliography(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: Reports';

        $this->layout->view('reports/bibliography_view', $data);
        
    }
    
    function getBibliography(){
		$this->load->model('lithefire_model','lithefire',TRUE);
	        $db = 'library';
	        $filter = "";
	        $group = "";
	
	        $start=$this->input->post('start');
	        $limit=$this->input->post('limit');
	
	        $sort = $this->input->post('sort');
	        $dir = $this->input->post('dir');
	        $querystring = $this->input->post('query');
	
	        if(empty($sort) && empty($dir)){
	            	$sort = "ACCESSNO ASC";
	        }else{
	            	$sort = "$sort $dir";
	        }
	
	        if(!empty($querystring)){
	        	$filter = "";
	        }
	        
		
	        $records = array();
	        $table = "BIBLIO a LEFT JOIN BOOKS b ON a.ACCESSNO = b.ACCESSNO";
	        $fields = array("a.ACCESSNO, a.BIBLIO, b.CALLNO");
	
	        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
	        //die($this->db->last_query());
	
	        $temp = array();
	        $total = 0;
	        
	        if($records){
	        	foreach($records as $row):
				$temp[] = $row;
	            		$total++;
	       	 	endforeach;
	        }
	        
	        $data['data'] = $temp;
	        $data['success'] = true;
	        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
	        die(json_encode($data));
	}
 
    function classificationCounter(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: Reports';

        $this->layout->view('reports/classification_counter_view', $data);
        
    }   
    
    function getClassificationCounter(){
	        $this->load->model('lithefire_model','lithefire',TRUE);
	        $db = 'fr';
	        $filter = "";
	        $group = "b.DESCRIPTION";
	
	        $start=$this->input->post('start');
	        $limit=$this->input->post('limit');
	
	        $sort = $this->input->post('sort');
	        $dir = $this->input->post('dir');
	        $querystring = $this->input->post('query');
	
	        if(empty($sort) && empty($dir)){
	            	$sort = "a.CLASIDNO ASC";
	        }else{
	            	$sort = "$sort $dir";
	        }
	
	        if(!empty($querystring)){
	        	$filter = "";
	        }
	        
		
	        $records = array();
	        $table = "lithefzj_library.BOOKS a LEFT JOIN lithefzj_engine.FILECLAS b ON a.CLASIDNO = b.CLASIDNO";
	        $fields = array("b.DESCRIPTION, COUNT(b.DESCRIPTION) AS NUMBER_TITLES, SUM(a.COPYRIGHT >= year( now( ) )-5) AS COPYRIGHT, SUM(a.VOLUME != '') AS NUMBER_VOLUMES");
	
	        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
	        //die($this->db->last_query());
	
	        $temp = array();
	        $total = 0;
	        
	        if($records){
	        	foreach($records as $row):
				$temp[] = $row;
	            		$total++;
	       	 	endforeach;
	        }
	        
	        $data['data'] = $temp;
	        $data['success'] = true;
	        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
	        die(json_encode($data));
	}
	
	function bookInventoryChecker(){

	        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
	        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
	        $data['title'] = 'ILS: Reports';

        	$this->layout->view('reports/book_inventory_checker_view', $data);
        
    	}
    	
    	function getBookInventoryChecker(){
		$this->load->model('lithefire_model','lithefire',TRUE);
	        $db = 'library';
	        $filter = "";
	        $group = "";
	
	        $start=$this->input->post('start');
	        $limit=$this->input->post('limit');
	
	        $sort = $this->input->post('sort');
	        $dir = $this->input->post('dir');
	        $querystring = $this->input->post('query');
	
	        if(empty($sort) && empty($dir)){
	            	$sort = "ACCESSNO ASC";
	        }else{
	            	$sort = "$sort $dir";
	        }
	
	        if(!empty($querystring)){
	        	$filter = "";
	        }
	        
		
	        $records = array();
	        $table = "BOOKS";
	        $fields = array("ACCESSNO, TITLE, AT_LIBRARY");
	
	        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
	        //die($this->db->last_query());
	
	        $temp = array();
	        $total = 0;
	        
	        if($records){
	        	foreach($records as $row):
				$temp[] = $row;
	            		$total++;
	       	 	endforeach;
	        }
	        
	        $data['data'] = $temp;
	        $data['success'] = true;
	        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
	        die(json_encode($data));
	}
	
	function updateBookInventoryChecker(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'library';
        $table = "BOOKS";
		$param = "ACCESSNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        
		$post = $this->input->post();
		
		$AT_LIBRARY = $post['AT_LIBRARY'];
		
		if($AT_LIBRARY == "true"){
			$var = 1;
		}elseif($AT_LIBRARY == "false"){
			$var = 0;
		}	
        
		$input = array("AT_LIBRARY"=>$var);

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);
        die(json_encode($data));
    }
    
    function generateBooklist(){

	        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
	        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
	        $data['title'] = 'ILS: Reports';

        	$this->layout->view('reports/generate_booklist_view', $data);
        
    	}
    
    function getGenerateBooklist(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
        $filter = "a.IS_DELETE = 0";
        $group = "";
		$fr_db = $this->config->item("fr_db");
		$ils_db = $this->config->item("ils_db");
	
		$CLASIDNO = $this->input->post('CLASIDNO');

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "TITLE ASC";
        }else{
            $sort = "$sort $dir";
        }
		
		$filter2 = "(";
		
		if(!empty($CLASIDNO)){
			if($filter2 == "(")
				$filter2 .="g.CLASIDNO LIKE '%$CLASIDNO%'";
			else
				$filter2 .=" AND g.CLASIDNO LIKE '%$CLASIDNO%'";
		}
		
		$filter2 .= ")";
				
		if($filter2 != "()")
		$filter .= " AND $filter2";
		
        $records = array();
        $table = "$ils_db.BOOKS a LEFT JOIN $fr_db.FILELOCA b ON a.LOCAIDNO = b.LOCAIDNO LEFT JOIN $fr_db.FILEPUBL c ON a.PUBLIDNO = c.PUBLIDNO
        LEFT JOIN $fr_db.FILECOUN d ON a.COUNIDNO = d.COUNIDNO LEFT JOIN $fr_db.BOOKTYPE e ON a.BOTYIDNO = e.BOTYIDNO LEFT JOIN
        $fr_db.FILECATE f ON a.CATEIDNO = f.CATEIDNO LEFT JOIN $fr_db.FILECLAS g ON a.CLASIDNO = g.CLASIDNO";
        $fields = array("a.*", "b.DESCRIPTION AS LOCATION", "c.DESCRIPTION AS PUBLISHER", "d.DESCRIPTION AS COUNTRY", "e.DESCRIPTION AS BOOKTYPE", "f.DESCRIPTION AS CATEGORY, g.*");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());

		//die($this->lithefire->currentQuery());
        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):

            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }
    
    function getClassificationCombo(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'fr';
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');

	
        if(empty($sort) && empty($dir)){
            $sort = "DESCRIPTION ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(CLASIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%')";
        }
        
		$ils_db = $this->config->item("ils_db");
		
        $records = array();
        $table = "lithefzj_engine.FILECLAS";
        $fields = array("CLASIDNO as id", "DESCRIPTION as name");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):

            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

    
}
?>