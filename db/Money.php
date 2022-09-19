<?php

    class Money extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        //表示用家計簿ファイル
        public function selectMoneyAll(int $user_id,int $year,int $month,int $medium)
        {
            $sql = 'select money.*,subjects.account from money inner join subjects on ';
            $sql = $sql . 'money.account=subjects.id where money.is_deleted=0 and money.user_id=:user_id';
            $sql = $sql . ' and year(spent_date)=:year ';
            $sql = $sql . 'and month(spent_date)=:month and medium_id=:medium_id ';
            $sql = $sql . 'order by spent_date,line;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_INT);
            $stmt->bindValue(':month',$month,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //入力した行をワークに入れる
        public function insertWkNowMoney(int $user_id,int $medium_id,string $spent_date,int $account,string $description,string $shop,int $debit,int $credit)
        {
            
            $line = 0;
            
            $sql = 'insert into wk_money_now(user_id,medium_id,line,spent_date,account,description,shop,debit,credit) values(:user_id,:medium_id,:line,:spent_date,:account,:description,:shop,:debit,:credit); ';            
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':line',$line,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->bindValue(':account',$account,PDO::PARAM_STR);
            $stmt->bindValue(':description',$description,PDO::PARAM_STR);
            $stmt->bindValue(':shop',$shop,PDO::PARAM_STR);
            $stmt->bindValue(':debit',$debit,PDO::PARAM_INT);
            $stmt->bindValue(':credit',$credit,PDO::PARAM_INT);
            $stmt->execute();

        }

        //入出金処理
        public function insertMoney(int $user_id,int $medium_id,string $spent_date,int $account,string $description,string $shop,int $debit,int $credit)
        {
            //ワークファイルのクリア
            $this->deleteWk('delete from wk_money_before;');
            $this->deleteWk('delete from wk_money_now;');
            $this->deleteWk('delete from wk_money_after');

            //入力した行をwk_money_nowに追加
            $this->insertWkNowMoney($user_id,$medium_id,$spent_date,$account,$description,$shop,$debit,$credit);

            //入力日付分の今までに入力した行をwk_money_beforeに移す
            $this->insertWkBeforeMoney($user_id,$medium_id,$spent_date);

            //入力日付分以降に入力した行をwk_money_afterに移す
            $this->insertWkAfterMoney($user_id,$medium_id,$spent_date);

            //Moneyファイルから当日以降のデータを削除
            $this->deleteMoneyNowAfter($user_id,$medium_id,$spent_date);

            //入力日付の今までに入力した行をMoneyファイルに移す
            $this->insertMoneyBefore();

            //今入力している行をMoneyファイルに移す
            $this->insertMoneyNow($user_id,$medium_id,$spent_date);

            //入力日以降の行をMoneyファイルに移す
            $this->insertMoneyAfter();

            //当月の繰越を追加
            $this->insertNowCarryforward($user_id,$medium_id,$spent_date);

            //消した分の繰越を追加
            $this->insertAfterCarryforward($user_id,$medium_id,$spent_date);

            //残高計算
            $this->calcBalance($user_id,$medium_id,$spent_date);
        }

        //ワークのクリア
        public function deleteWk(string $sql)
        {
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

        //入力日付分の今までに入力した行をwk_money_beforeに移す
        function insertWkBeforeMoney(int $user_id,int $medium_id,string $spent_date)
        {
            $sql = 'insert into wk_money_before(user_id,medium_id,line,spent_date,account,description';
            $sql = $sql.',shop,debit,credit) select user_id,medium_id,line,spent_date,account,';
            $sql = $sql.'description,shop,debit,credit from money where user_id =:user_id';
            $sql = $sql.' and medium_id=:medium_id and spent_date=:spent_date and is_deleted=0 order by spent_date,line;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->execute();
        }

        //入力日付分以降に入力した行をwk_money_afterに移す
        function insertWkAfterMoney(int $user_id,int $medium_id,string $spent_date)
        {
            $sql = 'insert into wk_money_after(user_id,medium_id,line,spent_date,account,description';
            $sql = $sql.',shop,debit,credit) select user_id,medium_id,line,spent_date,account,';
            $sql = $sql.'description,shop,debit,credit from money where user_id =:user_id';
            $sql = $sql.' and medium_id=:medium_id and spent_date>:spent_date and is_deleted=0 order by spent_date,line;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->execute();
        }

        // Moneyファイルから当日以降のデータを削除
        function deleteMoneyNowAfter(int $user_id,int $medium_id,string $spent_date)
        {
            $sql = 'update money set is_deleted = 1 where user_id=:user_id and medium_id=:medium_id and spent_date >= :spent_date;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->execute();
        }

        //入力日付の今までに入力した行をMoneyファイルに移す
        function insertMoneyBefore()
        {
            $sql = 'insert into money(user_id,medium_id,spent_date,line,account,description,shop,debit,';
            $sql = $sql.'credit) select user_id,medium_id,spent_date,line,account,description,shop,debit,';
            $sql = $sql.'credit from wk_money_before ;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

        //今入力している行をMoneyファイルに移す
        function insertMoneyNow($user_id,$medium_id,$spent_date)
        {
            //今日入力した最終行数を取得
            $line = $this->selectMoneyLineToday();
            
            //最終行数を更新
            if (is_null($line)){
                $line = 1;
            }else{
                $line += 1;
            }
            $this->updateWkNowLine($line);

            //NowワークをMoneyテーブルに移す
            $this->insertMoneyNowFromWk();
        }

        //入力日以降の行をMoneyファイルに移す
        function insertMoneyAfter()
        {
            $sql = 'insert into money(user_id,medium_id,spent_date,line,account,description,shop,debit,';
            $sql = $sql.'credit) select user_id,medium_id,spent_date,line,account,description,shop,debit,';
            $sql = $sql.'credit from wk_money_after ;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }
        
        //当月の繰越を追加
        function insertNowCarryforward(int $user_id,int $medium_id,string $spent_date)
        {
            //当月分の繰り越しが存在するかどうか？
            //存在しない
            if (!$this->is_carryforward($user_id,$medium_id,$spent_date)){
                //前月残高
                $balance = $this->selectMoneyBeforeBalance($user_id,$medium_id,$spent_date);
                //前月繰越の科目IDを取得
                $account = $this->selectAccount($user_id);
                //繰越行を追加する
                $this->insertLineNowCarryforward($user_id,$medium_id,$spent_date,$balance,$account);
            }
        }

        //当月分の繰り越しが存在するかどうか？
        function is_carryforward($user_id,$medium_id,$spent_date)
        {
            $year = date('Y', strtotime($spent_date));
            $month = date('m', strtotime($spent_date));
            
            $sql = 'select  * from money where user_id=:user_id and medium_id=:medium_id  ';
            $sql = $sql . 'and year(spent_date)=:year and month(spent_date)=:month and line=0 and is_deleted=0;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_INT);
            $stmt->bindValue(':month',$month,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->execute();

            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (isset($rec[0]['id'])){
                return true;
            }else{
                return false;
            }
        
        }

        //繰越行を追加する
        function insertLineNowCarryforward(int $user_id,int $medium_id,string $spent_date,int $balance,int $account)
        {
            $spent_date = substr($spent_date,0,7).'-01';
            
            $sql = 'insert into money(user_id,medium_id,line,spent_date,account,description,debit,credit,balance) ';
            $sql = $sql."values(:user_id,:medium_id,0,:spent_date,:account,'前月繰越',:balance,0,:balance); ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':account',$account,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->bindValue(':balance',$balance,PDO::PARAM_INT);
            $stmt->execute();
        }

        //当月の前月残高
        function selectMoneyBeforeBalance(int $user_id,int $medium_id,string $spent_date)
        {
            $year = date('Y', strtotime($spent_date));
            $month = date('m', strtotime($spent_date));

            $yearMonth = $year.$month;

            $sql = 'select  * from money where user_id=:user_id and medium_id=:medium_id ';
            $sql = $sql . "and concat(year(spent_date),lpad(month(spent_date),2,'0'))<:yearmonth and is_deleted=0 ";
            $sql = $sql . "order by spent_date desc,line desc limit 1";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':yearmonth',$yearMonth,PDO::PARAM_STR);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->execute();

            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (isset($rec[0]['id'])){
                return $rec[0]['balance'];
            }else{
                return 0;
            }
        }

        //消した分の繰越を追加
        function insertAfterCarryforward(int $user_id,int $medium_id,string $spent_date)
        {
            //翌日以降の行
            $rec = $this->selectMoneyAfter();

            //翌日以降のデータがなければ何もしない
            if (!is_null($rec)){

                
                //最初の月を格納
                if (!isset($rec)){
                    
                    $comp_date = date('m',$rec[0]['spent_date']);
                    
                    //全ての行を参照
                    foreach ($rec as $val){
    
                        //月が変わったかどうか
                        if ($comp_date != date('m',$val['spent_date'])){
                            
                            //月頭
                            $month_first_day = $val['spent_date'];
                            
                            //繰越を追加
                            $this->insertMoneyAfterCarryforward($user_id,$medium_id,$month_first_day);
    
                            //次の比較
                            $comp_date = $val['spent_date'];
                        }
                    }
                }
            }
        }

        //翌日以降の行
        function selectMoneyAfter()
        {
            $sql = 'select * from wk_money_after order by spent_date,line;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //繰越を追加
        function insertMoneyAfterCarryforward(int $user_id,int $medium_id,string $spent_date)
        {
            $sql = 'insert into money(user_id,medium_id,line,spent_date,account,description) ';
            $sql = $sql."values(:user_id,:medium_id,0 as line,:spent_date,0 as account,'前月繰越'); ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->execute();
        }

        //残高計算
        function calcBalance(int $user_id,int $medium_id,string $spent_date)
        {
            //入力日の直前の残高を取得
            $balance = $this->selectMoneyBalance($user_id,$medium_id,$spent_date);

            //入力日以降のレコードを取得
            $rec = $this->selectMoneyAfterInput($user_id,$medium_id,$spent_date);

            foreach ($rec as $val){

                //nullだったら0
                if (is_null($val['debit'])){
                    $debit = 0;
                }else{
                    $debit = $val['debit'];
                }

                if (is_null($val['credit'])){
                    $credit = 0;
                }else{
                    $credit = $val['credit'];
                }
                
                //繰越行かどうか？
                if ($val['line']==0){                    
                
                    //繰越行の借方と残高を変更
                    $this->updateMoneyCarryForward($val['id'],$balance);
                
                }else{

                    //残高の更新
                    $balance = $balance + $debit - $credit;
                 
                    //普通行の残高を変更
                    $this->updateMoneyBalanceCalc($val['id'],$balance);

                }
            }
        }

        //繰越行の借方と残高を変更
        function updateMoneyCarryForward(int $id,int $balance)
        {
            $sql = 'update money set debit=:balance,balance=:balance where id=:id ; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->bindValue(':balance',$balance,PDO::PARAM_INT);
            $stmt->execute();
        }

        //普通行の残高を変更
        function updateMoneyBalanceCalc(int $id,int $balance)
        {            
            $sql = 'update money set balance=:balance where id=:id ; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->bindValue(':balance',$balance,PDO::PARAM_INT);
            $stmt->execute();
        }

        //入力日の直前の残高を取得
        function selectMoneyBalance(int $user_id,int $medium_id,string $spent_date)
        {
            $sql = 'select  * from money where user_id=:user_id and medium_id=:medium_id ';
            $sql = $sql . "and spent_date < :spent_date and is_deleted=0 ";
            $sql = $sql . "order by spent_date desc,line desc limit 1";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->execute();

            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (isset($rec[0]['id'])){
                return $rec[0]['balance'];
            }else{
                return 0;
            }
        }

         //入力日以降のレコードを取得
        function selectMoneyAfterInput(int $user_id,int $medium_id,string $spent_date)
        {
            $sql = 'select  * from money where user_id=:user_id and medium_id=:medium_id ';
            $sql = $sql . "and spent_date >= :spent_date and is_deleted=0 ";
            $sql = $sql . "order by spent_date,line;";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //NowワークをMoneyテーブルに移す
        function insertMoneyNowFromWk()
        {
            $sql = 'insert into money(user_id,medium_id,spent_date,line,account,description,shop,debit,';
            $sql = $sql.'credit) select user_id,medium_id,spent_date,line,account,description,shop,debit,';
            $sql = $sql.'credit from wk_money_now ;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

        //最終行数を更新
        function updateWkNowLine(int $line)
        {
            $sql = 'update wk_money_now set line=:line;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':line',$line,PDO::PARAM_INT);
            $stmt->execute();
        }

        //今日入力した最終行数を取得
        function selectMoneyLineToday()
        {
            $sql = 'select  max(line) as maxline from wk_money_before; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();

            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (isset($rec)){
                return $rec[0]['maxline'];
            }else{
                return 0;
            }

        }

        //前月繰越の科目IDを取得
        function selectAccount(int $user_id)
        {
            $sql = "select id from subjects where user_id=:user_id and account='前月繰越'; ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$user_id,PDO::PARAM_INT);
            $stmt->execute();

            $rec = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if (isset($rec)){
                return $rec[0]['id'];
            }else{
                return 0;
            }

        }

        //家計簿テーブルの選択したレコードを返す
        public function selectMoney(int $id)
        {
            $sql = "select * from money where id=:id; ";
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        //家計簿テーブルのレコードの更新
        public function updateMoney(int $user_id,int $medium_id,int $id,string $spent_date,int $account,string $description,string $shop,int $debit,int $credit)
        {
            //修正行の更新
            $this->updateMoneyRecord($id,$spent_date,$account,$description,$shop,$debit,$credit);

            //残高計算
            $this->calcBalance($user_id,$medium_id,$spent_date);

        }

        //修正行の更新
        function updateMoneyRecord(int $id,string $spent_date,int $account,string $description,string $shop,int $debit,int $credit)
        {
            $sql = 'update money set spent_date=:spent_date,account=:account,description=:description,';
            $sql = $sql . 'shop=:shop,debit=:debit,credit=:credit ';
            $sql = $sql . 'where id=:id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->bindValue(':spent_date',$spent_date,PDO::PARAM_STR);
            $stmt->bindValue(':account',$account,PDO::PARAM_STR);
            $stmt->bindValue(':description',$description,PDO::PARAM_STR);
            $stmt->bindValue(':shop',$shop,PDO::PARAM_STR);
            $stmt->bindValue(':debit',$debit,PDO::PARAM_INT);
            $stmt->bindValue(':credit',$credit,PDO::PARAM_INT);
            $stmt->execute();

        }

        //行の削除
        public function deleteMoney(int $user_id,int $medium_id,int $id,string $spent_date)
        {
            //行の削除
            $this->deleteMoneyRecord($id);

            //残高計算
            $this->calcBalance($user_id,$medium_id,$spent_date);
        }

        function deleteMoneyRecord(int $id)
        {
            $sql = 'update money set is_deleted=1 where id=:id; ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':id',$id,PDO::PARAM_INT);
            $stmt->execute();          
        }
    }

?>