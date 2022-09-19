<?php

//ファイルの読み込み
require_once('../util/Utl.php');
require_once('../const/Const.php');
require_once('../db/Db.php');
require_once('../db/Subject.php');

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
$_SESSION['from'] = 'Subject_update_action';

//必須入力チェック
//アカウント名
if (empty($_POST['account'])) {
    
    //エラーメッセージ
    $_SESSION['error'][] = NO_ACCOUNT;
}

//計算フラグ
if (is_null($_POST['calc_flg'])) {

    //エラーメッセージ
    $_SESSION['error'][] = NO_CALC_FLG;
}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./subject_add.php');
    exit;
}

//文字数チェック
//科目名
if (mb_strlen($_POST['account']) > 32) {

    //エラーメッセージ
    $_SESSION['error'][] = LOT_OF_WORD_SUBJECT;
}


//計算フラグは数字のみ
if (!is_numeric($_POST['calc_flg'])){

    //エラーメッセージ
    $_SESSION['error'][] = NOT_NUMBER;

}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./account_add.php');
    exit;
}

try {

    //データベース接続
    $db = new Subject;


    //アカウント情報を更新する
    $db->updateSubject($_POST['id'], $_POST['account'],$_POST['calc_flg']);

    //アカウント作成画面に移動
    header('Location:./subject_list.php');
    exit;

} catch (Exception $e) {

    //エラーメッセージ
    $_SESSION['error'][] = DB_ERR;

    //アカウント作成画面に移動
    header('Location:./error.php');
    exit;
}
