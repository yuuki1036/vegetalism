<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once(dirname(__FILE__) . '/model.php');
list($cart_data_set, $total, $num_total, $error, $log) = getCartData();
$title = 'カート';
?>

<?php include_once(dirname(__FILE__) . '/header.php'); ?>
<?php include_once(dirname(__FILE__) . '/search.php'); ?>

<?php if($error) : ?>
    <p class="alert alert-danger"><?=h($error)?></p>
    <p><a href="index.php">トップページへ移動</a></p>
<?php else : ?>
    <?php if(count($cart_data_set)) : ?>
        <p class="float-left mb-2">ショッピングカート</p>
        <a href="cart_empty.php" class="btn btn-info active btn-sm float-right mr-3 mb-2" role="button" aria-pressed="true">カートを空にする</a>
        <table class="table">
            <thead>
                <tr class="text-center">
                    <th>商品名</th><th>価格</th><th>数量</th><th colspan="2" class="pr-0">小計（税込）</th>
                </tr>
            </thead>

            <tbody>
                <?php foreach($cart_data_set as $data) : ?>
                    <tr class="text-center">
                        <td><?=h($data['name'])?></td>
                        <td><?=h($data['value'])?></td>
                        <td><?=h($data['num'])?></td>
                        <td class="pr-0 mr-0"><?=h($data['subtotal'])?>円</td>
                        <td class="px-0 ml-0 mr-3" width="30"><a href="cart_delete.php?id=<?=h($data['vegetable_id'])?>" class="badge badge-secondary text-white">削除</a></td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <th colspan="3" class="text-right">合計（計<?=h($num_total)?>点）</th>
                    <th class="text-center pr-0 mr-0"><?=h($total)?>円</th>
                    <th></th>
                </tr>
            </tbody>

        </table>

        <p class="text-right pb-0 mb-0"><a href="buy.php" class="btn btn-danger active btn-sm mr-3" role="button" aria-pressed="true">&emsp;購入する&emsp;</a>
        <p class="small text-right mr-3 mb-4">実際に購入手続きは行われません</p>
        <p><a href="index.php" class="">お買い物を続ける</a></p>


    <?php else : ?>
        <P>カートは空です。</P>
        <div class="select-action">
            <a href="index.php">お買い物を続ける</a>
        </div>
    <?php endif; ?>

<?php endif; ?>

<?php include_once(dirname(__FILE__) . '/footer.php'); ?>
