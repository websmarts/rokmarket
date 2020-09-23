<?php


//TWS::pr($this->data['result']);
$o .= '<form method="post" action="">';
$o .= '<div class="row" style="border-bottom:6px solid #ccc;margin-bottom:6px;">';
$o .= '<div class="col-md-2 col-md-offset-1">'."\n";
$o .= 'Report Start Date<br />';
$o .= '<input class="datepicker" type="text" name="report_start_date" value="'.$this->data['reportStartDate'].'" style="width:7em">';
$o .= '</div>';

$o .= '<div class="col-md-1 ">'."\n";
$o .= '&nbsp;<br /><input class="btn btn-success" type="submit" name="b" value="Refresh report" >';
$o .= '</div>';

$o .= '<div class="col-md-2 col-md-offset-1">'."\n";
$o .= 'Report End Date<br />';
$o .= '<input class="datepicker" type="text" name="report_end_date" value="'.$this->data['reportEndDate'].'" style="width:7em">';
$o .= '</div>';

$o .= '<div class="col-md-3 col-md-offset-1">'."\n";
$checked = $this->data['showPayments'] > 0 ? ' checked ': '';
$o .= ' <input type="checkbox" name="show_payments" '.$checked.' value="1"  > Show payments<br />';
$checked = $this->data['showCredits'] > 0 ? ' checked ': '';
$o .= ' <input type="checkbox" name="show_credits" '.$checked.'  value="1"  > Show credits<br />';
$checked = $this->data['showCharges'] > 0 ? ' checked ': '';
$o .= ' <input type="checkbox" name="show_charges" '.$checked.' value="1" > Show extra charges<br />';

$o .= '</div>';

$o .='</div>';
$o .= '</form>';


$o.='<h3>Transactions report:</h3>';


$o .= '<table  class="table table-striped" >';
$o .= '<thead>';
$o .= '<tr>';
$o .= '<th width="100">Date</th>';
$o .= '<th width="100">Payments<br />($)</th>';
$o .= '<th width="100">Credits<br />($)</th>';
$o .= '<th width="100">Charges<br />($)</th>';
$o .= '<th >Note</th>';
$o .= '<th >Stall</th>';
//$o .= '<th >Market</th>';

$o .= '</tr>';
$o .='</thead>';
$o .= '<tbody>';


$total=array();
$url = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','&ag=stalls&aga=edit&stall_id=');
if(TWS::isIterable($this->data['result'])){
    foreach($this->data['result'] as $r){
        $o .= '<tr>';
        $o .= '<td>'.$r['transaction_date'].'</td>';
        
        if($r['transaction_type'] == 1){
           $o .= '<td>'.$r['amount'].'</td>'; 
        } else {
            $o .= '<td>&nbsp;</td>'; 
        }
        
         if($r['transaction_type'] == -1){
           $o .= '<td>'.$r['amount'].'</td>'; 
        } else {
            $o .= '<td>&nbsp;</td>'; 
        }
        
         if($r['transaction_type'] == -2){
           $o .= '<td>'.$r['amount'].'</td>'; 
        } else {
            $o .= '<td>&nbsp;</td>'; 
        }
        
        
        
        $o .= '<td>'.$r['note'].'</td>';
        $o .= '<td><a href="'.$url.$r['stall_id'].'">'.$r['stallname'].'</a></td>';
        //$o .= '<td>'.$r['market_id'].(!empty($r['site_reference'])?' / ':'') .$r['site_reference'].'</td>';
        $o .= '</tr>';
        
        $total[$r['transaction_type']] +=$r['amount'];
    }
}
$o .='<tr><td><strong>TOTAL</strong></td><td><strong>'.number_format($total[1],2).'</strong></td><td><strong>'.number_format($total[-1],2).'</strong></td><td><strong>'.number_format($total[-21],2).'</strong></td><td colspan="2">&nbsp;</td></tr>';
$o .= '</tbody>';
$o .= '</table>';



?>