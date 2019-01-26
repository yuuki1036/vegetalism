<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once(dirname(__FILE__) . '/../common.php');
$title = '野菜管理';
$pageName = '野菜管理画面';


/**
*全野菜データ取得
*
*@return array 野菜データ連想配列
*/
function getManageVegetableData(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $st = $pdo->query("SELECT * FROM vegetables");
    $mg_veg_set = $st->fetchAll();
    return $mg_veg_set;
}


/**
*野菜データ作成
*
*POSTがあればチェック後、テーブル`vegetables`にINSERT文を発行する。
*旬の登録があれば、テーブル`seasons`にINSERT文を発行する。
*
*@return array [新規野菜データ連想配列, エラーメッセージ, ログ]
*/
function createVegetableData(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $error = $log = '';

    if($_POST){
        //NOT NULLカラムだけチェック
        if(!$_POST['name']) $error .= '商品名が入力されていません。<br>';
        if(!$_POST['type']) $error .= '種類を選択していません。<br>';
        if(preg_match('/\D/', $_POST['type'])) $error .= '種類が不正です。<br>';
        if(!$_POST['weight']) $error .= '重さが入力されていません。<br>';
        if(preg_match('/\D/', $_POST['weight'])) $error .= '重さが不正です。<br>';
        if(!$_POST['value']) $error .= '価格が入力されていません。<br>';
        if(preg_match('/\D/', $_POST['value'])) $error .= '価格が不正です。<br>';

        //旬の入力があれば、返り値から要素数１２の配列を作成する
        $season = array_fill(1, 12, 0);
        if(isset($_POST['season'])){
            foreach($_POST['season'] as $month) $season[$month] = 1;
            $_POST['season'] = $season;
        }

        if(!$error){
            //`vegetables`に対するINSERT文作成・実行
            $v_field = $v_val = '';
            foreach($_POST as $key => $v){
                if($v && is_string($v)){
                    $v_field .= "`$key`, ";
                    $v_val .= "'$v', ";
                }
            }
            $v_field = substr($v_field, 0, -2);
            $v_val = substr($v_val, 0, -2);
            $pdo->query("INSERT INTO `vegetables` ( $v_field ) VALUES ( $v_val )");

            //付与されたIDを取得する
            $st = $pdo->query("SELECT `vegetable_id` FROM `vegetables` WHERE `name`='{$_POST['name']}'");
            $data = $st->fetch();
            //`seasons`に対するINSERT文作成・実行
            $s_field = '`season_id`, ';
            $s_val = "'{$data['vegetable_id']}', ";
            foreach(MONTH_LIST as $idx => $v){
                if($season[$idx+1] == 1){
                    $s_field .= "`$v`, ";
                    $s_val .= "'1', ";
                }
            }
            $s_field = substr($s_field, 0, -2);
            $s_val = substr($s_val, 0, -2);
            $pdo->query("INSERT INTO `seasons` ( $s_field ) VALUES ( $s_val )");
            header('Location: index.php');
            exit();
        }
    }
    $log .= '$_POST = ' . a($_POST) . '<br>';
    $cr_veg_data = $_POST;
    return [$cr_veg_data, $error, $log];
}


/**
*野菜データ修正
*
*GETから得たidに対応する旬データを取得して修正画面を作る。
*POSTがあれば、チェック後にテーブル`vegetables`・`seasons`に対するUPDATE文を発行する。
*
*@return array [野菜データ連想配列, エラーメッセージ, ログ]
*/
function editVegetableData(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $error = $log = '';
    $log .= '$_POST = ' . a($_POST) . '<br>';

    if($_POST){
        //NOT NULLカラムだけチェック
        if(!$_POST['name']) $error .= '商品名が入力されていません。<br>';
        if(!$_POST['type']) $error .= '種類を選択していません。<br>';
        if(preg_match('/\D/', $_POST['type'])) $error .= '種類が不正です。<br>';
        if(!$_POST['weight']) $error .= '重さが入力されていません。<br>';
        if(preg_match('/\D/', $_POST['weight'])) $error .= '重さが不正です。<br>';
        if(!$_POST['value']) $error .= '価格が入力されていません。<br>';
        if(preg_match('/\D/', $_POST['value'])) $error .= '価格が不正です。<br>';

        if(!$error){
            //`vegetables`に対するUPDATE文発行
            $sql = "UPDATE `vegetables` SET ";
            foreach($_POST as $fld => $val) {
                if($fld == 'season' || $fld == 'id') continue;
                $sql .= "`$fld`='$val', ";
            }
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE `vegetable_id`='{$_POST['id']}'";
            $pdo->query($sql);

            if(isset($_POST['season'])){
                //`seasons`に対するUPDATE文作成・実行
                $update = '';
                for($i=1; $i<=12; $i++){
                    $update .= in_array($i, $_POST['season']) ? "`" . MONTH_LIST[$i-1] . "`='1', " : "`" . MONTH_LIST[$i-1] . "`=NULL, ";
                }
                $update = substr($update, 0, -2);
                $sql = "UPDATE `seasons` SET $update WHERE `season_id`='{$_POST['id']}'";
                $log .= "SQL>seasons_update:<br>$sql<br>";
                $pdo->query($sql);
                header('Location: index.php');
                exit();
            }
        }
    }
    $id = $_GET['id'];
    //idより修正野菜データ・旬データ取得
    $st1 = $pdo->query("SELECT * FROM vegetables WHERE vegetable_id='$id'");
    $st2 = $pdo->query("SELECT * FROM seasons WHERE season_id='$id'");
    $ed_veg_data = $st1->fetch();
    $season = $st2->fetch();
    $season = array_values($season);
    $ed_veg_data['season'] = $season;
    $log .= '$ed_veg_data = ' . a($ed_veg_data) . '<br>';

    return [$ed_veg_data, $error, $log];
}


//画像アップロード (true->アイコン用, false->詳細画像)
function uploadImage($size_bl){
    $pdo = connect(MANAGE_DB_ACCESS);
    $error = $log = '';
    $log .= '$_POST = ' . a($_POST) . '<br>';

    if($_POST){
        $id = $_POST['id'];
        $name = $size_bl ? 'sm-' . $id : 'lg-' . $id;
        $path = '../images/' . $name . '.jpg';
        if(move_uploaded_file($_FILES['pic']['tmp_name'], "$path")){
            header('Location: index.php');
            exit();
        }else{
            $error .= 'ファイルを選択してください。<br>';
        }
    }else{
        $id = $_GET['id'];
    }
    return [$id, $error, $log];
}


/**
*野菜データ削除
*
*削除する野菜を食材登録している料理があれば、その料理データの該当野菜idをNULLにする。
*テーブル`vegetables`の一致するidに対してDELETE文を発行する。
*テーブル`seasons`の一致するidに対してDELETE文を発行する。
*/
function deleteVegetableData(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $log = '';
    $id = $_GET['id'];
    //野菜idを食材登録している料理を探す
    $st = $pdo->query("SELECT * FROM `cuisine` WHERE
        `a`='$id' OR `b`='$id' OR `c`='$id' OR `d`='$id' OR `e`='$id' OR
        `f`='$id' OR `g`='$id' OR `h`='$id' OR `i`='$id' OR `j`='$id'");
    $incData = $st->fetchAll();
    $log .= '$incData = ' . a($incData) . '<br>';

    if($incData){
        //見つかった料理データに対して1回ずつUPDATE文を発行
        foreach($incData as $item){
            $sql = "UPDATE `cuisine` SET ";
            foreach($item as $fld => $val){
                if($fld != 'cuisine_id'){
                    $sql .= ($val == $id || $val == '') ? "`$fld`=NULL, " : "`$fld`='$val', ";
                }
            }
            $sql = substr($sql, 0, -2);
            $sql .= " WHERE `cuisine_id`='{$item['cuisine_id']}'";
            $log .= "SQL>cuisine_update:<br>$sql<br>";
            $pdo->query($sql);
        }
    }
    $pdo->query("DELETE FROM `vegetables` WHERE `vegetable_id`='$id'");
    $pdo->query("DELETE FROM `seasons` WHERE `season_id`='$id'");
    header('Location: index.php');
    exit();
}
