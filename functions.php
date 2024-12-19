<?php
$dblink=db_connect("");
$username = "";
$password = "";
$now = date("Y-m-d H:i:s");

//connect to $db as webuser
function db_connect($db){
	$hostname="";
	$username="";
	$password="";
	$dblink=new mysqli($hostname,$username,$password,$db);
	if(mysqli_connect_error())
		die("Error connecting to the database: ".mysqli_connect_error());
	return $dblink;
}

function log_error($message) {
	date_default_timezone_set('America/Chicago');
	$now = date("Y-m-d H:i:s");
    $logMessage = "[$now] $message\n";
    file_put_contents('/var/www/html/error_log.txt', $logMessage, FILE_APPEND);
}

// executes curl request from api
function make_curl_request($url, $data) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'content-type: application/x-www-form-urlencoded',
        'content-length: ' . strlen($data)
    ));
    $time_start = microtime(true);
    $result = curl_exec($ch);
    $time_end = microtime(true);
    $execTime = ($time_end - $time_start) / 60;
    curl_close($ch);

    return ['result' => $result, 'execTime' => $execTime]; 
}

// create session call
function create_session($username, $password) {
    $data = "username=$username&password=$password";
    $response = make_curl_request('https://cs4743.professorvaladez.com/api/create_session', $data);
    return json_decode($response['result'], true);
}

// clear session call
function clear_session($username, $password) {
    $data = "username=$username&password=$password";
    $response = make_curl_request('https://cs4743.professorvaladez.com/api/clear_session', $data);
    return json_decode($response['result'], true);
}

// query files call
function query_files($username, $sid) {
    $data = "uid=$username&sid=$sid";
    $response = make_curl_request('https://cs4743.professorvaladez.com/api/query_files', $data);
    return json_decode($response['result'], true);
}

// request file call
function request_file($username, $sid, $file_id) {
    $data = "sid=$sid&uid=$username&fid=$file_id";
    return make_curl_request('https://cs4743.professorvaladez.com/api/request_file', $data);
}

// close session call
function close_session($sid) {
    $data = "sid=$sid";
    make_curl_request('https://cs4743.professorvaladez.com/api/close_session', $data);
}

function request_documents($username, $sid){
	$data = "uid=$username&sid=$sid";
    return make_curl_request('https://cs4743.professorvaladez.com/api/request_all_documents', $data);
    return json_decode($response['result'], true);
}

// Function to write content to file under files dir
function write_to_file($file_name, $content) {
	date_default_timezone_set('America/Chicago');
	$fileSize = strlen($content);
	$upload_type = 'cron';
	$file_name = addslashes($file_name);
	// Extract the date and time from the file name (supports document types like 'Financial_1' or 'Financial')
    preg_match('/(\d{8}_\d{2}_\d{2}_\d{2})/', $file_name, $matches);

    // If a match is found, use that as the file date; otherwise, use the current timestamp
    $file_date = isset($matches[1]) ? $matches[1] : date("Y-m-d H:i:s");

	$file_data = implode(" | ", [
		$file_name,
		$fileSize,
		$file_date,
		$upload_type 
	]);
	
	try {
        $fp = "/var/www/html/audit/file/$file_name";
		$fp = fopen($fp, "wb");
        fwrite($fp, $content);
        fclose($fp);
        echo "\r\nFile $file_name written to filesystem\r\n"; 
		
		//log file metadata to its own file (this is where we will parse data from)
		$fp = "/var/www/html/audit/file_to_db_log.txt";
		$fp = fopen($fp, "ab");
		fwrite($fp,$file_data . PHP_EOL);
		fclose($fp);
		echo "\r\nLog entry written successfully.\n";
		
    } catch (Exception $e) {
        log_error("Failed to write file $file_name: " . $e->getMessage());
    }
}


function checkMimeType($file_path) {
    $mimeType = mime_content_type($file_path);
    // Check if mime type is PDF
    if ($mimeType === 'application/pdf') {
        return true;
    } else {
        log_error("Invalid MIME type for file $file_path. Expected application/pdf, got $mimeType.");
        return false;
    }
}


function insertFileDataToDatabase($fileData) {
    // Connect to db documents
    $dblink = db_connect("documents");
    if (!$dblink) {
        log_error("Database connection failed.");
        return; // Exit if connection fails
    }

    // Extract data from $fileData
    $loanNumber = $fileData[0];
    $fileSize = (string) $fileData[1];
    $uploadDate = trim($fileData[2]);
    $contentClean = addslashes($fileData[3]); 
    $uploadType = $fileData[4];
	$fileDate = $fileData[5];
	$fileType = $fileData[6];
	$loanNumId = $fileData[7];

    $sqlFileData = "INSERT INTO `file_data` (`loan_number`, `file_size`, `upload_date`, `upload_type`, `file_type`, `file_date`, `loan_number_id`) 
                    VALUES ('$loanNumber', '$fileSize', '$uploadDate', '$uploadType', '$fileType', '$fileDate', '$loanNumId')";
    if (!$dblink->query($sqlFileData)) {
        log_error("Failed to insert file data: " . $dblink->error);
        return;
    }
	
    $fileDataId = $dblink->insert_id; //gets id from new data inserted

    // put in file content to (file_content) and points to row in file_data
    $sqlFileContent = "INSERT INTO `file_content` (`file_data_id`, `file_content`) 
                       VALUES ('$fileDataId', '$contentClean')";
    if (!$dblink->query($sqlFileContent)) {
        log_error("Failed to insert file content: " . $dblink->error);
    } else {
        echo "\r\nFile data for $loanNumber successfully inserted into database.\n";
    }

    // Close db connection
    $dblink->close();
}


// Logs session info to the database
function log_session_info($sid, $call_date, $num_files, $total_size, $exec_time) {
    // Connect to the database
    $dblink = db_connect("sessionsDB");
    if (!$dblink) {
        log_error("Database connection failed for logging session info.");
        return;
    }

    $sql = "INSERT INTO `sessions` (`user_sid`, `call_date`, `num_files`, `size_files`, `exec_time`) 
            VALUES ('$sid', '$call_date', '$num_files', '$total_size', '$exec_time')";
    
    if (!$dblink->query($sql)) {
        log_error("Failed to insert session info: " . $dblink->error);
    } else {
        echo "\nSession info successfully logged for SID $sid.\n";
    }

    // Close the database connection
    $dblink->close();
}
function redirect($uri){
	?>
	<script type="text/javascript">
	<!--
	document.location.href="<?php echo $uri; ?>";
	-->
	</script>
	<?php die;
}

function load_error_to_db(){
	$dblink = db_connect("errors");
	$errorLogPath = "/var/www/html/error_log.txt";
	if (file_exists($errorLogPath)) {
        
		$errorLogEntries = file($errorLogPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($errorLogEntries as $entry) {
            // take date out of [] and get error message after ] and space
            if (preg_match('/\[(.*?)\] (.*)/', $entry, $result)) {
                $date = $result[1];   
                $errorMessage = $result[2];

                $sql = "INSERT INTO `error_log` (`date`, `error`) VALUES ('$date', '$errorMessage')";
                   if (!$dblink->query($sql)) {
        				log_error("Failed to insert error_log info " . $dblink->error);
    			} else {
        			echo "\nSuccessfully inserted error log info.\n";
    			}
            }
        }
	file_put_contents($errorLogPath, '');
    } 
	else {
        echo "Error log file not found.\n";
    }
	$dblink->close();
}

function insertInvalidFileToDatabase($fileData){
	$dblink = db_connect("errors");
    if (!$dblink) {
        log_error("Database connection failed.");
        return; // Exit if connection fails
    }
    // Extract data from $fileData
    $loanNumber = $fileData[0];
	$fileType = $fileData[1];
	$error = $fileData[2];

    $sql = "INSERT INTO `invalid_file_data` (`loan_number`, `loan_type`, `error`) 
                    VALUES ('$loanNumber', '$fileType', '$error')";
    if (!$dblink->query($sql)) {
        log_error("Failed to insert file data: " . $dblink->error);
        return;
    }
	$dblink->close();
}

function checkAndInsertLoanNum($loanNumber) {
    $dblink = db_connect("documents");

    // Check if loan number exists in loan_numbers table
    $sql = "SELECT auto_id FROM loan_numbers WHERE loan_number = '$loanNumber'";
    $result = $dblink->query($sql);

    if ($result && $result->num_rows > 0) { //loan num exists
        $dblink->close();
        return "exists";  //sends to redirect to existing upload page
    } else {
        // Insert new loan number & get the auto id 
        $sql = "INSERT INTO loan_numbers (loan_number) VALUES ('$loanNumber')";
        if ($dblink->query($sql)) {
            $loanNumId = $dblink->insert_id;  // gets fk
        } else {
            log_error("Failed to insert loan: $loanNumber" . $dblink->error);
            $dblink->close();
            return null;
        }
    }
    $dblink->close();
    return $loanNumId;
}

function getLoanNumber($autoID){
	$dblink = db_connect("documents");
	$sql = "SELECT loan_number FROM loan_numbers WHERE auto_id = '$autoID'";
	$result = $dblink->query($sql) or
		die("Something went wrong with $sql".$dblink->error);
		$dblink->close();
	if ($result) {
    while ($data = $result->fetch_array(MYSQLI_ASSOC)) {
        return ($data['loan_number']); 
    }
}
	$dblink->close();
}

//global average size of all documents
function getGlobalSize(){
	$dblink = db_connect("documents");
	$sql = "SELECT AVG(file_size) AS avg_size FROM file_data";
    $result = $dblink->query($sql) or
		die("Something went wrong with $sql".$dblink->error);
    return $result->fetch_assoc()['avg_size'];
}

//get global avg num of docs across loans
function getGlobalDocNum(){
	$dblink = db_connect("documents");
	$sql = "SELECT AVG(doc_count) AS avg_docs_per_loan
FROM (
    SELECT loan_number, COUNT(*) AS doc_count
    FROM file_data
    GROUP BY loan_number
) AS loan_docs";
$result = $dblink->query($sql) or
	die("Something went wrong with $sql".$dblink->error);
return $result->fetch_assoc()['avg_docs_per_loan'];

}


?>
