<?php
class Season{

   

    function Season() {
        
        $this->checkDbSeasons();
    }

    function setSeason($year){
        if(preg_match('/\d{4}/',$year)){
            $_SESSION[$this->sesionDataKey]['season']=$year;
            $this->season=$year;
        } else {
            die(__FUNCTION__ .' called with invalid YEAR ='.$year);
        }
        return $year; // may as well return set season

    }

    function getNowSeason(){

        $nowYear = date('Y',$this->nowtime); 
        $nowMonth = date('n',$this->nowtime); // numeric month

        if($nowMonth < 6){
            $seasonStartYear = $nowYear -1;
        } else {
            $seasonStartYear = $nowYear;
        }
        return $seasonStartYear;

    }

    /**
    * Gets the currently selected season 
    * 
    * returns season start year
    * 
    */
    function getSeason(){
        if(isSet($_SESSION[$this->sessionDataKey]['season']) && preg_match('/\d{4}/',$_SESSION[$this->sessionDataKey]['season']) ){
            $this->season =  $_SESSION[$this->sessionDataKey]['season'];
            return $this->season;
        } else {
            return false;
        }
    }

    /**
    * Check to see if we have a future season in the database
    * 
    */
    function checkDbSeasons(){
        // Get the current list of markets 

        $today = date('Y-m-d',$this->nowtime);
        $nowYear = date('Y',$this->nowtime); 
        $nowMonth = date('n',$this->nowtime); // numeric month

        $lastMarket = DBX::getRow('select market_date from markets order by market_date desc limit 1 ');

        if(!$lastMarket){ // markets table is empty !
            // create the current season
            if($nowMonth < 6){
                $startYear = $nowYear -1;
            } else {
                $startYear = $nowYear;
            }
            $this->addMarketSeason($startYear);
            $this->setSeason($startYear);
        } else {
            // Check if lastMarket is in next season and if not then add a new season
            $lastMarketYear = substr($lastMarket['market_date'],0,4);
            if( ($nowMonth < 6 && (($lastMarketYear - $nowYear) < 1) ) || ($nowMonth > 5 && (($lastMarketYear - $nowYear) < 2)) ){
                // then last_market year should be nowYear plus one
                $this->_addMarketSeason($lastMarketYear);
            }           
        }
    }

    function addMarketSeason($startYear){

        foreach($this->config['market_months_startyear'] as $m){    
            $date = date('Y-m-d', strtotime($this->config['market_month_day'].' '.$this->months[$m]. ' ' . $startYear ) ); 
            if(!DBX::insert('markets',array('market_date'=>$date,'status'=>'Active'))){
                TWS::pr(DBX::showError());             
            }
        }
        $nextYear=++$startYear;
        foreach($this->config['market_months_finishyear'] as $m){
            $date = date('Y-m-d', strtotime('+1 week sat '.$this->months[$m]. ' ' .$nextYear) );
            if(!DBX::insert('markets',array('market_date'=>$date,'status'=>'Active'))){
                TWS::pr(DBX::showError());       
            }            
        }
    }
}
?>
