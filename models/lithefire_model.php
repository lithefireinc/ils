<?php

class Lithefire_model extends CI_Model
{
    private $fr_db;
    private $temp_db;

    private $today;
    private $now;
	
	

    function Lithefire_model()
    {
        parent::__construct();
        $this->today = date("Y-m-d");
        $this->now = date("H:i:s");
		putenv("TZ=ASIA/Manila");

    }

    function getAllRecords($db, $table, $fields, $start, $limit, $sort, $filter, $group, $having = "")
    {
        $this->fr_db = $this->load->database($db, TRUE);

        $this->fr_db->select($fields, FALSE);



        if(!empty($filter)){
                $this->fr_db->where($filter);
        }
		
		if(!empty($having)){
                $this->fr_db->having($having);
        }

        if(!empty($group)){
            if(is_array($group)){
            foreach($group as $key =>$val):
                 $this->fr_db->group_by($key, $val);
             endforeach;
            }else{
                $this->fr_db->group_by($group);
            }
        }

        if(!empty($sort))
        $this->fr_db->order_by($sort);

        if(!empty($limit))
        $this->fr_db->limit($limit, $start);


        $query = $this->fr_db->get($table);
		//die($this->fr_db->last_query());
        if($query->num_rows()>0){

        // return result set as an associative array

        return $query->result_array();

        }
    }

    function countFilteredRows($db, $table, $filter, $group)
    {
        $this->fr_db = $this->load->database($db, TRUE);
        $this->fr_db->select("*", FALSE);

        if(!empty($filter)){
                $this->fr_db->where($filter);
        }

        if(!empty($group)){
            if(is_array($group)){
            foreach($group as $key =>$val):
                 $this->fr_db->group_by($key, $val);
             endforeach;
            }else{
                $this->fr_db->group_by($group);
            }
        }

        $query=$this->fr_db->get($table);

//die($this->fr_db->last_query());
        // return result set as an associative array

        return $query->num_rows();



    }

    function insertRow($db, $table, $input)
    {
        $this->fr_db = $this->load->database($db, TRUE);

        if(empty($input)){
            $data['success'] = false;
            $data['data'] = "Data is empty";
            return;
        }


        if($this->fr_db->insert($table, $input))
        {
            $data['success'] = true;
			$data['id'] = $this->fr_db->insert_id();
            $data['data'] = "Record successfully added";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function updateRow($db, $table, $input, $filter)
    {
        if(empty($input))
            return;
        $this->fr_db = $this->load->database($db, TRUE);

        $this->fr_db->where($filter);
        if($this->fr_db->update($table, $input))
        {

            $data['success'] = true;
            $data['data'] = "Record successfully updated";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }

    function getRecordWhere($db, $table, $filter, $fields){
        $this->db = $this->load->database($db, TRUE);
		if(!empty($filter))
        $this->db->where($filter);
        $this->db->select($fields);

        $query=$this->db->get($table);
//die($this->fr_db->last_query());
        // return result set as an associative array

        return $query->result_array();

    }

    function getFieldWhere($db, $table, $filter, $fields){
        $this->db = $this->load->database($db, TRUE);
        if(!empty($filter))
        $this->db->where($filter);
        $this->db->select($fields);

        $query=$this->db->get($table);

        // return result set as an associative array
        //die($this->db->last_query());
        foreach($query->result_array() as $key => $val)
                return $val[$fields];

    }

    function deleteRow($db, $table, $filter){

        $this->fr_db = $this->load->database($db, TRUE);

        $this->fr_db->where($filter);
        if($this->fr_db->delete($table))
        {
            $data['success'] = true;
            $data['data'] = "Record successfully deleted";
            return $data;
        }
        $data['success'] = false;
        $data['data'] = "There was an error encountered. Please contact your administrator";
        return $data;
    }
	
	function encryptString($string, $key){
		$encrypted = base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, md5($key), $string, MCRYPT_MODE_CBC, md5(md5($key))));
		return $encrypted;
	}
	
	function decryptString($string, $key){
		$decrypted = rtrim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, md5($key), base64_decode($string), MCRYPT_MODE_CBC, md5(md5($key))), "\0");
		return $decrypted;
	}
	
	function getNextCharId($db, $table, $id_field, $length = 5){

       
       $this->fr_db = $this->load->database($db, TRUE);

       $this->fr_db->select("AUTO_INCREMENT", FALSE);
	   $this->fr_db->where(array("table_name"=>$table));
	   $this->fr_db->where("table_schema=DATABASE()");
	   $query=$this->fr_db->get("information_schema.tables");

        // return result set as an associative array
        //die($this->db->last_query());
        foreach($query->result_array() as $key => $val){
        			$id = str_pad($val['AUTO_INCREMENT'], $length, "0", STR_PAD_LEFT);
        			
        			if(strlen($val['AUTO_INCREMENT']) > strlen($id))
                		return $val['AUTO_INCREMENT'];
					else
						return $id;
		}
    }
	
	function getField($field){

			$keyVal = array();

			while (list($key, $val) = each($field)) {

			if($val != "")
				$keyVal[] = "`".$key."`";
			}
			$arrKey	= implode(',',$keyVal);

			return $arrKey;

	}

	function getValue($field){

			$value = array();

			while (list($key, $val) = each($field)) {

			if($val != "")
				$value[] = "\"".addslashes($val)."\"";
			}
			
			$val	= implode(',',$value);

			return $val;

	}
	
	function currentQuery(){
		return $this->fr_db->last_query();
	}
	
	function getObjectWhere($db, $table, $filter, $fields){
        $this->db = $this->load->database($db, TRUE);
		if(!empty($filter))
        $this->db->where($filter);
        $this->db->select($fields);

        $query=$this->db->get($table);
//die($this->fr_db->last_query());
        // return result set as an associative array

        return $query;

    }

}

?>