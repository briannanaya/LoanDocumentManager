<?php
//KEY: camelCase is local variables, name_name is functions
include("functions.php"); 
date_default_timezone_set('America/Chicago');
//make sure were getting a payload if no then try again until we recieve a paload
clear_session($username, $password); // clears any prev status

$cinfo = create_session($username, $password);
if ($cinfo[0] == "Status: OK") {
	//logging session info
	$sid = $cinfo[2];
	$numFiles = 0;
	$totalSize = 0;
	$now = date("Y-m-d H:i:s");
	$time_start = microtime(true);
	
	$payLoadReceieved = false; //checks if there was even a payload to avoid false payloads
	
	while(!$payLoadReceieved){
    	$data = query_files($username, $sid); 
    		if (!empty($data)) {
        		$tmp = explode(":", $data[1]);
        		$payLoad = json_decode($tmp[1]);
		
			if(is_array($payLoad)){
				$payLoadReceieved = true;
				foreach ($payLoad as $value) { 
					$response = request_file($username, $sid, $value);
					if (isset($response['result']) && strstr($response['result'], "Status")) { 
						log_error("Error with file ID $value: " . $response['result']);
						continue;
					} else {
						$content = $response['result'];
						if (strlen($content) == 0) {
							log_error("File $value received with zero length.");
							$error = "File recieved zero length";
							$split = explode("-", $value);
							$loanNumber = $split[0];
        					$fileType = $split[1];
							insertInvalidFileToDatabase([$loanNumber, $fileType, $error]);
							continue;
						}
					write_to_file($value, $content);
					$numFiles++;
					$totalSize += strlen($content);
				}
        	}
    	} else{
				log_error("Payload format is incorrect or empty.");
			}
		}else{
			log_error("Failed to receive a valid payload.");
			}
		}
		$time_end = microtime(true);
		$execTime = ($time_end-$time_start)/60;
    	close_session($sid);
    	echo "\r\nSession closed. Done\r\n";
	
		log_session_info($sid,$now,$numFiles,$totalSize,$execTime);
} else {
		log_error("Failed to create session: " . print_r($cinfo, true));
	}
?>
