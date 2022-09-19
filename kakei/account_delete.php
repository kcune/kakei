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

    //トークン取得
    $token = Util::getToken();

    try{

        //データベース接続
        $db = new Account;

        //アカウント情報取得
        $rec = $db->selectAccount($_SESSION['user_id']);

    }catch(Exception $e){

        //エラーメッセージ
        $_SESSION['error'][] = DB_ERR;
        //ログイン画面に移動
        header('Location:./error.php');
        exit;

    }

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/doll.jpg" id="favicon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Web家計簿 退会</title>
    <style>

    .navmoji{
        margin-left: 30px;
        font-size:large;
    }

    .formgroup{
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .sengen{
        margin: 150px 30px;
    }

    .botan{
        margin: 150px 30px;
    }

    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">退会</span>
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
                <form action="./account_delete_action.php" method="post">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <!-- 退会の確認 -->
                    <div class="sengen">
                        <?php echo $rec[0]['user_name']; ?>さん　本当に退会されますか？
                    </div>
                    <!-- ボタン -->
                    <div class="botan">
                        <button type="submit" class="btn btn-success">退会</button>
                        <a href="javascript:history.back()" class="btn btn-success">戻る</a>
                    </div>
                </form>

            </div>
            <div class="col-sm-3"></div>
        </div>
        <!-- 入力フォーム ここまで -->

    </div>
</body>
</html>