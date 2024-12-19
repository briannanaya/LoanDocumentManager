<?php
include("functions.php"); 

// Path to the file log
$fileLogPath = "/var/www/html/logs/file_log.txt";

if (file_exists($fileLogPath)) {
    $fileLogEntries = file($fileLogPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

    foreach ($fileLogEntries as $entry) {
        $fileData = explode(" | ", $entry);
        $fileName = $fileData[0];
        $fileSize = $fileData[1];
        $uploadDate = $fileData[2];
        $uploadType = $fileData[3];
		
		$loanNumber = strtok($fileName, '-');
        $fileType = strtok('-');
        $fileDate = substr(strrchr($fileName, '-'), 1);
        // file path to get content
        $filePath = "/var/www/html/files/$fileName";

        // Check if file exists and has pdf MIME type
        if (file_exists($filePath) && checkMimeType($filePath)) {
            // get file content
            $content = file_get_contents($filePath);

            if ($content === false) {
                log_error("Failed to read content from file $filePath.");
                continue;
            }
			// insert loanNumber into loan_numbers if unique
			$sql = "INSERT IGNORE INTO loan_numbers (loan_number) VALUES ('$loanNumber')";
			if (!$dblink->query($sql)) {
        		log_error("Failed to insert to loan_number: " . $dblink->error);
			} else {
        		echo "\nLoan number successfully inserted.\n";
    		}
			
			//get loan number auto id to insert as a fk into file_data
			$sql = "SELECT auto_id FROM loan_numbers WHERE loan_number = '$loanNumber'";
			$result = $dblink->query($sql);
            $loanNumberId = $result->fetch_row()[0];
			if (!$loanNumberId) {
                log_error("Loan retrieval error.");
                continue;
            }
			
            //insert file data to db (file_data in documents)
            insertFileDataToDatabase([$loanNumber, $fileSize, $uploadDate, $content, $uploadType, $fileDate, $fileType, $loanNumberId]);
            echo "Inserted file data for $fileName into database.\n";
        } else {
            log_error("Skipped insertion for file $fileName due to MIME type mismatch or missing file.");
			$error = "MIME type mismatch or missing file";
			insertInvalidFileToDatabase([$loanNumber, $fileType, $error]);
        }
    }
} else {
    log_error("File log $fileLogPath not found.");
}
//clears everything in file_log
file_put_contents($fileLogPath, '');
?>