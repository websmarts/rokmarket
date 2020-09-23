<?php

//$o .=  TWS::pr($_SESSION[TWS_APP_SESSION_DATAKEY],0) ;
//$o .= TWS::pr($this->data['stalltypes']);

// display the list of markets for the currently selected season. If no season selected then use current date to determine the season
if (TWS::isIterable($this->data['seasons'])) {
    $o .= '<hr><form action="' . TWS::modx()->makeUrl(TWS::modx()->documentIdentifier, '', 'ag=markets&aga=setseason') . '" method="post" class="form-inline" role="form" style="margin-top: 10px;">' . "\n";
    $o .= '<div class="form-group">' . "\n";
    $o .= '<label for="seasonstartyear" >Select season: </label>' . "\n";
    $o .= '<select class="form-control" id="seasonstartyear" name="seasonstartyear" style="width: 120px;">' . "\n";
    foreach ($this->data['seasons'] as $s) {
        $selected = isset($_SESSION[TWS_APP_SESSION_DATAKEY]['season']) && ($_SESSION[TWS_APP_SESSION_DATAKEY]['season'] == $s['seasonstartyear']) ? ' selected="selected" ' : ' ';
        $o .= '<option value="' . $s['seasonstartyear'] . '"' . $selected . ' >' . $s['seasonstartyear'] . '-' . substr($s['seasonstartyear'] + 1, 2, 2) . '&nbsp;</option>' . "\n";
    }
    $o .= '</select>' . "\n";
    $o .= '</div>' . "\n";
    $o .= '<button name="b" value="switch" type="submit" class="btn btn-primary">Go</button>' . "\n";
    // Output season rates table
    $o .= '<p>&nbsp;</p><hr>';
    $o .= '<div  style="width:820px; border:1px solid #ccc;padding:5px;background:#efefef"><table>';
    $o .= '<tr>';
    $o .= '<th width="200" height="30">Booking type</th>';
    $o .= '<th width="200">Standard</th>';
    $o .= '<th width="200">Prompt payment</th>';
    $o .= '<th width="200">Cancellation credit</th>';

    $o .= '</tr>';

    foreach ($this->data['stalltypes'] as $k => $st) {
        $o .= '<tr>';
        $o .= '<td>' . $st['description'] . '</td>';
        $o .= '<td ><input size="3" name="std_site_fee[' . $k . ']" value="' . $st['std_site_fee'] . '" ></td>';
        $o .= '<td ><input size="3"  name="prompt_payment_site_fee[' . $k . ']" value="' . $st['prompt_payment_site_fee'] . '" ></td>';
        $o .= '<td ><input size="3"  name="cancellation_credit[' . $k . ']" value="' . $st['cancellation_credit'] . '" ></td>';

        $o .= '</tr>';

    }
    $o .= '<tr><td colspan="4" align="right" style="padding: 10px;">';
    $o .= '<button name="b" value="update" type="submit" class="btn btn-primary">Update Fee Information</button>' . "\n";
    $o .= '</td></tr>';
    $o .= '</table></div>';

    $o .= "</form>";
    $o .= '<p>&nbsp;</p><hr>';

} else {
    $o .= '<div class="error">ERROR - NO SEASONS FOUND IN MARKETS TABLE!</div>';
}

// output season markets
//$o .= TWS::pr($this->data['season'],0);

if (TWS::isIterable($this->data['season'])) {
    $o .= '<table class="table table-striped">';
    $o .= '<thead>';
    $o .= '<tr>';
    $o .= '<th>Market Date</th>';
    $o .= '<th>Status</th>';
    $o .= '<th>Open/Close</th>';
    $o .= '<th>Cancel*</th>';
    $o .= '</tr>';

    $o .= '<tbody>';

    $o .= '<tr><td colspan="4">* to cancel a market you must enter <strong>cancel market</strong> in the confirm field </td></tr>';

    foreach ($this->data['season'] as $s) {
        $o .= '<tr>';
        $o .= '<td>' . $s['market_date'] . '</td>';
        $o .= '<td>' . $s['status'] . '</td>';
        $o .= '<td><a href="' . $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier, '', 'ag=markets&aga=statustoggle&market_id=' . $s['market_id']) . '">Toggle status</a></td>';
        $o .= '<td>';

        $o .= '<form action="' . TWS::modx()->makeUrl(TWS::modx()->documentIdentifier, '', 'ag=markets&aga=cancel&market_id=' . $s['market_id']) . '" method="post" class="form-inline" role="form" style="margin-top: 3px;">' . "\n";
        $o .= '<div class="form-group">' . "\n";
        $o .= '<label for="confirm_cancellation" ></label>' . "\n";
        $o .= '<input class="form-control" id="confirm_cancellation" name="confirm_cancellation" style="width: 120px;" placeholder="Confirm code">' . "\n";
        $o .= '</div>' . "\n";
        $o .= '<button name="b" value="cancel" type="submit" class="btn btn-primary btn-xs">Cancel</button>' . "\n";
        $o .= '</form>';
        $o .= '</tr>';
    }
    $o .= '</tbody>';
    $o .= '</table>';
}
