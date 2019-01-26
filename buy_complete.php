<?php
include_once(dirname(__FILE__) . '/model.php');
$title = 'メール送信完了';
$_SESSION = [];
?>

<?php include_once(dirname(__FILE__) . '/header.php'); ?>
<?php include_once(dirname(__FILE__) . '/search.php'); ?>

<p>注文確認メール（架空）を送信しました。</p>
<p class="mt-5"><a href="index.php">お買い物を続ける</a></p>


<?php include_once(dirname(__FILE__) . '/footer.php'); ?>
