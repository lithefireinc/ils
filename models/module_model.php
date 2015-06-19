<?php

class Module_model extends Commonmodel
{

function Module_model()
{
parent::__construct();
}

function getFilteredModule($id = 0, $start, $limit, $sort, $dir, $query, $queryby){
       // $this->database = $this->load->database($db, TRUE);
        $this->db->select("module.id, module.description as module, b.description as category", FALSE);
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

        $where = "module.id NOT IN (SELECT module_id from module_group_access WHERE group_id = $id)";

        $this->db->where($where);

        $this->db->where("module.is_public", 0);

        $this->db->join("module_category b", "module.category_id = b.id", 'left');

      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->db->get("module");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }
}


}

?>
