<?php
include("pdo_ini.php");
$pdo = new PDO(ILSDSN, USER, PASS);
$row = 0;

$filename = 'csv/AUTHORS.csv';


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
    $sql ="INSERT INTO AUTHORS (AUTHIDNO, AUTHOR, DCREATED, TCREATED) VALUES (?,?,?,?)";
	
	$sSQL = "INSERT INTO BOOKAUTHOR (ACCESSNO, AUTHIDNO, DCREATED, TCREATED) VALUES (?,?,?,?)";
	
	$sSQL3 = "SELECT AUTHIDNO FROM AUTHORS WHERE AUTHOR = ?";
	//$sql = "insert into PELIBIPA_DARRYL values(?,?,?,?,?)"
	$stmt = $pdo->prepare($sql);
	$stmt2 = $pdo->prepare($sSQL);
	$stmt3 = $pdo->prepare($sSQL3);
	
    while (($data = fgetcsv($handle, 1000, ",")) !== FALSE) {
        
	
       if($row != '0'){
       	
		 foreach($pdo->query("SELECT LPAD(MAX(SUBSTR(AUTHIDNO, 2))+1, 10, '0') AS IDCHAR FROM AUTHORS") as $row):
           if($row['IDCHAR'] === null)
            $AUTHIDNO = '0000000001';
           else
            $AUTHIDNO = $row['IDCHAR'];
         endforeach;
		$insert = array($AUTHIDNO, $data[1], $date, $time);
		 
        $stmt3->execute(array($data[1]));
		$stmt3->setFetchMode(PDO::FETCH_ASSOC);
		$author = $stmt3->fetch();
		
		if(empty($author))
       	$stmt->execute($insert);
		else
	    $AUTHIDNO = $author['AUTHIDNO'];
	    
	    $stmt2->execute(array($data[0], $AUTHIDNO, $date, $time));
		   
	   }
	   
	   
       $row++;
       
       
    }
    fclose($handle);

	$pdo = null;
	
die("Import Successful");
       
}else{
	
	die(json_encode($data));
}
