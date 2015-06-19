<?php

class Commonmodel extends CI_Model
{
    private $database;
    private $temp_db;

    
    function Commonmodel()
    {
        parent::__construct();
       // $this->database = $this->load->database('sms', TRUE);
    }

    function getAllRecords($db = "default", $table = "", $sort = "id", $dir = "DESC", $queryby ="", $query = "", $fields = "*", $start =0, $limit = 25)
    {
        $this->database = $this->load->database($db, TRUE);
        $this->database->select($fields);
        $this->database->limit($limit, $start);
        $this->database->order_by($sort, $dir);
        if(!empty($query) && !empty($queryby)){
            if(is_array($queryby)){
                foreach ($queryby as $row):
                    $this->database->or_like($row, $query);
                endforeach;
            }else
            $this->database->like($queryby, $query);
        }
      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->database->get($table);
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }

    }

    function getFilteredRecords($db = "default", $table = "", $sort = "id", $dir = "DESC", $queryby ="", $query = "", $fields = "*", $start =0, $limit = 25, $filter = array(), $join =array())
    {
        $this->database = $this->load->database($db, TRUE);
        $this->database->select($fields, FALSE);
        $this->database->limit($limit, $start);
        $this->database->order_by($sort, $dir);
        if(!empty($query) && !empty($queryby)){
            if(is_array($queryby)){
                foreach ($queryby as $row):
                    $this->database->or_like($row, $query);
                endforeach;
            }else
            $this->database->like($queryby, $query);
        }

        if(!empty($filter)){
            $this->database->where($filter);
        }

        if(!empty($join)){
            foreach($join as $key => $value){
            $this->database->join($key, $value, 'left');
            }
        }
      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->database->get($table);
        
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

    }

    }

    function getNumRecords($db = "default", $table = "", $queryby = "", $query = "", $filter=array(), $join = array()){
        $this->database = $this->load->database($db, TRUE);
        if(!empty($query) && !empty($queryby)){
            if(is_array($queryby)){
                foreach ($queryby as $row):
                    $this->database->or_like($row, $query);
                endforeach;
            }else
            $this->database->like($queryby, $query);
        }

        if(!empty($filter)){
            $this->database->where($filter);
        }

        if(!empty($join)){
            foreach($join as $key => $value){
            $this->database->join($key, $value, 'left');
            }
        }

        return $this->database->count_all_results($table);

    }

    function getFilteredNumRecords($db = "default", $table = "", $sort = "id", $dir = "DESC", $queryby ="", $query = "", $fields = "*", $start =0, $limit = 25, $filter = array(), $join =array())
    {
        $this->database = $this->load->database($db, TRUE);
        $this->database->select($fields, FALSE);

        if(!empty($query) && !empty($queryby)){
            if(is_array($queryby)){
                foreach ($queryby as $row):
                    $this->database->or_like($row, $query);
                endforeach;
            }else
            $this->database->like($queryby, $query);
        }

        if(!empty($filter)){
            $this->database->where($filter);
        }

        if(!empty($join)){
            foreach($join as $key => $value){
            $this->database->join($key, $value, 'left');
            }
        }
      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->database->get($table);


        // return result set as an associative array

        return $query->num_rows();



    }

    function insertRecord($db = "default", $table = "", $fields = array()){
        if(empty($fields))
            return;
        $this->database = $this->load->database($db, TRUE);

        $this->database->where($fields);
        if($this->database->count_all_results($table))
        {
            $data['success'] = false;
            $data['data'] = "Record already exists!";
            return $data;
        }
        if($this->database->insert($table, $fields))
        {
            $data['success'] = true;
            $data['data'] = "Record successfully added";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function updateRecord($db = "default", $table = "", $fields = array(), $param = "id", $value = ""){
        if(empty($fields))
            return;
        $this->database = $this->load->database($db, TRUE);
        $this->temp_db = $this->load->database($db, TRUE);

        $this->temp_db->where($fields);
        $this->temp_db->where($param." !=", $value);
        if($this->temp_db->count_all_results($table))
        {
            $data['success'] = false;
            $data['data'] = "Record already exists!";
            return $data;
        }
        $this->database->where($param, $value);
        if($this->database->update($table, $fields))
        {
            $data['success'] = true;
            $data['data'] = "Record successfully updated";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function deleteRecord($db = "default", $table = "", $param = "id", $value = ""){

        $this->database = $this->load->database($db, TRUE);

        $this->database->where($param, $value);
        if($this->database->delete($table))
        {
            $data['success'] = true;
            $data['data'] = "Record successfully deleted";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function getRecordWhere($db, $table, $field, $param, $fields){
        $this->database = $this->load->database($db, TRUE);
        $this->database->where($field,$param);
        $this->database->select($fields);

        $query=$this->database->get($table);

        // return result set as an associative array

        return $query->result_array();

    }

    function getFieldWhere($db, $table, $field, $param, $fields){
        $this->database = $this->load->database($db, TRUE);
        $this->database->where($field,$param);
        $this->database->select($fields);

        $query=$this->database->get($table);

        // return result set as an associative array

        foreach($query->result_array() as $key => $val)
                return $val[$fields];

    }

    function getNextId($db, $table){
        $this->database = $this->load->database('info_schema', TRUE);
        $this->database->where("TABLE_SCHEMA",$db);
        $this->database->where("TABLE_NAME",$table);
        $this->database->select("AUTO_INCREMENT");

        $query=$this->database->get("TABLES");

        // return result set as an associative array

        foreach($query->result_array() as $key => $val)
                return $val['AUTO_INCREMENT'];

    }

   /* function getReferredByWhere($field,$param){

        $this->sms->where($field,$param);

        $query=$this->sms->get('filereferredby');

        // return result set as an associative array

        return $query->result_array();

    }

    // get total number of users

    function getNumReferredBy(){

        return $this->sms->count_all('filereferredby');

    }

    function checkExisting($field,$param){

        $this->sms->where($field,$param);

        $query=$this->sms->get('filereferredby');

        // return result set as an associative array

        return $query->num_rows();

    }*/



}

?>