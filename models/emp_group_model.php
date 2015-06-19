<?php
Class Emp_group_model extends Commonmodel
{
    function Emp_group_model(){
        parent::__construct();
    }

    function getAllEmpGroupMembers($emp_group_id, $start, $limit, $sort, $dir, $query)
    {
        $fields = array("CONCAT(b.lastname, ', ', b.firstname) AS emp_name", "b.username", "a.employee_id", "a.start_date", "a.end_date", "a.id");
        $this->db->select($fields);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        $this->db->where("employee_group_id", $emp_group_id);
        if(!empty($query)){
            $this->db->or_like("emp_name", $query);
            $this->db->or_like("username", $query);
            $this->db->or_like("employee_id", $query);
            $this->db->or_like("start_date", $query);

        }



        $this->db->join("tbl_employee_info b", "a.employee_id = b.id");
        $query = $this->db->get("tbl_employee_group_members a");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
    }

    function getAppFlow($emp_group_id, $start, $limit, $sort, $dir, $query)
    {
        $fields = array("a.id", "a.employee_group_id", "a.app_type_id", "a.app_tree_id", "b.description as app_type", "c.description as app_tree");
        $this->db->select($fields);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        $this->db->where("a.employee_group_id", $emp_group_id);
        if(!empty($query)){
            $this->db->or_like("b.description", $query);
            $this->db->or_like("c.description", $query);
        }



        $this->db->join("tbl_app_type b", "a.app_type_id = b.id", "LEFT");
        $this->db->join("tbl_app_tree c", "a.app_tree_id = c.id", "LEFT");
        $query = $this->db->get("tbl_app_flow a");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
    }

    function countAppFlow($emp_group_id){
        $this->db->where("employee_group_id", $emp_group_id);
        return $this->db->count_all_results("tbl_app_flow");

    }

    function countEmpGroupMembers($emp_group_id){
        $this->db->where("employee_group_id", $emp_group_id);
        return $this->db->count_all_results("tbl_employee_group_members");

    }

}

?>
