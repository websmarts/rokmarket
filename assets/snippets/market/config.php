<?php


define('COVID_MODE', true); // eg changes when collectuions fall due

define('PAST_LOCKED', false); // can make changes to dates in the past if true, else only now and future can be adjusted

 



  

   // Cancellations need to be made within this many hours of the market start time

  $config['cancellation_hours'] = 34; 

  

  // No show allowance per season

  $config['no_show_allowance'] = 3;

  

  

  // strtotime to determine 2nd Saturday of the month

    $config['market_month_day'] = '+1 week sat';

    

    // Market months in start year of a season

    $config['market_months_startyear'] = array(9,10,11,12);

    

    // Market months in second year of season

    $config['market_months_finishyear'] = array(1,2,3,4,5);

  

?>

