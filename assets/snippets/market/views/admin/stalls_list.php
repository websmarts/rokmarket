<?php


//$o .=  TWS::pr($_SESSION['myapp'],0) ; 

//$o .= TWS::pr($this->data['stalls'],false);


$o .='<p><a class="btn btn-primary" href="'.$url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=stalls&aga=edit&stall_id=0').'">Add a Stall</a></p>';
//$o .='&nbsp;<a class="btn btn-primary" href="'.$url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=stalls&aga=listperms').'">List Permenants</a>';
if(TWS::isIterable($this->data['stalls'])){
    $o .= '<table id="mytable" class="table table-striped tablesorter">';
    $o .= '<thead>';
    $o .= '<tr>';
    $o .= '<th>Type(s)</th>';
    $o .= '<th>Stall name</th>';
    $o .= '<th>Contact firstname</th>';
    $o .= '<th>Contact lasttname</th>';
    $o .= '<th>Status</th>';
    //$o .= '<th>Type</th>';
    //$o .= '<th>P.Sites</th>';
    $o .= '<th>Description</th>';
   
   
    $o .= '<th class="no_order">Action</th>';
    $o .= '</tr>';
    $o .='</thead>';
    $o .= '<tbody>';

    foreach($this->data['stalls'] as $r){
        $o .= '<tr>';
        $o .= '<td>'.$r['stalltype'].'</td>';
        $o .= '<td>'.$r['name'].'</td>';
        $o .= '<td>'.$r['stallholders'][0]['firstname'].'</td>';
        $o .= '<td>'.$r['stallholders'][0]['lastname'].'</td>';
        $o .= '<td>'.$r['status'].'</td>';
        //$o .= '<td>'.$r['stalltype'].'</td>';
       // $o .= '<td>'.implode(',',$r['permanent_sites']).'</td>';
       
        
        $o .= '<td>'.$r['description'].'</td>';
        
        
        
        $o .= '<td><a href="'.$url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=stalls&aga=edit&stall_id='.$r['stall_id']) .'">Edit</a></td>';
        $o .= '</tr>';
    }
    $o .= '</tbody>';
    $o .= '</table>';
    
    // Format dataTable
    $o .= "\n".'<script type="text/javascript"> 
     $(document).ready(function() {
        $("#mytable").dataTable( {
            "order": [ [ 3, "asc" ] ],
            "columnDefs": [
                { "orderable": false, "targets": "no_order" }
              ]
        } );
    } );
    </script>';
} else {
    $o .= '<p>No records found</p>';
}
?>