<?php 
        $products =& $this->data['products'];
        $basket=& $this->data['basket'];
        
        if(is_array($products) && count($products))
        {
            $n = 0; // row counter
            $o .= '<form method="post" action="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier).'" >';
            $o .= '<input type="hidden" name="formname" value="member_order_form" />';
            $o .= '<table class="table table-striped" >'."\n";
            $o .= '<thead>';
            $o .= '<tr>
                    <th>ID</th>
                    <th>Product</th>
                    <th>Description</th>
                  
                    <th>Weight <br />(Kg)</th>
                    <th>Splitable</th>
                    <th>Price<br /> ($)</th>
                    
                    <th>Num<br />Units</th>
                    <th>Num <br />Kg</th>
                    
                    
                   </tr>
                   </thead>
                   <tbody>
                  ';
            foreach($products as $p) 
            {
                
               $splitable = '';
                if($p['splitable'] == 1) {
                    $splitable = 'No';
                } else if($p['splitable'] == 2){
                    $splitable = 'Yes';
                }
                $o .=    '<tr class="'.$rowclass.'">
                        <td>'.$p['id'].'</td>
                        <td>'.$p['name'].'</td>
                        <td>'.$p['description'].'</td>
                        <td>'.$p['weight'].'</td>
                        <td>'.$splitable.'</td>
                        <td>'.$p['price'].'</td>';
                        
               $unit_qty = isSet($basket[$p['id']]['unit_qty']) && $basket[$p['id']]['unit_qty'] > 0 ? $basket[$p['id']]['unit_qty'] :'';        
               $o .= '<td><input style="width:3em" type="text" name="unit_qty['.$p['id'].']" size="3" value="'.$unit_qty.'"  /></td>';
                        
                        // only show weight_qty if product is splitable
                if ($splitable == 'Yes'){
                    $weight_qty = isSet($basket[$p['id']]['weight_qty']) && $basket[$p['id']]['weight_qty'] > 0 ? $basket[$p['id']]['weight_qty'] : '';
                    $o .= '<td><input  style="width:3em" type="text" name="weight_qty['.$p['id'].']" size="3" value="'.$weight_qty.'" /></td>';
                } else {
                    $o .= '<td>na</td>';
                }
                        
                $o .= '</tr>';
            } 
            $o .= '</tbody>';
            $o .= '</table>'; 
            
            $o .= '<p style="text-align: right; padding:8px;"><input class="btn btn-primary" type="submit" name="b" value="Save" /></p>';
            $o .= '</form>';
        }
        ?>