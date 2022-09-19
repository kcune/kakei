<?php

    //ファイルの読み込み
    require_once('../util/Utl.php');
    require_once('../db/Db.php');
    require_once('../db/Medium.php');

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
        $db = new Medium;
        
        //勘定科目テーブルから有効な全情報を取得する
        $rec = $db->selectMediumAll($_SESSION['user_id']);
        
        //画面遷移用
        $_SESSION['from'] = 'Medium_list';

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
    <title>Web家計簿 口座</title>
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

    </style>
</head>
<body>
    
    <!-- ナビゲーション -->
    <nav class="navbar navbar-expand-md navbar-dark bg-success">
        <div class="navmoji">
            <span class="navbar-brand">口座</span>
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
                <?php if ($_SESSION['menu_id']=='PC'): ?>
                    <a href="./menu.php" class="btn btn-success">戻る</a>
                <?php else: ?>
                    <a href="./menu_phone.php" class="btn btn-success">戻る</a>
                <?php endif; ?>
                <!-- 表示用テーブル -->
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <td>口座名</td>
                            <td></td>
                            <td></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($rec as $val): ?>
                            <tr>
                                <!-- 科目名 -->
                                <td><?php echo $val['medium']; ?></td>
                                <!-- 修正ボタン -->
                                <td style="width: 40px;">
                                    <form method="post" action="./medium_update.php">
                                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                                        <input type="hidden" name="id" value="<?php echo $val['id'] ?>">
                                        <input type="hidden" name="medium" value="<?php echo $val['medium'] ?>">
                                        <input type="submit" value="修正" class="btn btn-success">
                                    </form>
                                </td>
                                <!-- 削除ボタン -->
                                <td style="width: 40px;">
                                    <form method="post" action="./medium_delete_action.php">
                                        <input type="hidden" name="token" value="<?php echo $token; ?>">
                                        <input type="hidden" name="id" value="<?php echo $val['id'] ?>">
                                        <input type="submit" value="削除" class="btn btn-success">
                                    </form>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <!-- ボタン -->
                <a href="./medium_add.php" class="btn btn-success">追加</a>
            </div>
            <div class="col-sm-4"></div>
        </div>
        <!-- 入力フォーム ここまで -->
    </div>
</body>
</html>