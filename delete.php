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

    $mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

    if($mysqli->connect_errno){
        $error_message[] = 'failed to connect DB. errno ' .
            $mysqli->connect_errno . ' : ' . $mysqli->connect_errno;
    } else {
        $sql = "DELETE FROM message WHERE id = $message_id";
        $res = $mysqli->query($sql);
    }
    $mysqli->close();

    if($res){
        header("Location: ./admin.php");
    }
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="utf-8">
<title>simple message board (deleting)</title>
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
.text-confirm{
    margin-bottom: 20px;
    font-size: 86%;
    line-height: 1.6em;
}

</style>
</head>


<body>
<h1>simple message board (deleting)</h1>

<?php if(!empty($error_message)): ?>
    <ul class="error_message">
        <?php foreach( $error_message as $value ): ?>
        <li><?php echo $value; ?></li>
        <?php endforeach;?>
    </ul>
<?php endif; ?>

<p class="text-confirm">the post below will be deleted.<br>
if it is ok, press delete button.</p>

<form method="post">
    <div>
        <label for="view_name">name</label>
        <input id="name" type="text" name="view_name" value="<?php
if(!empty($message_data['view_name'])){ echo
    $message_data['view_name']; } ?>" disabled>
    </div>
    <div>
        <label for="message">message</label>
        <textarea id="message" type="text" name="message" disabled><?php 
    if(!empty($message_data['message'])){ echo $message_data['message'];
    } ?></textarea>
    </div>
    <a class="btn_cancel" href="admin.php">cancel</a>
    <input type="submit" name="btn_submit" value="delete">
    <input type="hidden" name="message_id" value="<?php echo
    $message_data['id']; ?>">
</form>
</body>

</html>

