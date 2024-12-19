
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search Document Type</title>
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
if(!isset($_POST['filter']) && !isset($_POST['search']))
{
	echo' <div id="page-inner">';
	echo'<h1 class="page-head-line">Select the document Type</h1>';
	echo'<div class="panel-body">';
	echo '<form action="" method="post">';
	echo '<div class="form-group">';
	echo '<label for="docType" class="control-label">Document Type</label>';
	echo '<select class="form-control" name="docType">';
	$sql="Select * from `doc_types`";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql <br>".$dblink->error);
	while($data=$result->fetch_array(MYSQLI_ASSOC))
	{
		echo '<option value="'.$data['auto_id'].'">'.$data['name'].'</option>';
	}
	echo '</select>';
	
	//option to filter by loan number
	echo '<label for="loanNum" class="control-label">Option to Filter by Loan Number</label>';
	echo '<select class="form-control" name="loanNum">';
	$sql="Select * from `loan_numbers`";
	$result=$dblink->query($sql) or
		die("Something went wrong with $sql <br>".$dblink->error);
	while($data=$result->fetch_array(MYSQLI_ASSOC))
	{
		echo '<option value="'.$data['auto_id'].'">'.$data['loan_number'].'</option>';
	}
	echo '</select>';
	
	echo '<hr>';
	echo '<button type="submit" name="filter" value="filter" class="btn btn-lg btn-block btn-success">Search and Filter with Loan Number</button>';
	echo '<button type="submit" name="search" value="search" class="btn btn-lg btn-block btn-success">Search without Loan Number</button>';
	echo '</form>';
	echo '</div>';
	echo '</div>';
	echo'</div>';
	echo'</div>';
}
	
		
if (isset($_POST['search']))
{
	$docTypeID=$_POST['docType'];
	$sql="Select `name` from `doc_types` where `auto_id` = '$docTypeID'";
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	$tmp=$result->fetch_array(MYSQLI_ASSOC);
	$docType=$tmp['name'];
	$docTypeSql=str_replace(" ","_",$docType);
	echo' <div id="page-inner">';
	echo'<h1 class="page-head-line">Results for document type:'.$docType.'</h1>';
	echo'<div class="panel-body">';
	echo '<table class="table table-hover">';
	echo '<tbody>';
	$sql="Select `loan_number`,`file_type`, `file_date`,`auto_id` from `file_data` where `file_type` like '%$docTypeSql%'"; 
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	while($data=$result->fetch_array(MYSQLI_ASSOC)){
		echo '<tr><td>';
		$file = "Loan Number: ".$data['loan_number']." - File Type: ".$data['file_type']. " - File Date: ".$data['file_date']; 
		echo '<a href="view_doc.php?docid='.$data['auto_id'].'">'.$file.'</a>'; 
		echo '</td></tr>'; 
	}
}
	
if (isset($_POST['filter']))
{
	$docTypeID=$_POST['docType'];
	$loanID=$_POST['loanNum'];
	$sql="Select `name` from `doc_types` where `auto_id` = '$docTypeID'";
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	$tmp=$result->fetch_array(MYSQLI_ASSOC);
	$docType=$tmp['name'];
	$docTypeSql=str_replace(" ","_",$docType);
	
	$sql="Select `loan_number` from `loan_numbers` where `auto_id` = '$loanID'";
	$result=$dblink->query($sql) or
		log_error("Something went wrong with $sql".$dblink->error);
	$tmp=$result->fetch_array(MYSQLI_ASSOC);
	$loanNum=$tmp['loan_number'];

	echo' <div id="page-inner">';
	echo'<h1 class="page-head-line">Results for document type:'.$docType.'</h1>';
	echo'<div class="panel-body">';
	echo '<table class="table table-hover">';
	echo '<tbody>';
	$sql="Select `loan_number`,`file_type`, `file_date`, `auto_id` from `file_data` where `file_type` like '%$docTypeSql%' AND `loan_number` = '$loanNum'"; 
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
    