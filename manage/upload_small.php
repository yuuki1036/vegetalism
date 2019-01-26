<?php
include_once(dirname(__FILE__) . '/model.php');
list($id, $error, $log) = uploadImage(true);
?>

<?php include_once(dirname(__FILE__) . '/../header.php') ?>

<p class="h5">アイコン画像登録</p>
<p>画像サイズは２００×２００px。最大１００pxで表示。</p>
<?php if ($error) echo "<p class=\"alert alert-danger\">$error</p>" ?>

<p><img src="<?=img_path($id)?>" class="img-thumbnail" width="100" alt=""></p>

<form method="post" enctype="multipart/form-data">

    <div class="input-group w-75">
        <div class="input-group-prepend">
            <span class="input-group-text" id="inputGroupFileAddon01">アイコン画像</span>
        </div>
        <div class="custom-file">
            <input type="file" name="pic" class="custom-file-input" id="inputGroupFile01" aria-describedby="inputGroupFileAddon01">
            <label class="custom-file-label" for="inputGroupFile01">選択</label>
        </div>
    </div>

    <input type="hidden" name="id" value="<?=$id?>">
    <button type="submit" class="btn btn-outline-secondary btn-block w-75">登録</button>

</form>


<?php include_once(dirname(__FILE__) . '/../footer.php'); ?>
