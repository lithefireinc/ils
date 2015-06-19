<?php
/* 
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class Apps_model extends Commonmodel
{

function Apps_model()
{
parent::__construct();
$this->db = $this->load->database("default", TRUE);
}

function getEmpGroup($id){
    $fields = "employee_group_id";

    $today = date("Y-m-d");
    $this->db->where("employee_id", $id)->where("start_date <=", $today)->where("end_date is NULL")->select($fields);
    $query = $this->db->get("tbl_employee_group_members");
   // die($this->db->last_query());
    foreach($query->result_array() as $key => $val)
                return $val[$fields];
}

function getAppFlow($emp_group, $app_type_id){
    $fields = "id";

    $today = date("Y-m-d");
    $this->db->where("employee_group_id", $emp_group)->where("app_type_id", $app_type_id)->select($fields);
    $query = $this->db->get("tbl_app_flow");
   // die($this->db->last_query());
    foreach($query->result_array() as $key => $val)
                return $val[$fields];
}

function getAppFlowDetails($emp_group, $app_type_id){
    $fields = "a.employee_group_id, a.app_tree_id, b.app_group_id";

    $this->db->where("a.employee_group_id", $emp_group)->where("app_type_id", $app_type_id)->join("tbl_app_tree_details b", "a.app_tree_id = b.app_tree_id")
            ->where("parent is NULL")->select($fields);
    $query = $this->db->get("tbl_app_flow a");
    //die($this->db->last_query());
    if($query->num_rows()>0){

        // return result set as an associative array
        return $query->result_array();

    }
}

function getApprovers($app_flow, $app_type){
    $fields = array("a.id as app_tree_details_id", "e.id", "c.id as app_group_id", "c.description", "CONCAT(e.lastname, ', ', e.firstname) AS member_name");
    $today = date('Y-m-d');
    $this->db->where("b.employee_group_id", $app_flow)->where("b.app_type_id", $app_type)->where("d.end_date IS NULL")->select($fields)->join("tbl_app_flow b", "a.app_tree_id = b.app_tree_id")
        ->join("tbl_app_group c", "a.app_group_id = c.id")->join("tbl_app_group_members d", "c.id = d.app_group_id")
        ->join("tbl_employee_info e", "e.id = d.employee_id")->order_by("a.id ASC");

    $query = $this->db->get("tbl_app_tree_details a");
   // die($this->db->last_query());
    if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }

}

        function getPendingApplicationsPerApprover($approver_id, $start, $limit, $sort, $dir, $query){
            
            $result_array = array();


        $app_groups = $this->db->where("employee_id", $approver_id)->select("app_group_id")->get("tbl_app_group_members");
       //print $this->db->last_query();
        $app_group_id = array();
        foreach($app_groups->result_array() as $app_row):
                $app_group_id[] = $app_row['app_group_id'];
        endforeach;

        $applications = $this->db->where_in("app_group_id", $app_group_id)->where("is_active = 1")->where("status_id = 1")
                ->select("application_pk, app_type")->get("tbl_application_audit");
        $app_type = array();
       // die($this->db->last_query());
        foreach($applications->result_array() as $application_row):
                $app_type[$application_row['app_type']][] = $application_row['application_pk'];
        endforeach;

        foreach($app_type as $key =>$value):
        $fields = array("a.id","a.date_requested", "CONCAT(b.lastname, ', ', b.firstname) AS emp_name", "c.app_type", "c.id as audit_id");
        $this->db->select($fields)->limit($limit, $start)->order_by($sort, $dir)->where_in("a.id", $value);
        $this->db->join("tbl_employee_info b", "b.id = a.employee_id")->join("tbl_application_audit c", "c.application_pk = a.id");

        if(!empty($query)){
            $this->db->or_like("b.firstname", $query);
            $this->db->or_like("b.lastname", $query);
           // $this->db->or_like("d.description", $query);

        }
        $this->db->where("app_type", $key)->where("is_active = 1")->where("status_id = 1");
		
        $table = "";
        switch($key){
            case "Leave": $table = "tbl_leave_application";
                break;
            case "OT": $table = "tbl_ot_application";

                break;
            case "Client Schedule": $table = "tbl_client_schedule";
                break;
                
			case "TITO": $table = "tbl_tito_application";
                break;
            case "Training": $table = "tbl_training";
                break;    
			default: $table = "tbl_leave_application";	
        }
        $pending_applications = $this->db->get($table." a");
        //die($table);
       // if($table == "tbl_leave_application")
       // die($this->db->last_query());
        if($pending_applications->num_rows() > 0){
        foreach($pending_applications->result_array() as $row){
            $result_array[] = $row;
        }

        //return $query->result_array();
        }
        endforeach;
        return $result_array;
        /*$fields = array("a.id", "a.app_group_id", "b.description as app_group", "a.parent", "c.description as parent_name");
        $this->db->select($fields);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        $this->db->where("app_tree_id", $app_tree_id);
        if(!empty($query)){
            $this->db->or_like("b.description", $query);
            $this->db->or_like("c.description", $query);

        }



        $this->db->join("tbl_app_group b", "a.app_group_id = b.id", "LEFT");
        $this->db->join("tbl_app_group c", "a.parent = c.id", "LEFT");
        $query = $this->db->get("tbl_app_tree_details a", FALSE);
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }*/

    }

    function countApplicationsByApprover($approver_id){

        $app_groups = $this->db->where("employee_id", $approver_id)->select("app_group_id")->get("tbl_app_group_members");
        $app_group_id = array();
        foreach($app_groups->result_array() as $app_row):
                $app_group_id[] = $app_row['app_group_id'];
        endforeach;

        $this->db->where_in("app_group_id", $app_group_id)->where("is_active = 1")->where("status_id = 1");
        return $this->db->count_all_results("tbl_application_audit");

    }

    function getAuditDetails($id){
        $this->db->select("id, application_pk, app_type_id, app_type, requestor, employee_group_id, app_group_id, app_tree_id, status_id")
                ->where("id", $id)->where("is_active", 1);
        $query = $this->db->get("tbl_application_audit");
       //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }

    function getAuditApproverDetails($id, $app_type){
        $fields = array("a.approver_id","CONCAT(b.lastname, ', ', b.firstname) AS approver", "action_timestamp", "c.description as status", "a.remarks");
        $this->db->select($fields)
                ->where("a.application_pk", $id)->where("a.app_type", $app_type)->where("a.approver_id IS NOT NULL")
                ->join("tbl_employee_info b", "b.id = a.approver_id")->join("tbl_app_status c", "a.status_id = c.id");
        $query = $this->db->get("tbl_application_audit a");
        //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }

    function approveApplication($audit_id, $remarks){

    }

    function getNextApprover($app_tree_id, $app_group_id){
        $fields = array("app_group_id");
        $this->db->where("app_tree_id", $app_tree_id)->where("parent", $app_group_id)->select($fields);

        $query = $this->db->get("tbl_app_tree_details");
        //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }

    function insertAuditDetails($audit_fields){

            //die($this->db->insert_id());
            if($this->db->insert("tbl_application_audit", $audit_fields)){
            $data['success'] = true;
            $data['data'] = "Record successfully added";
            return $data;
            }

        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }


}

?>
