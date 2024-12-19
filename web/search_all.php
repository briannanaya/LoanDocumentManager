
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search All Files</title>
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
include("../functions.php");
$dblink=db_connect("documents");
	echo' <div id="page-inner">';
	echo'<h1 class="page-head-line">Results for All Files:'.$loanNum.'</h1>';
	echo'<div class="panel-body">';
	echo '<table class="table table-hover">';
	echo '<tbody>';
	$sql="Select `loan_number`,`file_type`, `file_date`, `auto_id` from `file_data`"; 
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	while($data=$result->fetch_array(MYSQLI_ASSOC)){
		echo '<tr><td>';
		$file = "Loan Number: ".$data['loan_number']." - File Type: ".$data['file_type']. " - File Date: ".$data['file_date']; 
		echo '<a href="view_doc.php?docid='.$data['auto_id'].'">'.$file.'</a>'; 
		echo '</td></tr>'; 
	}

	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo'</div>';
	echo'</div>';
?>
</body>
</html>