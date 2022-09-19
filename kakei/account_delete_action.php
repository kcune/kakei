<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../db/Db.php');
    require_once('../db/Account.php');

    //セッション開始
    Util::sessionStart();

    //ここから画面を開けないようにする
    if(!Util::isLogin()){
        
        //ログイン画面に移動
        header('Location:./login.php');
        exit;

    }

    //サニタイズ
    $_POST = Util::sani($_POST);
    
    //不正アクセス防止
    if(!Util::isToken($_POST['token'])){

        //エラーメッセージ
        $_SESSION['error'][] = ACCESS_FAILER;
        //ログイン画面に移動
        header('Location:./login.php');
        exit;
        
    }

    //画面遷移用
    $_SESSION['from'] = 'account_delete_action';

    //必須入力チェック
    //アカウント名
    if(empty($_SESSION['user_id'])){

        //エラーメッセージ
        $_SESSION['error'][] = NO_USER;

    }

    //エラーメッセージがあれば次の処理に飛ばない
    if(isset($_SESSION['error'])){

        //アカウント作成画面に移動
        header('Location:./account_delete.php');
        exit;

    }

    //文字数チェック
    //アカウント名
    if(mb_strlen($_SESSION['user_id'])>32){

        //エラーメッセージ
        $_SESSION['error'][] = LOT_OF_WORD_ACCOUNT;

    }

    
    //エラーメッセージがあれば次の処理に飛ばない
    if(isset($_SESSION['error'])){
        
        //アカウント作成画面に移動
        header('Location:./account_delete.php');
        exit;

    }
    
    try{
        
        //データベース接続
        $db = new Account;
        
        $is_admin = $db->is_admin($_SESSION['user_id']);
        $dbs = $db->admin_count();
    
        //管理者が一人のとき管理者は退会できない
        if ($is_admin && $dbs[0]['cnt']==1){
    
            //エラーメッセージ
            $_SESSION['error'][] = ACCOUNT_DELETE_FAILER;
    
        }
    
        //エラーメッセージがあれば次の処理に飛ばない
        if(isset($_SESSION['error'])){
            
            //アカウント作成画面に移動
            header('Location:./account_delete.php');
            exit;

        }

        //該当アカウントをテーブル全てからアカウント情報を削除する
        $db->deleteAllTables($_SESSION['user_id']);

        //アカウント作成画面に移動
        header('Location:./login.php');
        exit;


    }catch(Exception $e){

        //エラーメッセージ
        $_SESSION['error'][] = DB_ERR;

        //アカウント作成画面に移動
        header('Location:./error.php');
        exit;

    }










?>