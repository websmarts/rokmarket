<?php
// Create js Model
//$o .=  TWS::pr($_SESSION['tws-flashdata'],0) ; 
//$o .=  TWS::pr($this->data['allocation_requests'],0) ; // MAR table entries for stall
//$o .=  TWS::pr($this->data['casual_bookings'],0) ; // MS entries for any  casual bookings for this stall
//$o .=  TWS::pr($this->data['permanent_bookings'],0) ;
//$o .=  TWS::pr($this->data['show_locations'],0) ;
//$o .=  TWS::pr($this->data['casual_requests'],0) ;// puts casual request quantity in usable form eg 1, 1F etc for Casual qty box
//$o .= TWS::pr($this->data['payments'],0); 
//$o .= TWS::pr($this->data['pastAndFutureMarkets'],0); 
//$o .= TWS::pr($this->data['primary_contact'],0);
// Determine which markets have casual bookings
$casualBookings = array();
if (TWS::isIterable($this->data['casual_bookings'])) {
    foreach ($this->data['casual_bookings'] as $b) {
        $casualBookings[$b['market_id']] = true;
    }
}
$o .= '<div class="row">';
$o .= '<div id="loading"></div>';
$o .= '<div class="col-md-3 col-md-offset-1" >' . "\n";
$o .= $this->data['f']->render_form_open();
$o .= $this->data['f']->render_element($this->data['form_elements']['name']);
$o .= $this->data['f']->render_element($this->data['form_elements']['description']);
$o .= $this->data['f']->render_element($this->data['form_elements']['main_contact']);
$o .= '<div>Ph: ' . $this->data['primary_contact']['phone'] . ' ' . $this->data['primary_contact']['mobile'] . '</div>';
$o .= '<div style="padding-bottom:10px;">Email: ' . $this->data['primary_contact']['email'] . '</div>';
$o .= '<div style="padding-bottom:10px;">Address: ' . $this->data['primary_contact']['address1'];
$o .= !empty($this->data['primary_contact']['address2']) ? '<br />' . $this->data['primary_contact']['address2'] : '';
$o .= !empty($this->data['primary_contact']['city']) ? '<br />' . $this->data['primary_contact']['city'] : '';
$o .= !empty($this->data['primary_contact']['postcode']) ? ' ' . $this->data['primary_contact']['postcode'] : '';
$o .= '<br /><a href="' . TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier, '', '?ag=contacts&aga=edit&contact_id=' . $this->data['primary_contact']['contact_id']) . '">edit contact</a>';
$o .= '</div>';
//$o .= $this->data['f']->render_element($this->data['form_elements']['contacts']);
$o .= '</div>';
$o .= '<div class="col-md-3 col-md-offset-1" >' . "\n";
//$o .= $this->data['f']->render_element($this->data['form_elements']['mailname']);
$o .= $this->data['f']->render_element($this->data['form_elements']['category']);
$o .= $this->data['f']->render_element($this->data['form_elements']['notes']);
$o .= '</div>';
$o .= '<div class="col-md-3 col-md-offset-1" >' . "\n";
$o .= $this->data['f']->render_element($this->data['form_elements']['application_form_on_file']);
$o .= $this->data['f']->render_element($this->data['form_elements']['foodseller']);
$o .= $this->data['f']->render_element($this->data['form_elements']['council_registration']);
$o .= $this->data['f']->render_element($this->data['form_elements']['insurance']);
$o .= '<hr>';
$o .= $this->data['f']->render_element($this->data['form_elements']['season_pass']);
$o .= '<hr>';
$o .= $this->data['f']->render_element($this->data['form_elements']['status']);
$o .= $this->data['f']->render_element($this->data['form_elements']['stall_id']);// hidden
//$o .= $this->data['f']->render_element($this->data['form_elements']['save_button']);
//$o .= $this->data['f']->render_element($this->data['form_elements']['delete_button']);
$o .= '</div>';// end col-3-md content column
$o .= '</div>';// end of ROW
$o .= '<div style="text-align: center">';
$o .= $this->data['f']->render_element($this->data['form_elements']['save_button']);
$o .= '</div>';
$o .= '<div class="row">';
$o .= '<div class="col-md-10 col-md-offset-1" >' . "\n";
if ($this->data['stall']['status'] == 'Active') {
    $o .= '<hr>';
    $o .= '<div class="row" style="border:1px dashed #888 ;padding:5px;margin-bottom: 10px;">';
    $o .= '<div class="col-md-4" >' . "\n";
    $o .= $this->data['f']->render_element($this->data['form_elements']['permanent_site_ids']);
    $o .= $this->data['f']->render_element($this->data['form_elements']['relinquish_site']);
    $o .= '</div>';
    $o .= '<div class="col-md-4" >' . "\n";
    $o .= '<p>Indicate if permanent site receives either the community stall discount rate or the free rate</p>';
    $o .= $this->data['f']->render_element($this->data['form_elements']['community_stall']);
    $o .= $this->data['f']->render_element($this->data['form_elements']['community_stall_free']);
    $o .= '</div>';
    $o .= '</div>';
    $o .= '<table width="100%" style="border:0px solid red;" >';
    $o .= '<tr>';
    $o .= '<td width="100">Market</td>';
    $o .= '<td width="100">Permanent <br /><input type="checkbox" id="select_all" /> Select all</td>';
    $o .= '<td width="200">Status</td>';
    $o .= '<td width="100">Casual</td>';
    $o .= '<td width="150">Site info</td>';
    $o .= '<td width="150">Requested</td>';
    $o .= '<td align="center">Show Site<br>location</td>';
    $o .= '</tr>';
    $perm_status_opts = array("Active", "Cancelled", "Cancelled late", "No show", "Closed");
    $cas_status_opts = array("Active", "Cancelled", "Cancelled late", "No show");
    if (TWS::isIterable($this->data['markets'])) {
        foreach ($this->data['markets'] as $m) {
            $o .= '<tr>';
            $o .= '<td>' . $m['market_date'] . '</td>';
            $checked = '';
            if (TWS::isIterable($this->data['permanent_bookings'])) {
                foreach ($this->data['permanent_bookings'] as $msid => $ms) {
                    if ($ms['market_id'] == $m['market_id']) {
                        $checked = ' checked ';
                        break;
                    }
                }
            }
            if (isset($this->data['closed_markets'][$m['market_id']])) {
                $disabled = ' disabled ';
            } else {
                $disabled = '';
            }
            //$disabled="";
            $o .= '<td><input ' . $checked . $disabled . ' class="bookbox" style="width:3em; float:left; margin-left: 10px;" type="checkbox"  name="permanent_allocation_request[' . $m['market_id'] . ']" ></td>';
            $o .= '<td>';
            $o .= '<select ' . $disabled . ' name="attendance[permanent][' . $m['market_id'] . ']">';
            $o .= '<option value="">-- Not booked --</option>';
            foreach ($perm_status_opts as $opt) {
                $selected = '';
                if (TWS::isIterable($this->data['permanent_bookings'])) {
                    foreach ($this->data['permanent_bookings'] as $msid => $ms) {
                        if ($ms['market_id'] == $m['market_id'] && $ms['status'] == $opt) {
                            $selected = ' selected ';
                            break;
                        }
                    }
                }
                $o .= '<option ' . $selected . ' value="' . $opt . '">' . $opt . '</option>';
            }
            $o .= '</select>';
            $o .= '</td>';
            $o .= '<td><input ' . $disabled . 'style="width:3em;" type="text" value="' . $this->data['casual_requests'][$m['market_id']] . '" name="casual_allocation_request[' . $m['market_id'] . ']" ></td>';
            if ($casualBookings[$m['market_id']]) {
                //$o .= '<td>';
                // if no casual bookings for this market - dont show dropdown
                //$o .= '<select '.$disabled.' name="attendance[casual]['.$m['market_id'].']">';
                //$o .= '<option value="">-- Not Assigned --</option>';
                foreach ($cas_status_opts as $opt) {
                    $selected = '';
                    $siteRefs = array();
                    if (TWS::isIterable($this->data['casual_bookings'])) {
                        foreach ($this->data['casual_bookings'] as $msid => $ms) {
                            if ($ms['market_id'] == $m['market_id'] && $ms['status'] == $opt) {
                                $selected = ' selected ';
                                break;
                            }
                        }
                        foreach ($this->data['casual_bookings'] as $msid => $ms) {
                            if ($ms['market_id'] == $m['market_id']) {
                                $siteRefs[] = $ms['site_reference'] . ' ' . $ms['status'];
                            }
                        }
                    }
                    //$o .= '<option '.$selected.' value="'.$opt.'" >'.$opt.'</option>';
                } 
                //$o .= '</select>';
                //$o .='</td>';
                $o .= '<td>' . implode('<br>', $siteRefs) . '</td>';
            } else {
                $o .= '<td>-</td> ';
            }
            $o .= '<td>';
            $datetime = $this->data['allocation_requests'][$m['market_id']]['requested'] > '2013' ? substr($this->data['allocation_requests'][$m['market_id']]['requested'], 0, 16) : '';
            $o .= '<input value="' . $datetime . '" name="requested_datetime[' . $m['market_id'] . ']" type="text" value="" class="form-control datetimepicker" />';
            $o .= '</td>';
            $checked = isset($this->data['show_locations'][$m['market_id']]) ? ' checked="checked" ' : ' ';
            $o .= '<td align="center"><input type="checkbox" name="show_location[' . $m['market_id'] . ']" ' . $checked . '></td>';
            $o .= '</tr>';
        }
    }
    $o .= '</table>';
}
$o .= '<hr>';
$transactionTypes = array(
    1 => 'Payment',
    -1 => 'Stall credit',
    -2 => 'Stall charge'
);
// PAYMENTS PANEL
$o .= '<div id="payments">';
//$o .= $this->data['f']->render_element($this->data['form_elements']['credit']);
$o .= '<h4>Payments</h4>';
$o .= $this->data['f']->render_element($this->data['form_elements']['prompt_payment_discount']);
if (TWS::isIterable($this->data['payments'])) {
    $o .= '<table style="border:1px solid #888;">';
    $o .= '<tr><th width="100">Date</th><th width="100">($)</th><th width="100">Type</th><th>Note</th></tr>';
    $totalPayments = 0;
    foreach ($this->data['payments'] as $pid => $payment) {
        $o .= '<tr><td valign="top">' . $payment['transaction_date'] . '</td><td  valign="top">' . $payment['amount'] . '</td><td  valign="top">' . $transactionTypes[$payment['transaction_type']] . '</td><td  valign="top">' . $payment['note'] . '</td></tr>';
        $totalPayments += $payment['amount'];
    }
    $o .= '<tr style="background: #eee"><td align="right" height="30">Total:&nbsp;</td><td><strong>' . $totalPayments . '</strong></td><td colspan="2">&nbsp;</td></tr>';
    $o .= '</table>';
} else {
    $o .= '<p>NO PAYMENTS FOUND FOR CURRENT SEASON</p>';
}
$o .= '<p style="margin-top: 16px;">Season charges estimate: $' . number_format($this->data['charges'], 2) . '</p>';
$o .= '<p>Estimate owing: $' . number_format($this->data['charges'] - $totalPayments, 2) . '</p>';
$o .= '<p style="margin-top:40px;"><a class="btn btn-warning" href="' . TWS::modx()->makeUrl(TWS::modx()->documentIdentifier, '', '&ag=payments&stall_id=' . $_GET['stall_id']) . '">Add/Edit transactions</a></p>';
$o .= '</div>';// end payments panel
$o .= '</div>';
$o .= '</div>';// end of row
$o .= $this->data['f']->render_form_close();

?>