<?php
include_once(dirname(__FILE__) . '/model.php');
list($veg_name_set, $ed_cuis, $error, $log) = editCuisineData();
?>

<?php include_once(dirname(__FILE__) . '/../../header.php'); ?>

<form method="post">
    <div class="input-group mb-2">
        <div class="input-group-prepend w-100">
            <span class="input-group-text">料理名</span>
            <input type="text" name="name" class="form-control" value="<?=$ed_cuis['name']?>">
        </div>
    </div>

    <?php if($veg_name_set) : ?>
        <div class="input-group mb-2">
            <div class="input-group-prepend w-100">
                <span class="input-group-text mr-3">使用食材（10個まで）</span>
            </div>
                <?php for($i=0; $i<count($veg_name_set); $i++) : ?>
                    <div class="form-check form-check-inline d-inline-block">
                        <input type="checkbox" name="cuisine[]" class="form-check-input" id="inlineCheckbox<?=$i?>" value="<?=$veg_name_set[$i]['vegetable_id']?>" <?=isset($ed_cuis['cuisine']) && in_array($veg_name_set[$i]['vegetable_id'], $ed_cuis['cuisine'], true) ? "checked" : ''?>>
                        <label class="form-check-label" for="inlineCheckbox<?=$i?>"><?=$veg_name_set[$i]['name']?></label>
                    </div>
                <?php endfor; ?>
        </div>
    <?php endif; ?>

    <input type="hidden" name="id" value="<?=$ed_cuis['cuisine_id']?>">
    <button type="submit" class="btn btn-outline-secondary btn-block">更新</button>
</form>

<?php include_once(dirname(__FILE__) . '/../../footer.php'); ?>
