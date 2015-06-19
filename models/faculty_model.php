<?php

class Faculty_model extends Commonmodel
{

private $fr_db;
private $temp_db;

private $today;
private $now;

function Faculty_model()
{
parent::__construct();
$this->today = date("Y-m-d");
$this->now = date("H:i:s");

}

function getAllRows($db, $table, $fields, $start, $limit, $sort, $dir, $query, $filter = array())
{
        $this->fr_db = $this->load->database($db, TRUE);

        $this->fr_db->select($fields, FALSE);


        if(!empty($query)){
            if(is_array($query)){
             foreach($query as $key =>$val):
                 $this->fr_db->or_like($key, $val);
             endforeach;
            }else{
            $this->fr_db->where($query);
            }

        }

        if(!empty($filter)){
            if(is_array($filter)){
            foreach($filter as $key =>$val):
                 $this->fr_db->where($key, $val);
             endforeach;
            }else{
                $this->fr_db->where($filter);
            }
        }

        if(!empty($dir))
        $this->fr_db->order_by($sort, $dir);
        else
        $this->fr_db->order_by($sort);

        if(!empty($limit))
        $this->fr_db->limit($limit, $start);


        $query = $this->fr_db->get($table);
      // die($this->fr_db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
}

function countFilteredRows($db, $table, $query = "", $filter = array())
{
        $this->fr_db = $this->load->database($db, TRUE);
        $this->fr_db->select("*", FALSE);

        if(!empty($query)){
            if(is_array($query)){
             foreach($query as $key =>$val):
                 $this->fr_db->or_like($key, $val);
             endforeach;
            }else{
            $this->fr_db->where($query);
            }

        }

        if(!empty($filter)){
            if(is_array($filter)){
            foreach($filter as $key =>$val):
                 $this->fr_db->where($key, $val);
             endforeach;
            }else{
                $this->fr_db->where($filter);
            }
        }

      //  $query=$this->sms->query("SELECT * FROM filereferrefr_dby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->fr_db->get($table);


        // return result set as an associative array

        return $query->num_rows();



}

function insertRow($db, $table, $input){
        $this->db = $this->load->database($db, TRUE);

        if(empty($input)){
            $data['success'] = false;
            $data['data'] = "Data is empty";
            return;
        }
     

        if($this->db->insert($table, $input))
        {
            $data['success'] = true;
            $data['data'] = "Record successfully added";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
     }

     function updateRow($db, $table, $input, $param, $value){
        if(empty($input))
            return;
        $this->db = $this->load->database($db, TRUE);
        $this->temp_db = $this->load->database($db, TRUE);

        $input['DMODIFIED'] = $this->today;
        $input['TMODIFIED'] = $this->now;
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


}

?>