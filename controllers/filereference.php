<?php
class Filereference extends MY_Controller{

    function Filereference(){
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

	function bookType(){



        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: File Reference';


        
        $this->layout->view('filereference/book_type_view', $data);
        
    }
    
	function getBookType(){
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
            $sort = "a.BOTYIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(BOTYCODE LIKE '%$querystring%' OR BOTYIDNO LIKE '%$querystring%' OR a.DESCRIPTION LIKE '%$querystring%' OR b.DESCRIPTION LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "BOOKTYPE a LEFT JOIN FILECLAS b ON a.CLASIDNO = b.CLASIDNO";
        $fields = array("BOTYCODE", "BOTYIDNO", "a.CLASIDNO", "a.DESCRIPTION", "b.DESCRIPTION AS CLASSIFICATION");

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

	function getBookTypeCombo(){
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
            $filter = "(BOTYCODE LIKE '%$querystring%' OR BOTYIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "BOOKTYPE";
        $fields = array("BOTYCODE", "BOTYIDNO as id", "DESCRIPTION as name");

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

	function addBookType(){
        $db = 'fr';
        $table = "BOOKTYPE";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		$input['BOTYIDNO'] = $this-> lithefire->getNextCharId($db, $table, 'BOTYIDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }

    function loadBookType(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";
        

        $id=$this->input->post('id');
        $table = "BOOKTYPE a LEFT JOIN FILECLAS b ON a.CLASIDNO = b.CLASIDNO";
		$param = "BOTYIDNO";

        $filter = "$param = '$id'";
        $fields = array("BOTYCODE", "BOTYIDNO", "a.DESCRIPTION", "b.CLASIDNO", "b.DESCRIPTION AS CLASSIFICATION");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateBookType(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "BOOKTYPE";
        
       // $fields = $this->input->post();
		$param = "BOTYIDNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."' AND BOTYCODE != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteBookType(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "BOOKTYPE";
        $param = "BOTYIDNO";
       // $fields = $this->input->post();
		
        $id=$this->input->post('id');
		$db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

	function classification(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: File Reference';


        $this->layout->view('filereference/classification_view',$data);

    }
    
	function getClassification(){
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
            $sort = "CLASIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(CLASIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILECLAS";
        $fields = array("CLASCODE", "CLASIDNO", "DESCRIPTION");

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

	function addClassification(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'fr';
        $table = "FILECLAS";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		$input['CLASIDNO'] = $this->lithefire->getNextCharId($db, $table, 'CLASIDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }

    function loadClassification(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";
        

        $id=$this->input->post('id');
        $table = "FILECLAS";
		$param = "CLASIDNO";

        $filter = "$param = '$id'";
        $fields = array("CLASCODE", "CLASIDNO", "DESCRIPTION");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateClassification(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "FILECLAS";
        
       // $fields = $this->input->post();
		$param = "CLASIDNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."' AND CLASCODE != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteClassification(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "FILECLAS";
        $param = "CLASIDNO";
       // $fields = $this->input->post();
		$db = "fr";
        $id=$this->input->post('id');
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

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
            $sort = "DESCRIPTION";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(CLASIDNO LIKE '%$querystring%' OR CLASCODE LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILECLAS";
        $fields = array("CLASCODE", "CLASIDNO as id", "DESCRIPTION as name");

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

	function country(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: File Reference';


  
        $this->layout->view('filereference/country_view', $data);

    }
    
	function getCountry(){
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
            $sort = "COUNIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(COUNIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILECOUN";
        $fields = array("COUNCODE", "COUNIDNO", "DESCRIPTION");

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

	function addCountry(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'fr';
        $table = "FILECOUN";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		$input['COUNIDNO'] = $this->lithefire->getNextCharId($db, $table, 'COUNIDNO', 7);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }

    function loadCountry(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";
        

        $id=$this->input->post('id');
        $table = "FILECOUN";
		$param = "COUNIDNO";

        $filter = "$param = '$id'";
        $fields = array("COUNCODE", "COUNIDNO", "DESCRIPTION");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateCountry(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "FILECOUN";
        
       // $fields = $this->input->post();
		$param = "COUNIDNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."' AND COUNIDNO != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteCountry(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "FILECOUN";
        $param = "COUNIDNO";
       // $fields = $this->input->post();
		$db = "fr";
        $id=$this->input->post('id');
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function location(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: File Reference';


        $this->layout->view('filereference/location_view', $data);

    }
    
	function getLocation(){
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
            $sort = "LOCAIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(LOCAIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%' OR ACRONYM LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILELOCA";
        $fields = array("LOCACODE", "LOCAIDNO", "DESCRIPTION", "ACRONYM", "REMARKS");

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

	function getLocationCombo(){
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
            $sort = "LOCAIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(LOCAIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%' OR ACRONYM LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILELOCA";
        $fields = array("LOCACODE", "LOCAIDNO as id", "DESCRIPTION as name", "ACRONYM", "REMARKS");

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

	function addLocation(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'fr';
        $table = "FILELOCA";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		$input['LOCAIDNO'] = $this->lithefire->getNextCharId($db, $table, 'LOCAIDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }
	
	

    function loadLocation(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";
        

        $id=$this->input->post('id');
        $table = "FILELOCA";
		$param = "LOCAIDNO";

        $filter = "$param = '$id'";
        $fields = array("LOCACODE", "LOCAIDNO", "DESCRIPTION", "ACRONYM", "REMARKS");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateLocation(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "FILELOCA";
        
       // $fields = $this->input->post();
		$param = "LOCAIDNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."' AND LOCAIDNO != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteLocation(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "FILELOCA";
        $param = "LOCAIDNO";
       // $fields = $this->input->post();
		$db = "fr";
        $id=$this->input->post('id');
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function category(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: File Reference';

        $this->layout->view('filereference/category_view', $data);

    }
    
	function getCategory(){
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
            $sort = "CATEIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(CATEIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%' OR ACRONYM LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILECATE";
        $fields = array("CATECODE", "CATEIDNO", "DESCRIPTION", "ACRONYM", "FINE", "DAYSALLO");

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

	function getCategoryCombo(){
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
            $sort = "CATEIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(CATEIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%' OR ACRONYM LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILECATE";
        $fields = array("CATECODE", "CATEIDNO as id", "DESCRIPTION as name", "ACRONYM", "FINE", "DAYSALLO");

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

	function addCategory(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'fr';
        $table = "FILECATE";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		$input['CATEIDNO'] = $this->lithefire->getNextCharId($db, $table, 'CATEIDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }

    function loadCategory(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";
        

        $id=$this->input->post('id');
        $table = "FILECATE";
		$param = "CATEIDNO";

        $filter = "$param = '$id'";
        $fields = array("CATECODE", "CATEIDNO", "DESCRIPTION", "ACRONYM", "FINE", "DAYSALLO");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateCategory(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "FILECATE";
        
       // $fields = $this->input->post();
		$param = "CATEIDNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."' AND CATECODE != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteCategory(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "FILECATE";
        $param = "CATEIDNO";
       // $fields = $this->input->post();
		$db = "fr";
        $id=$this->input->post('id');
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function publishing(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: File Reference';


   
        $this->layout->view('filereference/publishing_view', $data);

    }
    
	function getPublishing(){
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
            $sort = "PUBLIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(PUBLCODE LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%' OR PUBLIDNO LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILEPUBL a LEFT JOIN FILECOUN b ON a.COUNIDNO = b.COUNIDNO";
        $fields = array("PUBLCODE", "PUBLIDNO", "a.COUNIDNO", "a.DESCRIPTION", "b.DESCRIPTION AS COUNTRY", "a.ACRONYM", "a.ADDR_01", "a.ADDR_02", "a.CONTPERSON", "a.CONTPHONE");

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

	function getPublishingCombo(){
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
            $filter = "(PUBLIDNO LIKE '%$querystring%' OR PUBLCODE LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILEPUBL";
        $fields = array("PUBLCODE", "PUBLIDNO as id",  "DESCRIPTION as name");

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

	function addPublishing(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'fr';
        $table = "FILEPUBL";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		$input['PUBLIDNO'] = $this->lithefire->getNextCharId($db, $table, 'PUBLIDNO', 10);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }

    function loadPublishing(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";
        

        $id=$this->input->post('id');
        $table = "FILEPUBL a LEFT JOIN FILECOUN b ON a.COUNIDNO = b.COUNIDNO";
		$param = "PUBLIDNO";

        $filter = "$param = '$id'";
        $fields = array("PUBLCODE", "PUBLIDNO", "a.DESCRIPTION", "b.COUNCODE", "b.COUNIDNO", "b.DESCRIPTION AS COUNTRY", "a.ACRONYM", "a.ADDR_01", "a.ADDR_02", "a.CONTPERSON", "a.CONTPHONE");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updatePublishing(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "FILEPUBL";
        
       // $fields = $this->input->post();
		$param = "PUBLIDNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        if($this->lithefire->countFilteredRows($db, $table, "DESCRIPTION = '".$this->input->post("DESCRIPTION")."' AND PUBLIDNO != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deletePublishing(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "FILEPUBL";
        $param = "PUBLIDNO";
       // $fields = $this->input->post();
		$db = "fr";
        $id=$this->input->post('id');
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function getCountryCombo(){
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
            $filter = "(COUNIDNO LIKE '%$querystring%' OR DESCRIPTION LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILECOUN";
        $fields = array("COUNCODE", "COUNIDNO as id", "DESCRIPTION as name");

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

	function getAuthorCombo(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "AUTHOR ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(AUTHIDNO LIKE '%$querystring%' OR AUTHOR LIKE '%$querystring%')";
        }
        
		$ils_db = $this->config->item("ils_db");
        $records = array();
        $table = "$ils_db.AUTHORS";
        $fields = array("AUTHCODE", "AUTHIDNO as id", "AUTHOR as name");

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

	function getItemStatusCombo(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$db = "fr";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "ITEMSTATUS";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
			if(empty($filter))
				$filter = "(ITSTIDNO LIKE '%$query%' OR ITEMSTATUS LIKE '%$query%')";
			else
				$filter .= " AND (ITSTIDNO LIKE '%$query%' OR ITEMSTATUS LIKE '%$query%')";
		}
		

        $records = array();
        $table = "FILEITST";
        $fields = array("ITSTIDNO as id", "ITEMSTATUS as name");

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

	function getCourseCombo(){
        
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$db = "fr";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
		$filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "COURSE";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
			if(empty($filter))
				$filter = "(COURIDNO LIKE '%$query%' OR COURSE LIKE '%$query%')";
			else
				$filter .= " AND (COURIDNO LIKE '%$query%' OR COURSE LIKE '%$query%')";
		}

        $records = array();
        $table = "FILECOUR";
        $fields = array("COURIDNO as id", "COURSE as name");

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

	function getSemesterCombo(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$db = "fr";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "SEMEIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }

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

	
	function getBookSubjectCombo(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'default';
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $querystring = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "SUBJECT ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(BOSUIDNO LIKE '%$querystring%' OR SUBJECT LIKE '%$querystring%')";
        }
        
		$ils_db = $this->config->item("ils_db");
		
        $records = array();
        $table = "$ils_db.SUBJECTS";
        $fields = array("BOSUIDNO as id", "SUBJECT as name");

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

	function getReligionCombo(){
        $this->load->model('lithefire_model','',TRUE);
        $db = "fr";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        //$db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        
		

        if(empty($sort) && empty($dir)){
            $sort = "RELIGION";
        }else{
        	$sort = "$sort $dir";
        }

        $records = array();
        $table = "FILERELI";
        $fields = array("RELIIDNO as id", "RELIGION as name");

        $filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		
		if(!empty($query))
			$filter .= " AND (RELIIDNO LIKE '%$query%' OR RELIGION LIKE '%$query')";

        $records = $this->lithefire_model->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
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
        $data['totalCount'] = $this->lithefire_model->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getCitizenshipCombo(){
        $this->load->model('lithefire_model','',TRUE);
        $db = "fr";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        //$db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        
		

        if(empty($sort) && empty($dir)){
            $sort = "CITIZENSHIP";
        }else{
        	$sort = "$sort $dir";
        }

        $records = array();
        $table = "FILECITI";
        $fields = array("CITIIDNO as id", "CITIZENSHIP as name");

        $filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		
		if(!empty($query))
			$filter .= " AND (CITIIDNO LIKE '%$query%' OR CITIZENSHIP LIKE '%$query')";

        $records = $this->lithefire_model->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
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
        $data['totalCount'] = $this->lithefire_model->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getSectionCombo(){
        $this->load->model('lithefire_model','',TRUE);
        $db = "fr";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        //$db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        
		

        if(empty($sort) && empty($dir)){
            $sort = "SECTION";
        }else{
        	$sort = "$sort $dir";
        }

        $records = array();
        $table = "lithefzj_ogs".SEMEIDNO.".FILESECT";
        $fields = array("SECTIDNO as id", "SECTION as name");

        $filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		
		if(!empty($query))
			$filter .= " AND (SECTIDNO LIKE '%$query%' OR SECTION LIKE '%$query')";

        $records = $this->lithefire_model->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
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
        $data['totalCount'] = $this->lithefire_model->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getSectionAllCombo(){
		 $this->load->model('lithefire_model','',TRUE);
        $db = "fr";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        //$db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        
		

        if(empty($sort) && empty($dir)){
            $sort = "SECTION";
        }else{
        	$sort = "$sort $dir";
        }

        $records = array();
        $table = "lithefzj_ogs".SEMEIDNO.".FILESECT";
        $fields = array("SECTIDNO as id", "SECTION as name");

        $filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		
		if(!empty($query))
			$filter .= " AND (SECTIDNO LIKE '%$query%' OR SECTION LIKE '%$query')";

        $records = $this->lithefire_model->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        	$temp[] = array("id"=>0, "name"=>"All Sections");
        foreach($records as $row):
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire_model->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getCombo($table, $id, $name, $sortby = ""){
        $this->load->model('lithefire_model','',TRUE);
        $db = "fr";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        //$db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        
		

        if(empty($sort) && empty($dir)){
        	if(!empty($sortby))
            	$sort = "$sortby";
			else 
				$sort = "$name";
			
        }else{
        	$sort = "$sort $dir";
        }

        $records = array();
        $table = "$table";
        $fields = array("$id as id", "$name as name");

        $filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		
		if(!empty($query))
			$filter .= " AND ($id LIKE '%$query%' OR $name LIKE '%$query%')";

        $records = $this->lithefire_model->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
       // die($this->lithefire_model->currentQuery());


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
        $data['totalCount'] = $this->lithefire_model->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function loadActiveSemester(){
         $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";


		$filter = "IS_ACTIVE = 1";
		
		$fr_db = $this->config->item("fr_db");
        $table = "FILESEME";
        $fields = array("SEMEIDNO", "SEMESTER as semester", "DESCRIPTIO");

       

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        foreach($records as $row):
            
            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }
	
	function getFilteredSemesterCombo(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        //$db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";
		
		$filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "SEMECODE";
            $dir = "DESC";
        }

        $records = array();
        $table = "FILESEME";
        $fields = array("SEMEIDNO as id", "SEMESTER as name");

        $filter = "ACTIVATED = 1 AND SEMEIDNO <= (SELECT SEMEIDNO FROM FILESEME WHERE IS_ACTIVE = 1)";

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):
          //  $row['COURSE'] = $this->commonmodel->getFieldWhere($db, "FILECOUR", "COURIDNO", $row['COURIDNO'], "COURSE");
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function room(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'OGS: Room FR';

        $this->layout->view('filereference/room_view', $data);
        
    }

    function getRoom(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
		
		$db = "fr";
		$filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		$fields = array("*");
		$table = "FILEROOM";
		$id = "ROOMIDNO";
		$name = "ROOM";



        

        if(empty($sort) && empty($dir)){
            $sort = "$name";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query))
			$filter .= " AND ($id LIKE '%$query%' OR $name LIKE '%$query%')";

        $records = array();
        

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);


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

    function addRoom(){
    	$db = 'fr';
        $table = "FILEROOM";
        $id_field = "ROOMIDNO";
		$input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field);
        
		$compare = "ROOM";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadRoom(){

        $id=$this->input->post('id');
        $table = "FILEROOM";
        $param = "ROOMIDNO";
        $fields = array("ROOMIDNO", "ROOM", "DESCRIPTIO");
		$filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateRoom(){
        
        

        $table = "FILEROOM";
        $param = "ROOMIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }
		
		$db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "ROOM = '".$this->input->post("ROOM")."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteRoom(){
        
        

        $table = "FILEROOM";
        $param = "ROOMIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Semester FR

    function semester(){
 

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'OGS: Semester FR';


        $this->layout->view('filereference/semester_view', $data);

    }

    function getSemester(){
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$db = "fr";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "SEMEIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }

        $records = array();
        $table = "FILESEME";
        $fields = array("SEMEIDNO", "SEMESTER", "DESCRIPTIO");

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

    function addSemester(){
        
        

        $table = "FILESEME";
        $id_field = "SEMEIDNO";
		$db = "fr";
 		$input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field);
        
		$compare = "SEMESTER";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadSemester(){
        
        

        $id=$this->input->post('id');
        $table = "FILESEME";
        $param = "SEMEIDNO";
        $fields = array("SEMEIDNO", "SEMESTER", "DESCRIPTIO");
		$filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateSemester(){
        
        

        $table = "FILESEME";
        $param = "SEMEIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }


  
        
        $compare = "SEMESTER";

        $db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteSemester(){
        
        

        $table = "FILESEME";
        $param = "SEMEIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //COURSE FR

    function course(){
        

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'OGS: Course FR';

        $this->layout->view('filereference/course_view', $data);
        
    }

    function getCourse(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";

       

        $records = array();
        $table = "FILECOUR";
        $fields = array("COURIDNO", "COURSE", "DESCRIPTIO", "ABBREV", "CLUSIDNO", "IDCHCODE");
        
		$db = 'fr';
        $filter = "";
        $group = "";
		if(empty($sort) && empty($dir)){
            $sort = "COURIDNO";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
            $filter = "(COURCODE LIKE '%$query%' OR COURIDNO LIKE '%$query%' OR DESCRIPTIO LIKE '%$query%')";
        }
		 
		
		
		$records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
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
        
        die(json_encode($data));
    }

    function addCourse(){
        
        

        $table = "FILECOUR";
        $id_field = "COURIDNO";
  		$db = "fr";
    	$input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field);
        
		$compare = "COURSE";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadCourse(){
        
        

        $id=$this->input->post('id');
        $table = "FILECOUR";
        $param = "COURIDNO";
        $fields = array("COURIDNO", "COURSE", "DESCRIPTIO", "ABBREV", "CLUSIDNO", "IDCHCODE");
		$filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateCourse(){
        
        

        $table = "FILECOUR";
        $param = "COURIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }


       

        $compare = "COURSE";

        $db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteCourse(){
        
        

        $table = "FILECOUR";
        $param = "COURIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //ADVISER FR

    function adviser(){
	    $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'FR: Adviser';


        $this->layout->view('filereference/adviser_view', $data);
    }

    function getAdviser(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";


        $schoolsort = false;

        if($sort == 'SCHOOL'){
            $schoolsort = true;
            $sort = "ADVIIDNO";
        }

        $records = array();
        $table = "FILEADVI";
        $fields = array("ADVIIDNO", "ADVISER", "YEAR", "SECTION", "ROOM", "IDNO");
		
		$db = 'fr';
        $filter = "";
        $group = "";

        if(empty($sort) && empty($dir)){
            $sort = "ADVIIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
            $filter = "(ADVICODE LIKE '%$query%' OR ADVIIDNO LIKE '%$query%' OR ADVISER LIKE '%$query%')";
        }
		 
		
		
		$records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $school = array();
        $section = array();
        $room = array();
        $total = 0;
		
		$ogs_db = $this->config->item("ogs_db").SEMEIDNO;
		
        if($records){
        foreach($records as $key => $row):

            $row['SCHOOL'] = $this->lithefire->getFieldWhere($db, "FILESCHO", "SCHOIDNO = '".$row['IDNO']."'", "SCHOOL");
            $row['SECTION'] = $this->lithefire->getFieldWhere("default", "$ogs_db.FILESECT", "SECTIDNO = '".$row['SECTION']."'", "SECTION");
            $row['ROOM'] = $this->lithefire->getFieldWhere($db, "FILEROOM", "ROOMIDNO = '".$row['ROOM']."'", "ROOM");

            $school[$key] = $row['SCHOOL'];
            $section[$key] = $row['SECTION'];
            $room[$key] = $row['ROOM'];

            $temp[] = $row;
            $total++;

        endforeach;
        }
       if($schoolsort){
          /* if($dir == 'ASC')
               array_multisort($school, SORT_ASC, $temp);
           elseif($dir == 'DESC')
               array_multisort($school, SORT_DESC, $temp);*/
           $this->sort_by_key($temp, "SCHOOL");
       }
        $data['data'] = $temp;
        $data['success'] = true;
        die(json_encode($data));
    }

    

    function addAdviser(){
        
        $db = "fr";

        $table = "FILEADVI";
        $id_field = "ADVIIDNO";
        
        $compare = array();

        $input = array("ADVISER"=>$this->input->post("ADVISER"), "IDNO"=>$this->input->post("IDNO"));
        $input['ADVIIDNO'] = $this->lithefire->getNextCharId($db, $table, $id_field, 10);
        
       /* if(!empty($input['IDNO']))
        $input['IDNO'] = $this->commonmodel->getFieldWhere($db, "FILESCHO", "SCHOOL", $input['IDNO'], "SCHOIDNO");
        if(!empty($input['SECTION']))
        $input['SECTION'] = $this->commonmodel->getFieldWhere("default", "FILESECT", "SECTION", $input['SECTION'], "SECTIDNO");
        if(!empty($input['ROOM']))
        $input['ROOM'] = $this->commonmodel->getFieldWhere($db, "FILEROOM", "ROOM", $input['ROOM'], "ROOMIDNO");
        */
       
       if($this->lithefire->countFilteredRows($db, $table, "IDNO = '".$input['IDNO']."'", "")){
			$data['success'] = false;
			$data['data'] = "ID Number already exists";
			die(json_encode($data));
		}
	   
        $data = $this->lithefire->insertRow($db, $table, $input);
		$profile_input = array("ADVIIDNO"=>$input['ADVIIDNO'], "LASTNAME"=>$this->input->post("LASTNAME"), "FIRSTNAME"=>$this->input->post("FIRSTNAME"), 
		"MIDDLENAME"=>$this->input->post("MIDDLENAME"), "GENDER"=>$this->input->post("GENDER"), "BIRTHDATE"=>$this->input->post("BIRTHDATE"));
		$this->lithefire->insertRow($db, "ADVIPROFILE", $profile_input);
        die(json_encode($data));
    }

    function loadAdviser(){
        
        
		$this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = "fr";

        $id=$this->input->post('id');
        $table = "FILEADVI a LEFT JOIN ADVIPROFILE b ON a.ADVIIDNO = b.ADVIIDNO";
        $param = "ADVIIDNO";
        $fields = array("a.ADVIIDNO", "a.IDNO", "a.ADVISER", "b.FIRSTNAME", "b.LASTNAME", "b.MIDDLENAME", "b.GENDER", "b.BIRTHDATE", "b.ADVIPICTURE");
		
		$filter = "a.ADVIIDNO = '$id'";
        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

           // $row['IDNO'] = $this->commonmodel->getFieldWhere($db, "FILESCHO", "SCHOIDNO", $row['IDNO'], "SCHOOL");
           // $row['SECTION'] = $this->commonmodel->getFieldWhere("default", "FILESECT", "SECTIDNO", $row['SECTION'], "SECTION");
            //$row['ROOM'] = $this->commonmodel->getFieldWhere($db, "FILEROOM", "ROOMIDNO", $row['ROOM'], "ROOM");
			if(empty($row['ADVIPICTURE']))
				$row['ADVIPICTURE'] = '/images/icon_pic.jpg';
            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateAdviser(){
        
        $db = "fr";

        $table = "FILEADVI";
        $param = "ADVIIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');
		$filter = "ADVIIDNO = '$id'";

        $input = array();
		$profile_input = array();
        foreach($this->input->post() as $key => $val){
        	if($key == 'id')
				continue;
            if($key == 'LASTNAME' || $key == 'FIRSTNAME' || $key == 'MIDDLENAME' || $key == 'GENDER' || $key == 'BIRTHDATE' || $key == 'ADVIPICTURE'){
                if(!empty($val)){
                $profile_input[$key] = $val;
            	}	
                continue;
			}
            if(!empty($val)){
                $input[$key] = $val;
            }
        }




       // $input = $this->input->post();
        $compare ='ADVISER';
        
		if($this->lithefire->countFilteredRows($db, $table, "ADVISER = '".$input['ADVISER']."' AND ADVIIDNO != '$id'", "")){
			$data['success'] = false;
			$data['data'] = "Record already exists";
			die(json_encode($data));
		}

       // $input['IDNO'] = $this->commonmodel->getFieldWhere($db, "FILESCHO", "SCHOOL", $input['IDNO'], "SCHOIDNO");
        //$input['SECTION'] = $this->commonmodel->getFieldWhere("default", "FILESECT", "SECTION", $input['SECTION'], "SECTIDNO");
        //$input['ROOM'] = $this->commonmodel->getFieldWhere($db, "FILEROOM", "ROOM", $input['ROOM'], "ROOMIDNO");
		if($this->lithefire->countFilteredRows($db, "ADVIPROFILE", "ADVIIDNO = '$id'", "")){
			$this->lithefire->updateRow($db, "ADVIPROFILE", $profile_input, $filter);
		}else{
		$profile_input = array("ADVIIDNO"=>$id, "LASTNAME"=>$this->input->post("LASTNAME"), "FIRSTNAME"=>$this->input->post("FIRSTNAME"), 
		"MIDDLENAME"=>$this->input->post("MIDDLENAME"), "GENDER"=>$this->input->post("GENDER"), "BIRTHDATE"=>$this->input->post("BIRTHDATE"));
		$this->lithefire->insertRow($db, "ADVIPROFILE", $profile_input);
		}
		
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteAdviser(){
        
        

        $table = "FILEADVI";
        $param = "ADVIIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Student Level FR

    function studentLevel(){
 

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'OGS: Student Level FR';


        
        $this->layout->view('filereference/studentLevel_view', $data);
        
    }

    function getStudentLevel(){
        
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $db = "fr";
		$filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "YEAR";
            
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query))
			$filter  = "(YEAR like '%$query%' OR DESCRIPTIO LIKE '%$query%')";
		

        $records = array();
        $table = "FILESTLE";
        $fields = array("STLEIDNO", "YEAR", "DESCRIPTIO");

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

    function addStudentLevel(){
        
        
		$db = "fr";
		
        $table = "FILESTLE";
        $id_field = "STLEIDNO";
		$input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field, 3);
        
		$compare = "YEAR";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadStudentLevel(){
        
        

        $id=$this->input->post('id');
        $table = "FILESTLE";
        $param = "STLEIDNO";
        $fields = array("STLEIDNO", "YEAR", "DESCRIPTIO");

        $filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateStudentLevel(){
        
        

        $table = "FILESTLE";
        $param = "STLEIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }



        $compare = "YEAR";

        $db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteStudentLevel(){
        
        

        $table = "FILESTLE";
        $param = "STLEIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Days FR

    function days(){
 

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'OGS: Days FR';


        $this->layout->view('filereference/days_view', $data);
		

    }

    function getDays(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";

 

        $records = array();
        $table = "FILEDAYS";
        $fields = array("DAYSIDNO", "DAYS", "DESCRIPTIO");

       $db = 'fr';
        $filter = "";
        $group = "";
		if(empty($sort) && empty($dir)){
            $sort = "DAYSIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
            $filter = "(DAYSCODE LIKE '%$query%' OR DAYSIDNO LIKE '%$query%' OR DESCRIPTIO LIKE '%$query%')";
        }
		 
		
		
		$records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
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
        die(json_encode($data));
    }

    function addDays(){
        
        
		$db = "fr";
        $table = "FILEDAYS";
        $id_field = "DAYSIDNO";
        $input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field);
        
		$compare = "DAYS";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadDays(){
        
        

        $id=$this->input->post('id');
        $table = "FILEDAYS";
        $param = "DAYSIDNO";
        $fields = array("DAYSIDNO", "DAYS", "DESCRIPTIO");

        $filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateDays(){
        
        

        $table = "FILEDAYS";
        $param = "DAYSIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }


        $compare = 'DAYS';

        $db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteDays(){
        
        

        $table = "FILEDAYS";
        $param = "DAYSIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Semester FR

    function school(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'Online Grading System';


        $this->layout->view('filereference/school_view', $data);

    }

    function getSchool(){
        
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
		
		$db = "fr";
		$filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		$fields = array("*");
		$table = "FILESCHO";
		$id = "SCHOIDNO";
		$name = "SCHOOL";



        

        if(empty($sort) && empty($dir)){
            $sort = "$name";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query))
			$filter .= " AND ($id LIKE '%$query%' OR $name LIKE '%$query%')";

        $records = array();
        

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);


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

    function addSchool(){
        
        
		$db = "fr";
        $table = "FILESCHO";
        $id_field = "SCHOIDNO";
        $input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field);

		$compare = "SCHOOL";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadSchool(){
        
        

        $id=$this->input->post('id');
        $table = "FILESCHO";
        $param = "SCHOIDNO";
        $fields = array("*");

        $filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateSchool(){
        
        

        $table = "FILESCHO";
        $param = "SCHOIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }



        $compare = 'SCHOOL';

        $db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteSchool(){
        
        

        $table = "FILESCHO";
        $param = "SCHOIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Remarks FR

    function remarks(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'OGS: Remarks FR';


        
        $this->layout->view('filereference/remarks_view', $data);
        
    }

    function getRemarks(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";

        if(empty($sort) && empty($dir)){
            $sort = "REMARKS";
            $dir = "ASC";
        }

        $records = array();
        $table = "FILEGRRE";
        $fields = array("REMAIDNO", "REMARKS", "DESCRIPTIO");

        $db = 'fr';
        $filter = "";
        $group = "";
		if(empty($sort) && empty($dir)){
            $sort = "REMAIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
            $filter = "(REMACODE LIKE '%$query%' OR REMAIDNO LIKE '%$query%' OR DESCRIPTIO LIKE '%$query%')";
        }
		 
		
		
		$records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
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
        die(json_encode($data));
    }

    function addRemarks(){
        
        
		$db = "fr";
        $table = "FILEGRRE";
        $id_field = "REMAIDNO";
        $input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field);

		$compare = "REMARKS";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadRemarks(){
        
        

        $id=$this->input->post('id');
        $table = "FILEGRRE";
        $param = "REMAIDNO";
        $fields = array("REMAIDNO", "REMARKS", "DESCRIPTIO");

        $filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateRemarks(){
        
        

        $table = "FILEGRRE";
        $param = "REMAIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }


       

        $compare = 'REMARKS';

        $db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteRemarks(){
        
        

        $table = "FILEGRRE";
        $param = "REMAIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Time FR

    function time(){

        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'Online Grading System';

        $this->layout->view('filereference/time_view', $data);
        
    }

    function getTime(){
    	$db = "fr";
		$filter = "ACTIVATED = 1";
		$group = "";
		$having = "";
		
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');

        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";

        if(empty($sort) && empty($dir)){
            $sort = "TIMEIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }

        $records = array();
        $table = "FILETIME";
        $fields = array("TIMEIDNO", "TIME", "DESCRIPTIO");

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

    function addTime(){
        
        
		$db = "fr";
        $table = "FILETIME";
        $id_field = "TIMEIDNO";
        $input = $this->input->post();
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field);
        
		$compare = "TIME";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadTime(){
        
        

        $id=$this->input->post('id');
        $table = "FILETIME";
        $param = "TIMEIDNO";
        $fields = array("TIMEIDNO", "TIME", "DESCRIPTIO");

        $filter = "$param = '$id'";
		$db = "fr";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateTime(){
        
        

        $table = "FILETIME";
        $param = "TIMEIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }


      

        $compare = 'TIME';

        $db = 'fr';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteTime(){
        
        

        $table = "FILETIME";
        $param = "TIMEIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "fr";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Subject FR

    function subject(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'Online Grading System';

        $this->layout->view('filereference/subject_view', $data);
        
    }

    function getSubject(){
        
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        $db = "fr";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "SUBJCODE ASC";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query))
			$filter = "(SUBJCODE LIKE '%$query%' OR COURSEDESC LIKE '%$query%' OR SUBJIDNO LIKE '%$query%')";

        $records = array();
        $table = "FILESUBJ";
        $fields = array("SUBJIDNO", "SUBJCODE", "COURSEDESC", "UNITS_LEC", "UNITS_LAB", "FEE_TUI", "FEE_LAB", "FEE_TUT", "FEE02_TUI", "FEE02_LAB", "FEE02_TUT", "REMARKS");

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

    function addSubject(){
		$db = "fr";
        $table = "FILESUBJ";
        $id_field = "SUBJIDNO";
        $id = $this->lithefire->getNextCharId($db, $table, $id_field);
		$input = $this->input->post();
		$input['SUBJIDNO'] = $id;
        
        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadSubject(){
        
        $this->load->model('lithefire_model', 'lithefire', TRUE);
		$db = "fr";
        $id=$this->input->post('id');
        $table = "FILESUBJ";
        $param = "SUBJIDNO";
		
		$filter = "$param = '$id'";
        $fields = array("SUBJIDNO", "SUBJCODE", "COURSEDESC", "UNITS_LEC", "UNITS_LAB", "FEE_TUI", "FEE_LAB", "FEE_TUT", "FEE02_TUI", "FEE02_LAB", "FEE02_TUT", "REMARKS");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateSubject(){
        

		$db = "fr";
        $table = "FILESUBJ";
        $param = "SUBJIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');
		$filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

		if($this->lithefire->countFilteredRows($db, $table, "SUBJCODE = '".$this->input->post("SUBJECT")."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteSubject(){
        
        $this->load->model('lithefire_model', 'lithefire', TRUE);

		$db = 'fr';
        $table = "FILESUBJ";
        $param = "SUBJIDNO";
       // $fields = $this->input->post();
		
        $id=$this->input->post('id');
		$filter = "$param = '$id'";
        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }

    //Section FR

    function section(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'Online Grading System';

        $this->layout->view('filereference/section_view', $data);

    }

    function getSection(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";


        $records = array();
        $table = "FILESECT";
        $fields = array("SECTIDNO", "SECTION", "DESCRIPTIO", "YEAR", "SECTORDER", "MALE", "FEMALE", "STUDCOUNT", "COURIDNO");

        $db = 'default';
        $filter = "";
        $group = "";
		if(empty($sort) && empty($dir)){
            $sort = "SECTIDNO DESC";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
            $filter = "(SECTCODE LIKE '%$query%' OR SECTIDNO LIKE '%$query%' OR DESCRIPTIO LIKE '%$query%' OR SECTION LIKE '%$query%')";
        }
		 
		
		
		$records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);


        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):
            $row['COURSE'] = $this->lithefire->getFieldWhere("fr", "FILECOUR", "COURIDNO = '".$row['COURIDNO']."'", "COURSE");
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        die(json_encode($data));
    }

    function addSection(){
        
     

        $table = "FILESECT";
        $id_field = "SECTIDNO";
       

        $db = "default";

        $input = $this->input->post();
		
		$input['COURIDNO'] = $this->lithefire->getFieldWhere("fr", "FILECOUR", "COURSE = '".$input['COURIDNO']."'", "COURIDNO");
        $input[$id_field] = $this->lithefire->getNextCharId($db, $table, $id_field, 3);
        
       
		$compare = "SECTION";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));
    }

    function loadSection(){
        
        
        $db = "default";

        $id=$this->input->post('id');
        $table = "FILESECT";
        $param = "SECTIDNO";
        $fields = array("SECTIDNO", "SECTION", "DESCRIPTIO", "YEAR", "SECTORDER", "MALE", "FEMALE", "STUDCOUNT", "COURIDNO");

        $filter = "$param = '$id'";


        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):
            $row['COURIDNO'] = $this->lithefire->getFieldWhere('fr', "FILECOUR", "COURIDNO = '".$row['COURIDNO']."'", "COURSE");
            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateSection(){
        
        

        $table = "FILESECT";
        $param = "SECTIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        $input['COURIDNO'] = $this->lithefire->getFieldWhere("fr", "FILECOUR", "COURSE = '".$input['COURIDNO']."'", "COURIDNO");




        $compare = 'SECTION';

        $db = 'default';
		$filter = "$param = '$id'";
		if($this->lithefire->countFilteredRows($db, $table, "$compare = '".$this->input->post($compare)."' AND $param != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteSection(){
        
        

        $table = "FILESECT";
        $param = "SECTIDNO";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $db = "default";
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function getParentCombo(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = "fr";
        $table = "PARENTS";
        $fields = array("PAREIDNO as id",  "NAME as name");
        $filter = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        

        if(empty($sort) && empty($dir)){
            $sort = "NAME ASC";
        }else{
            $sort = "$sort $dir";
        }
        
        if(!empty($query)){
            $filter = "(PAREIDNO LIKE '%$query%' OR NAME LIKE '%$query%')";
        }

        $group = array();

        $records = array();
        $records = $this->lithefire->getAllRecords($db, $table, $fields,  $start, $limit, $sort, $filter, $group);


        if($records){
        foreach($records as $row):

            $temp[] = $row;


        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getStudentCombo(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = "default";
		
		$ogs_db = $this->config->item("ogs_db").SEMEIDNO;
        $table = "$ogs_db.COLLEGE";
        $fields = array("STUDCODE",  "NAME as name", "STUDIDNO as id", "YEAR", "COURIDNO", "SECTIDNO");
        $filter = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        

        if(empty($sort) && empty($dir)){
            $sort = "NAME ASC";
        }else{
            $sort = "$sort $dir";
        }
        
        if(!empty($query)){
            $filter = "(STUDIDNO LIKE '%$query%' OR NAME LIKE '%$query%')";
        }

        $group = array();

        $records = array();
        $records = $this->lithefire->getAllRecords($db, $table, $fields,  $start, $limit, $sort, $filter, $group);


        if($records){
        foreach($records as $row):
			$row['SECTION'] = $this->lithefire->getFieldWhere("default", "$ogs_db.FILESECT", "SECTIDNO = '".$row['SECTIDNO']."'", "SECTION");
			$row['COURSE'] = $this->lithefire->getFieldWhere("fr", "FILECOUR", "COURIDNO = '".$row['COURIDNO']."'", "COURSE");
            $temp[] = $row;


        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function parents(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'Online Grading System';


        $this->layout->view('filereference/parents_view', $data);
    }

    function getParents(){
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        $db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";
		
		$filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "PAREIDNO DESC";
            
        }else{
        	$sort = "$sort $dir";
        }
		
		$fr_db = $this->config->item("fr_db");

        $records = array();
        $table = "PARENTS a LEFT JOIN $fr_db.FILEGEND b ON a.GENDIDNO = b.GENDIDNO";
        $fields = array("a.PARECODE", "a.PAREIDNO", "a.NAME", "a.FIRSTNAME", "a.MIDDLENAME", "a.LASTNAME", "b.GENDER", "a.BIRTHDATE",
		"a.EMAIL", "a.PHONE", "a.MOBILE");

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

    function addParent(){
        
        $db = "fr";

        $table = "PARENTS";
        $id_field = "PAREIDNO";
		$default_db = $this->config->item("default_db");
		
        $id = $this->getNextCharIdDefault($table, $id_field);
        
        $input = array();

        $input = $this->input->post();
        
		$input['FIRSTNAME'] = strtoupper($input['FIRSTNAME']);
		$input['MIDDLENAME'] = strtoupper($input['MIDDLENAME']);
		$input['LASTNAME'] = strtoupper($input['LASTNAME']);
		$input['NAME'] = $input['LASTNAME'].", ".$input['FIRSTNAME']." ".$input['MIDDLENAME'];
		$input['PAREIDNO'] = $id;
		
		if($this->lithefire->countFilteredRows($db, $table, "NAME = '".$input['NAME']."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }

        

        $data = $this->lithefire->insertRow($db, $table, $input);
		
		$username = strtolower($input['FIRSTNAME'].".".$input['LASTNAME']);
		$middle_initial = strtolower(substr($input['MIDDLENAME'],0,1));
		
		if($this->lithefire->countFilteredRows($db, 'tbl_user', "username = '$username'", "")){
            $username = strtolower($input['FIRSTNAME']."$middle_initial.".$input['LASTNAME']);
        }
		
		$user_input = array("username"=>$username, "password"=>md5("pmmsonline"), "user_type_code"=>"PRNT", "PARECODE"=>$data['id'], "PAREIDNO"=>$id);
		$this->lithefire->insertRow($db, "tbl_user", $user_input);
        die(json_encode($data));
    }

    function loadParent(){
         $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";


        $id=$this->input->post('id');
		
		$fr_db = $this->config->item("fr_db");
        $table = "PARENTS a LEFT JOIN $fr_db.FILEGEND b ON a.GENDIDNO = b.GENDIDNO";
       
        $filter = "PAREIDNO = '$id'";
         $fields = array("a.PARECODE", "a.PAREIDNO", "a.NAME", "a.FIRSTNAME", "a.MIDDLENAME", "a.LASTNAME", "b.GENDER", "a.BIRTHDATE",
		"a.EMAIL", "a.PHONE", "a.MOBILE", "b.GENDIDNO");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        foreach($records as $row):
            if(empty($row['PICTURE']))
				$row['PICTURE'] = base_url().'studentPhotos/icon_pic.jpg';
            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateParent(){
        $db = 'fr';

        $table = "PARENTS";

       // $fields = $this->input->post();

        $id=$this->input->post('id');
        $filter = "PAREIDNO = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }
		
		$input['NAME'] = $input['LASTNAME'].", ".$input['FIRSTNAME']." ".$input['MIDDLENAME'];
		
        if($this->lithefire->countFilteredRows($db, $table, "NAME = '".$input['NAME']."' AND PAREIDNO != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteParent(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "PARENTS";

        $id=$this->input->post('id');
        $filter = "PAREIDNO = '$id'";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
	
	function getAdviserCombo(){

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        $db = "fr";
        $filter = "";
        $group = "";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        

        if(empty($sort) && empty($dir)){
            $sort = "ADVISER ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($query)){
            $filter = "(ADVIIDNO LIKE '%$query%' OR ADVISER LIKE '%$query%')";
        }

        $records = array();
        $table = "FILEADVI";
        $fields = array("ADVIIDNO as id", "ADVISER as name");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):
          //  $row['COURSE'] = $this->commonmodel->getFieldWhere($db, "FILECOUR", "COURIDNO", $row['COURIDNO'], "COURSE");
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }
    
    function getRoomCombo(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";



        $records = array();
        $table = "FILEROOM";
        $fields = array("ROOMIDNO as id", "ROOM as name");

        $db = 'fr';
        $filter = "";
        $group = "";
		if(empty($sort) && empty($dir)){
            $sort = "ROOM";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
            $filter = "(ROOMCODE LIKE '%$query%' OR ROOMIDNO LIKE '%$query%' OR ROOM LIKE '%$query%')";
        }
		 
		
		
		$records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);

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
        die(json_encode($data));
    }

	function getSchoolCombo(){
        
        
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";

   

        $records = array();
        $table = "FILESCHO";
        $fields = array("SCHOIDNO as id", "SCHOOL as name");

        $db = 'fr';
        $filter = "";
        $group = "";
		if(empty($sort) && empty($dir)){
            $sort = "SCHOOL";
        }else{
        	$sort = "$sort $dir";
        }
		
		if(!empty($query)){
            $filter = "(SCHOCODE LIKE '%$query%' OR SCHOIDNO LIKE '%$query%' OR SCHOOL LIKE '%$query%')";
        }
		 
		
		
		$records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
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
        die(json_encode($data));
    }

	function getStudentLevelCombo(){
        
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$db = "fr";



        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $filter = "ACTIVATED = 1";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "YEAR";
        }
        
		if(!empty($query))
			$filter = "(YEAR LIKE '%$query%' OR STLEIDNO LIKE '%$query%' OR DESCRIPTIO LIKE '%$query%')";
        $records = array();
        $table = "FILESTLE";
        $fields = array("STLEIDNO as id", "YEAR", "DESCRIPTIO");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):
			$row['name'] = $row['YEAR']." (".$row['DESCRIPTIO'].")";
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function getSubjectCombo(){
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
            $sort = "SUBJCODE ASC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(SUBJCODE LIKE '%$querystring%' OR SUBJIDNO LIKE '%$querystring%' OR COURSEDESC LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILESUBJ";
        $fields = array("SUBJCODE", "SUBJIDNO as id", "SUBJCODE", "COURSEDESC");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);
       // die($this->db->last_query());


        $temp = array();
        $total = 0;
        if($records){
        foreach($records as $row):
			$row['name'] = "(".$row['SUBJCODE'].") ".$row['COURSEDESC'];
            $temp[] = $row;
            $total++;

        endforeach;
        }
        $data['data'] = $temp;
        $data['success'] = true;
        $data['totalCount'] = $this->lithefire->countFilteredRows($db, $table, $filter, $group);
        die(json_encode($data));
    }

	function itemStatus(){


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;
        $data['title'] = 'ILS: File Reference';


        $this->layout->view('filereference/item_status_view',$data);

    }
    
	function getItemStatus(){
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
            $sort = "ITSTIDNO DESC";
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($querystring)){
            $filter = "(ITSTIDNO LIKE '%$querystring%' OR ITEMSTATUS LIKE '%$querystring%')";
        }
        

        $records = array();
        $table = "FILEITST";
        $fields = array("ITSTCODE", "ITSTIDNO", "ITEMSTATUS");

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

	function addItemStatus(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = 'fr';
        $table = "FILEITST";
		$input = $this->input->post();
        if($this->lithefire->countFilteredRows($db, $table, "ITEMSTATUS = '".$this->input->post("ITEMSTATUS")."'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }
        
		$input['ITSTIDNO'] = $this->lithefire->getNextCharId($db, $table, 'ITSTDNO', 3);
        $data = $this->lithefire->insertRow($db, $table, $input);

        die(json_encode($data));
    }

    function loadItemStatus(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "fr";
        

        $id=$this->input->post('id');
        $table = "FILEITST";
		$param = "ITSTIDNO";

        $filter = "$param = '$id'";
        $fields = array("ITSTCODE", "ITSTIDNO", "ITEMSTATUS");

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);

        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateItemStatus(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = 'fr';

        $table = "FILEITST";
        
       // $fields = $this->input->post();
		$param = "ITSTIDNO";
        $id=$this->input->post('id');
        $filter = "$param = '$id'";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if($key == 'id')
                continue;
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        if($this->lithefire->countFilteredRows($db, $table, "ITEMSTATUS = '".$this->input->post("DESCRIPTION")."' AND ITSTIDNO != '$id'", "")){
            $data['success'] = false;
            $data['data'] = "Record already exists";
            die(json_encode($data));
        }


        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteItemStatus(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        

        $table = "FILEITST";
        $param = "ITSTIDNO";
       // $fields = $this->input->post();
		$db = "fr";
        $id=$this->input->post('id');
		$filter = "$param = $id";

        $data = $this->lithefire->deleteRow($db, $table, $filter);

        die(json_encode($data));
    }
}
?>