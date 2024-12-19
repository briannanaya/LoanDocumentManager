
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Upload New</title>
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
echo '<div id="page-inner">';
echo '<h1 class="page-head-line">Upload a New File to Database</h1>';
echo '<div class="panel-body">';
if (isset($_GET['error']) || isset($_GET['success']) )
{
	if($_GET['error']=="FileMimeInvalid")
		echo '<div class="alert alert-danger" role="alert">You must upload a PDF only!</div>';
	elseif ($_GET['error']=="loanNumNull")
		echo '<div class="alert alert-danger" role="alert">Loan Number cannot be blank!</div>';
	elseif ($_GET['error']=="loanNumInvalid")
		echo '<div class="alert alert-danger" role="alert">Loan Number must be 5-9 characters!</div>';
	elseif ($_GET['success']=="FileUploaded")
		echo '<div class="alert alert-success" role="alert">Success, file was uploaded!</div>';
}
echo '<form method="post" enctype="multipart/form-data" action="">';
echo '<input type="hidden" name="MAX_FILE_SIZE" value="10000000">';
echo '<div class="form-group">';
echo '<label for="loanNum" class="control-label">Loan Number</label>';
echo '<input type="text" name="loanNum" class="form-control">';
echo '</div>';
	
echo '<div class="form-group">';
echo '<label for="docType" class="control-label">Document Type</label>';
echo '<select class="form-control" name="docType">';
$dblink=db_connect("documents");
$sql="Select * from `doc_types`";
$result=$dblink->query($sql) or
	die("Something went wrong with $sql <br>".$dblink->error);
while($data=$result->fetch_array(MYSQLI_ASSOC))
{
	echo '<option value="'.$data['auto_id'].'">'.$data['name'].'</option>';
}
echo '</select>';
echo '</div>';
echo '<div class="form-group">';
echo '<label class="control-label col-lg-4">File Upload</label>';
echo '<div class="">';
echo '<div class="fileupload fileupload-new" data-provides="fileupload">';
echo '<div class="fileupload-preview thumbnail" style="width: 200px; height: 150px;"></div>';
echo '<div class="row">';
echo '<div class="col-md-2">';
echo '<span class="btn btn-file btn-primary">';
echo '<span class="fileupload-new">Select File</span>';
echo '<span class="fileupload-exists">Change</span>';
echo '<input name="userfile" type="file">';
echo '</span>';
echo '</div>';
echo '<div class="col-md-2">';
echo '<a href="#" class="btn btn-danger fileupload-exists" data-dismiss="fileupload">Remove</a>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '</div>';
echo '<hr>';
echo '<button type="submit" name="submit" value="submit" class="btn btn-lg btn-block btn-success">Upload File</button>';
echo '</form>';
echo '</div>';
echo '</div>';
echo '</div>';
if(isset($_POST['submit']) && $_POST['submit']=="submit")
{
	$fileMime=$_FILES['userfile']['type'];
	if($fileMime!="application/pdf")
	{
		redirect("upload_new.php?error=FileMimeInvalid");
	}
	else{
		$now=date("Y-m-d H:i:s");
		$loanNumber=$_POST['loanNum'];
		if($loanNumber == NULL){
			redirect("upload_new.php?error=loanNumNull");
		}
		elseif (!preg_match('/^[0-9]{1,9}$/',$loanNumber)){ 
			redirect("upload_new.php?error=loanNumInvalid");
		}
		//check if it is a existing loan number(parse from data in db) if yes redirect to erroreisting loan number
		$loanNumId = checkAndInsertLoanNum($loanNumber); 
    	if ($loanNumId === "Exists") { 
        	redirect("upload_existing.php?error=existingLoanNum");
			exit();
    	}
		
		$docId=$_POST['docType'];
		switch($docId) {
    case 1:
        $docType = "Tax_Returns";
        break;
    case 2:
        $docType = "MOU";
        break;
    case 3:
        $docType = "Credit";
        break;
    case 4:
        $docType = "Financial";
        break;
    case 5:
        $docType = "Other";
        break;
    case 6:
        $docType = "Closing";
        break;
    case 7:
        $docType = "Personal";
        break;
    case 8:
        $docType = "Legal";
        break;
    case 9:
        $docType = "Disclosures";
        break;
    case 10:
        $docType = "Internal";
        break;
    default:
        $docType = "Other";
        break;
}

		$fileUpload = $_FILES['userfile']['name'];
		$fileContents=$_FILES['userfile']['tmp_name'];
		$fp=fopen($fileContents,"r");
		$contents=fread($fp,filesize($fileContents));
		fclose($fp);
		$contentsClean=addslashes($contents);
		$fileSize=strlen($contentsClean);
		insertFileDataToDatabase([$loanNumber, $fileSize, $now, $contentsClean, 'manual', $now, $docType, $loanNumId]);
		redirect("upload_new.php?success=FileUploaded");

	}
}

?>
</body>
</html>