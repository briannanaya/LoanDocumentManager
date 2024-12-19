
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search Loan Number</title>
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
if(!isset($_POST['submit']))
{
	echo' <div id="page-inner">';
	echo'<h1 class="page-head-line">Select Date Range</h1>';
	echo'<div class="panel-body">';
	echo '<form action="" method="post">';
	echo '<div class="form-group">';
	echo '<label for="loanNum" class="control-label">Date</label>';
	
	echo '<div class="form-group">';
    echo '<label for="startDate" class="control-label">Start Date</label>';
    echo '<input type="date" class="form-control" name="startDate" />';
    echo '</div>';
    
    // End Date Field
    echo '<div class="form-group">';
    echo '<label for="endDate" class="control-label">End Date</label>';
    echo '<input type="date" class="form-control" name="endDate" />';
    echo '</div>';
	
	echo '<hr>';
	echo '<button type="submit" name="submit" value="submit" class="btn btn-lg btn-block btn-success">Search</button>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo'</div>';
	echo'</div>';
}
	
	
if (isset($_POST['submit']))
{
	$startDate = $_POST['startDate'];
	$endDate = $_POST['endDate'];

	echo' <div id="page-inner">';
	echo'<h1 class="page-head-line">Results for Date Range:' .$startDate.' - '.$endDate.'</h1>';
	echo'<div class="panel-body">';
	echo '<table class="table table-hover">';
	echo '<tbody>';
	$sql="Select `loan_number`,`file_type`, `file_date`, `auto_id` from `file_data` where `upload_date` BETWEEN '$startDate' AND '$endDate'"; 
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	while($data=$result->fetch_array(MYSQLI_ASSOC)){
		echo '<tr><td>';
		$file = "Loan Number: ".$data['loan_number']." - File Type: ".$data['file_type']. " - File Date: ".$data['file_date']; 
		echo '<a href="view_doc.php?docid='.$data['auto_id'].'">'.$file.'</a>'; 
		echo '</td></tr>'; 
	}
}
	echo '</tbody>';
	echo '</table>';
	echo '</div>';
	echo'</div>';
	echo'</div>';
?>
</body>
</html>