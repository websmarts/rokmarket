<?php


//$o .=  TWS::pr($_SESSION,0) ; 

/*
// display the list of markets for the currently selected season. If no season selected then use current date to determine the season
$o .= '<form action="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier,'','ag=locations').'" method="post" class="form" role="form" >'."\n";
$o .= '<table class="table table-striped">';
$o .= '<thead>';
$o .= '<tr>';
$o .= '<th>Name</th>'; 
$o .= '</tr>';
$o .= '<tbody>';
$o .= '<tr>';
$o .= '<td><input type="text" name="locations[0]" value="'.$r['name'].'"> New area.</td>';
$o .= '</tr>';

if(TWS::isIterable($this->data['locations'])){


    foreach($this->data['locations'] as $r){
        $o .= '<tr>';
        $o .= '<td><input type="text" name="locations['.$r['location_id'].']" value="'.$r['name'].'"></td>';

        $o .= '</tr>';
    }


} 
$o .= '</tbody>';
$o .= '</table>';
$o .= '<input type="hidden" name="aga" value="update" >'."\n";
$o .= '<button name="b" type="submit" class="btn btn-primary">Update</button>'."\n";
$o .= '</form>';
*/
 $dg = new C_DataGrid("SELECT * FROM locations","location_id","locations");
        //$dg -> set_query_filter("parent_id=0");

        // change default caption
        $dg -> set_caption("Locations");

        // hide ID  column
        
        $dg -> set_col_hidden("location_id",false);
        $dg->set_col_title("name","Name # ");
        
        
        //$dg->set_group_properties('location_id');
        

       

        $dg->enable_edit('INLINE', 'CRU');

        $dg->set_pagesize(10); //pagination pagesize
        //$dg->set_scroll(true);
        //$dg->enable_kb_nav(true);
        // enable integrated search
        //$dg->enable_search(true);

        
        $dg->display(false);
        $o .= $dg->get_display(true);
?>