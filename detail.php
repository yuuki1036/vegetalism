<?php
include_once(dirname(__FILE__) . '/model.php');
list($dtl_data, $is_season, $use_cuis, $error, $log) = getVegetableDetail();
$title = isset($dtl_data['name']) ? $dtl_data['name'] : '';
?>
<?php include_once(dirname(__FILE__) . '/header.php'); ?>
<?php include_once(dirname(__FILE__) . '/search.php'); ?>

<?php if(!$error) : ?>
    <p class="detail-img-outer pl-0 mt-4 mb-2">
        <img src="<?=img_path($dtl_data['vegetable_id'], 'large')?>" class="detail-img img-thumbnail" alt="">
    </p>

    <p>
        <span class="h4"><?=h($dtl_data['name'])?></span>
        <span><?=$dtl_data['area'] ? '（' . h($dtl_data['area']) . '産）' : ''?></span>
        <?php if($dtl_data['sale'] == 1) echo "<span class=\"badge badge-danger mr-1\">特価品</span>"?>
        <?php if($is_season) echo "<span class=\"badge badge-success\">今が旬の野菜</span>"?>
    </p>

        <p ><?=$dtl_data['comment'] ? nl2br(h($dtl_data['comment'])) : ''?>

    <div class="row">
        <div class="col-sm-7 my-2">
            <span><?=h($dtl_data['weight'])?><small>グラム</small>&emsp;</span>
            <?php if($dtl_data['sale'] == 1) : ?>
                <del><span><?=h($dtl_data['value'])?></span>円</del>&ensp;=&gt;
                <span class="h5 text-danger font-weight-bold"><?=h(round($dtl_data['value'] * DISCOUNT, 0, PHP_ROUND_HALF_UP))?></span><span class="text-danger font-weight-bold">円</span>（税込<?=h(round($dtl_data['value'] * DISCOUNT * TAX_IN, 0, PHP_ROUND_HALF_UP))?>円）</span>
            <?php else : ?>
                <span class="h5"><?=h($dtl_data['value'])?></span>円（税込<?=h(round($dtl_data['value'] * TAX_IN, 0, PHP_ROUND_HALF_UP))?>円）</span>
            <?php endif; ?>

        </div>

        <div class="col-sm-5 my-2">
            <form action="cart.php" method="post">
                <div class="input-group">
                    <select name="num" class="custom-select amount-select" id="inputGroupSelect04" aria-label="Example select with button addon">
                        <?php for($j=1; $j<=15; $j++) : ?>
                            <option><?=$j?></option>
                        <?php endfor; ?>
                    </select>

                    <div class="input-group-append">
                        <input type="hidden" name="id" value="<?=h($dtl_data['vegetable_id'])?>">
                        <button type="submit" class="btn btn-secondary btn-sm">&nbsp;カートへ入れる&nbsp;</button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <?php if($use_cuis) : ?>
        <p class="mt-3 mb-1"><?=$dtl_data['name']?>を使う料理はこちら。他の食材もチェックしよう！</p>
        <div class="owl-carousel owl-theme owl-loaded p-2" id="cuisine" data-num="<?=count($use_cuis)?>">
            <div class="owl-stage-outer">
                <div class="owl-stage">
                    <?php foreach($use_cuis as $cuisine_id) : ?>
                        <div class="owl-item">
                            <a href="index.php?cuisine=<?=h($cuisine_id)?>">
                                <img src="<?=img_path($cuisine_id, 'small','cuisine')?>">
                            </a>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    <?php endif; ?>
<?php else : ?>
    <p class="alert alert-danger"><?=h($error)?></p>
    <p><a href="index.php">トップページへ移動</a></p>
<?php endif; ?>


<?php include_once(dirname(__FILE__) . '/footer.php'); ?>
