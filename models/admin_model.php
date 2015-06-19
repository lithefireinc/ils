<?php
class Admin_model extends Commonmodel
{
    private $database;
    private $temp_db;

    private $today;
    private $now;

    function Admin_model()
    {
    parent::__construct();
    $this->today = date("Y-m-d");
    $this->now = date("H:i:s");
    }

    function getAllRows($table, $fields, $start, $limit, $sort, $dir, $query = array(), $filter = array())
    {

        $this->db->select($fields);

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;


        }

        if(!empty($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
        }

        $this->db->order_by($sort, $dir);
        if($start != 0 && $limit != 0)
        $this->db->limit($limit, $start);


        $query = $this->db->get($table);
        //die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }


     function countRows($table, $query = array(), $filter = array())
    {

        $this->db->select("*");

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;
        }

        if(!empty($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
        }


        $query=$this->db->get($table);


        // return result set as an associative array

        return $query->num_rows();



    }

     function insertRow($table,$input){

        if(empty($input)){
            $data['success'] = false;
            $data['data'] = "Data is empty";
            return;
        }

        $input['DCREATED'] = $this->today;
        $input['TCREATED'] = $this->now;
        $input['DMODIFIED'] = $this->today;
        $input['TMODIFIED'] = $this->now;

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

        $this->db->where($field,$param);
        $this->db->select($fields);

        $query=$this->db->get($table);

        // return result set as an associative array

        return $query->result_array();

    }

    function updateRow($table, $input, $param, $value){
        if(empty($input))
            return;

        
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

    function getMemo($table, $fields, $start, $limit, $sort, $dir, $query = array(), $filter = "")
    {
        
        $this->db->select($fields, FALSE);

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;


        }

        if(!empty($filter)){
            if(is_array($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
            }else{
                $this->db->where($filter);
            }
        }

        $this->db->order_by($sort, $dir);
        $this->db->limit($limit, $start);

        $this->db->where("a.employee_id = b.id")->where("a.requested_by = c.id");


        $query = $this->db->get($table." a, tbl_employee_info b, tbl_employee_info c");
        //die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }


     function countMemo($table, $query = array(), $filter = array())
    {

        $this->db->select("*");

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;
        }

        if(!empty($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
        }


        $query=$this->db->get($table);


        // return result set as an associative array

        return $query->num_rows();



    }

    function getSingleMemo($table, $field, $param, $fields){

        $this->db->where($field,$param);
        $fields = array("a.id", "a.employee_id", "CONCAT(b.lastname, ', ', b.firstname) AS employee_name", "a.date_effective",
                "a.reason");
        $this->db->select($fields, FALSE)->where("a.employee_id = b.id");

        $query=$this->db->get($table." a, tbl_employee_info b");

        // return result set as an associative array

        return $query->result_array();

    }

    function getNotification($table, $fields, $start, $limit, $sort, $dir, $query = array(), $filter = array())
    {

        $this->db->select($fields, FALSE);

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;


        }

        if(!empty($filter)){
            if(is_array($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
            }else{
                $this->db->where($filter);
            }
        }

        $this->db->order_by($sort, $dir);
        $this->db->limit($limit, $start);

        $this->db->where("a.requested_by = c.id")->join("tbl_employee_info b", "a.employee_id = b.id", "LEFT");


        $query = $this->db->get($table." a, tbl_employee_info c");
        //die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }


     function countNotification($table, $query = array(), $filter = array())
    {

        $this->db->select("*");

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;
        }

        if(!empty($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
        }


        $query=$this->db->get($table);


        // return result set as an associative array

        return $query->num_rows();



    }

    function getSingleNotification($table, $field, $param, $fields){

        $this->db->where($field,$param);
        
        $this->db->select($fields, FALSE)->join("tbl_employee_info b", "a.employee_id = b.id", "LEFT");

        $query=$this->db->get($table." a");

        // return result set as an associative array

        return $query->result_array();

    }

    function getSuspension($table, $fields, $start, $limit, $sort, $dir, $query = array(), $filter = array())
    {

        $this->db->select($fields, FALSE);

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;


        }

        if(!empty($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
        }

        $this->db->order_by($sort, $dir);
        $this->db->limit($limit, $start);

        $this->db->where("a.requested_by = c.id")->join("tbl_employee_info b", "a.employee_id = b.id", "LEFT");


        $query = $this->db->get($table." a, tbl_employee_info c");
        //die($this->db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
     }


     function countSuspension($table, $query = array(), $filter = array())
    {

        $this->db->select("*");

        if(!empty($query)){
             foreach($query as $key =>$val):
                 $this->db->or_like($key, $val);
             endforeach;
        }

        if(!empty($filter)){
            foreach($filter as $key =>$val):
                 $this->db->where($key, $val);
             endforeach;
        }


        $query=$this->db->get($table);


        // return result set as an associative array

        return $query->num_rows();



    }

    function getSingleSuspension($table, $field, $param, $fields){

        $this->db->where($field,$param);

        $this->db->select($fields, FALSE)->join("tbl_employee_info b", "a.employee_id = b.id", "LEFT");

        $query=$this->db->get($table." a");

        // return result set as an associative array

        return $query->result_array();

    }
}