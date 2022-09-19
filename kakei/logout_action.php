<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');

    //セッション開始
    Util::sessionStart();

    //セッション情報を削除する
    unset($_SESSION['login']);

    //ログイン画面にリダイレクト
    header('Location:./login.php');
    exit;

?>