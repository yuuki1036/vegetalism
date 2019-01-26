<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once(dirname(__FILE__) . '/model.php');
list($mail_address, $error, $log) = sendEmail();
$title = '購入';
?>

<?php include_once(dirname(__FILE__) . '/header.php'); ?>
<?php include_once(dirname(__FILE__) . '/search.php'); ?>
<?php if($error) echo "<p class=\"alert alert-danger\">$error</p>" ?>

<?php if(!empty($_SESSION['cart'])) : ?>
    <p>閲覧いただきありがとうございました。<br>メールアドレスを入力すると注文確認メール（架空）を送信します。</p>

    <form method="post">
        <div class="input-group mb-0">
            <input type="email" name="email" class="form-control input-email" aria-describedby="button-addon2" value="<?=$mail_address ? $mail_address : ''?>">
            <div class="input-group-append">
                <button type="submit" class="btn btn-secondary" id="button-addon2">&ensp;送信&ensp;</button>
            </div>
        </div>
        <small class="form-text text-muted mt-0">入力されたメールアドレスは送信処理後破棄いたします。</small>
    </form>
    <p class="detail-img-outer mt-4"><img src="images/mak800.jpg" class="detail-img img-thumbnail" alt=""></p>
    <p class="mt-5"><a href="index.php">お買い物を続ける</a></p>
<?php else : ?>
    <p class="alert alert-danger">カートが空です。手続きをやり直してください。</p>
    <p class="mt-5"><a href="index.php">トップページへ移動する</a></p>
<?php endif; ?>

<?php include_once(dirname(__FILE__) . '/footer.php'); ?>
