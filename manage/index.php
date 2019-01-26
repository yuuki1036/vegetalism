<?php
include_once(dirname(__FILE__) . '/model.php');
$mg_veg_set = getManageVegetableData();
?>

<?php include_once(dirname(__FILE__) . '/../header.php'); ?>

<div class="select-action">
    <a href="create.php" class="mr-2">新規作成</a>
    <a href="../index.php" class="mr-2">サイト確認</a>
    <a href="cuisine/index.php" class="mr-2">料理管理画面</a>
    <span>登録数 <?php if($mg_veg_set) echo count($mg_veg_set) ?></span>
</div>

<?php if($mg_veg_set) : ?>
    <table class="table table-sm table-bordered">
        <?php foreach($mg_veg_set as $item) : ?>
            <tr>
                <td rowspan="2">
                    <img src="<?=img_path($item['vegetable_id'])?>" class="mb-1" width="50">
                    <br>
                    <img src="<?=img_path($item['vegetable_id'], 'large')?>" width="100">
                </td>

                <td><?=$item['name']?></td>
                <td><?=TYPE_LIST[$item['type']-1]?></td>
                <td><?=$item['area']?></td>
                <td><?=$item['weight']?>g</td>
                <td><?=$item['value']?>円</td>

                <td rowspan="2">
                    <a href="edit.php?id=<?=$item['vegetable_id']?>">修正</a></p>
                    <p><a href="upload_small.php?id=<?=$item['vegetable_id']?>">small</a>&emsp;<a href="upload_large.php?id=<?=$item['vegetable_id']?>">large</a></p>
                    <p><a href="delete.php?id=<?=$item['vegetable_id']?>" onclick="return confirm('削除してよろしいですか？')">削除</a></p>
                </td>
            </tr>

            <tr>
                <td colspan="5"><p class="comment"><?=nl2br($item['comment'])?></p></td>
            </tr>
        <?php endforeach; ?>
    </table>
<?php endif; ?>

<?php include_once(dirname(__FILE__) . '/../footer.php'); ?>
