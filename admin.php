<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'kazuki');
define('DB_PASS', 'apgangan');
define('DB_NAME', 'board');

// password for admin page
define( 'PASSWORD', 'pass'); 

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

    if(!empty($_POST['admin_password']) && $_POST['admin_password'] 
        === PASSWORD){
        $_SESSION['admin_login'] = true;
    } else {
        $erro_messege[] = 'login failed.'; 
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
<title>admin page</title>
<style>
input[type="password"],
input[type="password"]{
    width: 200px;
}

</style>
</head>

<body>
<h1>admin page</h1>

<?php if(!empty($error_message)): ?>
    <ul class="error_message">
        <?php foreach( $error_message as $value ): ?>
        <li><?php echo $value; ?></li>
        <?php endforeach;?>
    </ul>
<?php endif; ?>

<section>

<?php if(!empty($_SESSION['admin_login']) && 
$_SESSION['admin_login'] === true): ?>

<form method="get" action="./download.php">
    <input type="submit" name="btn_download" value="download">
</form>

<!-- display posted message -->
<?php if(!empty($message_array)): ?>
<?php foreach($message_array as $value): ?>
<article>
    <div class="info">
        <h2><?php echo $value['view_name']; ?></h2>
        <time><?php echo date('Y年m月d日 H:i', strtotime($value['post_date'])); ?></time>
    </div>
    <p><?php echo $value['message']; ?></p>
</article>
<?php endforeach; ?>
<?php endif; ?>

<?php else: ?>
<!-- login form follows -->
<form method="post">
    <div>
        <label for="admin_password">login password</label>
        <input id="admin_password" type="password"
            name="admin_password" value="">
    </div>
    <input type="submit" name="btn_submit" value="login">
</form>

<?php endif; ?>

</section>
</body>

</html>

