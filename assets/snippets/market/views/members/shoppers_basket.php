<?php $o = '<h3>Order form: '.$this->data['member']['fullname'].'</h3>'; 

    $buygroupInfo =& $this->data['buygroup_info'];
    //TWS::pr($buygroupInfo);
    $o .= '<p>'.ucFirst($buygroupInfo['buygroup_name']).'</p>';
    $o .= '<p>Open from: '.$buygroupInfo['buygroup_start_date'].' to '.$buygroupInfo['buygroup_end_date']. '</p>';
    //$o .= '<p>Status: <strong>'.ucFirst($buygroupInfo['buygroup_status']).'</strong></p>'; 
    
    if($this->data['buygroup_is_open']){
       $o .= '<p>Status: <strong>Open</strong></p>';
    } else {
        if($buygroupInfo['buygroup_status'] =='open'){
            //dates must have expired
            $o .= '<p>Status: <strong>Not currently within in shopping dates range</strong></p>'; 
        } else {
            $o .= '<p>Status: <strong>'.ucFirst($buygroupInfo['buygroup_status']).'</strong></p>'; 
        }
    
    }
    
?>

<?php //TWS::pr($this->data['member'])?>


<?php 
    $member =& $this->data['member'];
    $products =& $this->data['products'];
    $basket =& $this->data['basket'];
    //TWS::pr($basket);

    if(is_array($products) && count($products))
    {
        $n = 0; // row counter
        $o .= '<form method="post" action="" >';
        $o .= '<input type="hidden" name="formname" value="shoppers_basket" />';
        $o .= '<input type="hidden" name="iid" value="'.$this->data['member']['internalKey'].'" />'."\n";
        $o .= '<table class="table table-striped" width="100%" >'."\n";
        $o .= '<thead>';
        $o .= '<tr>
        <th >ID</th>
        <th >Product</th>
        <th >Description</th>

        <th >Weight <br />(Kg)</th>
        <th >Splitable</th>
        <th >Price ($)</th>

        <th >Num<br />Units</th>
        <th >Num <br />Kg</th>';
        if(!$this->data['buygroup_is_open']) {
            $o .= '<th >Kg<br />Adjust</th>';
        }
        
        $o .='<th>Ext<br />Price($)<th>';

        $o .= '</tr>
        </thead>
        <tbody>
        ';
        $totalcost = 0;
        foreach($products as $p) 
        {

           $linecost_qty =0;
           $linecost_weight=0;
           $linecost_weight_adjust=0;

            $splitable = '';
            if($p['splitable'] == 1) {
                $splitable = 'No';
            } else if($p['splitable'] == 2){
                $splitable = 'Yes';
            }
            
             // display price for unit [and kg if splitable)]
            $displayPrice = number_format($p['price'],2);
            if ($splitable =='Yes'){
                $displayPrice .=' <br />('. number_format($p['price']/$p['weight'],2).'/ Kg)';
            }
            
            
            $o .=    '<tr>
            <td>'.$p['id'].'</td>
            <td>'.$p['name'].'</td>
            <td>'.$p['description'].'</td>
            <td>'.$p['weight'].'</td>
            <td>'.$splitable.'</td>
            <td>'.$displayPrice.'</td>';

            $unit_qty = isSet($basket[$p['id']]['unit_qty']) && $basket[$p['id']]['unit_qty'] > 0 ? $basket[$p['id']]['unit_qty'] :''; 
            $linecost_qty = $p['price'] * $unit_qty;

            if($this->data['buygroup_is_open']) {
                $o .= '<td><input style="width:2em" type="text" name="unit_qty['.$p['id'].']" size="3" value="'.$unit_qty.'"  /></td>';
            }   else if ($buygroupInfo['buygroup_status'] =='adjusting')  {
                $o .= '<td><input style="width:2em" type="hidden" name="unit_qty['.$p['id'].']" size="3" value="'.$unit_qty.'"  />'.$unit_qty.'</td>';
            } else {
                $o .= '<td>'.$unit_qty.'</td>';
            }


            // only show weight_qty if product is splitable
            if ($splitable == 'Yes'){
                $weight_qty = isSet($basket[$p['id']]['weight_qty']) && $basket[$p['id']]['weight_qty'] > 0 ? $basket[$p['id']]['weight_qty'] : '';
                $linecost_weight = $p['price'] * ($weight_qty/$p['weight']);
                if($this->data['buygroup_is_open']) {
                    $o .= '<td><input style="width:2em"  type="text" name="weight_qty['.$p['id'].']" size="3" value="'.$weight_qty.'" /></td>';
                }   else if ($buygroupInfo['buygroup_status'] =='adjusting')  {
                    $o .= '<td><input style="width:2em"  type="hidden" name="weight_qty['.$p['id'].']" size="3" value="'.$weight_qty.'" />'.$weight_qty.'</td>';
                } else{
                    $o .= '<td>'.$weight_qty.'</td>'; 
                }
            } else {
                $o .= '<td>-</td>';
            }
            if(!$this->data['buygroup_is_open']) {
                if ($splitable == 'Yes'){
                    $weight_qty_adjust = isSet($basket[$p['id']]['weight_qty_adjust'])  ? $basket[$p['id']]['weight_qty_adjust'] : '';
                    $linecost_weight_adjust = ($weight_qty_adjust/$p['weight']) * $p['price'];
                    if($weight_qty_adjust > 0 ){
                        $weight_qty_adjust = number_format($weight_qty_adjust,2);
                    } else {
                        $weight_qty_adjust ='';
                    }
                    $o .= '    <td>'.$weight_qty_adjust.'</td>';    


                } else {
                    $o .= '    <td>-</td>';  
                }
                // $o .= '    <td>&nbsp;</td>'; 
            } 
             if(!$this->data['buygroup_is_open']) {
            // Show adjusted price
                $o .='<td>'.number_format($linecost_qty + $linecost_weight_adjust,2).'</td>';
            } else {
                $o .='<td>'.number_format($linecost_qty + $linecost_weight,2).'</td>';
            }
            
            $o .='</tr>';
            $totalcost += $linecost_qty + $linecost_weight;
            $totalcost_adjust += $linecost_qty + $linecost_weight_adjust;
        } 
        $o .= '</tbody>';
        $o .= '</table>'; 
        
        if(!$this->data['buygroup_is_open']) {
            // Show adjusted price
            $totalDisplay = $totalcost_adjust;
            $adjusted = ' (adjusted) ';
        } else {
            $totalDisplay = $totalcost;
            $adjusted = '';
        }
        $o .= '<p style="padding:5px;border-top: 1px dashed #888;text-align: right;padding-right: 20px;font-weight: bold;font-size:130%;">Total  '.$adjusted.': $'.number_format($totalDisplay,2).'</p>';
        if($buygroupInfo['buygroup_status'] =='open') {
            $o .= '<p align="right" style="padding-right: 20px"><input class="btn btn-primary" type="submit" name="b" value="Update" /></p>';
        }
        $o .= '</form>';
    }
?>
  

