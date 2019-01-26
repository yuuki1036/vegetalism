<?php
include_once(dirname(__FILE__) . '/model.php');
list($cr_veg_data, $error ,$log) = createVegetableData();
?>

<?php include_once(dirname(__FILE__) . '/../header.php'); ?>

<h5>新規野菜データ登録</h5>
<?php if($error) echo "<p class=\"aleert alert-danger\">$error</p>" ?>

<form method="post">
    <div class="input-group mb-2">
        <div class="input-group-prepend w-100">
            <span class="input-group-text">商品名 / 検索用（任意）</span>
            <input type="text" name="name" class="form-control" value="<?=isset($cr_veg_data['name']) ? $cr_veg_data['name'] : ''?>">
            <input type="text" name="sub_name" class="form-control" value="<?=isset($cr_veg_data['sub_name']) ? $cr_veg_data['sub_name'] : ''?>">
        </div>
    </div>

    <div class="input-group mb-2">
        <div class="input-group-prepend w-50">
            <span class="input-group-text">種類</span>
            <select name="type" class="custom-select">
                <option value='0' <?=empty($cr_veg_data['type']) ? 'selected' : ''?>>未選択</option>
                <?php for($i=0; $i<7; $i++) : ?>
                    <option value='<?=$i+1?>' <?=isset($cr_veg_data['type']) && $cr_veg_data['type'] == $i+1 ? 'selected' : ''?>><?=TYPE_LIST[$i]?></option>
                <?php endfor; ?>
            </select>
        </div>
        <div class="input-group-prepend w-50">
            <span class="input-group-text">産地（任意）</span>
            <input type="text" name="area" class="form-control" value="<?=isset($cr_veg_data['area']) ? $cr_veg_data['area'] : ''?>">
        </div>
    </div>

    <div class="input-group mb-2">
        <div class="input-group-prepend w-50">
            <span class="input-group-text">重さ（グラム）</span>
            <input type="text" name="weight" class="form-control" value="<?=isset($cr_veg_data['weight']) ? $cr_veg_data['weight'] : ''?>">
        </div>

        <div class="input-group-prepend w-50">
            <span class="input-group-text">価格（円）</span>
            <input type="text" name="value" class="form-control" value="<?=isset($cr_veg_data['value']) ? $cr_veg_data['value'] : ''?>">
        </div>
    </div>

    <div class="input-group mb-2">
        <div class="input-group-prepend w-100">
            <span class="input-group-text">商品説明（任意）</span>
            <textarea name="comment" class="form-control" rows="5"><?=isset($cr_veg_data['comment']) ? $cr_veg_data['comment'] : ''?></textarea>
        </div>
    </div>

    <div class="input-group mb-2">
        <div class="input-group-prepend w-100">
            <span class="input-group-text mr-3">旬（任意）</span>
                <?php for($i=1; $i<=12; $i++) : ?>
                    <div class="form-check form-check-inline">
                        <input type="checkbox" name="season[]" class="form-check-input" id="inlineCheckbox<?=$i?>" value="<?=$i?>" <?=isset($cr_veg_data['season']) && $cr_veg_data['season'][$i] == '1' ? "checked" : ''?>>
                        <label class="form-check-label" for="inlineCheckbox<?=$i?>"><?=$i?></label>
                    </div>
                <?php endfor; ?>
        </div>
    </div>

    <button type="submit" class="btn btn-outline-secondary btn-block">更新</button>
</form>

<?php include_once(dirname(__FILE__) . '/../footer.php'); ?>
