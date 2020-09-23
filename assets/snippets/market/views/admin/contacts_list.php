<?php


//$o .=  TWS::pr($this->data['result'],0) ; 


$o .='<p><a class="btn btn-primary" href="'.$url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=contacts&aga=edit&contact_id=0').'">Add a Contact</a></p>';
//$o .='&nbsp;<a class="btn btn-primary" href="'.$url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=stalls&aga=listperms').'">List Permenants</a>';
if(TWS::isIterable($this->data['result'])){
    $o .= '<table id="mytable" class="table table-striped tablesorter">';
    $o .= '<thead>';
    $o .= '<tr>';
    $o .= '<th class="no_order">ID</th>';
    $o .= '<th>First name</th>';
    $o .= '<th>Last name</th>';
    $o .= '<th>Address</th>';
    $o .= '<th>Ph</th>';
    $o .= '<th>Email</th>';
    $o .= '<th>Status</th>';
    $o .= '<th  class="no_order">&nbsp;</th>';
   
    $o .= '</tr>';
    $o .='</thead>';
    $o .= '<tbody>';

    foreach($this->data['result'] as $r){
        $o .= '<tr>';
        $o .= '<td><a href="'. TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=contacts&aga=edit&contact_id='.$r['contact_id']) .'">Edit</a></td>';    
        $o .= '<td>'.$r['firstname'].'</td>';
        $o .= '<td>'.$r['lastname'].'</td>';
        $o .= '<td>'.$r['address1'].'</td>';
        $o .= '<td>'.$r['phone'].'</td>';
        $o .= '<td>'.$r['email'].'</td>';
        $o .= '<td>'.$r['status'].'</td>';
        $o .= '<td><a href="'. TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=stalls&contact_id='.$r['contact_id']) .'">Stalls</a></td>';
        
        $o .= '</tr>';
    }
    $o .= '</tbody>';
    $o .= '</table>';
} else {
    $o .= '<p>No records found</p>';
}

// Format dataTable
$o .= "\n".'<script type="text/javascript"> 
 $(document).ready(function() {
    $("#mytable").dataTable( {
        "order": [ [ 2, "asc" ] ],
        "columnDefs": [
            { "orderable": false, "targets": "no_order" }
          ]
    } );
} );
</script>';
?>