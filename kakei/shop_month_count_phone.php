<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../db/Db.php');
    require_once('../db/ShopCount.php');

    //セッション開始
    Util::sessionStart();

    //ここから画面を開けないようにする
    if(!Util::isLogin()){
        
        //ログイン画面に移動
        header('Location:./login.php');
        exit;

    }
    
    //このフォームからきたときだけチェックする
    if ($_SESSION['from'] == 'Shop_month_count_phone'){
        
        //サニタイズ
        $_POST = Util::sani($_POST);

        //不正アクセス防止
        if (!Util::isToken($_POST['token'])) {
            
            //エラーメッセージ
            $_SESSION['error'][] = ACCESS_FAILER;
            //ログイン画面に移動
            header('Location:./login.php');
            exit;
        }

        //必須入力チェック
        //年
        if (empty($_POST['year'])){
        
            //エラーメッセージ
            $_SESSION['error'][] = NO_YEAR;

        }

        //月
        if (empty($_POST['month'])){
        
            //エラーメッセージ
            $_SESSION['error'][] = NO_MONTH;

        }

        //数字かどうかのチェック
        //年
        if (!is_numeric($_POST['year'])){
        
            //エラーメッセージ
            $_SESSION['error'][] = NOT_NUMBER;

        }

        //月
        if (!is_numeric($_POST['month'])){
        
            //エラーメッセージ
            $_SESSION['error'][] = NOT_NUMBER;

        }

        //数字の桁数チェック
        //年
        if (mb_strlen($_POST['year']) != 4){
        
            //エラーメッセージ
            $_SESSION['error'][] = INVALID_YEAR;

        }

        //月
        if (mb_strlen($_POST['month']) > 2){
        
            //エラーメッセージ
            $_SESSION['error'][] = INVALID_MONTH;

        }

        //数字の妥当性チェック
        //年
        if ($_POST['year'] < 1900 ){
        
            //エラーメッセージ
            $_SESSION['error'][] = INVALID_YEAR;

        }        

        //年
        if ($_POST['month'] < 1 || $_POST['month'] > 12){
        
            //エラーメッセージ
            $_SESSION['error'][] = INVALID_MONTH;

        }        
    }

    //トークン取得
    $token = Util::getToken();

    if (empty($_SESSION['error'])){

        try{
            
            //データベース接続
            $db = new ShopCount;     

            //セッションに口座IDを入れる
            if ($_SESSION['from']=='menu'){

                //現在年
                $now_year = date('Y');
                $_SESSION['year'] = $now_year;
                
                //現在月
                $now_month = date('m');
                $_SESSION['month'] = $now_month;

            }elseif($_SESSION['from']=='Shop_month_count_phone'){
                                    
                $_SESSION['year'] = $_POST['year'];
                $_SESSION['month'] = str_pad($_POST['month'], 2, '0', STR_PAD_LEFT);

            }            

            //月毎のクロス集計を作る
            $rec_cross = $db->selectShopCrossMonthCountPhone($_SESSION['year'],$_SESSION['month']);

            //画面遷移用
            $_SESSION['from'] = 'Shop_month_count_phone';

        }catch(Exception $e){

            //エラーメッセージ
            $_SESSION['error'][] = DB_ERR;
            //ログイン画面に移動
            header('Location:./login.php');
            exit;

        }

    }else{
        
        //データベース接続
        $db = new ShopCount;

        //上記の勘定科目を使って日毎のクロス集計を作る
        $rec_cross = $db->selectShopCrossMonthCountPhone($_SESSION['year'],$_SESSION['month']);

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
    <title>Web家計簿 店別月間頻度表</title>
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

    .tuki{
        width: 50px;
        text-align: center;
    }    
    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">店別月間頻度表</span>
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
            <div class="col-sm-4"></div>
            <div class="col-sm-4">
                <!-- ボタン -->
                <a href="./menu_phone.php" class="btn btn-success mb-2">戻る</a>
                <!-- 日付 -->
                <form method="post" action="">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="text" name="year" class="nen" value="<?php echo $_SESSION['year']; ?>">年
                    <input type="text" name="month" class="tuki" value="<?php echo $_SESSION['month']; ?>">月
                    <input type="submit" class="btn btn-success" value="変更">
                </form>
                <!-- 表示用テーブル -->
                <table class="table table-striped table-hover mt-1">
                    <thead>
                        <tr>
                            <td>店名</td>
                            <td align="right">回数</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rec_cross as $val): ?>
                            <tr>
                                <!-- 店名 -->
                                <td><?php echo $val['shop']; ?></td>
                                <!-- 回数 -->
                                <td align="right"><?php echo number_format($val['credit_count']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-4"></div>
        </div>
        <!-- 入力フォーム ここまで -->
    </div>
</body>
</html>