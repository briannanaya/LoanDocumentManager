
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>View Document</title>
<!-- BOOTSTRAP STYLES-->
<link href="assets/assets/css/bootstrap.css" rel="stylesheet" />
<!-- FONTAWESOME STYLES-->
<link href="assets/assets/css/font-awesome.css" rel="stylesheet" />
   <!--CUSTOM BASIC STYLES-->
<link href="assets/assets/css/basic.css" rel="stylesheet" />
<!--CUSTOM MAIN STYLES-->
<link href="assets/assets/css/custom.css" rel="stylesheet" />
<!-- PAGE LEVEL STYLES -->
<link href="assets/assets/css/bootstrap-fileupload.min.css" rel="stylesheet" />
<!-- JQUERY SCRIPTS -->
<script src="assets/assets/js/jquery-1.10.2.js"></script>
<!-- BOOTSTRAP SCRIPTS -->
<script src="assets/assets/js/bootstrap.js"></script>
<script src="assets/assets/js/bootstrap-fileupload.js"></script>
</head>
<body>
	
<?php
	session_start();
	echo' <div id="page-inner">';
	echo'<h1 class="page-head-line">View Document</h1>';
	echo'<div class="panel-body">';
	include("../functions.php");
	$dblink=db_connect("documents");
	
	if (!isset($_GET['docid']) || !is_numeric($_GET['docid'])) {
    echo '<p>Invalid document ID.</p>';
    exit;
	}
	
	$docId=$_GET['docid'];
	
	date_default_timezone_set('America/Chicago');
	$now = date("Y-m-d H:i:s");
	
	$sql="Select * from `file_data` where `auto_id`='$docId'";
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	$data=$result->fetch_array(MYSQLI_ASSOC);
	
	if (!$data) {
    echo '<p>Document not found.</p>';
    exit;
	}
	//reconstruct name
	$loanNum = $data['loan_number'];
	$fileType = $data['file_type'];
	$fileDate = $data['file_date'];
	$docName = "{$loanNum}-{$fileType}-{$fileDate}.pdf";
	
	echo '<h3>Document Name:'.$docName.'</h3>'; 
	echo '<h3>Loan ID:'.$loanNum.'</h3>';
	echo '<h3>File Size:'.$data['file_size'].'</h3>';
	echo '<h3>Document Type:'.$fileType.'</h3>';
	
	if ($data['last_access'] == NULL){
		$sql = "UPDATE `file_data` SET `last_access` = '$now' WHERE `auto_id` = '$docId'";
		$result=$dblink->query($sql) or
			log_error("Something went wrong with $sql".$dblink->error);
		echo '<h3>Last Access:'.$now.'</h3>';
	}
	else{
		echo '<h3>Last Access:'.$data['last_access'].'</h3>';
		$sql = "UPDATE `file_data` SET `last_access` = '$now' WHERE `auto_id` = '$docId'";
		if (!$dblink->query($sql)) {
        log_error("Failed to insert last access data: " . $sql->error);
        return;
    	}
	}
	
	
	//take content blob from db back to file system to view by client
	$sql = "SELECT `file_content` FROM  `file_content` WHERE `file_data_id` = '$docId'";
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	$data=$result->fetch_array(MYSQLI_ASSOC);
	$content=$data['file_content']; 
	$filePath = "/var/www/html/view/$docName";	
	//make sure content isnt empty else error
	if (empty($content)) {
    echo '<p>Document content is unavailable. Please try again</p>';
    exit;
	}
	$fp=fopen($filePath,"wb"); 
	if (!$fp) {
    echo '<p>Failed to write file.</p>';
    exit;
	}
	fwrite($fp,$content);
	fclose($fp);
	
	echo '<h3><a href="/view/'.$docName.'" target="_blank">View Document</a></h3>';
	echo '</div>';
	echo '</div>';
	
?>
	
</body>
</html>