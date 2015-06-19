<?php
include("pdo_ini.php");
$pdo = new PDO(ILSDSN, USER, PASS);
$row = 0;

$filename = 'csv/SUBJECTS.csv';


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
    $sql ="INSERT INTO SUBJECTS (BOSUIDNO, SUBJECT, DCREATED, TCREATED) VALUES (?,?,?,?)";
	
	$sSQL = "INSERT INTO BOOKSUBJECT (ACCESSNO, BOSUIDNO, DCREATED, TCREATED) VALUES (?,?,?,?)";
	
	$sSQL3 = "SELECT BOSUIDNO FROM SUBJECTS WHERE SUBJECT = ?";
	//$sql = "insert into PELIBIPA_DARRYL values(?,?,?,?,?)"
	$stmt = $pdo->prepare($sql);
	$stmt2 = $pdo->prepare($sSQL);
	$stmt3 = $pdo->prepare($sSQL3);
	
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
	
       if($row != '0'){
       	
		 foreach($pdo->query("SELECT LPAD(MAX(SUBSTR(BOSUIDNO, 2))+1, 10, '0') AS IDCHAR FROM SUBJECTS") as $row):
           if($row['IDCHAR'] === null)
            $BOSUIDNO = '0000000001';
           else
            $BOSUIDNO = $row['IDCHAR'];
         endforeach;
		$insert = array($BOSUIDNO, $data[1], $date, $time);
		 
        $stmt3->execute(array($data[1]));
		$stmt3->setFetchMode(PDO::FETCH_ASSOC);
		$subject = $stmt3->fetch();
		
		if(empty($subject))
       	$stmt->execute($insert);
		else
	    $BOSUIDNO = $subject['BOSUIDNO'];
	    
	    $stmt2->execute(array($data[0], $BOSUIDNO, $date, $time));
		   
	   }
	   
	   
       $row++;
       
       
    }
    fclose($handle);

	$pdo = null;
	
die("Import Successful");
       
}else{
	
	die(json_encode($data));
}
