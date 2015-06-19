<?php
class App_group_model extends Commonmodel
{
    function App_group_model(){
        parent::__construct();
    }

    function getAllAppGroupMembers($app_group_id, $start, $limit, $sort, $dir, $query)
    {
        $fields = array("CONCAT(b.lastname, ', ', b.firstname) AS emp_name", "b.username", "a.employee_id", "a.start_date", "a.end_date", "a.id");
        $this->db->select($fields);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        $this->db->where("app_group_id", $app_group_id);
        if(!empty($query)){
            $this->db->or_like("emp_name", $query);
            $this->db->or_like("username", $query);
            $this->db->or_like("employee_id", $query);
            $this->db->or_like("start_date", $query);

        }

        

        $this->db->join("tbl_employee_info b", "a.employee_id = b.id");
        $query = $this->db->get("tbl_app_group_members a");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
    }

    function countAppGroupMembers($app_group_id){
        $this->db->where("app_group_id", $app_group_id);
        return $this->db->count_all_results("tbl_app_group_members");

    }
    
}

?>
