<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../db/Db.php');
    require_once('../db/Medium.php');
    require_once('../db/Subject.php');
    require_once('../db/Money.php');

    //セッション開始
    Util::sessionStart();

    //ここから画面を開けないようにする
    if(!Util::isLogin()){
        
        //ログイン画面に移動
        header('Location:./login.php');
        exit;
        
    }
    
    //このフォームからきたときだけチェックする
    if ($_SESSION['from'] == 'Record_list'){
        
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
        
        //口座変更の時
        if ($_POST['change_type']==0){

            //必須入力チェック
            //口座
            if (empty($_POST['medium_id'])){
            
                //エラーメッセージ
                $_SESSION['error'][] = NO_MEDIA;

            }

            //数字であること
            if (!is_numeric($_POST['medium_id'])){
            
                //エラーメッセージ
                $_SESSION['error'][] = NOT_NUMBER;

            }

            //不正な数字でないこと
            if ($_POST['medium_id']<0){
            
                //エラーメッセージ
                $_SESSION['error'][] = NUMBER_FAILER;

            }
            
        //日付変更の時
        }elseif($_POST['change_type']==1){

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
            if (mb_strlen($_POST['month'])>2){
            
                //エラーメッセージ
                $_SESSION['error'][] = INVALID_MONTH;

            }

            //数字の妥当性チェック
            //年
            if ($_POST['year'] < 1900 ){
            
                //エラーメッセージ
                $_SESSION['error'][] = INVALID_YEAR;

            }

            //月
            if ($_POST['month'] > 12 || $_POST['month'] < 1){
            
                //エラーメッセージ
                $_SESSION['error'][] = INVALID_MONTH;

            }
        }        
    }

    //トークン取得
    $token = Util::getToken();
    
    if (empty($_SESSION['error'])){
        
        try{
            
            //データベース接続
            $db_medium = new Medium;
            $db_money = new Money;
            
            //勘定科目テーブルから有効な全情報を取得する
            $rec_medium = $db_medium->selectMediumAll($_SESSION['user_id']);
            $rec_min_medium = $db_medium->minMedium($_SESSION['user_id']);
            
            //セッションに口座IDを入れる
            if ($_SESSION['from']=='menu'){
    
                $_SESSION['medium_id'] = $rec_min_medium[0]['min_id'];
                //現在年
                $now_year = date('Y');
                $_SESSION['year'] = $now_year;
                
                //現在月
                $now_month = date('m');
                $_SESSION['month'] = $now_month;
                
            }elseif($_SESSION['from']=='Record_list'){
                
                if ($_POST['change_type']==0){
                    
                    $_SESSION['medium_id'] = $_POST['medium_id'];
                    
                } else{
                    
                    $_SESSION['year'] = $_POST['year'];
                    $_SESSION['month'] = sprintf('%02d', $_POST['month']);
    
                }                
            }
            
            if (!is_null($_SESSION['medium_id'])){
                //表示用レコード
                $rec_money = $db_money->selectMoneyAll($_SESSION['user_id'],$_SESSION['year'],$_SESSION['month'],$_SESSION['medium_id']);
            } else {
                $rec_money = array();
            }
            
            //画面遷移用
            $_SESSION['from'] = 'Record_list';
    
        }catch(Exception $e){
    
            //エラーメッセージ
            $_SESSION['error'][] = DB_ERR;
            //ログイン画面に移動
            header('Location:./error.php');
            exit;
        }

    } else{

        //データベース接続
        $db_medium = new Medium;
        $db_money = new Money;

        //勘定科目テーブルから有効な全情報を取得する
        $rec_medium = $db_medium->selectMediumAll($_SESSION['user_id']);

        //表示用レコード
        $rec_money = $db_money->selectMoneyAll($_SESSION['user_id'],$_SESSION['year'],$_SESSION['month'],$_SESSION['medium_id']);

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
    <title>Web家計簿 入出金</title>
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

    .kouza{
        width:155px;
    }

    .btn_set{
        height:30px;
        width: 60px;
        padding: 0px;
    }

    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">入出金</span>
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
                <a href="./record_add.php" class="btn btn-success">追加</a>
                <a href="./menu.php" class="btn btn-success">戻る</a><br>
                <!-- 口座 -->
                <form method="post" action="">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="hidden" name="change_type" value="0">
                    <select name="medium_id" class="mt-3 kouza">
                        <?php foreach($rec_medium as $val): ?>
                            <?php if ($_SESSION['medium_id']==$val['id']): ?>
                                <option value="<?php echo $val['id']; ?>" selected><?php echo $val['medium']; ?></option>
                            <?php else: ?>
                                <option value="<?php echo $val['id']; ?>"><?php echo $val['medium']; ?></option>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </select>
                    <input type="submit" value="変更" class="btn btn-success">
                </form>
                <!-- 日付 -->
                <form method="post" action="">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="hidden" name="change_type" value="1">
                    <input type="text" name="year" class="nen" value="<?php echo $_SESSION['year']; ?>">年
                    <input type="text" name="month" class="tuki" value="<?php echo $_SESSION['month']; ?>">月
                    <input type="submit" class="btn btn-success" value="変更">
                </form>
                <!-- 表示用テーブル -->
                <table class="table table-striped table-hover mt-1">
                    <thead>
                        <tr>
                            <td>日付</td>
                            <td>科目</td>
                            <td>摘要</td>
                            <td>店名</td>
                            <td align="right">借方</td>
                            <td align="right">貸方</td>
                            <td align="right">残高</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rec_money as $val): ?>
                            <tr>
                                <!-- 日付 -->
                                <td width="100" height="20"><?php echo $val['spent_date']; ?></td>
                                <!-- 科目 -->
                                <td><?php echo $val['account']; ?></td>
                                <!-- 摘要 -->
                                <td width="350"><?php echo $val['description']; ?></td>
                                <!-- 店名 -->
                                <td width="150"><?php echo $val['shop']; ?></td>
                                <!-- 借方 -->
                                <td align="right"><?php echo number_format($val['debit']); ?></td>
                                <!-- 貸方 -->
                                <td align="right"><?php echo number_format($val['credit']); ?></td>
                                <!-- 残高 -->
                                <td align="right"><?php echo number_format($val['balance']); ?></td>
                                <!-- 修正ボタン -->
                                <td width="40">
                                    <form method="post" action="./record_update.php">
                                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                                        <input type="hidden" name="id" value="<?php echo $val['id'] ?>">
                                        <input type="hidden" name="medium_id" value="<?php echo $val['medium_id'] ?>">
                                        <input type="submit" value="修正" class="btn btn-success btn_set">
                                    </form>
                                </td>
                                <!-- 削除ボタン -->
                                <td width="40">
                                    <form method="post" action="./record_delete_action.php">
                                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                                        <input type="hidden" name="id" value="<?php echo $val['id'] ?>">
                                        <input type="hidden" name="medium_id" value="<?php echo $val['medium_id'] ?>">
                                        <input type="hidden" name="spent_date" value="<?php echo $val['spent_date'] ?>">
                                        <input type="submit" value="削除" class="btn btn-success btn_set">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- ボタン -->
                <a href="./record_add.php" class="btn btn-success">追加</a>
                <a href="./menu.php" class="btn btn-success">戻る</a><br>
            </div>
            <div class="col-sm-0"></div>
        </div>
        <!-- 入力フォーム ここまで -->
    </div>
</body>
</html>