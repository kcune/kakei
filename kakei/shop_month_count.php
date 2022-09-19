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
    if ($_SESSION['from'] == 'Shop_month_count'){
        
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

        //数字かどうかのチェック
        //年
        if (!is_numeric($_POST['year'])){
        
            //エラーメッセージ
            $_SESSION['error'][] = NOT_NUMBER;

        }

        //数字の桁数チェック
        //年
        if (mb_strlen($_POST['year']) != 4){
        
            //エラーメッセージ
            $_SESSION['error'][] = INVALID_YEAR;

        }

        //数字の妥当性チェック
        //年
        if ($_POST['year'] < 1900 ){
        
            //エラーメッセージ
            $_SESSION['error'][] = INVALID_YEAR;

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
                
            }elseif($_SESSION['from']=='Shop_month_count'){
                                    
                $_SESSION['year'] = $_POST['year'];

            }            

            //月毎のクロス集計を作る
            $rec_cross = $db->selectShopCrossMonthCount($_SESSION['year']);

            //画面遷移用
            $_SESSION['from'] = 'Shop_month_count';

        }catch(Exception $e){

            //エラーメッセージ
            $_SESSION['error'][] = DB_ERR;
            //ログイン画面に移動
            header('Location:./login.php');
            exit;

        }

    }else{
        
        //データベース接続
        $db = new MonthCount;

        //上記の勘定科目を使って日毎のクロス集計を作る
        $rec_cross = $db->selectCreditCrossMonth($_SESSION['year']);

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
            <div class="col-sm-0"></div>
            <div class="col-sm-12">
                <!-- ボタン -->
                <a href="./menu.php" class="btn btn-success mb-2">戻る</a>
                <!-- 日付 -->
                <form method="post" action="">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="hidden" name="change_type" value="1">
                    <input type="text" name="year" class="nen" value="<?php echo $_SESSION['year']; ?>">年
                    <input type="submit" class="btn btn-success" value="変更">
                </form>
                <!-- 表示用テーブル -->
                <table class="table table-striped table-hover mt-1">
                    <thead>
                        <tr>
                            <td>店名</td>
                            <td align="right">１月</td>
                            <td align="right">２月</td>
                            <td align="right">３月</td>
                            <td align="right">４月</td>
                            <td align="right">５月</td>
                            <td align="right">６月</td>
                            <td align="right">７月</td>
                            <td align="right">８月</td>
                            <td align="right">９月</td>
                            <td align="right">１０月</td>
                            <td align="right">１１月</td>
                            <td align="right">１２月</td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rec_cross as $val): ?>
                            <tr>
                                <!-- 日付 -->
                                <td><?php echo $val['shop']; ?></td>
                                <?php for ($i=1; $i<=12; $i++){ ?>
                                    <!-- 残高 -->
                                    <td align="right"><?php echo number_format($val['sub'.$i]); ?></td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <div class="col-sm-0"></div>
        </div>
        <!-- 入力フォーム ここまで -->
    </div>
</body>
</html>