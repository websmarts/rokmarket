<?php
//TWS::pr($this->data['result']);
$o = '';
$o .= '<form method="post" action="">';
$o .= '<div class="row" style="border-bottom:6px solid #ccc;margin-bottom:6px;">';
$o .= '<div class="col-md-2 col-md-offset-1">' . "\n";
$o .= 'Report Start Date<br />';
$o .= '<input class="datepicker" type="text" name="report_start_date" value="' . $this->data['reportStartDate'] . '" style="width:7em">';
$o .= '</div>';
$o .= '<div class="col-md-1 ">' . "\n";
$o .= '&nbsp;<br /><input class="btn btn-success" type="submit" name="b" value="Refresh report" >';
$o .= '</div>';
$o .= '<div class="col-md-2 col-md-offset-1">' . "\n";
$o .= 'Report End Date<br />';
$o .= '<input class="datepicker" type="text" name="report_end_date" value="' . $this->data['reportEndDate'] . '" style="width:7em">';
$o .= '</div>';
$o .= '<div class="col-md-3 col-md-offset-1">' . "\n";
$checked = $this->data['showPayments'] > 0 ? ' checked ' : '';
$o .= ' <input type="checkbox" name="show_payments" ' . $checked . ' value="1"  > Show payments<br />';
$checked = $this->data['showCredits'] > 0 ? ' checked ' : '';
$o .= ' <input type="checkbox" name="show_credits" ' . $checked . '  value="1"  > Show credits<br />';
$checked = $this->data['showCharges'] > 0 ? ' checked ' : '';
$o .= ' <input type="checkbox" name="show_charges" ' . $checked . ' value="1" > Show extra charges<br />';
$o .= '</div>';
$o .= '</div>';
$o .= '<div><input class="btn btn-success" type="submit" name="b" value="Transfer previous season credits" ></div>';
$o .= '</form>';
$o .= '<h3>Collections report:</h3>';

$o .= '<table  id="mytable" class="table table-striped" >';
$o .= '<thead>';
$o .= '<tr>';
$o .= '<th width="200">Stallname</th>';
$o .= '<th width="100">Amount<br />($)</th>';
$o .= '<th width="100">Type<br /></th>';
$o .= '<th >T Date</th>';
$o .= '<th class="no_order" >Note</th>';
$o .= '</tr>';
$o .= '</thead>';
$o .= '<tbody>';
$total = array();
$url = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier, '', '&ag=stalls&aga=edit&stall_id=');
if (TWS::isIterable($this->data['result'])) {
    foreach ($this->data['result'] as $r) {
        $o .= '<tr>';
        $o .= '<td><a href="' . $url . $r['stall_id'] . '">' . $r['stallname'] . '</a></td>';
        $o .= '<td>' . $r['amount'] . '</td>';
        $o .= '<td>' . $r['transaction_type'] . '</td>';
        $o .= '<td>' . $r['transaction_date'] . '</td>';
        $o .= '<td>' . $r['note'] . '</td>';
        //$o .= '<td>'.$r['market_id'].(!empty($r['site_reference'])?' / ':'') .$r['site_reference'].'</td>';
        $o .= '</tr>';
        $total[$r['transaction_type']] += $r['amount'];
    }
}
$o .= '<tr><td><strong>TOTAL</strong></td><td>[1] <strong>' . number_format($total[1], 2) . '</strong></td><td>[-1] <strong>' . number_format($total[-1], 2) . '</strong></td><td>[-2] <strong>' . number_format($total[-2], 2) . '</strong></td><td colspan="2">&nbsp;</td></tr>';
$o .= '</tbody>';
$o .= '</table>';
// Format dataTable
$o .= "\n" . '<script type="text/javascript">
 $(document).ready(function() {
    $("#Xmytable").dataTable( {
        "order": [ [ 3, "asc" ] ],
        "columnDefs": [
            { "orderable": false, "targets": "no_order" }
          ],
          "paging": false,
          "searching": false
    } );
} );
</script>';
