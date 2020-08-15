<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'kazuki');
define('DB_PASS', 'apgangan');
define('DB_NAME', 'board');

date_default_timezone_set('Asia/Tokyo');

$now_date = null;
$data = null;
$split_data =null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();
$message_id = null;
$mysqli = null;
$sql = null;
$res = null;
$error_message = array();
$message_data = array();

session_start();

// login as admin?
if(empty($_SESSION['admin_login']) || $_SESSION['admin_login'] !== true){
    // redirect to login page
    header("Location: ./admin.php");
}
if(!empty($_GET['message_id']) && empty($_POST['message_id'])){
    // get post by id
    $message_id = (int)htmlspecialchars($_GET['message_id'],
        ENT_QUOTES);

    // connect DB
    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    // check connect error
    if($mysqli->connect_errno){
        $error_message[] = "failed to connect DB. errno: ".
            $mysqli->connect_errno.' : '.$mysqli->connect_errno;
    } else {
        // get data
        $sql = "SELECT * FROM message WHERE id = $message_id";
        $res = $mysqli->query($sql);

        if($res){
            $message_data = $res->fetch_assoc();
        } else {
            // no data. return to admin page
            header("Location: ./admin.php");
        }
    } 
    $mysqli->close();
} else if(!empty($_POST['message_id'])){

    $message_id = (int)htmlspecialchars($_POST['message_id'],
        ENT_QUOTES);

    if(empty($_POST['view_name'])){
        $error_message[] = "enter name";
    } else {
        $message_data['view_name'] =
            htmlspecialchars($_POST['view_name'], ENT_QUOTES);
    }

    if(empty($_POST['message'])){
        $error_message[] = 'enter message';
    } else {
        $message_data['message'] = htmlspecialchars($_POST['message'],
            ENT_QUOTES);
    }
    if(empty($error_message)){
        $mysqli = new mysqli( DB_HOST, DB_USER, DB_PASS, DB_NAME); 

        if($mysqli->connect_errno){
            $eror_message[] = 'failed to connect DB. errno ' .
                $mysqli->connect_errno . ' : ' . $mysqli->connect_error;
        } else {
            $sql = "UPDATE message set view_name =
            '$message_data[view_name]', message= '$message_data[message]'
            WHERE id = $message_id";
            $res = $mysqli->query($sql);       
        }
        $mysqli->close();

        if($res){
            header("Location: ./admin.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="utf-8">
<title>simple message board (editing)</title>
<link rel="stylesheet" href="style.css">

<style>
.btn_cancel{
    display: inline-block;
    margin-right: 10px;
    padding: 10px 20px;
    color: #555;
    font-size: 86%;
    border-radius: 5px;
    border: 1px solid #999;
}
.btn_cancel:hover{
    color: #999;
    border-color: #999;
    text-decoration: none;
}

</style>
</head>


<body>
<h1>simple message board (editing)</h1>

<?php if(!empty($error_message)): ?>
    <ul class="error_message">
        <?php foreach( $error_message as $value ): ?>
        <li><?php echo $value; ?></li>
        <?php endforeach;?>
    </ul>
<?php endif; ?>

<form method="post">
    <div>
        <label for="view_name">name</label>
        <input id="name" type="text" name="view_name" value="<?php
if(!empty($message_data['view_name'])){ echo
    $message_data['view_name']; } ?>"/>
    </div>
    <div>
        <label for="message">message</label>
        <textarea id="message" type="text" name="message"><?php 
    if(!empty($message_data['message'])){ echo $message_data['message'];
    } ?></textarea>
    </div>
    <a class="btn_cancel" href="admin.php">cancel</a>
    <input type="submit" name="btn_submit" value="save">
    <input type="hidden" name="message_id" value="<?php echo
    $message_data['id']; ?>">
</form>
</body>

</html>

