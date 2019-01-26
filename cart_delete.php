<?php
include_once(dirname(__FILE__) . '/common.php');
$id = filter_input(INPUT_GET, 'id');
if($id){
    if(preg_match('/\A[0-9]{1,10}\z/', $id)){
        if(array_key_exists($id, $_SESSION['cart'])){
            unset($_SESSION['cart'][$id]);
            header('Location: cart.php');
        }else{
            die('指定の商品はカート内に存在しません。');
        }
    }else{
        die('不正な値です。');
    }
}else{
    die('値がありません。');
}
