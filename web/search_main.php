
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>Search Main</title>
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
echo' <div id="page-inner">';
echo'<h1 class="page-head-line">Select the search criteria</h1>';
echo'<div class="panel-body">';
echo'<p><a class="btn btn-primary" href="search_loanID.php">By Loan ID</a></p>';
echo'<p><a class="btn btn-primary" href="search_docType.php">By Document Type</a></p>';
echo'<p><a class="btn btn-primary" href="search_date.php">By Date</a></p>';
echo'<p><a class="btn btn-primary" href="search_all.php">View All Files</a></p>';
echo'</div>';
echo'</div>';
?>
</body>
</html>
    