<?php $o = '<h3>Shoppers List</h3>'; ?>

<?php 
$buygroupInfo =& $this->data['buygroup_info'];
//TWS::pr($buygroupInfo);
$o .= '<p>Group status: <strong>'.ucFirst($buygroupInfo['buygroup_status']).'</strong></p>';


?>

<?php

//$o .= TWS::pr($this->data);

$shoppers =& $this->data['shoppers'];
        if(is_array($shoppers) && count($shoppers))
        {
            $n = 0; // row counter
            
            $o .=  '<table class="table table-striped" width="100%">'."\n";
            $o .=  '<thead>';
            $o .=  '<tr>
                    <th width="50" >ID</th>
                    
                    <th width="">Name</th>
                    <th width="200" >Email</th>
                    <th width="120" >Phone</th>
                    <th width="100" align="right">&nbsp;</th>
                    <th>-</th>
                   </tr>
                   </thead>
                   <tbody>
                  ';
            $total = 0;
            foreach($shoppers as $m) 
            {
                $checked = $m['shopping_group_id']  > 0 ? ' checked="checked" ' : '';
                $rowclass = $n++ % 2 == 0 ? 'even' : 'odd';
                
                $url = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','ag=shoppers&amp;aga=basket&amp;iid='.$m['internalKey']);
                $o .=     '<tr class="'.$rowclass.'">
                        <td>'.$m['internalKey'].'</td>
                        <td>'.$m['fullname'].'</td>
                        <td>'.$m['email'].'</td>
                        <td>'.$m['phone']. ' '.$m['mobilephone'].'</td>  
                        <td align="right"><a href="'.$url.'">view order</a></td>
                        <td>'.$m['order_total'].'</td>
                        </tr>
                        ';
                $total += $m['order_total'];
                        
            } 
            
            $o .= '<tr><td colspan="5"  >&nbsp;</td><td align="right"><strong>'.$total .'</strong></td></tr>';
            
            $o .=  '</tbody>';
            $o .=  '</table>';
            
            
        }
        else {
            $o .= '<p>No Shoppers to list</p>';
        }
 
  
  
?>