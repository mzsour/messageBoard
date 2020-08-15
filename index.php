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

session_start();

if(!empty($_POST['btn_submit'])){
    if(empty($_POST['view_name'])){
        $error_message[] = 'you must enter name';
    } else {
        $clean['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);
        $clean['view_name'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['view_name']);
        
        $_SESSION['view_name'] = $clean['view_name'];
    }
    if(empty($_POST['message'])){
        $error_message[] = 'you must enter message';
    } else {
        $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
    }
    if(empty($error_message)){
        $mysqli = new mysqli('localhost', 'kazuki', 'apgangan', 'board'); 
        if($mysqli->connect_errno){
            $error_message[] = 'failed to connect DB. errNo: '.$mysqli->connect_errno.'
            : '.$mysqli->connect_error;
        } else {
            $mysqli->set_charset('utf8');
            $now_date = date("Y-m-d H:i:s");
            $sql = "INSERT INTO message (view_name, message, post_date) VALUES 
                ( '$clean[view_name]', '$clean[message]', '$now_date')";
        
            $res = $mysqli->query($sql);

            if($res){
                $success_message='message is successfully written.';
            } else {
                $error_message[] = "failed to write into DB.";
            }
            $mysqli->close();
        }
    } 
}

$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if($mysqli->connect_errno){
    $error_message[] = 'failed to read from DB. errNo:'.$mysqli->connect_errno.
                        ' : '.$mysqli->connect_error;
} else {
    $sql = "SELECT view_name, message, post_date FROM message ORDER BY
            post_date DESC";
    $res = $mysqli->query($sql);

    if($res){
        $message_array = $res->fetch_all(MYSQLI_ASSOC);
    }
    $mysqli->close();
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="utf-8">
<title>simple message board</title>
<link rel="stylesheet" href="style.css">
<style></style>
</head>

<body>
<h1>simple message board</h1>
<?php if(!empty($success_message)): ?>
    <p class="success_message"><?php echo $success_message; ?></p>
<?php endif; ?>
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
            if(!empty($_SESSION['view_name'])){
                echo $_SESSION['view_name'];
            }?>"/>
    </div>
    <div>
        <label for="message">message</label>
        <textarea id="message" type="text" name="message" value=""></textarea>
    </div>
    <input type="submit" name="btn_submit" value="submit">
</form>
<hr>
<section>
<!-- display posted message -->
<?php if(!empty($message_array)): ?>
<?php foreach($message_array as $value): ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    </div>
    <p><?php echo nl2br($value['message']); ?></p>
</article>
<?php endforeach; ?>
<?php endif; ?>
</section>
</body>

</html>

