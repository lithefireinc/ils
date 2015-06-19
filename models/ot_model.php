<?php
class Ot_model extends Commonmodel{

    private $temp_db;
    
    function Ot_model(){
        parent::__construct();
    }

    function insertOT($fields, $audit_fields){

        $this->db->where("employee_id", $fields['employee_id']);

        $date_filter = "('".$fields['date_from']."' BETWEEN date_from AND date_to AND '".$fields['date_to']."' BETWEEN date_from AND date_to OR date_from BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."' OR date_to BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."')";
        $this->db->where($date_filter, NULL, FALSE)->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk and b.app_type_id = 1");
        /*$this->db->or_where("date_from", $fields['date_from']);
        $this->db->or_where("date_to", $fields['date_from']);
        $this->db->or_where("date_from", $fields['date_to']);
        $this->db->or_where("date_to", $fields['date_to']);*/

        if($this->db->count_all_results("tbl_ot_application a"))
        {
            $data['success'] = false;
            $data['data'] = "Dates already filed from other OT applications.";
            return $data;
        }
        if($this->db->insert("tbl_ot_application", $fields))
        {
            //die($this->db->insert_id());
            $audit_fields['application_pk'] = $this->db->insert_id();
            if($this->db->insert("tbl_application_audit", $audit_fields)){
            $data['success'] = true;
            $data['data'] = "Application successfully saved.";
            return $data;
            }
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function getOTByEmployee($employee_id, $start, $limit, $sort, $dir, $query, $queryby){
        $table = "tbl_ot_application a";
        $fields = array("a.id", "a.date_from", "a.date_to", "a.no_hours", "b.status_id", "c.description as status", "a.reason", "a.date_requested", "b.id as audit_id", "b.app_type");

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
                ->join("tbl_application_audit b", "a.id = b.application_pk AND app_type_id = 1")->join("tbl_app_status c", "b.status_id = c.id");

        $query = $this->db->get($table);
       // die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }

    }

    function countOTByEmployee($employee_id){
        $this->db->where("employee_id", $employee_id);
        return $this->db->count_all_results("tbl_ot_application");

    }

    function getOTDetails($id){
        $fields = array("CONCAT(b.lastname, ', ', b.firstname) AS employee_name", "a.date_requested", "a.date_from", "a.date_to", "a.no_hours", "a.reason");
        $this->db->select($fields)
                ->where("a.id", $id)->join("tbl_employee_info b", "b.id = a.employee_id");
        $query = $this->db->get("tbl_ot_application a");
        //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }
	
	function getTITODetails($id){
        $fields = array("CONCAT(b.lastname, ', ', b.firstname) AS employee_name", "a.date_requested", "a.date_time_in", "a.date_time_out", "a.time_in", "a.time_out", "a.reason");
        $this->db->select($fields)
                ->where("a.id", $id)->join("tbl_employee_info b", "b.id = a.employee_id");
        $query = $this->db->get("tbl_tito_application a");
        //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }

    function getCSDetails($id){
        $fields = array("CONCAT(b.lastname, ', ', b.firstname) AS employee_name",
            "a.date_scheduled", "a.time_in", "a.time_out", "a.type", "a.client_id",
            "a.contact_person_id", "a.purpose_id", "a.date_requested", "a.agenda");
        $this->db->select($fields)
                ->where("a.id", $id)->join("tbl_employee_info b", "b.id = a.employee_id");
        $query = $this->db->get("tbl_client_schedule a");
        //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }
	
	function getTrainingDetails($id){
        $fields = array("CONCAT(b.lastname, ', ', b.firstname) AS employee_name",
            "a.date_start", "a.date_end", "a.start_time", "a.end_time", "a.type", "a.supplier_id",
            "a.training_type_id", "a.date_requested", "a.details", "a.title");
        $this->db->select($fields)
                ->where("a.id", $id)->join("tbl_employee_info b", "b.id = a.employee_id");
                
        $query = $this->db->get("tbl_training a");
        //die($this->db->last_query());
        if($query->num_rows()>0){

            // return result set as an associative array

            return $query->result_array();

        }
    }

    function insertCS($fields, $audit_fields){

        $this->db->where("employee_id", $fields['employee_id']);

        //$date_filter = "('".$fields['date_schedule']."' BETWEEN date_from AND date_to AND '".$fields['date_to']."' BETWEEN date_from AND date_to OR date_from BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."' OR date_to BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."')";
        $date_filter = "date_scheduled = '".$fields['date_scheduled']."' AND ('".$fields['time_in']."' BETWEEN time_in AND time_out AND '".$fields['time_out']."' BETWEEN time_in AND time_out OR time_in BETWEEN '".$fields['time_in']."' AND '".$fields['time_out']."' OR time_out BETWEEN '".$fields['time_in']."' AND '".$fields['time_out']."')";
        $this->db->where($date_filter, NULL, FALSE)->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk and b.app_type_id = 4");
        /*$this->db->or_where("date_from", $fields['date_from']);
        $this->db->or_where("date_to", $fields['date_from']);
        $this->db->or_where("date_from", $fields['date_to']);
        $this->db->or_where("date_to", $fields['date_to']);*/

        if($this->db->count_all_results("tbl_client_schedule a"))
        {
            $data['success'] = false;
            $data['data'] = "Dates already filed from other Client Schedules.";
            return $data;
        }
        
		$leave_filter = "'".$fields['date_scheduled']."' BETWEEN date_from AND date_to";
		$this->db->where($leave_filter, NULL, FALSE)->where("employee_id", $fields['employee_id'])
		->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 2");
		
		if($this->db->count_all_results("tbl_leave_application a"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed for a leave application.";
            return $data;
        }

		$training_filter = "'".$fields['date_scheduled']."' BETWEEN date_start AND date_end";
		$this->db->where($training_filter, NULL, FALSE)->where("employee_id", $fields['employee_id'])
		->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 6");
		
		if($this->db->count_all_results("tbl_training a"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed for training.";
            return $data;
        }
		
        if($this->db->insert("tbl_client_schedule", $fields))
        {
            //die($this->db->insert_id());
            $audit_fields['application_pk'] = $this->db->insert_id();
            if($this->db->insert("tbl_application_audit", $audit_fields)){
            $data['success'] = true;
            $data['data'] = "Application successfully saved.";
            return $data;
            }
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

	function insertTraining($fields, $audit_fields){

        $this->db->where("employee_id", $fields['employee_id']);

        //$date_filter = "('".$fields['date_schedule']."' BETWEEN date_from AND date_to AND '".$fields['date_to']."' BETWEEN date_from AND date_to OR date_from BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."' OR date_to BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."')";
        $date_filter = "('".$fields['date_start']."' BETWEEN date_start AND date_end AND '".$fields['date_end']."' BETWEEN date_start AND date_end OR date_start BETWEEN '".$fields['date_start']."' AND '".$fields['date_end']."' OR date_end BETWEEN '".$fields['date_start']."' AND '".$fields['date_end']."')";
        $this->db->where($date_filter, NULL, FALSE)->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk and b.app_type_id = 6");
        /*$this->db->or_where("date_from", $fields['date_from']);
        $this->db->or_where("date_to", $fields['date_from']);
        $this->db->or_where("date_from", $fields['date_to']);
        $this->db->or_where("date_to", $fields['date_to']);*/

        if($this->db->count_all_results("tbl_training a"))
        {
            $data['success'] = false;
            $data['data'] = "Dates already filed from other Trainings";
            return $data;
        }
        
		$cs_filter = "date_scheduled BETWEEN '".$fields['date_start']."' AND '".$fields['date_end']."'";
		$this->db->where($cs_filter, NULL, FALSE)->where("employee_id", $fields['employee_id'])
		->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 4");
		
		if($this->db->count_all_results("tbl_client_schedule a"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed for a client schedule.";
            return $data;
        }
		
		$leave_filter = "('".$fields['date_start']."' BETWEEN date_from AND date_to OR '".$fields['date_end']."' BETWEEN date_from AND date_to OR date_from BETWEEN '".$fields['date_start']."' AND '".$fields['date_end']."' OR date_to BETWEEN '".$fields['date_start']."' AND '".$fields['date_end']."')";
        $this->db->where($leave_filter, NULL, FALSE)->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk AND b.app_type_id = 2");

        if($this->db->count_all_results("tbl_leave_application a"))
        {
        //	die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed from a leave application.";
            return $data;
        }

        if($this->db->insert("tbl_training", $fields))
        {
            //die($this->db->insert_id());
            $audit_fields['application_pk'] = $this->db->insert_id();
            if($this->db->insert("tbl_application_audit", $audit_fields)){
            $data['success'] = true;
            $data['data'] = "Application successfully saved.";
            return $data;
            }
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function getCSByEmployee($employee_id, $start, $limit, $sort, $dir, $query, $queryby){
        $table = "tbl_client_schedule a";
        $fields = array("a.id", "b.status_id", "c.description as status", "a.agenda", "a.date_requested", "b.id as audit_id",
            "a.date_scheduled", "a.time_in", "a.time_out", "a.type", "a.client_id",
            "a.contact_person_id", "a.purpose_id", "b.app_type");

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
                ->join("tbl_application_audit b", "a.id = b.application_pk AND app_type_id = 4")->join("tbl_app_status c", "b.status_id = c.id");

        $query = $this->db->get($table);
       // die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }

    }
    
    function getTrainingByEmployee($employee_id, $start, $limit, $sort, $dir, $query, $queryby){
        $table = "tbl_training a";
        $fields = array("a.id", "b.status_id", "c.description as status", "a.details", "a.title", "a.date_requested", "b.id as audit_id",
            "a.date_start", "a.date_end", "a.start_time", "a.end_time", "a.type", "a.supplier_id",
            "a.training_type_id", "b.app_type");

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
                ->join("tbl_application_audit b", "a.id = b.application_pk AND app_type_id = 6")->join("tbl_app_status c", "b.status_id = c.id");

        $query = $this->db->get($table);
       // die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }

    }

    function insertCallLog($fields){

        $this->db->where("employee_id", $fields['employee_id']);

        $date_filter = "('".$fields['date_from']."' BETWEEN date_from AND date_to AND '".$fields['date_to']."' BETWEEN date_from AND date_to OR date_from BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."' OR date_to BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."')";
        $this->db->where($date_filter, NULL, FALSE);
        /*$this->db->or_where("date_from", $fields['date_from']);
        $this->db->or_where("date_to", $fields['date_from']);
        $this->db->or_where("date_from", $fields['date_to']);
        $this->db->or_where("date_to", $fields['date_to']);*/

        if($this->db->count_all_results("tbl_call_log a"))
        {
        	//die($this->db->last_query());
            $data['success'] = false;
            $data['data'] = "Dates already filed from other Call log entries.";
            return $data;
        }
        if($this->db->insert("tbl_call_log", $fields))
        {

            $data['success'] = true;
            $data['data'] = "Application successfully saved.";
            return $data;

        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function getCallLogs($start, $limit, $sort, $dir, $query)
    {
        $this->db = $this->load->database("default", TRUE);
        $fields = array("a.id", "d.calllog as call_log_type", "a.date_requested", "CONCAT(b.lastname, ', ', b.firstname) AS employee_name",  "a.date_from", "a.date_to", "a.no_days", "a.reason", "CONCAT(c.lastname, ', ', c.firstname) AS requested_by", "a.leave_filed");
        $this->db->select($fields, FALSE);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir)
                ->where("a.employee_id = b.id")->where("a.requested_by = c.id")->where("a.call_log_type_id = d.caloid");
        if(!empty($query)){
            if(is_array($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;
            }else{
            foreach($fields as $key => $value):
            $this->db->or_like($value, $query);
            endforeach;
            }

        }
		
		$fr_db = $this->config->item("engine_db");
		$default_db = $this->config->item("hris_db");


        $query = $this->db->get("tbl_call_log a, tbl_employee_info b, tbl_employee_info c, $fr_db.filecalo d");
       // die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }

     function countCallLogs($table, $query = "", $filter = array())
    {
        $this->db = $this->load->database("default", TRUE);

        if(!empty($query)){
            if(is_array($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;
            }else{
            foreach($fields as $key => $value):
            $this->db->or_like($value, $query);
            endforeach;
            }

        }

        if(!empty($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
        }

      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->db->get($table);


        // return result set as an associative array

        return $query->num_rows();



    }

    function loadCallLog($id){
        $fields = array("a.id", "employee_id", "date_from", "call_log_type_id","date_to", "portion", "no_days", "a.reason", "CONCAT(lastname, ', ', firstname) AS employee_name", "calllog", "a.leave_filed");
        $this->db->select($fields);
        $this->db->where("a.id", $id)->where("a.employee_id = b.id")->where("a.call_log_type_id = c.caloid");
        
		$fr_db = $this->config->item("engine_db");
		$default_db = $this->config->item("hris_db");

        $query=$this->db->get("tbl_call_log a, tbl_employee_info b, $fr_db.filecalo c");

        // return result set as an associative array

        return $query->result_array();

    }

    function updateCallLog($table, $input, $param, $value){


        if(empty($input))
            return;

        $this->temp_db = $this->load->database("default", TRUE);

        $this->temp_db->where($param." !=", $value);
        $this->temp_db->where("employee_id", $input['employee_id']);

        $date_filter = "('".$input['date_from']."' BETWEEN date_from AND date_to AND '".$input['date_to']."' BETWEEN date_from AND date_to OR date_from BETWEEN '".$input['date_from']."' AND '".$input['date_to']."' OR date_to BETWEEN '".$input['date_from']."' AND '".$input['date_to']."')";
        $this->temp_db->where($date_filter, NULL, FALSE);
        if($this->temp_db->count_all_results($table))
        {
            $data['success'] = false;
            $data['data'] = "Dates already filed from other Call log entries.";
            return $data;
        }
       // $input['DMODIFIED'] = $this->today;
       // $input['TMODIFIED'] = $this->now;
        $this->db->where($param, $value);
        if($this->db->update($table, $input))
        {
            $data['success'] = true;
            $data['data'] = "Record successfully updated";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function deleteCallLog($table, $param, $value){


        $this->db->where($param, $value);
        if($this->db->delete($table))
        {
            $data['success'] = true;
            $data['data'] = "Record successfully deleted";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

	function insertTITO($fields, $audit_fields){

        //$this->db->where("employee_id", $fields['employee_id']);

        //$date_filter = "('".$fields['date_time_in']."' BETWEEN date_time_in AND date_time_out AND '".$fields['date_time_out']."' BETWEEN date_time_in AND date_time_out OR date_from BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."' OR date_to BETWEEN '".$fields['date_from']."' AND '".$fields['date_to']."')";
        //$this->db->where($date_filter, NULL, FALSE)->where_in("b.status_id", array(1,2))->join("tbl_application_audit b", "a.id = b.application_pk");
        /*$this->db->or_where("date_from", $fields['date_from']);
        $this->db->or_where("date_to", $fields['date_from']);
        $this->db->or_where("date_from", $fields['date_to']);
        $this->db->or_where("date_to", $fields['date_to']);*/

        /*if($this->db->count_all_results("tbl_tito_application a"))
        {
            $data['success'] = false;
            $data['data'] = "Dates already filed from other TITO applications.";
            return $data;
        }*/
        if($this->db->insert("tbl_tito_application", $fields))
        {
            //die($this->db->insert_id());
            $audit_fields['application_pk'] = $this->db->insert_id();
            if($this->db->insert("tbl_application_audit", $audit_fields)){
            $data['success'] = true;
            $data['data'] = "Application successfully saved.";
            return $data;
            }
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

}

?>