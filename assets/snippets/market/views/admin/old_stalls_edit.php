<?php

// Create js Model



//$o .=  TWS::pr($_SESSION['tws-flashdata'],0) ; 
//$o .=  TWS::pr($this->data['allocation_requests'],0) ; 
//$o .=  TWS::pr($this->data['permanent_bookings'],0) ; 
//$o .=  TWS::pr($this->data['casual_requests'],0) ; 

$o.='<div class="row">';

$o .='<div id="loading"></div>';


$o .= '<div class="col-md-4 col-md-offset-1">'."\n";

$o .= $this->data['f']->render_form_open();
$o .= $this->data['f']->render_element($this->data['form_elements']['name']);
$o .= $this->data['f']->render_element($this->data['form_elements']['description']);
$o .= $this->data['f']->render_element($this->data['form_elements']['contacts']);
$o .= $this->data['f']->render_element($this->data['form_elements']['permanent_site_ids']);
$o .= $this->data['f']->render_element($this->data['form_elements']['category']);






$o .= $this->data['f']->render_element($this->data['form_elements']['notes']);
$o .= $this->data['f']->render_element($this->data['form_elements']['community_stall']);
$o .= $this->data['f']->render_element($this->data['form_elements']['application_form_on_file']);
$o .= $this->data['f']->render_element($this->data['form_elements']['foodseller']);
$o .= $this->data['f']->render_element($this->data['form_elements']['council_registration']);
$o .= $this->data['f']->render_element($this->data['form_elements']['insurance']);
$o .= $this->data['f']->render_element($this->data['form_elements']['prompt_payment_discount']);
$o .= $this->data['f']->render_element($this->data['form_elements']['credit']);
$o .='<p>Charges against credit='.$this->data['charges'].'</p>';
$o .= $this->data['f']->render_element($this->data['form_elements']['status']);


if(isSet($this->data['stall']['stall_id']) && $this->data['stall']['status'] == 'Active' ){ // dont show if creating new booking or stall not active
    if(  TWS::isIterable($this->data['permanent_sites'])){
        $o .= '<p>Permanent site(s): '. implode(',',$this->data['permanent_sites']) .'</p>';
       
    } 
    

}



$o .= $this->data['f']->render_element($this->data['form_elements']['stall_id']);// hidden
$o .= $this->data['f']->render_element($this->data['form_elements']['save_button']);
$o .= $this->data['f']->render_element($this->data['form_elements']['delete_button']);
$o .= $this->data['f']->render_form_close();
$o .= '</div>';// end 4 col content column





$o .= '<div class="col-md-6 col-md-offset-1">'."\n";




$o .= '<div class="row">';
$o .= '<div class="col-md-12 ">'."\n";
$o.='<h4 style="margin-left:-15px;">Market Allocation Requests</h4>';
$o .= $this->data['f']->render_element($this->data['form_elements']['permanent_site_ids']);





$o .= '<div style="clear: left;float:left; width: 100px;">&nbsp;&nbsp;Market date</div>';
$o .= '<div style="float:left; margin-left: 10px; width: 100px;">&nbsp;&nbsp;Permanent</div>';
$o .= '<div style="float:left; margin-left: 10px; width: 100px;">&nbsp;&nbsp;Casual</div>';

if(TWS::isIterable($this->data['markets'])){
    foreach($this->data['markets'] as $m){
       
        
        $o .= '<div style="clear: left;float:left; width: 100px;">&nbsp;&nbsp;'.$m['market_date'].'</div>';
        
        
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
        
        $o .= '<input '.$checked.$disabled.' style="width:3em; float:left; margin-left: 10px;" type="checkbox"  name="permanent_allocation_request['.$m['market_id'].']" >';
        
        $o .= '<input type="radio" name=market_status['.$m['market_id'].']" value="Active">';
        $o .= '<input type="radio" name=market_status['.$m['market_id'].']" value="Cancelled">';
        $o .= '<input type="radio" name=market_status['.$m['market_id'].']" value="Cancelled late">';
        $o .= '<input type="radio" name=market_status['.$m['market_id'].']" value="No Show">';
        
        
        $o .= '<input '.$disabled.'style="width:3em;float:left;margin-left:77px;" type="text" value="'.$this->data['casual_requests'][$m['market_id']].'" name="casual_allocation_request['.$m['market_id'].']" >';
        
        
    }
}

$o .='</div>';

$o .='</div>';// end row;
$o .= '</div>';

$o .= '</div>';// end of row




?>