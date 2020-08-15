<?php

// id info for DB
define('DB_HOST', 'localhost');
define('DB_USER', 'kazuki');
define('DB_PASS', 'apgangan');
define('DB_NAME', 'board');

// initialize vars
$csv_data = null;
$sql = null;
$res = null;
$message_array = array();

session_start();

if(!empty($_SESSION['admin_login']) && $_SESSION['admin_login'] === true){
    // make csv file and output it.
    header("Content-Type: application/octet-stream");
    header("Content-Disposition: attachment; filename=message.csv");
    header("Content-Transfer-Encoding: binary");

    // connect DB
    $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // check connection error
    if(!$mysqli->connect_errno){
        $sql = "SELECT * FROM message ORDER BY post_date ASC";
        $res = $mysqli->query($sql);

        if($res){
            $message_array = $res->fetch_all(MYSQLI_ASSOC);
        }
        $mysqli->close();
    }

    // create csv file
    if(!empty($message_array)){
        // first line of csv
        $csv_data .= '"ID", "name", "message", "date"'."\n";

        foreach($message_array as $value){
            $csv_data .= '"' . $value['id'] . '","' . $value['view_name'] 
                . '","' . $value['message'] . '","' . $value['post_date']
                . "\"\n";
        }
        // output file
        echo $csv_data;
        
    }

} else {
    // redirect to login page
    header("Location: ./admin.php");

}

return;


