<div class="search-box col-sm p-0 mb-5">
    <form action='index.php' method="get">

        <div class="input-group mb-2">
            <input type="search" name="name" class="form-control search-input-name" value="<?=isset($_GET['name']) ? h($_GET['name']) : ''?>">
            <div class="input-group-append">
                <button type="submit" class="btn btn-secondary">商品を探す</button>
            </div>
            <div class="input-group-append">
                <button class="btn btn-outline-info" type="button" data-toggle="collapse" data-target="#clps-search" aria-expanded="<?=$is_clps_open ? 'true' : 'false'?>" aria-controls="clps-search">詳細検索</button>
            </div>
        </div>

        <div class="collapse<?=$is_clps_open ? ' show' : ''?>" id="clps-search">
            <div class="card card-body search-detail-box">
                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">種類</span>
                    </div>
                    <select name="type" class="custom-select search-input-type">
                        <option value='0' <?=empty($_GET['type']) ? 'selected' : ''?>>選択しない</option>
                        <?php for($i=0; $i<8; $i++) : ?>
                            <option value='<?=$i+1?>' <?=isset($_GET['type']) && $_GET['type'] == $i+1 ? 'selected' : ''?>><?=h(TYPE_LIST[$i])?></option>
                        <?php endfor; ?>
                    </select>
                </div>

                <div class="input-group mb-2">
                    <div class="input-group-prepend">
                        <span class="input-group-text">価格</span>
                    </div>
                    <input name="value" type="text" value="<?=isset($_GET['value']) ? h($_GET['value']) : ''?>"　class="form-control search-input-value" aria-describedby="button-addon2">
                    <select name="value_cond" class="custom-select search-input-cond" id="input-value-cond">
                        <option value='0' <?=empty($_GET['value_cond']) ? 'selected' : ''?>>円</option>
                        <option value='1' <?=isset($_GET['value_cond']) && $_GET['value_cond'] == '1' ? 'selected' : ''?>>円以下</option>
                        <option value='2' <?=isset($_GET['value_cond']) && $_GET['value_cond'] == '2' ? 'selected' : ''?>>円以上</option>
                    </select>
                    <div class="input-group-append">
                        <input type="hidden" name="is_clps_open" class="is_clps_open" value="<?=h($is_clps_open)?>">
                        <button type="submit" class="btn btn-secondary">商品を探す</button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>
