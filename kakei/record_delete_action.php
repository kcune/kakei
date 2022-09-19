<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../db/Db.php');
    require_once('../db/Money.php');

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
    $_SESSION['from'] = 'Record_delete_action';

    //必須入力チェック
    //id
    if(empty($_POST['id'])){

        //エラーメッセージ
        $_SESSION['error'][] = NO_MEDIA;

    }

    //エラーメッセージがあれば次の処理に飛ばない
    if(isset($_SESSION['error'])){

        //アカウント作成画面に移動
        if ($_SESSION['menu_id']=='PC'){
            header('Location:./record_list.php');
        }else{
            header('Location:./record_phone.php');
        }
        exit;

    }
    
    // try{
        
        //データベース接続
        $db = new Money;

        //該当アカウントをテーブル全てからアカウント情報を削除する
        $db->deleteMoney($_SESSION['user_id'],$_POST['medium_id'],$_POST['id'],$_POST['spent_date']);

        //アカウント作成画面に移動
        if ($_SESSION['menu_id']=='PC'){
            header('Location:./record_list.php');
        }else{
            header('Location:./record_phone.php');
        }
        exit;
    

    // }catch(Exception $e){

    //     //エラーメッセージ
    //     $_SESSION['error'][] = DB_ERR;

    //     //アカウント作成画面に移動
    //     header('Location:./error.php');
    //     exit;

    // }










?>