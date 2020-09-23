<?php


$sql = 'SELECT * from stalls where `status`="Active" order by stalls.name asc';

$stalls = DBX::getRows($sql);
DBX::abortOnError();
$o='';
if(TWS::isIterable($stalls)){
    foreach($stalls as $s){
        $name =trim($s['name']);
        if(!empty($name)){
            $o .= '<div class="stall_holder">'."\n";
            $o .='<h3>'.$s['name'].'</h3>'."\n";
            $o .= '<p>'.$s['description'].'</p>'."\n";   
            $o .= '</div>'."\n";
        }

    }

}
echo $o;
?>
