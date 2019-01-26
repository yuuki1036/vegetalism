<?php
include_once(dirname(__FILE__) . '/model.php');
$mg_cuis_set = getManageCuisineData();
?>

<?php include_once(dirname(__FILE__) . '/../../header.php'); ?>

<div class="select-action">
    <a href="create.php" class="mr-2">新規作成</a>
    <a href="../../index.php" class="mr-2">サイト確認</a>
    <a href="../index.php" class="mr-2">野菜管理画面</a>
    <span>登録数 <?php if($mg_cuis_set) echo count($mg_cuis_set) ?></span>
</div>

<table class="table table-sm table-bordered">
    <?php if($mg_cuis_set) : ?>
        <?php foreach($mg_cuis_set as $item) : ?>
            <tr>
                <td>
                    <img src="<?=img_path($item['cuisine_id'], 'small', 'cuisine')?>"? width="100">
                </td>

                <td><?=$item['name']?></td>

                <td>
                    食材<?php $cnt = 0; foreach(USE_LIST as $no) if($item[$no] >= 1) $cnt++; echo $cnt;?>個
                </td>

                <td>
                <a href="edit.php?id=<?=$item['cuisine_id']?>" class="mr-2">修正</a>
                <a href="upload.php?id=<?=$item['cuisine_id']?>" class="mr-2">画像登録</a>
                <a href="delete.php?id=<?=$item['cuisine_id']?>" onclick="return confirm('削除してよろしいですか？')">削除</a>
                </td>
            </tr>
        <?php endforeach; ?>
    <?php endif; ?>
</table>

<?php include_once(dirname(__FILE__) . '/../../footer.php'); ?>
