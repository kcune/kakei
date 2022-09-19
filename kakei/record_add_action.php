<?php

//ファイルの読み込み
require_once('../util/Utl.php');
require_once('../const/Const.php');
require_once('../db/Db.php');
require_once('../db/Money.php');

//セッション開始
Util::sessionStart();

//ここから画面を開けないようにする
if (!Util::isLogin()) {

    //ログイン画面に移動
    header('Location:./login.php');
    exit;
}

//サニタイズ
$_POST = Util::sani($_POST);

//不正アクセス防止
if (!Util::isToken($_POST['token'])) {

    //エラーメッセージ
    $_SESSION['error'][] = ACCESS_FAILER;
    //ログイン画面に移動
    header('Location:./login.php');
    exit;
}

//画面遷移用
$_SESSION['spent_date'] = $_POST['spent_date'];
$_SESSION['account'] = $_POST['account'];
$_SESSION['description'] = $_POST['description'];
$_SESSION['shop'] = $_POST['shop'];
$_SESSION['debit'] = $_POST['debit'];
$_SESSION['credit'] = $_POST['credit'];
$_SESSION['from'] = 'Record_add_action';

//必須入力チェック
//アカウント名
if (empty($_POST['spent_date'])) {
    
    //エラーメッセージ
    $_SESSION['error'][] = NO_DATE;
}

//勘定科目
if (empty($_POST['account'])) {

    //エラーメッセージ
    $_SESSION['error'][] = NO_ACCOUNT;
}

//摘要
if (empty($_POST['debit']) && empty($_POST['credit'])) {

    //エラーメッセージ
    $_SESSION['error'][] = NO_MONEY;
}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./record_add.php');
    exit;
}

//文字数チェック
//日付
if (mb_strlen($_POST['spent_date']) != 10) {

    //エラーメッセージ
    $_SESSION['error'][] = DATE_FAILER;
}

//摘要
if (mb_strlen($_POST['description']) > 32) {

    //エラーメッセージ
    $_SESSION['error'][] = LOT_OF_WORD_DESCRIPTION;
}

//店名
if (mb_strlen($_POST['shop']) > 32) {

    //エラーメッセージ
    $_SESSION['error'][] = LOT_OF_WORD_SHOP;
}

//数字のみ入力チェック
//勘定科目
if (!is_numeric($_POST['account'])){

    //エラーメッセージ
    $_SESSION['error'][] = NOT_NUMBER;

}

//借方
if (!is_numeric($_POST['debit']) && !isset($_POST['debit'])){

    //エラーメッセージ
    $_SESSION['error'][] = NOT_NUMBER;

}

//貸方
if (!is_numeric($_POST['credit']) && !isset($_POST['credit'])){

    //エラーメッセージ
    $_SESSION['error'][] = NOT_NUMBER;

}

//日付チェック
if(preg_match('/\A[0-9]{4}-[0-9]{1,2}-[0-9]{1,2}\z/', $_POST['spent_date']) == false)
{
    //エラーメッセージ
    $_SESSION['error'][] = DATE_FAILER;

}

list($year, $month, $day) = explode('-', $_POST['spent_date']);

if(checkdate($month, $day, $year) == false)
{
    //エラーメッセージ
    $_SESSION['error'][] = DATE_FAILER;
}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./record_add.php');
    exit;
}

// try {

    //データベース接続
    $db = new Money;

    //Nullは入れない
    if (empty($_POST['debit'])){
        $_POST['debit'] = 0;
    }
    if (empty($_POST['credit'])){
        $_POST['credit'] = 0;
    }

    //アカウント情報を追加する
    $db->insertMoney($_SESSION['user_id'],$_SESSION['medium_id'],$_POST['spent_date'],$_POST['account'],$_POST['description'],$_POST['shop'],$_POST['debit'],$_POST['credit']);

    //アカウント作成画面に移動
    if ($_SESSION['menu_id']=='PC'){
        header('Location:./record_list.php');
    }else{
        header('Location:./record_phone.php');
    }
    exit;

// } catch (Exception $e) {

//     //エラーメッセージ
//     $_SESSION['error'][] = DB_ERR;

//     //アカウント作成画面に移動
//     header('Location:./error.php');
//     exit;
// }
