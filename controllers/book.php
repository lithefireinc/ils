<?php
class Book extends MY_Controller{

    function Book(){
        parent::__construct();

    }
	
	function entry(){
  

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: Book Entry';


  
        $this->layout->view('book/book_entry_view', $data);

    }
	
	function getBooks(){
        $db = 'default';
        $filter = "a.IS_DELETE = 0";
        $group = "";

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

        if(!empty($querystring)){
            $filter .= " AND (TITLE LIKE '%$querystring%' OR BOOKIDNO LIKE '%$querystring%' OR CALLNO LIKE '%$querystring%')";
        }
        
		$fr_db = $this->config->item("fr_db");
        $records = array();
        $table = "BOOKS a LEFT JOIN $fr_db.FILELOCA b ON a.LOCAIDNO = b.LOCAIDNO LEFT JOIN $fr_db.FILEPUBL c ON a.PUBLIDNO = c.PUBLIDNO
        LEFT JOIN $fr_db.FILECOUN d ON a.COUNIDNO = d.COUNIDNO LEFT JOIN $fr_db.BOOKTYPE e ON a.BOTYIDNO = e.BOTYIDNO LEFT JOIN
        $fr_db.FILECATE f ON a.CATEIDNO = f.CATEIDNO";
        $fields = array("a.*", "b.DESCRIPTION AS LOCATION", "c.DESCRIPTION AS PUBLISHER", "d.DESCRIPTION AS COUNTRY", "e.DESCRIPTION AS BOOKTYPE", "f.DESCRIPTION AS CATEGORY");

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
    
    function getSemesterCombo(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$db = "fr";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
		$CURR_SEMESTER = $this->lithefire->getFieldWhere('fr', 'FILESEME', 'IS_ACTIVE = 1', 'SEMEIDNO');
        $filter = "SEMEIDNO <= '$CURR_SEMESTER'";
		$group = "";
		$having = "";
		

        if(empty($sort) && empty($dir)){
            $sort = "SEMEIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query))
			$filter.="AND (SEMEIDNO LIKE '%$query%' OR SEMESTER LIKE '%$query%')";

        $records = array();
        $table = "FILESEME";
        $fields = array("SEMEIDNO as id", "SEMESTER as name", "DESCRIPTIO");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
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

	function checkAccessNumber(){
		$this->load->model('lithefire_model', 'lithefire', TRUE);
		$ACCESSNO = $this->input->post("ACCESSNO");
		
		if(empty($ACCESSNO)){
			$data['success'] = false;
			$data['data'] = "Access Number is required!";
			die(json_encode($data));
		}

		$db = "default";
		$table = "BOOKS";
		$filter = "ACCESSNO = '$ACCESSNO'";
		
		
		if($this->lithefire->countFilteredRows($db, $table, $filter, "")){
			$data['success'] = false;
			$data['data'] = "Access Number already exists!";
			die(json_encode($data));
		}
		
		$data['success'] = true;
		//$data['data'] = "Access Number already exists!";
		die(json_encode($data));
	}
	
	function addBookAuthor(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
        $table = "BOOKAUTHOR";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "AUTHIDNO = '".$input["AUTHIDNO"]."' AND ACCESSNO = '".$input['ACCESSNO']."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		//$input['BOTYIDNO'] = $this-> lithefire->getNextCharId($db, $table, 'BOTYIDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }
	
	function getTempBookAuthor(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
		$ACCESSNO = $this->input->post('ACCESSNO');
        $filter = "a.ACCESSNO = '$ACCESSNO'";
        $group = "";
		$fr_db = $this->config->item("fr_db");
		$ils_db = $this->config->item("ils_db");

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "b.AUTHOR ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter .= "AND (b.AUTHOR LIKE '%$querystring%' OR b.AUTHIDNO LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "$ils_db.BOOKAUTHOR a LEFT JOIN $ils_db.AUTHORS b ON a.AUTHIDNO = b.AUTHIDNO";
        $fields = array("ACCESSNO", "a.AUTHIDNO", "b.AUTHOR");

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

	function addBookSubject(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
        $table = "BOOKSUBJECT";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "BOSUIDNO = '".$input["BOSUIDNO"]."' AND ACCESSNO = '".$input['ACCESSNO']."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		//$input['BOTYIDNO'] = $this-> lithefire->getNextCharId($db, $table, 'BOTYIDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }
	
	function getTempBookSubject(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
		$ACCESSNO = $this->input->post('ACCESSNO');
        $filter = "a.ACCESSNO = '$ACCESSNO'";
        $group = "";
		$fr_db = $this->config->item("fr_db");
		$ils_db = $this->config->item("ils_db");

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "b.SUBJECT ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter .= "AND (b.SUBJECT LIKE '%$querystring%' OR b.BOSUIDNO LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "$ils_db.BOOKSUBJECT a LEFT JOIN $ils_db.SUBJECTS b ON a.BOSUIDNO = b.BOSUIDNO";
        $fields = array("ACCESSNO", "a.BOSUIDNO", "b.SUBJECT");

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

	function deleteTempData(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        
		$ACCESSNO = $this->input->post("ACCESSNO");

        $table = "BOOKAUTHOR";
		$table2 = "BOOKSUBJECT";
        $param = "ACCESSNO";
       // $fields = $this->input->post();
		$db = "default";
		$filter = "$param = '$ACCESSNO'";

        $this->lithefire->deleteRow($db, $table, $filter);
        $this->lithefire->deleteRow($db, $table2, $filter);
		$data['success'] = true;
        die(json_encode($data));
    }
	
	function addBook(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
        $table = "BOOKS";
		$input = $this->input->post();
		$date = date("Y-m-d");
		$time = date("H:i:s");
		
		$D_INVENTORY = "";
		$NOTES = "";
		$ITSTIDNO = "";
		$SEMEIDNO = "";
		$COURIDNO = "";
		if(!empty($input['D_INVENTORY']))
			$D_INVENTORY = $input['D_INVENTORY'];
		if(!empty($input['COURIDNO']))
			$COURIDNO = $input['COURIDNO'];
		if(!empty($input['SEMEIDNO']))
			$SEMEIDNO = $input['SEMEIDNO'];
		if(!empty($input['ITSTIDNO']))
			$ITSTIDNO = $input['ITSTIDNO'];
		if(!empty($input['NOTES']))
			$NOTES = $input['NOTES'];
		
		$insert = array("ACCESSNO"=>$input['ACCESSNO'], "CALLNO"=>$input['CALLNO'], "TITLE"=>$input['TITLE'],
		"LOCAIDNO"=>$input['LOCAIDNO'], "EDITION"=>$input['EDITION'], "VOLUME"=>$input['VOLUME'], "ISBN"=>$input['ISBN'],
		"PUBLIDNO"=>$input['PUBLIDNO'], "PLACE"=>$input['PLACE'], "COUNIDNO"=>$input['COUNIDNO'], "COPYRIGHT"=>$input['COPYRIGHT'],
		"PAGES"=>$input['PAGES'], "COPIES"=>$input['COPIES'], "PURCDATE"=>$input['PURCDATE'], "AMOUNT"=>$input['AMOUNT'],
		"PHYSDESC"=>$input['PHYSDESC'], "DCREATED"=>$date, "TCREATED"=>$time, "CATEIDNO"=>$input['CATEIDNO'],
		"BOTYIDNO"=>$input['BOTYIDNO'], "CLASIDNO"=>$input['CLASIDNO'], "DDC"=>$input['DDC'], "DDCDECI"=>$input['DDCDECI']);
		
		$insert2 = array("ACCESSNO"=>$input['ACCESSNO'], "D_INVENTORY"=>$D_INVENTORY, "ITSTIDNO"=>$ITSTIDNO);
		
		$insert3 = array("ACCESSNO"=>$input['ACCESSNO'], "COURIDNO"=>$COURIDNO, "SEMEIDNO"=>$SEMEIDNO);
		
		$insert4 = array("ACCESSNO"=>$input['ACCESSNO'], "BIBLIO"=>$input['BIBLIO']);
		
        if($this->lithefire->countFilteredRows($db, $table, "ACCESSNO = '".$input['ACCESSNO']."'", "")){
            $data['success'] = false;
            $data['data'] = "Access number already exists";
            die(json_encode($data));
        }
        
		//$input['BOTYIDNO'] = $this-> lithefire->getNextCharId($db, $table, 'BOTYIDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $insert);
		$this->lithefire->insertRow($db, "BOOKINVE", $insert2);
		$this->lithefire->insertRow($db, "BOOKMISC", $insert3);
		$this->lithefire->insertRow($db, "BIBLIO", $insert4);

        die(json_encode($data));
    }

	function loadBook(){
		$this->load->model('lithefire_model','lithefire',TRUE);
        $db = "default";
        

        $id=$this->input->post('id');
		
		$fr_db = $this->config->item("fr_db");
		$ils_db = $this->config->item("ils_db");
        $table = "$ils_db.BOOKS a LEFT JOIN $fr_db.FILELOCA b ON a.LOCAIDNO = b.LOCAIDNO LEFT JOIN $fr_db.FILEPUBL c ON a.PUBLIDNO = c.PUBLIDNO
        LEFT JOIN $fr_db.FILECOUN d ON a.COUNIDNO = d.COUNIDNO LEFT JOIN $ils_db.BIBLIO e ON a.ACCESSNO = e.ACCESSNO
        LEFT JOIN $ils_db.BOOKINVE f ON a.ACCESSNO = f.ACCESSNO LEFT JOIN $ils_db.BOOKMISC g ON a.ACCESSNO = g.ACCESSNO
        LEFT JOIN $fr_db.FILEITST h ON f.ITSTIDNO = h.ITSTIDNO LEFT JOIN $fr_db.FILECOUR i ON g.COURIDNO = i.COURIDNO
        LEFT JOIN $fr_db.FILESEME j ON g.SEMEIDNO = j.SEMEIDNO LEFT JOIN $fr_db.FILECLAS clas ON a.CLASIDNO = clas.CLASIDNO
        LEFT JOIN $fr_db.BOOKTYPE boty ON a.BOTYIDNO = boty.BOTYIDNO LEFT JOIN $fr_db.FILECATE cate ON a.CATEIDNO = cate.CATEIDNO";
        
		$param = "a.ACCESSNO";

        $filter = "$param = '$id'";
        $fields = array("a.ACCESSNO", "a.CALLNO", "a.TITLE", "a.LOCAIDNO","b.DESCRIPTION as LOCATION",
		"a.EDITION", "a.VOLUME", "a.ISBN", "a.PUBLIDNO", "c.DESCRIPTION AS PUBLISHER", "a.PLACE",
		"a.COUNIDNO", "d.DESCRIPTION as COUNTRY", "a.COPYRIGHT", "a.PHYSDESC", "a.PAGES",
		"a.COPIES", "a.PURCDATE", "a.AMOUNT", "e.BIBLIO", "h.ITSTIDNO", "h.ITEMSTATUS",
		"i.COURIDNO", "i.COURSE", "j.SEMEIDNO", "j.SEMESTER", "f.D_INVENTORY", "g.NOTES", "a.CLASIDNO", "clas.DESCRIPTION as CLASSIFICATION",
		"a.BOTYIDNO", "boty.DESCRIPTION as BOOKTYPE", "a.CATEIDNO", "cate.DESCRIPTION as CATEGORY", "DDC", "DDCDECI",
		"a.CALLNO as CALLNO2");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
	}
	
	function updateBook(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'default';

        $table = "BOOKS";
        
       // $fields = $this->input->post();
		$param = "ACCESSNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = $this->input->post();
		$date = date("Y-m-d");
		$time = date("H:i:s");
		
		$D_INVENTORY = "";
		$NOTES = "";
		$ITSTIDNO = "";
		$SEMEIDNO = "";
		$COURIDNO = "";
		if(!empty($input['D_INVENTORY']))
			$D_INVENTORY = $input['D_INVENTORY'];
		if(!empty($input['COURIDNO']))
			$COURIDNO = $input['COURIDNO'];
		if(!empty($input['SEMEIDNO']))
			$SEMEIDNO = $input['SEMEIDNO'];
		if(!empty($input['ITSTIDNO']))
			$ITSTIDNO = $input['ITSTIDNO'];
		if(!empty($input['NOTES']))
			$NOTES = $input['NOTES'];
		
		$insert = array("ACCESSNO"=>$input['ACCESSNO'], "CALLNO"=>$input['CALLNO'], "TITLE"=>$input['TITLE'],
		"LOCAIDNO"=>$input['LOCAIDNO'], "EDITION"=>$input['EDITION'], "VOLUME"=>$input['VOLUME'], "ISBN"=>$input['ISBN'],
		"PUBLIDNO"=>$input['PUBLIDNO'], "PLACE"=>$input['PLACE'], "COUNIDNO"=>$input['COUNIDNO'], "COPYRIGHT"=>$input['COPYRIGHT'],
		"PAGES"=>$input['PAGES'], "COPIES"=>$input['COPIES'], "PURCDATE"=>$input['PURCDATE'], "AMOUNT"=>$input['AMOUNT'],
		"PHYSDESC"=>$input['PHYSDESC'], "DCREATED"=>$date, "TCREATED"=>$time, "CATEIDNO"=>$input['CATEIDNO'],
		"BOTYIDNO"=>$input['BOTYIDNO'], "CLASIDNO"=>$input['CLASIDNO'], "DDC"=>$input['DDC'], "DDCDECI"=>$input['DDCDECI']);
		
		$insert2 = array("ACCESSNO"=>$input['ACCESSNO'], "D_INVENTORY"=>$D_INVENTORY, "ITSTIDNO"=>$ITSTIDNO);
		
		$insert3 = array("ACCESSNO"=>$input['ACCESSNO'], "COURIDNO"=>$COURIDNO, "SEMEIDNO"=>$SEMEIDNO);
		
		$insert4 = array("ACCESSNO"=>$input['ACCESSNO'], "BIBLIO"=>$input['BIBLIO']);

        if($this->lithefire->countFilteredRows($db, $table, "TITLE = '".$this->input->post("TITLE")."' AND ACCESSNO != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $insert, $filter);
        
		if($this->lithefire->countFilteredRows($db, "BOOKINVE", "ACCESSNO = '$id'", "")){
			$this->lithefire->updateRow($db, "BOOKINVE", $insert2, $filter);
		}else{
			$this->lithefire->insertRow($db, "BOOKINVE", $insert2);
		}
			
		if($this->lithefire->countFilteredRows($db, "BOOKMISC", "ACCESSNO = '$id'", "")){
			$this->lithefire->updateRow($db, "BOOKMISC", $insert3, $filter);
		}else{
			
			$this->lithefire->insertRow($db, "BOOKMISC", $insert3);
		}
		
		if($this->lithefire->countFilteredRows($db, "BIBLIO", "ACCESSNO = '$id'", "")){
			$this->lithefire->updateRow($db, "BIBLIO", $insert4, $filter);
		}else{
			
			$this->lithefire->insertRow($db, "BIBLIO", $insert4);
		}


        die(json_encode($data));
    }
    
	function deleteBook(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'default';

        $table = "BOOKS";
        
       // $fields = $this->input->post();
		$param = "ACCESSNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

       $insert = array("IS_DELETE"=>1);


        $data = $this->lithefire->updateRow($db, $table, $insert, $filter);
  


        die(json_encode($data));
    }
	
	function deleteBookAuthor(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "BOOKAUTHOR";
        $param = "AUTHIDNO";
       // $fields = $this->input->post();
		$db = "default";
        $id=$this->input->post('id');
		$ACCESSNO = $this->input->post('ACCESSNO');
		$filter = "$param = '$id' AND ACCESSNO = '$ACCESSNO'";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function deleteBookSubject(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "BOOKSUBJECT";
        $param = "BOSUIDNO";
       // $fields = $this->input->post();
		$db = "default";
        $id=$this->input->post('id');
		$ACCESSNO = $this->input->post('ACCESSNO');
		$filter = "$param = '$id' AND ACCESSNO = '$ACCESSNO'";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function search(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: Book Search';


  
        $this->layout->view('book/book_search_view', $data);

    }
	
	function getBookSearch(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
        $filter = "a.IS_DELETE = 0";
        $group = "";
		$fr_db = $this->config->item("fr_db");
		$ils_db = $this->config->item("ils_db");
		
		$TITLE = $this->input->post('TITLE');
		$CALLNO = $this->input->post('CALLNO');
		$ISBN = $this->input->post('ISBN');
		$BOSUIDNO = $this->input->post('BOSUIDNO');
		$AUTHIDNO = $this->input->post('AUTHIDNO');

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
		if(!empty($TITLE)){
			if($filter2 == "(")
			$filter2 .= "a.TITLE LIKE '%$TITLE%'";
			else
			$filter2 .= " AND a.TITLE LIKE '%$TITLE%'";
		}

		if(!empty($ISBN)){
			if($filter2 == "(")
			$filter2 .= "a.ISBN LIKE '%$ISBN%'";
			else
			$filter2 .= " AND a.ISBN LIKE '%$ISBN%'";
		}
		
		if(!empty($CALLNO)){
			if($filter2 == "(")
			$filter2 .= "a.CALLNO LIKE '%$CALLNO%'";
			else
			$filter2 .= " AND a.CALLNO LIKE '%$CALLNO%'";
		}
		
		$subject_filter = "";
		$subject_array = array();
		if(!empty($BOSUIDNO)){
			$subjects = $this->lithefire->getAllRecords("default", "$ils_db.BOOKSUBJECT", array('ACCESSNO'), "", "", "", "BOSUIDNO = '$BOSUIDNO'", "");
			
			foreach($subjects as $r):

            $subject_array[] = $r['ACCESSNO'];

        	endforeach;
			
		}
		if(!empty($AUTHIDNO)){
			$author = $this->lithefire->getAllRecords("default", "$ils_db.BOOKAUTHOR", array('ACCESSNO'), "", "", "", "AUTHIDNO = '$AUTHIDNO'", "");

			foreach($author as $r):

            $subject_array[] = $r['ACCESSNO'];

        	endforeach;
		}
		if(!empty($subject_array)){
		$subject_accessno = $this->lithefire->getValue($subject_array);
		if($filter2 == "(")
		$filter2 .= "a.ACCESSNO IN ($subject_accessno)";
		else 
		$filter2 .= " AND a.ACCESSNO IN ($subject_accessno)";
		
		}
		
		$filter2 .= ")";
				
		if($filter2 != "()")
		$filter .= " AND $filter2";
		
		

        if(!empty($querystring)){
            $filter .= " AND (TITLE LIKE '%$querystring%' OR BOOKIDNO LIKE '%$querystring%' OR CALLNO LIKE '%$querystring%')";
        }
        
		
        $records = array();
        $table = "$ils_db.BOOKS a LEFT JOIN $fr_db.FILELOCA b ON a.LOCAIDNO = b.LOCAIDNO LEFT JOIN $fr_db.FILEPUBL c ON a.PUBLIDNO = c.PUBLIDNO
        LEFT JOIN $fr_db.FILECOUN d ON a.COUNIDNO = d.COUNIDNO LEFT JOIN $fr_db.BOOKTYPE e ON a.BOTYIDNO = e.BOTYIDNO LEFT JOIN
        $fr_db.FILECATE f ON a.CATEIDNO = f.CATEIDNO";
        $fields = array("a.*", "b.DESCRIPTION AS LOCATION", "c.DESCRIPTION AS PUBLISHER", "d.DESCRIPTION AS COUNTRY", "e.DESCRIPTION AS BOOKTYPE", "f.DESCRIPTION AS CATEGORY");

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
    
    function borrowing(){
  

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: Book Borrowing/Returning';


  
        $this->layout->view('book/book_borrowing_view', $data);

    }
	
	function getBorrowedBooks(){
        $db = 'default';
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$STUDIDNO=$this->input->post('STUDIDNO');
		
		if(!empty($STUDIDNO))
			$filter = "STUDIDNO = '$STUDIDNO'";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "D_BORROWED DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
        	if(!empty($filter))
            	$filter .= " AND (TITLE LIKE '%$querystring%' OR ACCESSIDNO LIKE '%$querystring%' OR D_BORROWED LIKE '%$querystring%' OR D_RETURNED LIKE '%$querystring%')";
			else
				$filter = "(TITLE LIKE '%$querystring%' OR a.ACCESSNO LIKE '%$querystring%' OR D_BORROWED LIKE '%$querystring%' OR D_RETURNED LIKE '%$querystring%')";
        }
        
		$fr_db = $this->config->item("fr_db");
        $records = array();
        $table = "BORROWEDBOOKS a LEFT JOIN $fr_db.COLLHIST b ON a.STUDIDNO = b.STUDIDNO LEFT JOIN $fr_db.BOOKSTAT c ON a.BOSTIDNO = c.BOSTIDNO
        LEFT JOIN BOOKS d ON a.ACCESSNO = d.ACCESSNO LEFT JOIN $fr_db.FILECATE e ON d.CATEIDNO = e.CATEIDNO";
        $fields = array("a.*", "b.NAME", "c.BOOKSTAT", "d.TITLE", "e.FINE");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
 			$today = date("Y-m-d");
        foreach($records as $row):
			if($row['D_DUE'] < $today && $row['BOSTIDNO']== '00001'){
				$days = (strtotime($today)-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2);
				if($row['BOSTIDNO'] == '00001'){
					$row['BOOKSTAT'] = 'OVERDUE';
				}
			}
			elseif($row['D_DUE'] < $row['D_RETURNED']){
				
				$days = (strtotime($row['D_RETURNED'])-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2);
				if($row['BOSTIDNO'] == '00001'){
					$row['BOOKSTAT'] = 'OVERDUE';
				} 
			}

            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getStudentCombo(){
        $db = 'fr';
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "NAME ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(STUDIDNO LIKE '%$querystring%' OR NAME LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "COLLHIST a LEFT JOIN FILESTTY b ON a.STTYIDNO = b.STTYIDNO LEFT JOIN FILECOUR c ON a.COURIDNO = c.COURIDNO";
        $fields = array("a.STUDIDNO as id", "a.NAME", "b.STUDTYPE", "c.COURSE", "a.YEAR");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):
			$row['name'] = "(".$row['id'].") ".$row['NAME'];
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getBookCombo(){
        $db = 'default';
        $filter = "";
        $group = "";
		$fr_db = $this->config->item("fr_db");

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "NAME ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(ACCESSNO LIKE '%$querystring%' OR TITLE LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "BOOKS a LEFT JOIN $fr_db.FILELOCA b ON a.LOCAIDNO = b.LOCAIDNO
        LEFT JOIN $fr_db.FILECLAS c ON a.CLASIDNO = c.CLASIDNO LEFT JOIN $fr_db.BOOKTYPE d ON a.BOTYIDNO = d.BOTYIDNO
        LEFT JOIN $fr_db.FILECATE e ON a.CATEIDNO = e.CATEIDNO";
		
        $fields = array("a.ACCESSNO as id", "a.TITLE as name", "a.CALLNO", "a.COPIES", "a.B_COPIES", "b.DESCRIPTION as LOCATION"
        , "c.DESCRIPTION AS CLASSIFICATION", "d.DESCRIPTION as BOOKTYPE", "e.DESCRIPTION as CATEGORY", "e.FINE",
		"e.DAYSALLO");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        	
        foreach($records as $row):
			$borrowed = $this->lithefire->getRecordWhere($db, "BORROWEDBOOKS", "ACCESSNO = '".$row['id']."' AND BOSTIDNO = '00001'", array("COUNT(ACCESSNO) as ctr"));
			//die($borrowed[0]['ctr']);
			if($borrowed[0]['ctr'] < $row['COPIES'])
				$row['AVAIL'] = "Available";
			else
				$row['AVAIL'] = "No Copies Available";
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getStudentBorrowingHistory(){
        $db = 'library';
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$STUDIDNO=$this->input->post('STUDIDNO');
		
		$filter = "a.STUDIDNO = '$STUDIDNO'";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "D_BORROWED DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
        	if(!empty($filter))
            	$filter .= " AND (TITLE LIKE '%$querystring%' OR ACCESSIDNO LIKE '%$querystring%' OR D_BORROWED LIKE '%$querystring%' OR D_RETURNED LIKE '%$querystring%')";
			else
				$filter = "(TITLE LIKE '%$querystring%' OR ACCESSIDNO LIKE '%$querystring%' OR D_BORROWED LIKE '%$querystring%' OR D_RETURNED LIKE '%$querystring%')";
        }
        
		$fr_db = $this->config->item("fr_db");
        $records = array();
        $table = "BORROWEDBOOKS a LEFT JOIN $fr_db.COLLHIST b ON a.STUDIDNO = b.STUDIDNO LEFT JOIN $fr_db.BOOKSTAT c ON a.BOSTIDNO = c.BOSTIDNO
        LEFT JOIN BOOKS d ON a.ACCESSNO = d.ACCESSNO LEFT JOIN $fr_db.FILECATE e ON d.CATEIDNO = e.CATEIDNO";
        $fields = array("a.*", "b.NAME", "c.BOOKSTAT", "d.TITLE", "e.FINE");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
       $today = date("Y-m-d");
        foreach($records as $row):
			if($row['D_DUE'] < $today && $row['BOSTIDNO']== '00001'){
				$days = (strtotime($today)-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2);
				if($row['BOSTIDNO'] == '00001'){
					$row['BOOKSTAT'] = 'OVERDUE';
				}
			}
			elseif($row['D_DUE'] < $row['D_RETURNED']){
				
				$days = (strtotime($row['D_RETURNED'])-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2); 
			}

            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getBookBorrowingHistory(){
        $db = 'default';
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$ACCESSNO=$this->input->post('ACCESSNO');
		
		$filter = "a.ACCESSNO = '$ACCESSNO'";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "D_BORROWED DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
        	if(!empty($filter))
            	$filter .= " AND (TITLE LIKE '%$querystring%' OR ACCESSIDNO LIKE '%$querystring%' OR D_BORROWED LIKE '%$querystring%' OR D_RETURNED LIKE '%$querystring%')";
			else
				$filter = "(TITLE LIKE '%$querystring%' OR ACCESSIDNO LIKE '%$querystring%' OR D_BORROWED LIKE '%$querystring%' OR D_RETURNED LIKE '%$querystring%')";
        }
        
		$fr_db = $this->config->item("fr_db");
        $records = array();
        $table = "BORROWEDBOOKS a LEFT JOIN $fr_db.COLLHIST b ON a.STUDIDNO = b.STUDIDNO LEFT JOIN $fr_db.BOOKSTAT c ON a.BOSTIDNO = c.BOSTIDNO
        LEFT JOIN BOOKS d ON a.ACCESSNO = d.ACCESSNO LEFT JOIN $fr_db.FILECATE e ON d.CATEIDNO = e.CATEIDNO";
        $fields = array("a.*", "b.NAME", "c.BOOKSTAT", "d.TITLE", "e.FINE");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        $today = date("Y-m-d");
        foreach($records as $row):
			if($row['D_DUE'] < $today && $row['BOSTIDNO']== '00001'){
				$days = (strtotime($today)-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2);
				if($row['BOSTIDNO'] == '00001'){
					$row['BOOKSTAT'] = 'OVERDUE';
				}
			}
			elseif($row['D_DUE'] < $row['D_RETURNED']){
				
				$days = (strtotime($row['D_RETURNED'])-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2); 
			}

            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }
    
    function borrowBook(){
    	$db = 'default';
        $table = "BORROWEDBOOKS";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "ACCESSNO = '".$input["ACCESSNO"]."' AND STUDIDNO = '".$input["STUDIDNO"]."' AND BOSTIDNO = '00001'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
		$today = date("Y-m-d");
		$DAYSALLO = $input['DAYSALLO'];
		$D_DUE = date('Y-m-d', strtotime("$today+$DAYSALLO days"));
		$is_weekend = date('w', strtotime($D_DUE));  
		
			if($is_weekend == 0)
			$D_DUE = date('Y-m-d', strtotime("$D_DUE+1 day"));
			elseif($is_weekend == 6)
			$D_DUE = date('Y-m-d', strtotime("$D_DUE+2 days"));
			
			/*$today = date("Y-m-d");
		$D_TODAY = date("Y-m-d");
		$DAYSALLO = $input['DAYSALLO'];
		
		$i = 0;
		while($i <= $DAYSALLO):
			$today = date("Y-m-d", strtotime("$today+1 day"));
			$is_weekend = date('w', strtotime($today));
			if(in_array($is_weekend, array(0,6))){
				continue;
			}
			
			$i++;
		endwhile;
		
		$D_DUE = $today;*/  
		
        $fields = array("ACCESSNO"=>$input['ACCESSNO'], "STUDIDNO"=>$input['STUDIDNO'], "D_BORROWED"=>$today, "D_DUE"=>$D_DUE, "BOSTIDNO"=>"00001");
		//$input['BOTYIDNO'] = $this-> lithefire->getNextCharId($db, $table, 'BOTYIDNO', 3);
		
        $data = $this->lithefire->insertRow($db, $table, $fields);
		

        die(json_encode($data));
    }
	
	function loadBorrowedBook(){
        
        $db = "default";
        $fr_db = $this->config->item("fr_db");

        $id=$this->input->post('id');
        $table = "BORROWEDBOOKS a LEFT JOIN BOOKS b ON a.ACCESSNO = b.ACCESSNO LEFT JOIN $fr_db.FILECATE c ON b.CATEIDNO = c.CATEIDNO
        LEFT JOIN $fr_db.COLLHIST d ON a.STUDIDNO = d.STUDIDNO LEFT JOIN $fr_db.FILECOUR e ON d.COURIDNO = e.COURIDNO
        LEFT JOIN $fr_db.BOOKSTAT f ON a.BOSTIDNO = f.BOSTIDNO LEFT JOIN $fr_db.FILELOCA g ON b.LOCAIDNO = g.LOCAIDNO
        LEFT JOIN $fr_db.FILECLAS h ON b.CLASIDNO = h.CLASIDNO LEFT JOIN $fr_db.BOOKTYPE i ON b.BOTYIDNO = i.BOTYIDNO
        ";
		$param = "id";

        $filter = "$param = '$id'";
        $fields = array("a.ACCESSNO", "a.STUDIDNO", "b.TITLE as BOOK", "b.CALLNO", "a.D_BORROWED", "a.D_DUE", "a.D_RETURNED", "a.BOSTIDNO", "c.FINE",
		"a.PAID", "d.NAME", "e.COURSE", "d.YEAR", "f.BOOKSTAT", "a.BOSTIDNO", "g.DESCRIPTION AS LOCATION", "h.DESCRIPTION AS CLASSIFICATION",
		"i.DESCRIPTION AS BOOKTYPE", "c.DESCRIPTION as CATEGORY", "c.DAYSALLO", "b.COPIES");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);
		
		$today = date("Y-m-d");

        $temp = array();

        foreach($records as $row):
        	$borrowed = $this->lithefire->getRecordWhere($db, "BORROWEDBOOKS", "ACCESSNO = '".$row['ACCESSNO']."' AND BOSTIDNO = '00001'", array("COUNT(ACCESSNO) as ctr"));
			//die($borrowed[0]['ctr']);
			if($borrowed[0]['ctr'] < $row['COPIES'])
				$row['AVAIL'] = "Available";
			else
				$row['AVAIL'] = "No Copies Available";
			if($row['D_DUE'] < $today && $row['BOSTIDNO']== '00001'){
				$days = (strtotime($today)-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2);
				/*if($row['BOSTIDNO'] == '00001'){
					$row['BOOKSTAT'] = 'OVERDUE';
				}*/
			}
			elseif($row['D_DUE'] < $row['D_RETURNED']){
				
				$days = (strtotime($row['D_RETURNED'])-strtotime($row['D_DUE']))/86400;

				$row['FINE_DUE'] = number_format($row['FINE']*$days, 2); 
			}
			$row['STUDENT'] = "(".$row['STUDIDNO'].") ".$row['NAME'];
			$row['ACCESSNO2'] = $row['ACCESSNO'];
            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }
    
    function returnBook(){
        $db = 'default';

        $table = "BORROWEDBOOKS";
        
       // $fields = $this->input->post();
		$param = "id";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = $this->input->post();
		$fields = array("FINE_DUE"=>$input['FINE_DUE'], "D_RETURNED"=>$input['D_RETURNED'], "PAID"=>$input['PAID'], "BOSTIDNO"=>'00002');
        $data = $this->lithefire->updateRow($db, $table, $fields, $filter);


        die(json_encode($data));
    }
}
?>