<?php $o = '<h3>Members List</h3>'; ?>

<?php

//$o .= $this->pr($this->data);

$members =& $this->data['members'];
        if(is_array($members) && count($members))
        {
            $n = 0; // row counter
            $o .= '<form action="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','ag=members').'" method="post" />';
            $o .=  '<table class="table table-striped" width="100%">'."\n";
            $o .=  '<thead>';
            $o .=  '<tr>
                    <th width="100" >Delete</th>
                    
                    <th width="">Name</th>
                    <th width="200" >Email</th>
                    <th width="100" align="right">Shopper</th>
                   </tr>
                   </thead>
                   <tbody>
                  ';
            foreach($members as $m) 
            {
                $checked = $m['shopping_group_id']  > 0 ? ' checked="checked" ' : '';
                $rowclass = $n++ % 2 == 0 ? 'even' : 'odd';
                
                
                $o .=     '<tr class="'.$rowclass.'">
                        <td><a onclick="return confirm(\'Are you sure want to delete this member\');" title="delete member" href="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','ag=members&aga=delete&mid='.$m['internalKey']).'"><i class="icon-remove-circle"></i></a></td>
                        <td>'.$m['fullname'].'</td>
                        <td>'.$m['email'].'</td> 
                        <td align="right"><input '.$checked.' type="checkbox" name="shoppers['.$m['internalKey'].']"</td>
                        </tr>
                        ';
            } 
            
            $o .=  '</tbody>';
            $o .=  '</table>';
            $o .= '<div style="text-align:right;padding-right:5px;margin-top:12px;"><input type="submit" name="b" value="Save" class="btn" /></div>';
            $o .=  '</form>'; 
        }
 
  

?>