<?php


//TWS::pr($this->data['result']);
$o.='<h3>Season payments</h3>';
$o .= '<a class="btn btn-warning" href="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','&ag=stalls&aga=edit&stall_id='.$_REQUEST['stall_id']).'">Back to - View stall info</a>';

$o .='<p>Payment TYPEs:<br> A <strong>Payment</strong> adds to the amount available to offset or cover costs<br>
A <strong>Stall Charge</strong> applies an additional cost to the stallholder<br>A <strong>Stall Credit</strong> will increase the amount available to cover stallholder costs
<br>A <strong>Refund</strong> can be made by entering a negative Payment </p>';


$o.='<form method="post" action="" >';
$o .='<input type="hidden" name="stall_id" value="'.$_REQUEST['stall_id'].'" >';

$o .= '<table  class="table table-striped" >';
$o .= '<thead>';
$o .= '<tr>';
$o .= '<th width="100">Date</th>';
$o .= '<th width="100">Amount($)</th>';
$o .= '<th >Type</th>';
$o .= '<th >Note</th>';
$o .= '<th >&nbsp;</th>';

$o .= '</tr>';
$o .='</thead>';
$o .= '<tbody>';

$o .= '<tr>';
$o .= '<td><input class="datepicker" type="text" name="payment_date[0]" style="width:7em"></td>';
$o .= '<td><input type="text" name="amount[0]" style="width:4em"></td>';
$o .= '<td>
        <select name="transaction_type[0]" style="width:8em">
        <option value="1" >Payment</option>
        <option value="-1" >Stall credit</option>
        <option value="-2" >Stall charge</option>
        </select>

        </td>';
$o .= '<td><input type="text" name="note[0]" style="width:50em"></td>';
$o .= '<td>&nbsp;</td>';
$o .= '</tr>'; 

$transactionTypes = array(
    1  =>'Payment',
    -1 =>'Stall credit',
    -2 =>'Stall charge'
);
if(TWS::isIterable($this->data['result'])){
    $url = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','&ag=payments&aga=delete&stall_id='.$_REQUEST['stall_id'].'&tid=');
    foreach($this->data['result'] as $r){
        $o .= '<tr>';
        $o .= '<td>'.$r['transaction_date'].'</td>';
        $o .= '<td>'.$r['amount'].'</td>';
        $o .= '<td>'.$transactionTypes[$r['transaction_type']].'</td>';
        $o .= '<td>'.$r['note'].'</td>';
        $o .= '<td><a href="'.$url.$r['id'].'" onclick="return confirm(\'Confirm delete?\');" >Delete</a></td>';
        $o .= '</tr>';
    }
}
$o .= '</tbody>';
$o .= '</table>';
$o .='<input type="submit" name="b" value="Save" >';
$o.='</form>'


?>