<?php

    //ファイル読み込み
    require_once('../util/Utl.php');
    require_once('../const/Const.php');
    require_once('../db/Db.php');
    require_once('../db/Login.php');

    //セッション開始
    Util::sessionStart();

    //サニタイズ
    $_POST = Util::sani($_POST);

    //不正なトークンのチェック
    if(!Util::isToken($_POST['token'])){

        //エラーメッセージ
        $_SESSION['error'][] = ACCESS_FAILER;
        
    }

    //3回ログインに失敗するとログイン出来なくなる
    if(!isset($_SESSION['cnt'])){
        $_SESSION['cnt'] = 1;
    }elseif($_SESSION['cnt']>3){
        
        //エラーメッセージ
        $_SESSION['error'][] = LOGIN_LIMIT;

    }else{
        $_SESSION['cnt']++;
    }

    //必須入力チェック
    //アカウント名
    if(empty($_POST['user_id'])){
        
        //エラーメッセージ
        $_SESSION['error'][] = NO_USER;

    }

    //パスワード
    if(empty($_POST['password'])){
        
        //エラーメッセージ
        $_SESSION['error'][] = NO_PASSWORD;

    }
    
    //エラーがあればログイン画面に戻る
    if(isset($_SESSION['error'])){
        
        //ログイン画面に移動
        header('Location:./login.php');
        exit;

    }

    try{

        //データベース接続
        $db = new Login;

        //パスワードが一致しているか？
        if(!$db->is_match($_POST['user_id'],$_POST['password'])){

            //エラーメッセージ
            $_SESSION['error'][] = LOGIN_FAILER;
            //ログイン画面にリダイレクト
            header('Location:./login.php');
            exit;

        }

        //エラーカウンタクリア
        unset($_SESSION['cnt']);

        //ここからしか入れないように
        $_SESSION['login'] = 'login';

        //ユーザーIDを取得してセッションに格納
        $user_id = $db->getUserID($_POST['user_id']);
        $_SESSION['user_id'] = $user_id[0]['id'];

        //メニュー画面にリダイレクト
        $dbs = $db->getAdminFlg($user_id[0]['id']);
        if ($dbs[0]['admin_flg']==0){
            header('Location:./menu_user.php');
        }else{
            header('Location:./menu_admin.php');
        }
        exit;

    }catch(Exception $e){

        //エラーメッセージ
        $_SESSION['error'][] = DB_ERR;
        //エラー画面に移動
        header('Location:./error.php');
        exit;        

    }

?>