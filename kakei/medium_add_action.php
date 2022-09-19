<?php

//ファイルの読み込み
require_once('../util/Utl.php');
require_once('../const/Const.php');
require_once('../db/Db.php');
require_once('../db/Medium.php');

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
$_SESSION['medium'] = $_POST['medium'];
$_SESSION['from'] = 'Medium_add_action';

//必須入力チェック
//アカウント名
if (empty($_POST['medium'])) {
    
    //エラーメッセージ
    $_SESSION['error'][] = NO_MEDIA;
}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./medium_add.php');
    exit;
}

//文字数チェック
//科目名
if (mb_strlen($_POST['medium']) > 32) {

    //エラーメッセージ
    $_SESSION['error'][] = LOT_OF_WORD_MEDIA;
}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./account_add.php');
    exit;
}

try {

    //データベース接続
    $db = new Medium;

    //アカウント情報を追加する
    $db->insertMedium($_SESSION['user_id'], $_POST['medium']);

    //アカウント作成画面に移動
    header('Location:./medium_list.php');
    exit;

} catch (Exception $e) {

    //エラーメッセージ
    $_SESSION['error'][] = DB_ERR;

    //アカウント作成画面に移動
    header('Location:./error.php');
    exit;
}
