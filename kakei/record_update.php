<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../db/Db.php');
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

    //修正レコードを取得
    try {

        //データベース接続
        $db = new Money;
    
        //アカウント情報を追加する
        $rec = $db->selectMoney($_POST['id']);

    } catch (Exception $e) {
    
        //エラーメッセージ
        $_SESSION['error'][] = DB_ERR;
    
        //アカウント作成画面に移動
        header('Location:./account_add_confirm.php');
        exit;
    }    

    //トークン取得
    $token = Util::getToken();

    //初期値
    if ($_SESSION['from']=='Record_list'){

        $spent_date = $rec[0]['spent_date'];
        $account = $rec[0]['account'];
        $description = $rec[0]['description'];
        $shop = $rec[0]['shop'];
        $debit = $rec[0]['debit'];
        $credit = $rec[0]['credit'];

    }else{

        $spent_date = $_SESSION['spent_date'];
        $account = $_SESSION['account'];
        $description = $_SESSION['description'];
        $shop = $_SESSION['shop'];
        $debit = $_SESSION['debit'];
        $credit = $_SESSION['credit'];

    }

    //画面遷移用
    $_SESSION['from'] = 'Record_update';

    try {

        //データベース接続
        $db = new Subject;
    
        //アカウント情報を追加する
        $rec = $db->selectSubjectAll($_SESSION['user_id']);

    } catch (Exception $e) {
    
        //エラーメッセージ
        $_SESSION['error'][] = DB_ERR;
    
        //アカウント作成画面に移動
        header('Location:./account_add_confirm.php');
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
    <title>Web家計簿 入出金修正</title>
    <style>

    .navmoji{
        margin-left: 30px;
        font-size:large;
    }

    .formgroup{
        margin-top: 5px;
        margin-bottom: 5px;
    }

    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">入出金修正</span>
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
        <div class="row">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <form action="./record_update_action.php" method="post">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <input type="hidden" name="id" value="<?php echo $_POST['id'] ?>">
                    <input type="hidden" name="medium_id" value="<?php echo $_POST['medium_id'] ?>">
                    <!-- 日付 -->
                    <div class="form-group formgroup">
                        <label for="spent_date">日付</label>
                        <input type="text" class="form-control" id="spent_date" name="spent_date" value="<?php echo $spent_date; ?>">
                    </div>
                    <!-- 科目 -->
                    <label for="account" class="formgroup">科目</label>
                    <select class="form-select" aria-label="Default select example" name="account">
                        <?php foreach($rec as $val): ?>
                            <option value="<?php echo $val['id'] ?>" <?php if($val['id']==$account){echo 'selected';} ?>><?php echo $val['account'] ?></option>
                        <?php endforeach; ?>
                    </select>
                    <!-- 摘要 -->
                    <div class="form-group formgroup">
                        <label for="description">摘要</label>
                        <input type="text" class="form-control" id="description" name="description" value="<?php echo $description; ?>">
                    </div>
                    <!-- 店名 -->
                    <div class="form-group formgroup">
                        <label for="shop">店名</label>
                        <input type="text" class="form-control" id="shop" name="shop" value="<?php echo $shop; ?>">
                    </div>
                    <!-- 借方 -->
                    <div class="form-group formgroup">
                        <label for="debit">借方</label>
                        <input type="text" class="form-control" id="debit" name="debit" value="<?php echo $debit; ?>">
                    </div>
                    <!-- 貸方 -->
                    <div class="form-group formgroup">
                        <label for="credit">貸方</label>
                        <input type="text" class="form-control" id="credit" name="credit" value="<?php echo $credit; ?>">
                    </div>
                    <!-- ボタン -->
                    <button type="submit" class="btn btn-success">修正</button>
                    <?php if ($_SESSION['menu_id']=='PC'): ?>
                        <a href="./record_list.php" class="btn btn-success">戻る</a>
                    <?php else: ?>
                        <a href="./record_phone.php" class="btn btn-success">戻る</a>
                    <?php endif; ?>
                </form>

            </div>
            <div class="col-sm-3"></div>
        </div>
        <!-- 入力フォーム ここまで -->

    </div>
</body>
</html>