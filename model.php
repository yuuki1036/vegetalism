<?php
include_once(dirname(__FILE__) . '/common.php');
$pageName = 'VEGETALISM';


/**
*詳細検索の開閉状態チェック
*
*ページ遷移時の状態フラグを取得する
*/
function searchClpsCheck(){
    global $is_clps_open;
    if(isset($_GET['is_clps_open'])) $is_clps_open = filter_input(INPUT_GET, 'is_clps_open');
    if(isset($_POST['is_clps_open'])) $is_clps_open = filter_input(INPUT_POST, 'is_clps_open');
}


/**
*野菜データ検索
*
*検索BOXでは指定した名前・種類・金額で検索する。検索値はGETにて取得する。
*値が空欄・不正なものは無視する。有効な値が１つでもあれば、SELECT文を作成・発行する。
*金額は金額の値と条件がどちらも有効である場合のみ有効とする。
*料理をクリックした場合のみGETで料理データが送られる。
*その場合は対象の料理に関連付けられた野菜を返す。
*GETがなければDATA_LIST_TYPEで、GETがあれば指定された条件のSELECT文を発行する。
*また、全ての検索値が無効及び空の場合は、DATA_LIST_TYPEで条件のSELECT文を発行する。
*
*検索の状態に関わらず全料理データを取得する。
*
*@return array [野菜データ, 検索タイプ, 全料理データ, ログ]
*/
function getVegetableData(){
    $pdo = connect(PUBLIC_DB_ACCESS);
    $error = $log = [];
    $veg_data_set = [];
    $cuis_data = '';
    $search_type = ''; //HTML分岐用

    $log[] = '$_GET = ' . a($_GET);

    //全料理データ取得
    $sql = 'SELECT cuisine_id FROM cuisine';
    $st = $pdo->query($sql);
    $cuis_id_set = $st->fetchAll();
    shuffle($cuis_id_set);
    $log[] = "SQL > $sql";

    //料理データの送信を確認
    $cuis_id = filter_input(INPUT_GET, 'cuisine', FILTER_SANITIZE_NUMBER_INT);
    if($cuis_id){
        if(preg_match('/\A[0-9]{1,10}\z/u', $cuis_id)){
            //対象の料理データ取得
            $sql = 'SELECT * FROM cuisine WHERE cuisine_id=?';
            $st = $pdo->prepare($sql);
            $st->bindValue(1, $cuis_id, PDO::PARAM_INT);
            $st->execute();
            $cuis_data = $st->fetch();
            $log[] = "SQL > $sql";

            if($cuis_data){
                $log[] = '$cuis_data = ' . a($cuis_data);
                //関連する野菜IDの配列を作成
                $veg_id_set = array_slice(array_values($cuis_data), 2);
                //野菜データを取得
                foreach($veg_id_set as $veg_id){
                    if($veg_id == '') break;
                    $sql = 'SELECT * FROM vegetables WHERE vegetable_id=?';
                    $st = $pdo->prepare($sql);
                    $st->bindValue(1, $veg_id, PDO::PARAM_INT);
                    $st->execute();
                    $veg_data_set[] = $st->fetch();
                }
                if($veg_data_set){
                    //野菜データを返す
                    $search_type = 'cuisine';
                    $log[] = 'search_type is cuisine';
                    $log = implode('<br>', $log);
                    $error = implode('<br>', $error);
                    shuffle($veg_data_set);
                    return [$veg_data_set, $search_type, $cuis_data, $cuis_id_set, $error, $log];
                }else{
                    $error[] = '関連付けられた野菜がありません。';
                }
            }else{
                $error[] = '指定された料理データは存在しません。';
            }
        }else{
            $error[] = '不正な値が送信されました。';
        }
    }

    //検索BOXのSQL文作成準備
    $sql = 'SELECT * FROM vegetables WHERE ';
    $conditions = []; //where句の条件
    $ph_type = [];  //PDO::PARAM_定数
    $ph_value = [];
    //名前のバリデーション・SQL文作成
    $name = filter_input(INPUT_GET, 'name');
    if($name){
        if(preg_match('/\A[[:^cntrl:]]{1,20}\z/u', $name)){
            $conditions[] = "CONCAT(name, sub_name) COLLATE utf8_unicode_ci LIKE ? ESCAPE '#'";
            $ph_type[] = PDO::PARAM_STR;
            $ph_value[] = '%' . escapeWildcard($name) . '%';
            $log[] = '$name OK';
        }else{
            $error[] = '不正な名前です。';
        }
    }
    //種類のバリデーション・SQL文作成
    $type = filter_input(INPUT_GET, 'type');
    if($type){
        if(preg_match('/\A[1-7]{1}\z/u', $type)){
            $conditions[] = "type=?";
            $ph_type[] = PDO::PARAM_INT;
            $ph_value[] = $type;
            $log[] = '$type OK';
        }else{
            $error[] = '不正な種類です。';
        }
    }
    //金額のバリデーション・SQL文作成
    //２つの値が有効な場合のみWHERE句に追加する
    $value = filter_input(INPUT_GET, 'value');
    $value_cond = filter_input(INPUT_GET, 'value_cond');
    if($value && $value_cond !== 0){ //$value_condは初期値が0
        if(preg_match('/\A[0-9]{1,10}\z/u', $value) && preg_match('/\A[0-2]{1}\z/u', $value_cond)){
            if($value_cond === '0'){
                $conditions[] = 'value=?';
            }elseif($value_cond === '1'){
                $conditions[] = 'value<=?';
            }else{
                $conditions[] = 'value>=?';
            }
            $ph_type[] = PDO::PARAM_INT;
            $ph_value[] = $value;
            $log[] = '$value OK';
        }else{
            $error[] = '不正な価格です。';
        }
    }

    if($conditions){
        //検索BOXのSQL文発行
        $sql .= implode(' AND ', $conditions);
        $st = $pdo->prepare($sql);
        for($i=0; $i<count($conditions); $i++){
            $st->bindValue($i + 1, $ph_value[$i], $ph_type[$i]);
        }
        $st->execute();
        $search_type = 'search_box';
        $log[] = 'search_type is search_box';
    }else{
        if(DATA_LIST_TYPE === 'all'){
            //全野菜データ取得
            $sql = 'SELECT * FROM vegetables';
            $st = $pdo->query($sql);
            $search_type = 'all';
            $log[] = 'search_type is all';
        }else{
            //旬の野菜データ検索
            global $season;
            $sql .= 'EXISTS ( SELECT * FROM seasons WHERE `' . $season . '`= 1 AND vegetables.vegetable_id=season_id )';
            $st = $pdo->query($sql);
            $search_type = 'season';
            $log[] = 'search_type is season';
        }
    }

    $log[] = "SQL > $sql";
    $veg_data_set = $st->fetchAll();
    shuffle($veg_data_set);

    $error = implode('<br>', $error);
    $log = implode('<br>', $log);

    return [$veg_data_set, $search_type, $cuis_data, $cuis_id_set, $error, $log];
}


/**
*詳細データ取得
*
*指定された野菜データを取得する。
*今月が旬であるかを判定し、この野菜IDが含まれている料理データを抽出する。
*
*@return array [野菜データ, 旬のフラグ, 使用料理データ, エラー,　ログ]
*/
function getVegetableDetail(){
    $pdo = connect(PUBLIC_DB_ACCESS);
    $error = $log = [];
    $dtl_data = $use_cuis = [];
    $is_season = '';

    $log[] = '$_GET = ' . a($_GET);

    $id = (string)filter_input(INPUT_GET, 'id');
    if($id){
        if(preg_match('/\A[0-9]{1,5}\z/u', $id)){
            //野菜データ取得
            $sql = 'SELECT * FROM vegetables WHERE vegetable_id=?';
            $st = $pdo->prepare($sql);
            $st->bindValue(1, $id, PDO::PARAM_INT);
            $st->execute();
            $dtl_data = $st->fetch();
            $log[] = "SQL > $sql";
            if($dtl_data){
                //今月は旬であるかどうか
                global $season; //月名
                $sql = 'SELECT `' . $season . '` FROM seasons WHERE season_id=?';
                $st = $pdo->prepare($sql);
                $st->bindValue(1, $id, PDO::PARAM_INT);
                $st->execute();
                $is_season = $st->fetch()[$season];
                $log[] = "SQL > $sql";
                $log[] = '$is_season = ' . $is_season;

                //全料理データを取得
                $sql = 'SELECT * FROM cuisine';
                $st = $pdo->query($sql);
                $cuis_data_set = $st->fetchAll();
                $log[] = "SQL > $sql";
                //全料理データに対して対象野菜IDがあるか検索
                $use_cuis = [];
                foreach($cuis_data_set as $row){
                    $slice = array_slice(array_values($row),2); //idとnameを除く
                    if(in_array($id, $slice)) $use_cuis[] = $row['cuisine_id'];
                }
                $log[] = '$use_cuis = ' . a($use_cuis);
            }else{
                $error[] = 'お探しの商品データは存在しません。';
            }
        }else{
            $error[] = '不正な値が送信されました。';
        }
    }else{
        $error[] = '値がありません。';
    }

    $log = implode('<br>', $log);
    $error = implode('<br>', $error);

    return [$dtl_data, $is_season, $use_cuis, $error, $log];
}



/**
*カートデータ作成
*
*カートデータが無ければPOSTよりカートデータを作成し、
*カートデータがあればPOSTからのデータを追加する。
*
*@return array [カート内野菜の配列, 合計金額, 数量合計, エラー, ログ]
*/
function getCartData(){
    $pdo = connect(PUBLIC_DB_ACCESS);
    $log = $error = [];
    $cart_data_set = [];
    $total = 0; //金額合計
    $num_total = 0; //数量合計

    $log[] = '$_POST = ' . a($_POST);

    if(!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

    $id = filter_input(INPUT_POST, 'id');
    $num = filter_input(INPUT_POST, 'num');
    if($id && $num){
        if(preg_match('/\A[0-9]{1,10}\z/u', $id) && preg_match('/\A[0-9]{1,2}\z/u', $num)){
            $log[] = '$_SESSION[cart] = ' . a($_SESSION['cart']);
            $log[] = '$id and $value is OK';
            //idのインデックスに数量を入れる
            if(array_key_exists($id, $_SESSION['cart'])){
                $_SESSION['cart'][$id] += $num;
            }else{
                $_SESSION['cart'][$id] = $num;
            }
        }else{
            $error[] = '不正な値です。';
        }
    }else{
        $error[] = '商品情報がありません。';
    }


    $log[] = 'added $_SESSION[cart] = ' . a($_SESSION['cart']);

    //配列の数（商品の数）だけSELECT文を発行する
    foreach($_SESSION['cart'] as $id => $amount){
        $sql = 'SELECT vegetable_id, name, value, sale FROM vegetables WHERE vegetable_id=?';
        $st = $pdo->prepare($sql);
        $st->bindValue(1, $id, PDO::PARAM_INT);
        $st->execute();
        $cart_data = $st->fetch();
        if($cart_data){
            //金額や数量を計算
            $cart_data['num'] = $amount;
            //セール品ならディスカウント
            if($cart_data['sale'] == 1) $cart_data['value'] *= DISCOUNT;
            $cart_data['subtotal'] = round($cart_data['value'] * $amount * TAX_IN, 0, PHP_ROUND_HALF_UP);
            $cart_data_set[] = $cart_data;
            $total += $cart_data['subtotal'];
            $num_total += $amount;
        }else{
            $error[] = '存在しないIDが含まれています。';
            break;
        }
    }

    $error = implode('<br>', $error);
    $log = implode('<br>', $log);

    return [$cart_data_set, $total, $num_total, $error, $log];
}


/**
*セールデータ取得・更新
*
*セール商品と終了期日を取得する。
*セールの更新日は毎週月曜00:00:00。
*セール開始から一週間が過ぎた場合、セール商品を再設定する。（セール商品の数も変える）
*また、セール開始日と終了日をDBに登録する。
*
*@return array [セール野菜, セール終了日, ログ]
*/
function updateSale(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $log = [];

    //セール開始日と終了期日を取得
    $st = $pdo->query('SELECT * FROM sale');
    $sale_time = $st->fetch();
    $start_time = $sale_time['start_time'];
    $limit_date = $sale_time['limit_date'];
    //セール開始時刻と現在時刻のオブジェクト作成
    $start_time = new DateTime($start_time);
    $log[] = '$start_time = ' . $start_time->format('Y-m-d H:i:s');
    $now = new DateTime();
    $log[] = '$now = ' . $now->format('Y-m-d H:i:s');
    //セール開始からの経過時間算出
    $interval = date_diff($start_time, $now);
    $log[] = '$interval = ' . $interval->format('%a days');
    $diff = $interval->format('%a');

    if($diff >= 7){
        //セール内容を更新。現在のセールフラグ初期化
        $log[] = 'SALE UPDATE';
        $sql = 'UPDATE vegetables SET sale=0';
        $pdo->query($sql);
        $log[] = 'SQL > ' . $sql;
        //野菜IDの配列作成
        $sql = 'SELECT vegetable_id FROM vegetables';
        $st = $pdo->query($sql);
        $log[] = 'SQL > ' . $sql;
        $veg_all_id = $st->fetchAll();
        $veg_all_id = array_column($veg_all_id, 'vegetable_id');
        $log[] = '$veg_all_id = ' . a($veg_all_id);
        //セール対象の数を乱数で生成
        $rand_int = count($veg_all_id) < 5 ? rand(2, count($veg_all_id)) : rand(2,5);
        //野菜ID配列よりセール対象にする野菜をランダムで決定
        $idx_set = array_rand($veg_all_id, $rand_int);
        //対象の野菜のセールフラグを立てる
        foreach($idx_set as $idx){
            $veg_id = $veg_all_id[$idx];
            $pdo->query('UPDATE vegetables SET sale=1 WHERE vegetable_id=' . $veg_id);
        }

        //現在の週の月曜日にあたる日付（セール開始日）を算出
        $now_week = $now->format('w');
        $log[] = '$now_week = ' . $now_week;
        //月曜日まで一日ずつ戻す
        while($now_week !== '1'){
            $now->modify('-1 days');
            $log[] = '$now modified = ' . $now->format('Y-m-d H:i:s');
            $now_week = $now->format('w');
        }
        //算出した日付の時刻をリセットし、文字列を取得
        $this_week_monday = $now;
        $this_week_monday->setTime(0,0,0);
        $set_time_str = $this_week_monday->format('Y-m-d H:i:s');
        $log[] = '$set_time_str = ' . $set_time_str;

        //今週のセール期日を算出する
        $limit_date = $this_week_monday->modify('+6 days')->format('n月j日');
        $log[] = '$limit_date = ' . $limit_date;
        //セール開始日と終了期日を更新する
        $pdo->query("UPDATE sale SET start_time='$set_time_str', limit_date='$limit_date'");
    }

    //セール商品を取得
    $st = $pdo->query('SELECT vegetable_id, name, weight, value FROM vegetables WHERE sale=1');
    $sale_veg_set = $st->fetchAll();
    shuffle($sale_veg_set);

    $log = implode('<br>', $log);

    return [$sale_veg_set, $limit_date, $log];
}



/**
*注文確認メール送信
*
*メールアドレスバリデーション後、SESSIONより作成した注文内容を送信する。
*@return array [メールアドレス, エラー, ログ]
*/
function sendEmail(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $error = $log = [];
    $total = 0;
    $body = "ご注文内容の確認\n\n";

    $log[] = '$_POST = ' . a($_POST);

    $email = filter_input(INPUT_POST, 'email');
    if(!$email){
        $error = implode('<br>', $error);
        $log = implode('<br>', $log);
        return [$email, $error, $log];
    }

    if(!isValidEmail($email, true)) $error[] = 'メールアドレスが不正です。';

    foreach($_SESSION['cart'] as $id => $num){
        $st = $pdo->prepare('SELECT name, value, sale FROM vegetables WHERE vegetable_id=?');
        $st->bindValue(1, $id, PDO::PARAM_INT);
        $st->execute();
        $veg_data = $st->fetch();
        if($veg_data['sale'] == 1) $veg_data['value'] *= DISCOUNT;
        $total += $veg_data['value'] * $num * TAX_IN;
        $body .= "商品名： {$veg_data['name']}\n単価： {$veg_data['value']}円\n数量： $num" . "個\n\n";
    }
    $body .= "合計金額(税込)：" . round($total, 0, PHP_ROUND_HALF_UP) . "円\n\n";
    $body .= "VEGETALISMをご利用いただきありがとうございました。";


    if(!$error){
        $from = "vegetalism@email.vegetalism.ml";
        $to = $email;
        if(mb_send_mail($to, "(架空)VEGETALISM 注文確認", $body, "FROM: $from")){
            header('Location: buy_complete.php');
            exit();
        }else{
            $error[] = 'メール送信に失敗しました。';
        }
    }
    $log[] = '$body = ' . $body;
    $log[] = '$_SESSION[cart] = ' . a($_SESSION['cart']);

    $error = implode('<br>', $error);
    $log = implode('<br>', $log);

    return [$email, $error, $log];
}
