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

    //確認画面から戻ってきたときアカウント名を保持する
    if($_SESSION['from']=='Subject_list'){
        $account = null;
        $calc_flg = 0;
    }else{
        $account = $_SESSION['account'];
        $calc_flg = $_SESSION['calc_flg'];
    }

    //画面遷移用
    $_SESSION['from'] = 'Subject_add';

?>
<!DOCTYPE html>
<html lang="ja">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" href="../img/doll.jpg" id="favicon">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <title>Web家計簿 勘定科目追加</title>
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
            <span class="navbar-brand">勘定科目追加</span>
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
            <div class="col-sm-5">
                <form action="./subject_add_action.php" method="post">
                    <input type="hidden" name="token" value="<?php echo $token; ?>">
                    <!-- 科目名 -->
                    <div class="form-group formgroup">
                        <label for="account">科目名</label>
                        <input type="text" class="form-control" id="account" name="account" value="<?php echo $account; ?>">
                    </div>
                    <!-- 権限 -->
                    <label for="calc_flg">計算する</label>
                    <select class="form-select formgroup" aria-label="Default select example" name="calc_flg">
                        <?php if ($calc_flg==0): ?>
                            <option value="0" selected>計算する</option>
                            <option value="1">計算しない</option>
                        <?php else: ?>
                            <option value="0">計算する</option>
                            <option value="1" selected>計算しない</option>
                        <?php endif; ?>
                    </select>

                    <!-- ボタン -->
                    <button type="submit" class="btn btn-success">追加</button>
                    <a href="./subject_list.php" class="btn btn-success">戻る</a>
                </form>

            </div>
            <div class="col-sm-3"></div>
        </div>
        <!-- 入力フォーム ここまで -->

    </div>
</body>
</html>