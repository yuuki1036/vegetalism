<?php
include_once(dirname(__FILE__) . '/model.php');
list($veg_name_set, $cr_cuis, $error ,$log) = createCuisineData();
?>

<?php include_once(dirname(__FILE__) . '/../../header.php'); ?>

<h5>新規料理データ登録</h5>
<?php if($error) echo "<p class=\"aleert alert-danger\">$error</p>" ?>

<form method="post">
    <div class="input-group mb-2">
        <div class="input-group-prepend w-100">
            <span class="input-group-text">料理名</span>
            <input type="text" name="name" class="form-control" value="<?=isset($create　CuisineData['name']) ? $cr_cuis['name'] : ''?>">
        </div>
    </div>

    <?php if($veg_name_set) : ?>
        <div class="input-group mb-2">
            <div class="input-group-prepend w-100">
                <span class="input-group-text mr-3">使用食材（10個まで）</span>
            </div>
                <?php for($i=0; $i<count($veg_name_set); $i++) : ?>
                    <div class="form-check form-check-inline d-inline-block">
                        <input type="checkbox" name="cuisine[]" class="form-check-input" id="inlineCheckbox1" value="<?=$veg_name_set[$i]['vegetable_id']?>" <?=isset($cr_cuis['cuisine']) && in_array($veg_name_set[$i]['vegetable_id'], $cr_cuis['cuisine'], true) ? "checked" : ''?>>
                        <label class="form-check-label" for="inlineCheckbox1"><?=$veg_name_set[$i]['name']?></label>
                    </div>
                <?php endfor; ?>
        </div>
    <?php endif; ?>

    <button type="submit" class="btn btn-outline-secondary btn-block">更新</button>
</form>

<?php include_once(dirname(__FILE__) . '/../../footer.php'); ?>
