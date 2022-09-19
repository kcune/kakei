<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../const/Const.php');

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
    // if(!Util::isToken($_POST['token'])){

    //     //エラーメッセージ
    //     $_SESSION['error'][] = ACCESS_FAILER;
    //     //ログイン画面に移動
    //     header('Location:./login.php');
    //     exit;
        
    // }

    //トークン取得
    $token = Util::getToken();

    //画面がアカウント作成画面に戻った時用
    $_SESSION['user_id'] = $_POST['user_id'];
    $_SESSION['from'] = 'account_add_confirm';

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

    //パスワード確認用
    if(empty($_POST['password_confirm'])){

        //エラーメッセージ
        $_SESSION['error'][] = NO_PASSWORD;
    }

    //エラーメッセージがあれば次の処理に飛ばない
    if(isset($_SESSION['error'])){

        //アカウント作成画面に移動
        header('Location:./account_add.php');
        exit;

    }

    //文字数チェック
    //アカウント名
    if(mb_strlen($_POST['user_id'])>50){

        //エラーメッセージ
        $_SESSION['error'][] = LOT_OF_WORD_ACCOUNT;

    }

    //パスワード
    if(mb_strlen($_POST['password'])>255){

        //エラーメッセージ
        $_SESSION['error'][] = LOT_OF_WORD_PASS;

    }

    //パスワード確認用
    if(mb_strlen($_POST['password_confirm'])>255){

        //エラーメッセージ
        $_SESSION['error'][] = LOT_OF_WORD_PASS;

    }

    //エラーメッセージがあれば次の処理に飛ばない
    if(isset($_SESSION['error'])){

        //アカウント作成画面に移動
        header('Location:./account_add.php');
        exit;

    }

    //パスワードとパスワードの確認が不一致
    if($_POST['password'] != $_POST['password_confirm']){

        //エラーメッセージ
        $_SESSION['error'][] = PASS_NOT_MATCH;

        //リダイレクト
        header('Location:./account_add.php');
        exit;

    }


?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/heart.png" id="favicon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Web家計簿 アカウント作成</title>
    <style>

    .navmoji{
        margin-left: 30px;
        font-size:large;
    }

    .formgroup{
        margin-top: 20px;
        margin-bottom: 20px;
    }

    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">アカウント作成 確認</span>
        </div>
    </nav>
    <!-- ナビゲーション ここまで --> 
    
    <div class="container" style="width: 800px;">
        
        <!-- エラーメッセージ ここから --> 
        <?php if(isset($_SESSION['error'])): ?>            
            <div class="row my-2">
                <div class="col-sm-3"></div>
                <div class="col-sm-6 alert alert-danger alert-dismissble fade show">
                    <?php foreach($_SESSION['error'] as $r): ?>
                        <?php echo $r.'<br>'; ?>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
                <div class="col-sm-3"></div>
            </div>
        <?php endif; ?>
        <!-- エラーメッセージ ここまで -->

        <!-- 入力フォーム ここから -->
        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <form action="./account_add_action.php" method="post">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <!-- アカウント名 -->
                    <div class="form-group formgroup">
                        <label for="user">アカウント名</label><br><br>
                        <?php echo $_POST['user_id']; ?>
                        <input type="hidden" class="form-control" id="user_id" name="user_id" value="<?php echo $_POST['user_id']; ?>">
                    </div>
                    <!-- パスワード -->
                    <div class="form-group formgroup">
                        <label for="password">パスワード</label><br><br>
                        <?php echo $_POST['password']; ?>
                        <input type="hidden" class="form-control" id="password" name="password" value="<?php echo $_POST['password']; ?>">
                    </div>
                    <!-- パスワード -->
                    <div class="form-group formgroup">
                        <label for="password">パスワード</label><br><br>
                        <?php echo $_POST['password']; ?>
                        <input type="hidden" class="form-control" id="password" name="password" value="<?php echo $_POST['password']; ?>">
                    </div>
                    <!-- 権限 -->
                    <div class="form-group formgroup">
                        <label for="admin_flg">権限</label><br><br>
                        <?php if($_POST['admin_flg']==0){echo '利用者';}else{echo '管理者';} ?>
                        <input type="hidden" class="form-control" id="admin_flg" name="admin_flg" value="<?php echo $_POST['admin_flg']; ?>">
                    </div>
                    <!-- ボタン -->
                    <button type="submit" class="btn btn-success">アカウント作成</button>
                    <a href="./account_add.php" class="btn btn-success">戻る</a>
                </form>

            </div>
            <div class="col-sm-3"></div>
        </div>
        <!-- 入力フォーム ここまで -->

    </div>
</body>
</html>