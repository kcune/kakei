<?php

    class Balance extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function selectBalanceAll()
        {
            //ワーククリア
            $this->wkBalanceClear();

            //全ての口座レコードを取得
            $rec = $this->selectMediumAll();

            //口座ごとに残高を追加
            foreach ($rec as $val){
                
                //残高をもとめる
                $balance = $this->selectBalanceMedium($val['id']);

                //ワークに残高を入れる
                $this->insertWkBalance($val['id'],$balance);

            }

            //口座IDを口座名につなげて表示
            return $this->selectWkBalanceAll();
        }

        //ワーククリア
        function wkBalanceClear()
        {
            $sql = 'delete from wk_balance ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
        }

        //口座全表示
        function selectMediumAll()
        {            
            $sql = 'select id from medium where user_id=:user_id and is_deleted=0 order by id ';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        }

        //家計簿テーブルから各口座の残高を求める
        function selectBalanceMedium(int $medium_id)
        {
            $sql = 'select medium_id,balance from money where user_id=:user_id and is_deleted=0 ';
            $sql = $sql . 'and medium_id=:medium_id order by spent_date desc,line desc limit 1;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->execute();
    
            $rec =  $stmt->fetchAll(PDO::FETCH_ASSOC);
            $balance = 0;
            foreach($rec as $val){
                if (is_null($val['balance'])){
                    $balance = 0;
                }else{
                    $balance = $val['balance'];
                }
            }

            return $balance;

        }

        //残高ワークに残高を入れる
        function insertWkBalance(int $medium_id,int $balance)
        {
            $sql = 'insert into wk_balance(user_id,medium_id,balance) ';
            $sql = $sql . 'values(:user_id,:medium_id,:balance);';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':medium_id',$medium_id,PDO::PARAM_INT);
            $stmt->bindValue(':balance',$balance,PDO::PARAM_INT);
            $stmt->execute();
        }

        //口座IDを口座名につなげて表示
        function selectWkBalanceAll()
        {
            $sql = 'select wk_balance.*,medium.medium from wk_balance inner join medium on ';
            $sql = $sql . 'wk_balance.medium_id = medium.id ;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->execute();
    
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
          
        }

    }

?>