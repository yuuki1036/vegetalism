<?php
session_start();

//環境設定

//DBアクセス情報選択 ['/local.php','/remote.php']
include_once(dirname(__FILE__) . '/local.php');
//ログ表示切り替え
const LOG_DISP_MODE = false;
//index.phpを開いたときに表示する野菜データのリストタイプ ['all':全ての野菜, 'season:旬の野菜']
const DATA_LIST_TYPE = 'season';
//詳細検索メニューの開閉状態 ['':close, '1':open]
$is_clps_open = '';
//現在の月取得
$season = date('M');
$month = (int)date('m');
//月変更テスト
//$testDate = new DateTime('2019-12-01');
//$season = $testDate->format('M');
//$month = (int)$testDate->format('m');


//消費税込
const TAX_IN = 1.08;
//セール品割引率
const DISCOUNT = 0.8;
//種類のリスト
const TYPE_LIST = ["果菜類", "葉菜類", "根菜類", "いも類", "きのこ", "その他"];
//`seasons`のフィールド名
const MONTH_LIST = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
//`cuisine`のフィールド名
const USE_LIST = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j'];




/**
*mySQL接続
*
*PDOオブジェクトを作成する。エラー時はエラーメッセージをHTMLに出力する。
*
*@param array $access アクセス権 ['PUBLIC_DB_ACCESS', 'MANAGE_DB_ACCESS']
*@return object PDOオブジェクト
*/
function connect($access){
    try{
        $pdo = new PDO(
            $access['setting'],
            $access['user_name'],
            $access['password'],
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::MYSQL_ATTR_MULTI_STATEMENTS => false,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            ]
        );
        return $pdo;
    }catch(PDOException $e){
        header('Content-Type: text/plain; charset=UTF-8', true, 500);
        exit($e->getMessage());
    }
}


/**
*LIKE句使用時のエスケープ
*
*@param string $str 置換対象文字列
*@return string 置換した文字列
*/
function escapeWildcard($str){
    return mb_ereg_replace('([_%#])', '#/1', $str);
}


/**
*HTML表示時のエスケープ処理
*
*htmlspecialcharsのラッパー関数
*
*@param string $str HTMLに出力したい文字列
*@return string エスケープした文字列
*/
function h($str){
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}


/**
*画像のパス作成
*
*対象idの絶対参照パスを作成する
*
*@param string $id 対象のid
*@param string $size 画像サイズ ['small', 'large'] default='small'
*@param string $type 画像の種類 ['vegetable', 'cuisine'] default='vegetable'
*/
function img_path($id, $size='small', $type='vegetable'){
    if($type === 'vegetable'){
        $check_name = $size === 'small' ? "sm-$id" : "lg-$id";
    }else{
        $check_name = "cu-$id";
    }
    $name = file_exists($_SERVER['DOCUMENT_ROOT'] . ROOT_NAME . '/images/' . $check_name . '.jpg') ? $check_name : 'noimage';
    return ROOT_NAME . '/images/' . $name . '.jpg';
}


/**
*配列を文字列に変換（ログ表示用）
*
*@param array $arr 二次元配列までOK
*@return string 文字列で表した配列の中身
*/
function a($arr){
    $str = '[ ';
    foreach($arr as $k1 => $v1){
        if(is_array($v1)){
            $str .= "'$k1' = > [ ";
            foreach($v1 as $k2 => $v2){
                $str .= "'$k2' > $v2, ";
            }
            $str .= " ], ";
        }else{
        $str .= "'$k1' => $v1, ";
        }
    }
    return $str . ' ]';
}


/**
*メールアドレスバリデーション
*
*$check_dnsがtrueならばDNSがの存在もチェックする。
*
*@param string $email メールアドレス
*@param bool DNSチェックの有無 default=false
*@return bool バリデーション結果
*/
function isValidEmail($email, $check_dns = false){
    switch (true) {
        case false === filter_var($email, FILTER_VALIDATE_EMAIL, FILTER_FLAG_EMAIL_UNICODE):
        case !preg_match('/@([^@\[]++)\z/', $email, $m):
            return false;

        case !$check_dns:
        case checkdnsrr($m[1], 'MX'):
        case checkdnsrr($m[1], 'A'):
        case checkdnsrr($m[1], 'AAAA'):
            return true;

        default:
            return false;
    }
}
