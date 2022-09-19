<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../db/Db.php');
    require_once('../db/YearCount.php');

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
        $db = new YearCount;

        //基準年
        $year = date('Y', strtotime('-4 year'));

        //月毎のクロス集計を作る
        $rec_cross = $db->selectCreditCrossYear();

        //画面遷移用
        $_SESSION['from'] = 'YearCount';

    }catch(Exception $e){

        //エラーメッセージ
        $_SESSION['error'][] = DB_ERR;
        //ログイン画面に移動
        header('Location:./login.php');
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
    <title>Web家計簿 年間集計表</title>
    <style>

    .navmoji{
        margin-left: 30px;
        font-size:large;
    }

    .formgroup{
        margin-top: 50px;
        margin-bottom: 50px;
    }

    .group-float{
        display: flex;
        margin: 10px;
    }

    .item-group{
        margin-left: 10px;
    }

    .nen{
        width: 60px;
        margin-top: 10px;
        text-align: center;
    }
    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">年間集計表</span>
        </div>
    </nav>
    <!-- ナビゲーション ここまで --> 
    
    <div class="container">
        
        <!-- エラーメッセージ ここから --> 
        <?php if(isset($_SESSION['error'])): ?>            
            <div class="row my-2">
                <div class="col-sm-2"></div>
                <div class="col-sm-8 alert alert-danger alert-dismissble fade show">
                    <?php foreach($_SESSION['error'] as $r): ?>
                        <?php echo $r.'<br>'; ?>
                    <?php endforeach; ?>
                    <?php unset($_SESSION['error']); ?>
                </div>
                <div class="col-sm-2"></div>
            </div>
        <?php endif; ?>
        <!-- エラーメッセージ ここまで -->

        <!-- 入力フォーム ここから -->
        <div class="row my-2">
            <div class="col-sm-2"></div>
            <div class="col-sm-8">
                <!-- ボタン -->
                <a href="./menu.php" class="btn btn-success mb-2">戻る</a>
                <!-- 表示用テーブル -->
                <table class="table table-striped table-hover mt-1">
                    <thead>
                        <tr>
                            <td>科目</td>
                            <?php for ($i=$year;$i<=$year+4;$i++){ ?>
                                <td align="right"><?php echo $i.'年'; ?></td>
                            <?php } ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rec_cross as $val): ?>
                            <tr>
                                <!-- 日付 -->
                                <td><?php echo $val['ac']; ?></td>
                                <?php for ($i=$year;$i<=$year+4;$i++){ ?>
                                    <!-- 残高 -->
                                    <td align="right"><?php echo number_format($val['sub'.$i]); ?></td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-2"></div>
        </div>
        <!-- 入力フォーム ここまで -->
    </div>
</body>
</html>