<?php
    
    //ファイルの読み込み
    require_once('../const/Const.php');
    require_once('../util/Utl.php');

    //セッション開始
    Util::sessionStart();

?>
<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv="content-type" content="text/html; charset=utf-8">
    <title>Web家計簿 エラー</title>
    <link rel="stylesheet" href="../css/bootstrap.min.css">
    <link rel="icon" href="../img/doll.jpg" id="favicon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <style>
    
    .navmoji{
        margin-left: 30px;
        font-size:large;
    }

    </style>
</head>

<body>
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">エラー</span>
        </div>
    </nav>

    <div class="container" >
        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-3">
                <h1></h1>
            </div>
            <div class="col-sm-3"></div>
        </div>

        <div class="row my-2">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <!-- エラーメッセージ -->
                <div class="row my-2">
                    <div class="col-sm-2"></div>
                    <div class="col-sm-8 alert alert-danger alert-dismissble fade show">
                        <!-- エラー表示 -->
                        <?php foreach($_SESSION['error'] as $r): ?>
                            <?php echo $r; ?>
                        <?php endforeach; ?>
                        <!-- エラー情報削除 -->
                        <?php unset($_SESSION['error']); ?><br>
                        <a href="./login.php" class="btn btn-danger">戻る</a>
                    </div>
                    <div class="col-sm-2"></div>
                </div>
                <!-- エラーメッセージ ここまで -->
            </div>
            <div class="col-sm-3"></div>
        </div>

    </div>

</body>

</html>