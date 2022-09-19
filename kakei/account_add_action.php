<?php

//ファイルの読み込み
require_once('../util/Utl.php');
require_once('../const/Const.php');
require_once('../db/Db.php');
require_once('../db/Account.php');

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
$_SESSION['user_id'] = $_POST['user_id'];
$_SESSION['from'] = 'account_add_action';


//必須入力チェック
//アカウント名
if (empty($_POST['user_id'])) {

    //エラーメッセージ
    $_SESSION['error'][] = NO_USER;
}

//パスワード
if (empty($_POST['password'])) {

    //エラーメッセージ
    $_SESSION['error'][] = NO_PASSWORD;
}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./account_add.php');
    exit;
}

//文字数チェック
//アカウント名
if (mb_strlen($_POST['user_id']) > 32) {

    //エラーメッセージ
    $_SESSION['error'][] = LOT_OF_WORD_ACCOUNT;
}

//パスワード
if (mb_strlen($_POST['password']) > 255) {

    //エラーメッセージ
    $_SESSION['error'][] = LOT_OF_WORD_PASS;
}

//エラーメッセージがあれば次の処理に飛ばない
if (isset($_SESSION['error'])) {

    //アカウント作成画面に移動
    header('Location:./account_add.php');
    exit;
}

// try {

    //データベース接続
    $db = new Account;

    //既にアカウント名が存在しているか？
    if ($db->is_exist_account($_POST['user_id'])) {

        //エラーメッセージ
        $_SESSION['error'][] = EXIST_ACCOUNT;
        //アカウント作成画面に移動
        header('Location:./account_add.php');
        exit;
    }

    //アカウント情報を追加する
    $db->insertAccount($_POST['user_id'], $_POST['password'],$_POST['admin_flg']);

    //アカウント作成画面に移動
    header('Location:./login.php');
    exit;

// } catch (Exception $e) {

//     //エラーメッセージ
//     $_SESSION['error'][] = DB_ERR;

//     //アカウント作成画面に移動
//     header('Location:./account_add.php');
//     exit;
// }
