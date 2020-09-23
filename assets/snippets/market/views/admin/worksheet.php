<?php


//TWS::pr($this->data['result']);
//TWS::pr($this->data['markets']);

if($this->data['markets'][$_REQUEST['market_id']]['market_date'] <= $this->data['nowdate']){
    $o .= '<h3 class="noprint" style="padding:3px;color:#ffffff;background:#5aa0d5;">&nbsp;Payments received may be saved using this form</h3>';
    $marketDay=true;
} else {
    $marketDay = false;
}

if(TWS::isIterable($this->data['result'])){
    $o .= $marketDay ? '<form method="post" action="">' :'';
    $o .= '<table  class="table table-striped" >';
    $o .= '<thead>';
    $o .= '<tr>';
    $o .= '<th>Site#</th>';
    $o .= '<th>Name</th>';
    $o .= '<th>Contact</th>';
    $o .= '<th>Description</th>';
   // $o .= '<th>Note</th>';
    $o .= '<th width="40">Type</th>';
    $o .= '<th width="70">Collect</th>';
    if(TWS::isIterable($this->data['future_markets'])){
            foreach($this->data['future_markets'] as $mid){
              $o .= '<th>'.$this->data['markets'][$mid]['M'].'</th>';  
            }
    }
    
   
    
   
    $o .= '</tr>';
    $o .='</thead>';
    $o .= '<tbody>';

    $charged=array();
    foreach($this->data['result'] as $r){
        $o .= '<tr>';
        $o .= '<td>'.$r['site_reference'].'</td>';
        $o .= '<td><a href="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','ag=stalls&aga=edit&stall_id='.$r['stall_id']).'">'.substr($r['name'],0,20).'</a></td>';
        $o .= '<td>'.$r['contact'].'</td>';
        
        $o .= '<td>'.substr($r['description'],0,40);
        if(!empty($r['note'])){
             $o .= '<div class="note">'.$r['note'] .'</div>';
        }      
        $o .='</td>';
       // $o .= '<td>'.$r['note'].'</td>';
        $o .= '<td>'.$r['stalltype'].'</td>';
        
        if(!isSet($charged[$r['stall_id']])){
            $charge=$r['credit'];
        } else {
            $charge=0;
        }
        if($charge < 0){
            $charge = $charge * -1;
            if($marketDay){
               $o .= '<td><input style="width:2em; border:1px solid #000;font-size:14pt; " type="text" name="payment['.$r['stall_id'].']" value="'.$charge.'" ></td>';  
            } else {
                 $o .= '<td><div style="font-size:14pt; " >'.$charge.'</div></td>'; 
            }
           
        } else {
           $o .= '<td>&nbsp;</td>';  
        }
        
        
        $charged[$r['stall_id']] = true;
        
        $nMax=3;
        if(TWS::isIterable($this->data['future_markets'])){
            foreach($this->data['future_markets'] as $mid){
                if(isSet($r['future_bookings']['permanent'][$mid])){
                    
                    if($r['future_bookings']['permanent'][$mid] == 'Active'){
                        $ind = 'A'; 
                    } else if ($r['future_bookings']['permanent'][$mid] == 'Cancelled'){
                        $ind = 'C';
                    } else if ($r['future_bookings']['permanent'][$mid] == 'Cancelled late'){
                        $ind = 'CL';
                    } else if ($r['future_bookings']['permanent'][$mid] == 'No Show'){
                        $ind = 'NS';
                    }
                } else if(isSet($r['future_bookings']['casual'][$mid])){
                    $ind='cas';
                } else {
                    $ind ='-';
                }
                
                
                
                $o .= '<td>'.$ind.'</td>';
            }
        }
       
        
       
        $o .= '</tr>';
    }
    $o .= '</tbody>';
    $o .= '</table>';
    $o .= $marketDay ? '<input  style="float:right; margin-right:20px;" type="submit" name="b" value="Save Market Collections" class="btn btn-primary noprint" ></form>' : '';
    
} else {
    $o .= '<p>No records found</p>';
}

?>