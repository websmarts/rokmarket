<?php
/**
* Function sorts an array of market sites so they are ordered nicely
* by the site_reference value
*/
function sort_sites($a,$b){
    if($a == $b){
        return 0;
    }

    $aStr = preg_replace("/[0-9]/",'',$a);
    $aNum = preg_replace("/[^0-9]/",'',$a);
    $bStr= preg_replace("/[0-9]/",'',$b);
    $bNum= preg_replace("/[^0-9]/",'',$b);

    if($aStr < $bStr){
        return -1;
    } else if ($aStr > $bStr){
        return 1;
    } else if ($aStr == $bStr){
        return ($aNum < $bNum) ? -1 : 1;
    }


}
?>
