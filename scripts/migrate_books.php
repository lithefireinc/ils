<?php
include("pdo_ini.php");
$pdo = new PDO(ILSDSN, USER, PASS);
$row = 0;

$filename = 'csv/BOOKS.csv';


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
    $sql ="INSERT INTO BOOKS (
             `ACCESSNO`,
             `CALLNO`,
             `TITLE`,
             `LOCAIDNO`,
             `EDITION`,
             `VOLUME`,
             `ISBN`,
             `PUBLIDNO`,
             `PUBLISHER`,
             `PLACE`,
             `COUNIDNO`,
             `COPYRIGHT`,
             `PAGES`,
             `COPIES`,
             `PURCDATE`,
             `AMOUNT`,
             `PHYSDESC`,
             `BOTYIDNO`,
             `CLASIDNO`,
             `DDC`,
             `DDCDECI`,
             `CATEIDNO`,
             `BORROWED`,
             `DUEDATE`,
             `SELECTED`, DCREATED, TCREATED) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)";
	//$sql = "insert into PELIBIPA_DARRYL values(?,?,?,?,?)"
	$stmt = $pdo->prepare($sql);
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
	
       if($row != '0'){
       	if($data[24])
       		$selected = 1;
		else 
			$selected = 0;
		
		if($data[22])
       		$borrowed = 1;
		else 
			$borrowed = 0;
		$insert = array($data[0], $data[1], $data[2], $data[3], $data[4], $data[5], $data[6], $data[7], $data[8],
		 $data[9],  $data[10], $data[11], $data[12], $data[13], date('Y-m-d', strtotime($data[14])), $data[15], $data[16],
		  $data[17], $data[18], $data[19], $data[20], $data[21], $borrowed, date('Y-m-d', strtotime($data[23])), $selected, $date, $time);
		 
       
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
