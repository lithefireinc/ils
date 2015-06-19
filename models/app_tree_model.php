<?php
class App_tree_model extends Commonmodel
{
    function App_tree_model(){
        parent::__construct();
    }

    function getAppTreeDetails($app_tree_id, $start, $limit, $sort, $dir, $query)
    {
        $fields = array("a.id", "a.app_tree_id","a.app_group_id", "b.description as app_group", "a.parent", "c.description as parent_name");
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
        $query = $this->db->get("tbl_app_tree_details a");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
    }

    function getAppGroupParent($app_tree_id, $start, $limit, $sort, $dir, $query){
        $fields = array("b.id", "b.description as name");

        $this->db->select($fields);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        $this->db->where("app_tree_id", $app_tree_id);
        $this->db->where("parent is NULL");

        if(!empty($query)){
            $this->db->or_like("b.description", $query);
            $this->db->or_like("b.id", $query);
        }

        $this->db->join("tbl_app_group b", "a.app_group_id = b.id");
        $query = $this->db->get("tbl_app_tree_details a");

        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
    }

    function countAppTreeDetails($app_tree_id){
        $this->db->where("app_tree_id", $app_tree_id);
        return $this->db->count_all_results("tbl_app_tree_details");

    }

}

?>