<?php

    class ShopCount extends Db
    {

        public function __construct()
        {
            parent::__construct();
        }

        public function selectShopCrossMonthTotal(string $year)
        {

            $sql = 'select ';
            $sql = $sql . 'shop,';

            for ($i=1;$i<=12;$i++){
                $sql = $sql . 'sum(case when month(spent_date)='.$i;
                $sql = $sql . ' then credit else 0 end) as sub' . $i . ',';
            }

            $sql = $sql . 'sum(credit) as sum_credit ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . "year(spent_date)=:year and shop <> '' and ";
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . 'group by shop ';
            $sql = $sql . 'order by sum(credit) desc;';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        public function selectShopCrossMonthCount(string $year)
        {

            $sql = 'select ';
            $sql = $sql . 'shop,';

            for ($i=1;$i<=12;$i++){
                $sql = $sql . 'sum(case when month(spent_date)='.$i;
                $sql = $sql . ' then 1 else 0 end) as sub' . $i . ',';
            }

            $sql = $sql . 'count(credit) as count_credit ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . "year(spent_date)=:year and shop <> '' and ";
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . 'group by shop ';
            $sql = $sql . 'order by count(credit) desc;';

            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        public function selectShopCrossMonthTotalPhone(string $year,string $month)
        {

            $sql = 'select ';
            $sql = $sql . 'shop,sum(credit) as credit_sum ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and month(spent_date)=:month and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . " and shop <> '' ";
            $sql = $sql . 'group by shop ';
            $sql = $sql . 'order by sum(credit) desc,shop;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->bindValue(':month',$month,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }

        public function selectShopCrossMonthCountPhone(string $year,string $month)
        {

            $sql = 'select ';
            $sql = $sql . 'shop,count(credit) as credit_count ';
            $sql = $sql . 'from money inner join subjects on money.account=subjects.id ';
            $sql = $sql . 'where ';
            $sql = $sql . 'money.user_id=:user_id and subjects.user_id=:user_id and '; 
            $sql = $sql . 'year(spent_date)=:year and month(spent_date)=:month and ';
            $sql = $sql . 'calc_flg=0 and money.is_deleted=0 and subjects.is_deleted=0 ';
            $sql = $sql . " and shop <> '' ";
            $sql = $sql . 'group by shop ';
            $sql = $sql . 'order by count(credit) desc,shop;';
            $stmt = $this->dbh->prepare($sql);
            $stmt->bindValue(':user_id',$_SESSION['user_id'],PDO::PARAM_INT);
            $stmt->bindValue(':year',$year,PDO::PARAM_STR);
            $stmt->bindValue(':month',$month,PDO::PARAM_STR);
            $stmt->execute();

            return $stmt->fetchAll(PDO::FETCH_ASSOC);

        }        
    }

?>