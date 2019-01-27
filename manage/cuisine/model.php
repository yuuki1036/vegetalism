<?php
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");

include_once(dirname(__FILE__) . '/../../common.php');

$title = '料理管理';
$pageName = '料理管理画面';


/**
*全料理データ取得
*
*@return array 料理データ連想配列
*/
function getManageCuisineData(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $st = $pdo->query("SELECT * FROM `cuisine`");
    $mg_cuis_set = $st->fetchAll();
    return $mg_cuis_set;
}


/**
*料理データ作成
*
*野菜データを取得して新規作成画面を作る。
*POSTがあればチェック後、テーブル`cuisine`にINSERT文を発行する。
*
*@return array [野菜データ連想配列, 新規料理データ連想配列, エラーメッセージ, ログ]
*/
function createCuisineData(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $error = $log = '';
    $log .= '$_POST = ' . a($_POST) . '<br>';

    if($_POST){
        //`name`はNOT NULLのためチェック
        if(!$_POST['name']) $error .= '料理名が入力されていません。<br>';
        //食材登録用のフィールドは１０個
        if(isset($_POST['cuisine']) && count($_POST['cuisine']) > 10) $error .= '食材が１０個を超えています。';

        if(!$error){
            //テーブル`cuisine`に対するINSERT文発行
            $field = "`name`, ";
            $values = "'{$_POST['name']}', ";
            if(isset($_POST['cuisine'])){
                foreach($_POST['cuisine'] as $idx => $veg_id){
                    $field .= "`" . USE_LIST[$idx] . "`, ";
                    $values .= "'$veg_id', ";
                }
            }
            $field = substr($field, 0, -2);
            $values = substr($values, 0, -2);
            $sql = "INSERT INTO `cuisine` ( $field ) VALUES ( $values )";
            $log .= 'SQL ><br>' . $sql . '<br>';
            $pdo->query($sql);
            header('Location: index.php');
            exit();
        }
    }
    //チェックボックス作成に使う野菜データ取得
    $st = $pdo->query("SELECT `vegetable_id`, `name` FROM `vegetables`");
    $veg_name_set = $st->fetchAll();
    $log .= '$veg_name_set = ' . a($veg_name_set) . '<br>';

    $cr_cuis = $_POST;
    return [$veg_name_set, $cr_cuis, $error, $log];
}


/**
*料理データ修正
*
*野菜データと、GETから得たidに対応する料理データを取得して修正画面を作る。
*POSTがあれば、チェック後にテーブル`cuisine`に対するUPDATE文を発行する。
*
*@return array [野菜データ連想配列, 料理データ連想配列, エラーメッセージ, ログ]
*/
function editCuisineData(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $error = $log = '';
    $log .= '$_POST = ' . a($_POST) . '<br>';

    if($_POST){
        //`name`はNOT NULLのためチェック
        if(!$_POST['name']) $error .= '料理名が入力されていません。<br>';
        //食材登録用のフィールドは１０個
        if(isset($_POST['cuisine']) && count($_POST['cuisine']) > 10) $error .= '食材が１０個を超えています。';

        if(!$error){
            //テーブル`cuisine`に対するUPDATE文発行
            $set = "`name`='{$_POST['name']}', ";
            for($i=0; $i<10; $i++){
                if(isset($_POST['cuisine'][$i])){
                    $set .= "`" . USE_LIST[$i] ."`='" . $_POST['cuisine'][$i] . "', ";
                }else{
                    $set .= "`" . USE_LIST[$i] . "`=NULL, ";
                }
            }
            $set = substr($set, 0, -2);
            $sql = "UPDATE `cuisine` SET $set WHERE `cuisine_id`='{$_POST['id']}'";
            $log .= 'SQL>cuisine_update:<br>' . $sql . '<br>';
            $pdo->query($sql);
            header('Location: index.php');
            exit();
        }
    }
    //チェックボックス作成に使う野菜データ取得
    $st = $pdo->query("SELECT `vegetable_id`, `name` FROM `vegetables`");
    $veg_name_set = $st->fetchAll();
    $log .= '$veg_name_set = ' . a($veg_name_set) . '<br>';
    //料理データを取得し、修正データを作成する
    $id = $_GET['id'];
    $st = $pdo->query("SELECT * FROM `cuisine` WHERE `cuisine_id`='$id'");
    $cuis_data = $st->fetch();
    $ed_cuis = ['cuisine_id' => $cuis_data['cuisine_id'], 'name' => $cuis_data['name'], 'cuisine' => []];
    foreach(USE_LIST as $no){
        if($cuis_data[$no]) $ed_cuis['cuisine'][] = $cuis_data[$no];
    }
    $log .= '$ed_cuis = ' . a($ed_cuis) . '<br>';

    return [$veg_name_set, $ed_cuis, $error, $log];
}


/**
*料理画像登録
*
*formから送られた画像データをidから作成したファイル名を付けて保存する。
*
*@return array [料理id, エラーメッセージ, ログ]
*/
function uploadCuisineImage(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $error = $log = '';
    $log .= '$_POST = ' . a($_POST) . '<br>';

    if($_POST){
        //idよりファイル名作成
        $id = $_POST['id'];
        $path = $_SERVER['DOCUMENT_ROOT'] . ROOT_NAME . '/images/cu-' . $id . '.jpg';
        $log .= '$path = ' . $path . '<br>';
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
*料理データ削除
*/
function deletecuis_data(){
    $pdo = connect(MANAGE_DB_ACCESS);
    $id = $_GET['id'];
    $pdo->query("DELETE FROM `cuisine` WHERE `cuisine_id`='$id'");
    header('Location: index.php');
    exit();
}
