<?php


//$o .=  TWS::pr($_SESSION['myapp'],0) ; 

/*
$o .='<a class="btn btn-primary" href="'.$url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=sites&aga=edit&site_id=0').'">Add a Site</a>';
if(TWS::isIterable($this->data['sites'])){
    $o .= '<table class="table table-striped">';
    $o .= '<thead>';
    $o .= '<tr>';
    $o .= '<th>Reference</th>';
    $o .= '<th>Location</th>';
    $o .= '<th>Note</th>';
    $o .= '<th>Action</th>';
    $o .= '</tr>';

    $o .= '<tbody>';

    foreach($this->data['sites'] as $r){
        $o .= '<tr>';
        $o .= '<td>'.$r['site_reference'].'</td>';
        $o .= '<td>'.$r['location_id'].'</td>';
        $o .= '<td>'.$r['note'].'</td>';
        
        
        $o .= '<td><a href="'.$url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier,'','ag=sites&aga=edit&site_id='.$r['site_id']) .'">Edit</a></td>';
        $o .= '</tr>';
    }
    $o .= '</tbody>';
    $o .= '</table>';
} else {
    $o .= '<p>No records found</p>';
}
*/
//echo ' just about to creat a DG instance';
 $dg = new C_DataGrid("SELECT site_reference,location_id,site_id,note FROM sites","site_id","sites");
        //$dg -> set_query_filter("parent_id=0");

        // change default caption
        $dg -> set_caption("Sites");

        // hide ID  column
        
        $dg -> set_col_hidden("site_id",false);
        $dg->set_col_title("site_reference","Site # ");
        $dg->set_col_title("note","Site note");
        $dg->set_col_title("location_id","Area located");
        
        //$dg->set_group_properties('location_id');
        

        $dg->set_col_edittype('location_id',"select", "SELECT location_id,`name`  from `locations`");

        $dg->enable_edit('INLINE', 'CRUD');

        //$dg->set_pagesize(10); //pagination pagesize
        $dg->set_scroll(true);
        //$dg->enable_kb_nav(true);
        // enable integrated search
        $dg->enable_search(true);

        // Second grid as detail grid
        if(0){
            $sdg = new C_DataGrid("SELECT * FROM z_properties","property_id",'z_properties');
            $sdg -> set_caption("Contact - Properties");
            //$sdg -> set_col_hidden("id");

            //$sdg->set_col_edittype('fund_category_id',"select", "SELECT id,`name` from `z_fund_categories`");
            //$sdg->set_col_edittype('job_id',"select", "SELECT job_id,concat(`reference`,' : ', `name` ) from `z_jobs`");
            //$sdg->set_col_edittype('fund_id',"select", "SELECT fund_id, `name`  from `z_funds`");
            //$sdg -> set_col_readonly("fund_id"); 

            // Define Master detail grid relationship by passing the detail grid object as the first parameter, then the foriegn key name
            $dg->set_masterdetail($sdg, 'contact_id');
        }    
        $dg->display(false);
        $o .= $dg->get_display(true);
?>