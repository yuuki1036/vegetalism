<?php
include_once(dirname(__FILE__) . '/model.php');
list($sale_veg_set, $limit_date, $log2) = updateSale();
list($veg_data_set, $search_type, $cuis_data, $cuis_id_set, $error, $log) = getVegetableData();
searchClpsCheck();
$title = '';
?>

<?php include_once(dirname(__FILE__) . '/header.php'); ?>
<?php include_once(dirname(__FILE__) . '/search.php'); ?>

<?php if($error) echo "<p class=\"alert alert-danger\">$error"?></p>

<?php if(count($veg_data_set)) : ?>
    <?php if($search_type === 'search_box') : ?>
        <p><?=count($veg_data_set)?>件の商品が見つかりました。</p>
    <?php elseif($search_type === 'season') : ?>
        <p class="font-weight-bold"><span class="badge badge-info">PICK UP</span>&ensp;<?=h($month)?>月の旬の野菜</p>
    <?php elseif($search_type === 'cuisine') : ?>
        <img src="<?=img_path($cuis_data['cuisine_id'], 'small', 'cuisine')?>" class=" d-inline-block" width="50">
        <p class="d-inline-block ml-2">今夜は<?=h($cuis_data['name'])?>にしよう！</p>
    <?php endif; ?>
    <div class="itembox-outer d-flex flex-wrap p-0 m-0 mt-3 mb-5">
        <?php foreach($veg_data_set as $item) : ?>
            <div class="itembox m-1 bg-light">
                <div class="float-left item-img p-2">
                    <a href="detail.php?id=<?=$item['vegetable_id']?>&is_clps_open=<?=h($is_clps_open)?>"><img src="<?=img_path($item['vegetable_id'])?>" class=""  width="100" alt=""></a>
                </div>
                <div class="summary float-right p-1 pr-2">
                    <p class="mb-2">
                        <a href="detail.php?id=<?=$item['vegetable_id']?>&is_clps_open=<?=h($is_clps_open)?>"><?=h($item['name'])?></a>
                        <?php if($item['sale'] == 1) echo "<span class=\"badge badge-danger float-right mt-1 mr-1\">特価</span>"?>
                    </p>
                    <p class="float-left mb-2"><?=h($item['weight'])?><small>グラム</small></p>
                    <?php if($item['sale'] == 1) : ?>
                         <p class="float-right mb-2 text-danger font-weight-bold"><?=h(round($item['value'] * DISCOUNT, -1, PHP_ROUND_HALF_UP))?><small>円</small></p>
                    <?php else : ?>
                        <p class="float-right mb-2"><?=h($item['value'])?><small>円</small></p>
                    <?php endif; ?>
                    <form action="cart.php" method="post">
                        <div class="input-group">
                            <select name="num" class="custom-select amount-select2" id="inputGroupSelect04" aria-label="Example select with button addon">
                                <?php for($j=1; $j<=15; $j++) : ?>
                                    <option><?=$j?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="input-group-append">
                                <input type="hidden" name="id" value="<?=h($item['vegetable_id'])?>">
                                <input type="hidden" name="is_clps_open" class="is_clps_open" value="<?=h($is_clps_open)?>">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">カートへ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p>条件に該当する商品は見つかりませんでした。</p>
<?php endif; ?>

<?php if($sale_veg_set && ($search_type === 'season' || $search_type === 'all')) : ?>
    <p><span class="badge badge-danger">SALE</span>&ensp;<span class="font-weight-bold">今週の安売り情報</span><span class="text-danger">（<?=h($limit_date)?>まで）</span></p>
    <div class="itembox-outer d-flex flex-wrap p-0 m-0 mt-3 mb-5">
        <?php foreach($sale_veg_set as $item) : ?>
            <div class="itembox m-1 bg-light">
                <div class="float-left item-img p-2">
                    <a href="detail.php?id=<?=$item['vegetable_id']?>&is_clps_open=<?=h($is_clps_open)?>"><img src="<?=img_path($item['vegetable_id'])?>" class=""  width="100" alt=""></a>
                </div>
                <div class="summary float-right p-1 pr-2">
                    <p class="mb-2"><a href="detail.php?id=<?=$item['vegetable_id']?>&is_clps_open=<?=h($is_clps_open)?>"><?=h($item['name'])?></a></p>
                    <p class="float-left mb-2"><?=h($item['weight'])?><small>グラム</small></p><p class="float-right mb-2 text-danger font-weight-bold"><?=h(round($item['value'] * DISCOUNT, -1, PHP_ROUND_HALF_UP))?><small>円</small></p>
                    <form action="cart.php" method="post">
                        <div class="input-group">
                            <select name="num" class="custom-select amount-select2" id="inputGroupSelect04" aria-label="Example select with button addon">
                                <?php for($j=1; $j<=15; $j++) : ?>
                                    <option><?=$j?></option>
                                <?php endfor; ?>
                            </select>
                            <div class="input-group-append">
                                <input type="hidden" name="id" value="<?=h($item['vegetable_id'])?>">
                                <input type="hidden" name="is_clps_open" class="is_clps_open" value="<?=h($is_clps_open)?>">
                                <button type="submit" class="btn btn-outline-secondary btn-sm">カートへ</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>

<?php if($cuis_id_set) : ?>
    <p class="pt-2 mb-1">今夜は何を作ろう？<br>料理をクリックして使用食材をチェック！</p>
    <div class="owl-carousel owl-theme owl-loaded p-2 bg-light" id="cuisine" data-num="<?=count($cuis_id_set)?>">
        <div class="owl-stage-outer">
            <div class="owl-stage">
                <?php foreach($cuis_id_set as $row) : ?>
                    <div class="owl-item">
                        <a href="index.php?cuisine=<?=$row['cuisine_id']?>">
                            <img src="<?=img_path($row['cuisine_id'], 'small','cuisine')?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endif; ?>



<?php include_once(dirname(__FILE__) . '/footer.php'); ?>
