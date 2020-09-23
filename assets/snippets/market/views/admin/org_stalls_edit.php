<?php

// Create js Model



//$o .=  TWS::pr($_SESSION['tws-flashdata'],0) ; 

$o.='<div class="row">';
$o .= '<ul class="nav nav-tabs" id="myTab">
  <li class="active"><a href="#stallinfo" data-toggle="tab">Stall</a></li>
  <li><a href="#sites" data-toggle="tab">Sites</a></li>
  
</ul>';

$o .= '<div class="tab-content">';
$o .= '<div class="tab-pane active" id="stallinfo">';
$o.='<div class="col-md-3 col-md-offset-1">'."\n";
//$o .= $this->data['f']->render();

$o .= $this->data['f']->render_form_open();
$o .= $this->data['f']->render_element($this->data['form_elements']['name']);
$o .= $this->data['f']->render_element($this->data['form_elements']['description']);
$o .= $this->data['f']->render_element($this->data['form_elements']['contacts']);
$o .= $this->data['f']->render_element($this->data['form_elements']['category']);
$o .= $this->data['f']->render_element($this->data['form_elements']['stalltype']);

$o .= $this->data['f']->render_element($this->data['form_elements']['status']);
$o .= $this->data['f']->render_element($this->data['form_elements']['notes']);

$o .= $this->data['f']->render_element($this->data['form_elements']['stall_id']);// hidden
$o .= $this->data['f']->render_element($this->data['form_elements']['save_button']);
$o .= $this->data['f']->render_element($this->data['form_elements']['delete_button']);
$o .= $this->data['f']->render_form_close();

$o .= '</div>'."\n";

$o .= '<div class="col-md-7 col-md-offset-1">'."\n";
$o .= '<h3>'. $this->data['season']. ' Season markets</h3>'."\n";

//$o .= TWS::pr($this->data['markets'],false);



if ( TWS::isIterable($this->data['markets'])){
    $o .= '<form class="form-horizontal" role="form" acton="" method="" >'."\n";
    foreach($this->data['markets'] as $m){
        
        $past_market = date('Y-m-d') > $m['market_date'];
        $disabled = $past_market ? ' disabled ' : '';
        
        
        // adjust the select size if more than one stall booked
        $select_size = count($this->data['stall_sites'][$m['market_id']] ) > 0 ? count($this->data['stall_sites'][$m['market_id']] ) : 1;
        
        $o .='<div class="form-group '.$disabled .'">'."\n";

        
        $o .= '<label class="col-sm-2 control-label">'.$m['market_date'].'</label>';
        
    

        $o .= '<div class="col-sm-5">';     
        $o .= '<select '. $disabled .'  multiple size="'.$select_size.'" name="site_ids['.$m['market_id'].']" style="width:18em">';
        // put the selected sites at the top of the list and select them
        $skip_site_ids=array();
        if(TWS::isIterable( $this->data['stall_sites'][$m['market_id']] )){
            foreach($this->data['stall_sites'][$m['market_id']] as $site){
                $o .= '<option selected value="'.$site['site_id'].'">'.$site['site_reference'].' ('.$site['status'].') ' .$site['location'].'</option>';
                $skip_site_ids[$site['site_id']]=$site['site_id'];
            }
        }
        // Show all other sites that are currently available 
        if(TWS::isIterable( $this->data['available_sites'][$m['market_id']] )){
            foreach($this->data['available_sites'][$m['market_id']] as $site){
                if(in_array($site['site_id'],$skip_site_ids)) continue;
                $o .= '<option value="'.$site['site_id'].'">'.$site['site_reference'].' '.$site['location'].'</option>';
            }
        }       
        $o .= '</select>';      
        $o .= '</div>';

        $o .= '<div class="col-sm-5" style="text-align: right">'; 
        if(!$past_market){
           $o .= '<a href="" class="btn btn-danger">Cancel</a>';  
        }
        
        $o .= '</div>'."\n";
               
        
        $o .= '</div>'."\n";
        
    }
    $o .='<input type="submit" name="b" value="Save market sites" class="btn btn-primary">'."\n";
    $o .= '</form>'."\n";
}
$o .= '</div>';//
$o .= '</div>';// end first tab-pane
$o .= '</div>';// end  tab-content




$o .= '</div>';// end row




?>