<?php
$o='';

//$o .= TWS::pr($this->data['result'],0);
//TWS::pr($this->data['markets']);


if(TWS::isIterable($this->data['result']) && count($this->data['result'])){
    $o = '<table class="table" style="width:700px;margin-top:80px; margin-left:100px;margin-right:100px;">';
    $o .='<tr>';
        
        $o .='<td width="300">Stall</td>';
        $o .='<td width="300">Contact</td>';
        $o .='<td>Site number</td>';
        
        $o .='</tr>';
    
    foreach($this->data['result'] as $r){
        //$o .= TWS::pr($r,0);
        $o .='<tr>';
        
        $o .='<td>'.$r['name'].'</td>';
        $o .='<td>'.$r['firstname'].' '.$r['lastname'].'</td>';
        $o .='<td>'.$r['site_reference'].'</td>';
        
        $o .='</tr>';
    }
    $o .= '</table>';
    
} else {
    $o .= '<p>No records found</p>';
}

?>