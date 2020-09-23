<?php

// Create js Model



//$o .=  TWS::pr($_SESSION['tws-flashdata'],0) ; 
//$o .=  TWS::pr($this->data['allocation_requests'],0) ; 
//$o .=  TWS::pr($this->data['casual_bookings'],0) ; 
//$o .=  TWS::pr($this->data['casual_requests'],0) ; 

$o.='<div class="row">';

$o .='<div id="loading"></div>';


$o .= '<div class="col-md-3 col-md-offset-1">'."\n";

$o .= $this->data['f']->render_form_open();
$o .= $this->data['f']->render_element($this->data['form_elements']['name']);
$o .= $this->data['f']->render_element($this->data['form_elements']['description']);
$o .= $this->data['f']->render_element($this->data['form_elements']['contacts']);

$o .= $this->data['f']->render_element($this->data['form_elements']['category']);
$o .= $this->data['f']->render_element($this->data['form_elements']['notes']);

$o .= $this->data['f']->render_element($this->data['form_elements']['application_form_on_file']);
$o .= $this->data['f']->render_element($this->data['form_elements']['foodseller']);
$o .= $this->data['f']->render_element($this->data['form_elements']['council_registration']);
$o .= $this->data['f']->render_element($this->data['form_elements']['insurance']);


$o .= $this->data['f']->render_element($this->data['form_elements']['status']);

$o .= $this->data['f']->render_element($this->data['form_elements']['stall_id']);// hidden
//$o .= $this->data['f']->render_element($this->data['form_elements']['save_button']);
//$o .= $this->data['f']->render_element($this->data['form_elements']['delete_button']);

$o .= '</div>';// end col-4-md content column


$o .='<div  class="col-md-1">';
$o .='<div style="">';
$o .= $this->data['f']->render_element($this->data['form_elements']['save_button']);
$o .='</div>';
$o .='</div>';


$o .= '<div class="col-md-6">'."\n";


$o .='<div class="row" style="border:1px dashed #888 ;padding:5px;margin-bottom: 10px;">';
    $o .= '<div class="col-md-4" >'."\n";
        $o .= $this->data['f']->render_element($this->data['form_elements']['permanent_site_ids']);
    $o .='</div>';
    $o .= '<div class="col-md-8" >'."\n";
        $o .= '<p>Indicate if permanent site receives a community stall discount or is free</p>';
        $o .= $this->data['f']->render_element($this->data['form_elements']['community_stall']);
        $o .= $this->data['f']->render_element($this->data['form_elements']['community_stall_free']);
    $o .='</div>';
$o .='</div>';



$o .= '<table >';
$o .='<tr>';
$o.='<td width="100">Market</td>';
$o.='<td width="100">Permenant</td>';
$o.='<td width="200">Status</td>';
$o.='<td width="100">Casual</td>';
$o.='<td width="100">Status</td>';
$o.='</tr>';

$perm_status_opts = array("Active","Cancelled","Cancelled late","No show");
$cas_status_opts = array("Active","No show");


if(TWS::isIterable($this->data['markets'])){
    foreach($this->data['markets'] as $m){

        $o .='<tr>';
        $o .= '<td>'.$m['market_date'].'</td>';


        $checked = '';

        if(TWS::isIterable($this->data['permanent_bookings'])){
            foreach($this->data['permanent_bookings'] as $msid=>$ms){
                if($ms['market_id'] == $m['market_id']){
                    $checked = ' checked ';
                    break;
                }
            }
        }

        if(PAST_LOCKED){
            $disabled = isSet($this->data['pastAndFutureMarkets']['past'][$m['market_id']]) ? ' disabled ' : '';
        } else {
            $disabled ='';
        }

        //$disabled="";

        $o .= '<td><input '.$checked.$disabled.' style="width:3em; float:left; margin-left: 10px;" type="checkbox"  name="permanent_allocation_request['.$m['market_id'].']" ></td>';

        $o .= '<td>';
        $o .= '<select '.$disabled.' name="attendance[permanent]['.$m['market_id'].']">';
        $o .= '<option value="">-- Not booked --</option>';
        foreach($perm_status_opts as $opt){

            $selected = '';
            if(TWS::isIterable($this->data['permanent_bookings'])){
                foreach($this->data['permanent_bookings'] as $msid=>$ms){
                    if($ms['market_id'] == $m['market_id'] && $ms['status'] == $opt){
                        $selected  = ' selected ';
                        break;
                    }
                }
            }


            $o .= '<option '.$selected.' value="'.$opt.'">'.$opt.'</option>';
        } 
        $o .= '</select>';
        $o .='</td>';


        $o .= '<td><input '.$disabled.'style="width:3em;" type="text" value="'.$this->data['casual_requests'][$m['market_id']].'" name="casual_allocation_request['.$m['market_id'].']" ></td>';

        $o .= '<td>';
        $o .= '<select '.$disabled.' name="attendance[casual]['.$m['market_id'].']">';
        $o .= '<option value="">-- Not booked --</option>';
        foreach($cas_status_opts as $opt){
            $selected = '';
            if(TWS::isIterable($this->data['casual_bookings'])){
                foreach($this->data['casual_bookings'] as $msid=>$ms){
                    if($ms['market_id'] == $m['market_id'] && $ms['status'] == $opt){
                        $selected  = ' selected ';
                        break;
                    }
                }
            }
            $o .= '<option '.$selected.' value="'.$opt.'" >'.$opt.'</option>';
        } 
        $o .= '</select>';
        $o .='</td>';
        $o .= '</tr>';


    }
}

$o.='</table>';


// PAYMENTS PANEL
$o.='<div id="payments">';
//$o .= $this->data['f']->render_element($this->data['form_elements']['credit']);
$o .='<h4>Payments</h4>';
$o .= $this->data['f']->render_element($this->data['form_elements']['prompt_payment_discount']);
if(TWS::isIterable($this->data['payments'])){
    $o .= '<table style="border:1px solid #888;">';
    $o .= '<tr><th width="100">Date</th><th width="100">($)</th><th>Note</th></tr>';
    $totalPayments=0;
    foreach($this->data['payments'] as $pid=>$payment){
        $o .= '<tr><td>'.$payment['payment_date'].'</td><td>'.$payment['amount'].'</td><td>'.$payment['note'].'</td></tr>';
        $totalPayments += $payment['amount'];
    }
    $o .= '<tr style="background: #eee"><td align="right" height="30">Total:&nbsp;</td><td><strong>'.$totalPayments.'</strong></td><td>&nbsp;</td></tr>';
    $o .='</table>';
}

$o .='<p style="margin-top: 16px;">Season charges estimate: $'.number_format($this->data['charges'],2).'</p>';
$o .='<p>Estimate owing: $'.number_format(  $this->data['charges']-$totalPayments,2).'</p>';
$o .= '<p style="margin-top:40px;"><a class="btn btn-warning" href="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','&ag=payments&stall_id='.$_GET['stall_id']).'">Enter a payment</a></p>';
$o .= '</div>';// end payments panel



$o .= '</div>';

$o .= '</div>';// end of row

$o .= $this->data['f']->render_form_close();



?>