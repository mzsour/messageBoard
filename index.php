<?php
define('FILENAME', './message.txt');

date_default_timezone_set('Asia/Tokyo');

$now_date = null;
$data = null;
$file_handle = null;
$split_data =null;
$message = array();
$message_array = array();
$success_message = null;
$error_message = array();
$clean = array();

if(!empty($_POST['btn_submit'])){
    if(empty($_POST['view_name'])){
        $error_message[] = 'you must enter name';
    } else {
        $clean['view_name'] = htmlspecialchars($_POST['view_name'], ENT_QUOTES);
        $clean['view_name'] = preg_replace('/\\r\\n|\\n|\\r/', '', $clean['view_name']);
    }
    if(empty($_POST['message'])){
        $error_message[] = 'you must enter message';
    } else {
        $clean['message'] = htmlspecialchars($_POST['message'], ENT_QUOTES);
        $clean['message'] = preg_replace('/\\r\\n|\\n|\\r/', '<br>', $clean['message']);
    }
    if(empty($error_message)){
        if( $file_handle = fopen( FILENAME, "a")){
            $now_date = date("Y-m-d H:i:s");
            $data = "'".$clean['view_name']."', '".$clean['message']."','".$now_date."'\n";
            fwrite($file_handle, $data);
            fclose($file_handle);
            $success_message = "your message is successfully submitted";
        } 
    } 
}

if($file_handle = fopen(FILENAME, 'r')){
    while($data = fgets($file_handle)){
        $split_data = preg_split('/\'/', $data);

        $message = array(
            'view_name' => $split_data[1],
            'message' => $split_data[3],
            'post_date' => $split_data[5]
        );
        array_unshift($message_array, $message);
    }
    fclose($file_handle);
}
?>

<!DOCTYPE html>
<html lang="ja">

<head>
<meta charset="utf-8">
<title>simple message board</title>
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
        <input id="name" type="text" name="view_name" values="">
    </div>
    <div>
        <label for="message">message</label>
        <textarea id="message" type="text" name="message" values=""></textarea>
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
    <p><?php echo $value['message']; ?></p>
</article>
<?php endforeach; ?>
<?php endif; ?>
</section>
</body>

</html>

