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
    $_SESSION['menu_id'] = 'phone';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/doll.jpg" id="favicon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Web家計簿 スマートフォン用メニュー</title>
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
        width: 180px;
        height: 50px;
        margin: 5px;
    }

    table{
        margin-top: 10px;
        margin-left: auto;
        margin-right: auto;
    }

    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">スマートフォン用メニュー</span>
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
            <div class="center-block d-flex justify-content-center">
                <table>
                    <tr>
                        <!-- 勘定科目 -->
                        <td>
                            <a href="./subject_list.php" class="btn btn-success btn-default">勘定科目</a>
                        </td>
                        <!-- 日計表 -->
                        <td>
                            <a href="./daycount_phone.php" class="btn btn-success btn-default">日 計 表</a>
                        </td>
                    </tr>
                    <tr>
                        <!-- 口座 -->
                        <td>
                            <a href="./medium_list.php" class="btn btn-success btn-default">口 座</a>
                        </td>
                        <!-- 月間集計表 -->
                        <td>
                            <a href="./monthcount_phone.php" class="btn btn-success btn-default">月間集計表</a>
                        </td>
                    </tr>
                    <tr>
                        <!-- 入出金 -->
                        <td>
                            <a href="./record_phone.php" class="btn btn-success btn-default">入 出 金</a>
                        </td>
                        <!-- 年間集計表 -->
                        <td>
                            <a href="./yearcount_phone.php" class="btn btn-success btn-default">年間集計表</a>
                        </td>
                    </tr>
                    <tr>
                        <!-- 残高一覧 -->
                        <td>
                            <a href="./balance.php" class="btn btn-success btn-default">残高一覧</a>
                        </td>
                        <!-- 店別月間集計表 -->
                        <td>
                            <a href="./shop_month_total_phone.php" class="btn btn-success btn-default">店別月間集計表</a>
                        </td>
                    </tr>
                    <tr>
                        <!-- 退会 -->
                        <td>
                            <a href="./account_delete.php" class="btn btn-success btn-default">退 会</a>
                        </td>
                        <!-- 店別月間頻度表 -->
                        <td>
                            <a href="./shop_month_count_phone.php" class="btn btn-success btn-default">店別月間頻度表</a>
                        </td>
                    </tr>
                    <tr>
                        <!-- ログアウト -->
                        <td>
                            <a href="./logout_action.php" class="btn btn-success btn-default">ログアウト</a>
                        </td>
                        <!-- 摘要別月間集計表 -->
                        <td>
                            <a href="./description_month_total_phone.php" class="btn btn-success btn-default">摘要別月間集計表</a>
                        </td>
                    </tr>
                    <tr>
                        <!-- 戻る -->
                        <td>
                            <a href="./menu_user.php" class="btn btn-success btn-default">戻　る</a>
                        </td>
                        <!-- 摘要別月間頻度表 -->
                        <td>
                            <a href="./description_month_count_phone.php" class="btn btn-success btn-default">摘要別月間頻度表</a>
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