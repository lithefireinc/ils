<?php
class UserMatrix extends MY_Controller
{
    function UserMatrix(){
        parent::__construct();
        
    }

    function index()
    {
    
        $data['header'] = 'Header Section';
		$data['title'] = $this->config->item("sysname").': User Access Control';
        $data['footer'] = 'Footer Section';


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;

        //$this->load->view('header_view', $data);
        //$this->load->view('menu_view', $data);
        $this->layout->view('usermatrix/userMatrix_view', $data);
        //$this->load->view('login_view');
        //$this->load->view('footer_view', $data);
    }

    function getModuleGroup(){
        $db = "default";
        $table = "module_group";
        $fields = "*";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $filter = "";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "id DESC";
        }else{
        	$sort = "$sort $dir";
        }

        if(!empty($query)){
            $filter = "description LIKE '%$query%'";
        }



        $records = array();
        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);



        $temp = array();
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

    function insertModuleGroup(){
        $db = "default";
        $table = "module_group";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));

    }

    function loadModuleGroup(){

        $db = "default";
        $table = "module_group";
        $param = "id";
        $fields = "*";

        $id=$this->input->post('id');


		$filter = "$param = '$id'";
        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);



        $temp = array();

        foreach($records as $row):

            $data["data"] = $row;


        endforeach;

       // $data['data'] = $temp;
        $data['success'] = true;

        die(json_encode($data));
    }

    function updateModuleGroup(){
        $db = "default";
        $table = "module_group";
        $param = "id";
       // $fields = $this->input->post();

        $id=$this->input->post('id');

        $input = array();
        foreach($this->input->post() as $key => $val){
            if(!empty($val)){
                $input[$key] = $val;

            }
        }
		$filter = "$param = '$id'";
		
        $records = array();
        $data = $this->lithefire->updateRow($db, $table, $input, $filter);


        die(json_encode($data));
    }

    function deleteModuleGroup(){
        $db = "default";
        $table = "module_group";
        $param = "id";

        $id=$this->input->post('id');

		$filter = "$param = '$id'";

        $data = $this->lithefire->deleteRow($db, $table, $filter);


        die(json_encode($data));
    }

    function getModuleGroupUsers(){
        $db = "default";
        $table = "module_group_users";
        $fields = "*";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";
        $id = $this->input->post('id');
		
		$filter = "group_id = '$id'";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "username ASC";
        }else{
        	$sort = "$sort $dir";
        }

        if(!empty($query)){
            $filter .= "AND (username LIKE '%$query%')";
        }
       // $filter = "";
        //$filter = array("group_id"=>$id);
        //$join = array();

        $records = array();
        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);



        $temp = array();
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

    function insertModuleGroupUsers(){
        $db = "default";
        $table = "module_group_users";

        $input = array();
        foreach($this->input->post() as $key => $val){
            if(!empty($val)){
                $input[$key] = $val;
            }
        }

        $data = $this->lithefire->insertRow($db, $table, $input);
        die(json_encode($data));

    }

    function deleteModuleGroupUsers(){
        $db = "default";
        $table = "module_group_users";
        $param = "id";

        $id=$this->input->post('id');

		$filter = "$param = '$id'";

        $data = $this->lithefire->deleteRow($db, $table, $filter);


        die(json_encode($data));
    }

    function getUserName(){
        $db = "default";
        $table = "tbl_user";
        $fields = "*";

        $this->load->model('lithefire_model', 'lithefire', TRUE);


        $start=$this->input->post('start');
        $limit=$this->input->post('limit');


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        

        $id = $this->input->post('id');

        if(empty($sort) && empty($dir)){
            $sort = "username ASC";
        }else{
        	$sort = "$sort $dir";
        }

        $filter = "username NOT IN (SELECT username from module_group_users WHERE group_id = $id)";
		$group = "";
		$having = "";
		
		if(!empty($query))
		$filter .= " AND username LIKE '%$query%'";




        $records = array();
        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);

        


        $temp = array();
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

    function getModuleGroupAccess(){
        $db = "default";
        $table = "module_group_access a LEFT JOIN module b ON a.module_id = b.id LEFT JOIN module_category c ON b.category_id = c.id";
        $fields = "a.id, b.description as module, c.description as category";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        $queryby = "";
        $id = $this->input->post('id');
		$filter = "a.group_id = '$id' AND b.is_public = 0";
		$group = "";
		$having = "";

        if(empty($sort) && empty($dir)){
            $sort = "b.description";
        }else{
        	$sort = "$sort $dir";
        }

        if(!empty($query)){
            $filter .= " AND (b.description LIKE '%$query%')";
        }
        //$filter = array("module.is_public"=>0, "module_group_access.group_id"=>$id);
        //$filter = array("is_delete"=>0);

        //$join = array("module"=>"module.id = module_group_access.module_id", "module_category b"=>"module.category_id = b.id");

        $records = array();
        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);



        $temp = array();
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

    function insertModuleGroupAccess(){
        $db = "default";
        $table = "module_group_access";
        $group_id = $this->input->post("groupid");

        /*$input = array();
        foreach($this->input->post() as $key => $val){
            if(!empty($val)){
                $input[$key] = $val;
            }
        }*/
        $selected_items_json = $this->input->post('selected_items');

	$selected_items_json = str_replace("\\", "", $selected_items_json);
	$selected_item = json_decode($selected_items_json);

	if(empty($selected_item)){
		die(json_encode(array("success"=> false, "data" => "Unable to retrieve selected item.")));
	}
        $input = array();
        $input['group_id'] = $group_id;
        foreach($selected_item->data as $key => $value){
			try{
			$input['module_id'] = $value;
                        $data = $this->lithefire->insertRow($db, $table, $input);
			}catch(Exception $e){
				continue;
			}
	}


        die(json_encode($data));

    }

    function deleteModuleGroupAccess(){
        $db = "default";
        $table = "module_group_users";
        $param = "id";

        $id=$this->input->post('id');

		$filter = "$param = '$id'";

        $data = $this->lithefire->deleteRow($db, $table, $filter);


        die(json_encode($data));
    }

    function getModule(){
        $db = "default";
        $table = "module a LEFT JOIN module_category b ON a.category_id = b.id";
        $fields = array("a.id, a.description AS module, b.description AS category, a.link");
        $id = $this->input->post('id');

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
		$filter = "a.id NOT IN (SELECT module_id FROM module_group_access WHERE group_id = '$id') AND a.is_public = 0";
		$group = "";
		$having = "";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');
        $query = $this->input->post('query');
        
       

        if(empty($sort) && empty($dir)){
            $sort = "a.description";
        }else{
        	$sort = "$sort $dir";
        }

        if(!empty($query)){
            $filter .= "a.description LIKE '%$query%'";
        }

        //$filter = array("is_delete"=>0);


        $records = array();
        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having);
        


        $temp = array();
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

    function deleteModule(){
        $db = "default";
        $table = "module_group_access";
        $param = "id";

        $id=$this->input->post('id');


		$filter = "$param = '$id'";
        $data = $this->lithefire->deleteRow($db, $table, $filter);


        die(json_encode($data));
    }

    function administration()
    {
        $data['header'] = 'Header Section';
        $data['title'] = $this->config->item("sysname").': User Administration';
        $data['footer'] = 'Footer Section';


        $data['userId'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userId');
        $data['userName'] = $this->session->userdata($this->config->item("session_identifier", "ion_auth").'_userName');;

     
        $this->layout->view('usermatrix/user_administration_view', $data);
    
    }

    function getUsers(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = "default";
        $filter = "";
        $group = "";

        $start = $this->input->post("start");
        $limit = $this->input->post("limit");
        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir');


        if(empty($sort) && empty($dir)){
            $sort = "user_type ASC, username ASC";

        }else{
            $sort = "$sort $dir";
        }

        $query = $this->input->post('query');

        if(!empty($query))
            $filter = "(username LIKE '%$query%')";

     //   $table = "tbl_user a LEFT JOIN COLLEGE b ON a.STUDCODE = b.STUDCODE LEFT JOIN tbl_user_type c ON a.user_type_code = c.code";
      //  $fields = "a.id, a.username, a.ADVICODE, b.NAME as STUD_NAME, c.description as user_type, c.code";
         $table = "tbl_user a LEFT JOIN tbl_user_type b ON a.user_type_code = b.code";
        $fields = array("a.id", "username", "b.description as user_type", "b.code");

        $records = $this->lithefire->getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group);

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

    function loadUser(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = "default";
        $table = "tbl_user a LEFT JOIN tbl_user_type b ON a.user_type_code = b.code";
        $fields = array("a.id", "username", "b.description as user_type", "b.code");

        $id=$this->input->post('id');

        $filter = "a.id = '$id'";
        //$filter.=" AND a.COURIDNO = FILECOUR.COURIDNO AND a.CITIIDNO = FILECITI.CITIIDNO AND a.RELIIDNO = FILERELI.RELIIDNO";

        $records = array();
        $records = $this->lithefire->getRecordWhere($db, $table, $filter, $fields);



        $temp = array();

        foreach($records as $row):
            
            $data["data"] = $row;


        endforeach;

       // $data['data'] = $temp;
        $data['success'] = true;

        die(json_encode($data));
    }

    function getUserTypeCombo(){
        $this->load->model('lithefire_model','lithefire',TRUE);
        $db = "default";
        $filter = "";
        $group = "";

        $start=$this->input->post('start');
        $limit=$this->input->post('limit');
        //$db = "fr";


        $sort = $this->input->post('sort');
        $dir = $this->input->post('dir'); 
        $query = $this->input->post('query');


        if(empty($sort) && empty($dir)){
            $sort = "description ASC";
            
        }else{
            $sort = "$sort $dir";
        }

        if(!empty($query))
            $filter = "(code LIKE '%$query%' OR description LIKE '%$query%')";

        $records = array();
        $table = "tbl_user_type";
        $fields = array("code as id", "description as name");


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

    function changePassword(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = "default";
        $table = "tbl_user";

        $id = $this->input->post('id');

        $new_password = $this->ion_auth->hash_password($_POST['new_pass']);



        $input = array("password"=>$new_password);
        $filter = "id = '$id'";

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);
        $data['data'] = "Password Successfully changed";

        die(json_encode($data));

    }

    function updateUserName(){
        $this->load->model('lithefire_model', 'lithefire', TRUE);
        $db = "default";
        $table = "tbl_user";

        $id = $this->input->post('id');
		$user_type = $this->input->post('USERTYPE');
        
        $old_username = $this->lithefire->getFieldWhere("default", "tbl_user", "id = '$id'","username");
        $username = $this->input->post('username');

        if($this->lithefire->countFilteredRows($db, $table, "id != '$id' AND username = '$username'", "")){
            $data['success'] = false;
            $data['data'] = "Username already exists";
            die(json_encode($data));
        }


        $input = array("username"=>$username, "user_type_code"=>$user_type);
        $filter = "id = '$id'";

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);
        $data = $this->lithefire->updateRow($db, "module_group_users", array("username"=>$username), "username = '$old_username'");
        $data['data'] = "Username successfully updated";

        die(json_encode($data));

    }

	function updatePassword(){
        $db = "default";
        $table = "tbl_user";

        $id = $this->input->post('id');
        $password = $this->ion_auth->hash_password_db($id, $_POST['oldpass']);
		//die($password);
        $new_password = $this->ion_auth->hash_password($_POST['pass']);

        if(!$this->lithefire->countFilteredRows($db, $table, "id = '$id' AND password = '$password'", "")){
        	//die($this->lithefire->currentQuery());
            $data['success'] = false;
            $data['data'] = "Old password does not match";
            die(json_encode($data));
        }

        $input = array("password"=>$new_password);
        $filter = "id = '$id'";

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);
        $data['data'] = "Password Successfully changed";

        die(json_encode($data));
        
    }
	
	function employeePassword(){
		$employees = $this->lithefire->getAllRecords("default", "tbl_user", array("id", "username", "password"), "", "", "", "", "", "");
		
		foreach($employees as $e){
			$password = $this->ion_auth->hash_password($e['username']."321");
			$input = array("password"=>$password);
			$filter = "id = '".$e['id']."'";
			
			$this->lithefire->updateRow("default", "tbl_user", $input, $filter);
			
		}
		die("booyah");
		
	}
	
	function updateGradePassword(){
        $db = "default";
        $table = "GRADEENTRYPASSWORD";

        $id = $this->input->post('id');
        $password = md5($_POST['oldpass']);
		//die($password);
        $new_password = md5($_POST['pass']);

        if(!$this->lithefire->countFilteredRows($db, $table, "ADMINPASSWORD = '$password'", "")){
        	//die($this->lithefire->currentQuery());
            $data['success'] = false;
            $data['data'] = "Old password does not match";
            die(json_encode($data));
        }

        $input = array("ADMINPASSWORD"=>$new_password);
        $filter = "id = 1";

        $data = $this->lithefire->updateRow($db, $table, $input, $filter);
		
		$this->lithefire->insertRow($db, "GRADEPASSWORDLOG", array("USERNAME"=>$this->session->userData("userName")));
        $data['data'] = "Password Successfully changed";

        die(json_encode($data));
        
    }
}

?>
