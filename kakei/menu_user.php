<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');

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

    //画面遷移用
    $_SESSION['from'] = 'menu';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/doll.jpg" id="favicon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Web家計簿 利用者用メニュー</title>
    <style>

    .navmoji{
        margin-left: 30px;
        font-size:large;
    }

    .formgroup{
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .btn-default {
        width: 200px;
        margin: 5px;
    }

    table{
        margin-top: 20px;
        margin-left: auto;
        margin-right: auto;
    }

    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">利用者用メニュー</span>
        </div>
    </nav>
    <!-- ナビゲーション ここまで --> 
    
    <div class="container">
        
        <!-- エラーメッセージ ここから --> 
        <?php if(isset($_SESSION['error'])): ?>            
            <div class="row my-2">
                <!-- <div class="col-sm-3"></div> -->
                <div class="center-block alert alert-danger alert-dismissble fade show">
                    <?php foreach($_SESSION['error'] as $r): ?>
                        <?php echo $r.'<br>'; ?>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
                <!-- <div class="col-sm-3"></div> -->
            </div>
        <?php endif; ?>
        <!-- エラーメッセージ ここまで -->

        <!-- 入力フォーム ここから -->
        <div class="row my-2">
            <!-- <div class="col-sm-4"></div> -->
            <div class="center-block mb-6">
                <table>
                    <tr>
                        <!-- PCタブレット用 -->
                        <td>
                            <a href="./menu.php" class="btn btn-success btn-default">PC・タブレット用</a>
                        </td>
                    </tr>
                    <tr>
                        <!-- スマホ用 -->
                        <td>
                            <a href="./menu_phone.php" class="btn btn-success btn-default">スマートフォン用</a>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- <div class="col-sm-4"></div> -->
        </div>
        <!-- 入力フォーム ここまで -->
    </div>
</body>
</html>