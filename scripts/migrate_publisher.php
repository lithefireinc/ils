<?php
include("pdo_ini.php");
$pdo = new PDO(FRDSN, USER, PASS);
$row = 0;

$filename = 'csv/FILEPUBL.csv';


if(!file_exists($filename)){
	$data['success'] = false;
	$data['data'] = "File does not exist";
	die(json_encode($data));
}
//$pdo->exec("DELETE FROM PELIBIEA WHERE D_START ='$d_start' AND D_END = '$d_end'");

$insert = array();
$date = date("Y-m-d");
$time = date("H:i:s");
if (($handle = fopen($filename, "r")) !== FALSE) {
	
	$start=microtime(true);
    $sql ="INSERT INTO FILEPUBL (PUBLIDNO, DESCRIPTION, ACRONYM, ADDR_01, ADDR_02, PLACE, COUNIDNO, CONTPERSON, CONTPHONE, DCREATED, TCREATED) VALUES (?,?,?,?,?,?,?,?,?,?,?)";
	//$sql = "insert into PELIBIPA_DARRYL values(?,?,?,?,?)"
	$stmt = $pdo->prepare($sql);
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
	
       if($row != '0'){
		$insert = array($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8], $date, $time);
		 
       
       	$stmt->execute($insert);
	   
		   
	   }
	   
	   
       $row++;
       
       
    }
    fclose($handle);

	$pdo = null;
	
die("Import Successful");
       
}else{
	
	die(json_encode($data));
}
