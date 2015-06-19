<?php

class Employee_model extends Commonmodel
{

function Employee_model()
{
parent::__construct();
}

function getModuleUsers($id, $start, $limit, $sort, $dir, $query, $queryby){
       // $this->database = $this->load->database($db, TRUE);
        $this->db->select("id, username", FALSE);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        if(!empty($query) && !empty($queryby)){
            if(is_array($queryby)){
                foreach ($queryby as $row):
                    $this->db->or_like($row, $query);
                endforeach;
            }else
            $this->db->like($queryby, $query);
        }

        $where = "username NOT IN (SELECT username from module_group_users WHERE group_id = $id)";

        $this->db->where($where);

      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->db->get("tbl_employee_info");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }
}

function getAllEmployees($start, $limit, $sort, $dir, $query, $queryby){
        $this->db->select("id, CONCAT(lastname, ', ', firstname) AS name", FALSE);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        if(!empty($query) && !empty($queryby)){
            if(is_array($queryby)){
                foreach ($queryby as $row):
                    $this->db->or_like($row, $query);
                endforeach;
            }else
            $this->db->like($queryby, $query);
        }

        $where = "username NOT IN (SELECT username from module_group_users WHERE group_id = $id)";

        $this->db->where($where);

      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->db->get("tbl_employee_info");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }
}

function getLeaveCredits($employee_id, $year){
    $query = $this->db->select("vacation_leave, sick_leave, paternity_leave, maternity_leave, emergency_leave")
            ->where("employee_id", $employee_id)->where("year", $year)->get("tbl_employee_leave_credits");
		//die($this->db->last_query());
    if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }else{
        $data['succes'] = false;
        $data['msg'] = "No leave Credits setup for this employee.";
        die(json_encode($data));
    }
}

function getEmployeeLeaveCredits($employee_id, $year){
    $query = $this->db->select("vacation_leave, sick_leave, paternity_leave, maternity_leave, emergency_leave")
            ->where("employee_id", $employee_id)->where("year", $year)->get("tbl_employee_leave_credits");
		//die($this->db->last_query());
    if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }else{
        return array();
    }
}

function getUsedLeaves($employee_id, $reset_date){
    $query = $this->db->select("SUM(no_days) as days, c.description")
            ->where("a.employee_id", $employee_id)->where_in("b.status_id", array(1, 2))->where("is_active", 1)
            ->where("date_requested >= '$reset_date'")
            ->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 2")->join("tbl_leave_type c", "a.leave_type = c.id")
            ->group_by(array("employee_id", "c.description"))

            ->get("tbl_leave_application a");
//die($this->db->last_query());
    if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }
}


}

?>
