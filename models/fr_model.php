<?php
class Fr_model extends Commonmodel
{
    private $database;
    private $temp_db;

    private $today;
    private $now;

    function Fr_model()
    {
    parent::__construct();
    $this->today = date("Y-m-d");
    $this->now = date("H:i:s");
    }


    function getAllRooms($start, $limit, $sort, $dir, $query)
    {
        $this->db = $this->load->database("fr", TRUE);

        $fields = array("ROOMIDNO", "ROOM", "DESCRIPTIO");
        $this->db->select($fields);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        $this->db->where("ACTIVATED", 1);
        if(!empty($query)){
            $this->db->or_like("ROOM", $query);
            $this->db->or_like("DESCRIPTIO", $query);
        }


        $query = $this->db->get("FILEROOM");
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }

     function countRooms(){
        $this->db = $this->load->database("fr", TRUE);
        $this->db->where("ACTIVATED", 1);
        return $this->db->count_all_results("FILEROOM");
     }

     function getAllRows($table, $fields, $start, $limit, $sort, $dir, $query, $filter = array())
    {
        $this->db = $this->load->database("fr", TRUE);

        $this->db->select($fields, FALSE);
        
        
        $this->db->where("ACTIVATED", 1);


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

        $this->db->order_by($sort, $dir);
        $this->db->limit($limit, $start);


        $query = $this->db->get($table);
        //die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }

     function countRows($table){
        $this->db = $this->load->database("fr", TRUE);
        $this->db->where("ACTIVATED", 1);
        return $this->db->count_all_results($table);
     }

     function countFilteredRows($table, $query = "", $filter = array())
    {
        $this->db = $this->load->database("fr", TRUE);
        $this->db->select("ACTIVATED", FALSE);

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

        $this->db->where("ACTIVATED", 1);

      //  $query=$this->sms->query("SELECT * FROM filereferredby WHERE 1=1 $query ORDER BY $sort $dir");
        $query=$this->db->get($table);


        // return result set as an associative array

        return $query->num_rows();



    }

     function insertRow($table, $id_field, $id, $input, $compare){
        $this->db = $this->load->database("fr", TRUE);

        if(empty($input)){
            $data['success'] = false;
            $data['data'] = "Data is empty";
            return;
        }

        $this->db->where($compare);
        if($this->db->count_all_results($table))
        {
            $data['success'] = false;
            $data['data'] = "Record already exists!";
            return $data;
        }
       // die("123");
        $input[$id_field] = $id;
        $input['DCREATED'] = $this->today;
        $input['TCREATED'] = $this->now;
        $input['DMODIFIED'] = $this->today;
        $input['TMODIFIED'] = $this->now;
        $input['ACTIVATED'] = 1;

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

     function getRowWhere($table, $field, $param, $fields){
        $this->db = $this->load->database("fr", TRUE);
        $this->db->where($field,$param);
        $this->db->select($fields);

        $query=$this->db->get($table);

        // return result set as an associative array

        return $query->result_array();

    }

    function updateRow($table, $input, $param, $value, $compare){
        if(empty($input))
            return;
        $this->db = $this->load->database("fr", TRUE);
        $this->temp_db = $this->load->database("fr", TRUE);

        $this->temp_db->where($compare);
        $this->temp_db->where($param." !=", $value);
        if($this->temp_db->count_all_results($table))
        {
            $data['success'] = false;
            $data['data'] = "Record already exists!";
            return $data;
        }
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

    function deleteRow($table, $param, $value){

        $this->db = $this->load->database("fr", TRUE);

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

    //Default Database

    function getAllRowsDefault($table, $fields, $start, $limit, $sort, $dir, $query)
    {
        $this->db = $this->load->database("default", TRUE);

        $this->db->select($fields);
        $this->db->limit($limit, $start);
        $this->db->order_by($sort, $dir);
        $this->db->where("ACTIVATED", 1);
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


        $query = $this->db->get($table);
       // die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }

     function countRowsDefault($table){
        $this->db = $this->load->database("default", TRUE);
        $this->db->where("ACTIVATED", 1);
        return $this->db->count_all_results($table);
     }

     function countFilteredRowsDefault($table, $query = "", $filter = array())
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

     function insertRowDefault($table, $id_field, $id, $input, $compare){
        $this->db = $this->load->database("default", TRUE);

        if(empty($input)){
            $data['success'] = false;
            $data['data'] = "Data is empty";
            return;
        }

        $this->db->where($compare);
        if($this->db->count_all_results($table))
        {
            $data['success'] = false;
            $data['data'] = "Record already exists!";
            return $data;
        }
       // die("123");
        $input[$id_field] = $id;
        $input['DCREATED'] = $this->today;
        $input['TCREATED'] = $this->now;
        $input['DMODIFIED'] = $this->today;
        $input['TMODIFIED'] = $this->now;
        $input['ACTIVATED'] = 1;

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

     function getRowWhereDefault($table, $field, $param, $fields){
        $this->db = $this->load->database("default", TRUE);
        $this->db->where($field,$param);
        $this->db->select($fields);

        $query=$this->db->get($table);

        // return result set as an associative array

        return $query->result_array();

    }

    function updateRowDefault($table, $input, $param, $value, $compare){
        $this->db = $this->load->database("default", TRUE);

        if(empty($input))
            return;

        $this->temp_db = $this->load->database("default", TRUE);

        $this->temp_db->where($compare);
        $this->temp_db->where($param." !=", $value);
        if($this->temp_db->count_all_results($table))
        {
            $data['success'] = false;
            $data['data'] = "Record already exists!";
            return $data;
        }
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

    function deleteRowDefault($table, $param, $value){
        $this->db = $this->load->database("default", TRUE);

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
}

?>