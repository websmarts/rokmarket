<?php 
$o = '<h3>Products List</h3>'; 
$o .= '<p><a href="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier).'&ag=products&aga=edit&pid=0">Add Product</a></p>';
?>

<?php

//$o .= $this->pr($this->data);

if(isSet($_REQUEST['orderdir'])){
    if(strtolower($_REQUEST['orderdir']) == 'asc'){
        $orderdir='desc';
    } else {
        $orderdir='asc';
    }
    
} else { $orderdir='desc';}

$products =& $this->data['products'];
      
        if(is_array($products) && count($products))
        {
            $url = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','ag=products&amp;orderby=');
            $n = 0; // row counter
            $o .=  '<table class="table table-striped" cellspacing="0" >'."\n";
            $o .=  '<thead>';
            $o .=  '<tr>
                    <th width="20" ><a  title="sort list by id '.$orderdir.'" href="'.$url.'id&orderdir='.$orderdir.'" >ID</a></th>
                    <th width="200" ><a  title="sort list by name '.$orderdir.'" href="'.$url.'name&orderdir='.$orderdir.'" >Product</a></th>
                    <th width="400">Description</th>
                  
                    <th width="100">(Kg)</th>
                    <th width="100"><a  title="sort list by splitable '.$orderdir.'"  href="'.$url.'splitable&orderdir='.$orderdir.'" >Splitable</a></th>
                    <th width="100">Price ($)</th>
                    <th width="60"><a title="sort list by status '.$orderdir.'" href="'.$url.'status&orderdir='.$orderdir.'" >Status</a></th>
                    
                    <th width="100" align="right">&nbsp;</th>
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
                $status = '';
                if($p['status'] == 1) {
                    $status = 'Active';
                } else if($p['status'] == 2){
                    $status = 'Inactive';
                }
                
                $url = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','ag=products&amp;aga=edit&amp;pid='.$p['id']);
                $o .=     '<tr >
                        <td>'.$p['id'].'</td>
                        <td>'.$p['name'].'</td>
                        <td>'.$p['description'].'</td>
                        <td>'.$p['weight'].'</td>
                        <td>'.$splitable.'</td>
                        <td>'.$p['price'].'</td>
                        <td>'.$status.'</td>
                        <td align="right"><a href="'.$url.'">edit</a></td>
                        </tr>
                        ';
            } 
            $o .=  '</tbody>';
            $o .=  '</table>'; 
        }
        
      
?>