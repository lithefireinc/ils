<?php
class Leaves_model extends Commonmodel{
    function Leaves_model(){
        parent::__construct();
    }

    function insertLeave($fields, $audit_fields){

        
        if($this->db->insert("tbl_leave_application", $fields))
        {
            //die($this->db->insert_id());
            $leave_id = $audit_fields['application_pk'] = $this->db->insert_id();
            if($this->db->insert("tbl_application_audit", $audit_fields)){
            	
				
            $data['success'] = true;
			$data['leave_id'] = $leave_id;
            $data['data'] = "Record successfully added";
            return $data;
            }
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function getLeavesByEmployee($employee_id, $start, $limit, $sort, $dir, $query, $queryby){
        $table = "tbl_leave_application a";
        $fields = array("a.id", "a.date_from",
            "a.date_to", "a.no_days", "b.status_id", "c.description as status",
            "a.reason", "a.date_requested", "b.id as audit_id", "b.app_type", "d.description as leave_type");

        if(!empty($query) && !empty($queryby)){
            if(is_array($queryby)){
                foreach ($queryby as $row):
                    $this->db->or_like($row, $query);
                endforeach;
            }else
            $this->db->like($queryby, $query);
        }

        $this->db->select($fields, FALSE)->limit($limit, $start)->order_by($sort, $dir)
            ->where("employee_id", $employee_id)->where("is_active = 1")
                ->join("tbl_application_audit b", "a.id = b.application_pk AND app_type_id = 2")->join("tbl_app_status c", "b.status_id = c.id")
                ->join("tbl_leave_type d", "a.leave_type = d.id");

        $query = $this->db->get($table);
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }

    }

    function countLeavesByEmployee($employee_id){
        $this->db->where("employee_id", $employee_id);
        return $this->db->count_all_results("tbl_leave_application");

    }

    function getLeaveDetails($id){
        $fields = array("CONCAT(b.lastname, ', ', b.firstname) AS employee_name", "a.date_requested", "a.date_from", "a.date_to", "a.no_days", "a.reason", "c.description as leave_type");
        $this->db->select($fields)
                ->where("a.id", $id)->join("tbl_employee_info b", "b.id = a.employee_id")->join("tbl_leave_type c", "a.leave_type = c.id");
        $query = $this->db->get("tbl_leave_application a");
        //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }

    function checkValid($fields, $call_log_id = 0){
        $this->db->where("employee_id", $fields['employee_id']);

        $date_filter = "('".$fields['date_from']."' BETWEEN date_from AND date_to OR '".$fields['date_to']."' BETWEEN date_from AND date_to OR date_from BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."' OR date_to BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."')";
        $this->db->where($date_filter, NULL, FALSE)->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 2");
        /*$this->db->or_where("date_from", $fields['date_from']);
        $this->db->or_where("date_to", $fields['date_from']);
        $this->db->or_where("date_from", $fields['date_to']);
        $this->db->or_where("date_to", $fields['date_to']);*/
        //$query = $this->db->get("tbl_leave_application a", FALSE);
       // die($this->db->last_query());
        if($this->db->count_all_results("tbl_leave_application a"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed from other applications.";
            return $data;
        }
		
		$this->db->where($date_filter, NULL, FALSE)->where("employee_id", $fields['employee_id'])->where("id != ".$call_log_id);
		
		if($this->db->count_all_results("tbl_call_log"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed from a call log.";
            return $data;
        }
		
		$cs_filter = "date_scheduled BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."'";
		$this->db->where($cs_filter, NULL, FALSE)->where("employee_id", $fields['employee_id'])
		->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 4");
		
		if($this->db->count_all_results("tbl_client_schedule a"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed for a client schedule.";
            return $data;
        }
        
        $training_filter = "('".$fields['date_from']."' BETWEEN date_start AND date_end OR '".$fields['date_to']."' BETWEEN date_start AND date_end OR date_start BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."' OR date_end BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."')";
		$this->db->where($training_filter, NULL, FALSE)->where("employee_id", $fields['employee_id'])
		->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 6");
		
		if($this->db->count_all_results("tbl_training a"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed for a training.";
            return $data;
        }
		

        $data['success'] = true;
        return $data;
    }

}

?>