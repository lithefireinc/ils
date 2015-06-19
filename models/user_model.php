<?php

class User_model extends Commonmodel
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
        $query=$this->db->get("tbl_user");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }
}


}

?>