<?php
/**
*  This is a short description of Admin class
* 
* This is a longer description that
* can span a few lines
* 
* @author Ian Maclagan <iwmaclagan@gmail.com>
* 
* @since 1.0
*/
class Admin Extends App
{
    var $error;
 
    
    var $sessionDataKey = 'tws_market_app';
    var $ajax; // true if ajax request
    
    var $nowtime;// time in seconds for NOW 
    var $nowDate;// NOW in 'Y-M-d' format

    var $season; // the start Year of selected season 
    var $markets; // an array of markets in the selected season
    var $pastAndFutureMarkets;// markets keyed by ['past'] and ['future']

    


    function __construct($config){
        $this->Admin($config);
    }
    /**
    * The Amin method is the main constructor
    * 
    * 
    * 
    */
    function Admin($config = false){
        
        

        if(!$config){
            die ('no config files present');
        } else {
            $this->config = $config;
        }

        $this->viewDir = MODX_BASE_PATH . 'assets/snippets/market/views/admin/';
        
        /**
        * the REQUEST params that determine what method gets called
        */
         $this->requestMethodAction =isSet($_REQUEST['ag']) && !empty($_REQUEST['ag']) ? $_REQUEST['ag'] : 'markets';
         $this->requestMethod = isSet($_REQUEST['aga']) && !empty($_REQUEST['aga']) ? $_REQUEST['aga'] : '';
        
        
        
        /**
        * Check if ajax request
        */
        if(!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            /* special ajax here */
            $this->ajax = true;
        }



        $this->nowtime = $this->nowTime();
        $this->nowDate = date('Y-m-d',$this->nowtime);
        
        // Check if this and next season are loaded into the database - if not do it
        $this->_check_and_create_seasons(); 

        // Check if any SEASON has been set yet for application to work with
        // if not the set current season
        // sets $this->season
        if(!$this->getSeason()){
            $this->setSeason($this->getCurrentSeason()); // default to the NOW season
        }
        
        
        $this->season = new Season($this->nowtime);
        
        exit;

        

        // markets for set season
        $this->markets = $this->_get_season_markets($this->_get_set_season());

        // past and future markets
        //arry['past']=arry of past markets
        // arry['future'] array of future markets
        $this->pastAndFutureMarkets = $this->_get_past_and_future_markets();


        // Good place to add any formdef arrays - see bbsn i=for examples if needed
        $this->formdefs['contact']=array
        (
            'salutation' => array
            (
                'id' => 'saluation',
                'field_group' => '',
                'type' => 'text',
                'name' => 'salutation',
                'label' => 'Saluation',
                'validation_rule' => '',
                'validation_message' => '',
                'attributes' => ''
            ),
            'firstname' => array
            (
                'id' => 'firstname',
                'field_group' => '',
                'type' => 'text',
                'name' => 'firstname',
                'label' => 'First name',
                'validation_rule' => 'not-empty',
                'validation_message' => 'First name cannot be empty',
                'attributes' => ' required '
            ),
            'lastname' => array
            (
                'id' => 'lastname',
                'field_group' => '',
                'type' => 'text',
                'name' => 'lastname',
                'validation_rule' => 'not-empty',
                'validation_message' => 'Last name cannot be empty',
                'label' => 'Last name',
                'attributes' => ' required '
            ),
            'address1' => array
            (
                'id' => 'address1',
                'field_group' => '',
                'type' => 'text',
                'name' => 'address1',
                'label' => 'Address1',

            ),
            'address2' => array
            (
                'id' => 'address2',
                'field_group' => '',
                'type' => 'text',
                'name' => 'address2',
                'label' => 'Address2',

            ),
            'city' => array
            (
                'id' => 'city',
                'field_group' => '',
                'type' => 'text',
                'name' => 'city',
                'label' => 'City',

            ),
            'postcode' => array
            (
                'id' => 'postcode',
                'field_group' => '',
                'type' => 'text',
                'name' => 'postcode',
                'label' => 'Postcode',

            ),
            'phone1' => array
            (
                'id' => 'phone1',
                'field_group' => '',
                'type' => 'text',
                'name' => 'phone1',
                'label' => 'Phone1',

            ),
            'phone2' => array
            (
                'id' => 'phone2',
                'field_group' => '',
                'type' => 'text',
                'name' => 'phone2',
                'label' => 'Phone2',

            ),
            'email' => array
            (
                'id' => 'email',
                'field_group' => '',
                'type' => 'text',
                'name' => 'email',
                'label' => 'Email',

            ),
            'notes' => array
            (
                'id' => 'notes',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'notes',
                'label' => 'Notes'
            ),
            'b' => array
            (
                'id' => 'b',
                'type' => 'submit',
                'name' => 'b',
                'value' => 'Save',
                'attributes' => ' class="btn btn-primary" '
            )
        );




    }

    function _get_past_and_future_markets(){
        $markets=array();
        foreach($this->markets as $m){
            if($m['market_date'] < $this->nowDate){
                $markets['past'][$m['market_id']]=$m;
            } else {
                $markets['future'][$m['market_id']]=$m;
            }
        }
        return $markets;
    }
    /**
    * This method outputs the html menu for foodcoop App
    * 
    * @param mixed $actionGroup
    */
    function menu($actionGroup = ''){
        $url  = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier);
        $menu = '    <div style="margin-bottom: 10px;">
        <ul class="nav nav-tabs">
        <li ' . ($actionGroup == 'import' ? 'class="active"' : '') . '><a href="' . $url . '?ag=import&aga=">Importer</a></li>
        <li ' . ($actionGroup == 'markets' ? 'class="active"' : '') . '><a href="' . $url . '?ag=markets">Markets</a></li>
        <li ' . ($actionGroup == 'locations' ? 'class="active"' : '') . '><a href="' . $url . '?ag=locations">Locations</a></li>
        <li ' . ($actionGroup == 'sites' ? 'class="active"' : '') . '><a href="' . $url . '?ag=sites">Sites</a></li>
        <li ' . ($actionGroup == 'contacts' ? 'class="active"' : '') . '><a href="' . $url . '?ag=contacts">Contacts</a></li>                      
        <li ' . ($actionGroup == 'stalls' ? 'class="active"' : '') . '><a href="' . $url . '?ag=stalls">Stalls</a></li>
        <li ' . ($actionGroup == 'cancellations' ? 'class="active"' : '') . '><a href="' . $url . '?ag=cancellations">Cancellations</a></li>
        <li ' . ($actionGroup == 'site_allocation' ? 'class="active"' : '') . '><a href="' . $url . '?ag=site_allocation">Allocate sites</a></li>
        <li ' . ($actionGroup == 'postmarket' ? 'class="active"' : '') . '><a href="' . $url . '?ag=postmarket">Market day</a></li>

        <li>'.date('d-m-Y',$this->nowtime).'</li>
        </ul>
        </div>';

        echo $menu;
    }

    function setup(){

        $this->render('setup');
    }

    /**
    * This function sets the time the app works with.
    * If you want to test the app in a future or past time you can 
    * adjust this time to fool the app into thinking that time in now.
    * 
    */
    function nowTime(){
        return time();// - (365*24*3600);
    }

 

    function markets($action=false){

        if ($_POST ) {  
            if( $action == 'setseason'){
                $this->setSeason($_POST['seasonstartyear']);
                $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=markets';
                header('location: ' . $url);
                exit;
            }
        }

        // Check if request is calling for  statustoggle action 
        $marketId = isSet($_GET['market_id']) && (int) $_GET['market_id'] > 0 ? (int) $_GET['market_id'] : 0;
        if($action =='statustoggle' && $marketId  ){
            // toggle Active / Cancelled  status for the market
            $market = DBX::getRow('SELECT `status` from markets where market_id='.$marketId);
            if($market['status'] == 'Active'){
                DBX::update('markets',array('status'=>'Cancelled'), 'WHERE market_id='.$marketId);
            } else {
                DBX::update('markets',array('status'=>'Active'), 'WHERE market_id='.$marketId);
            }

            $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=markets';
            header('location: ' . $url);
            exit;
        }



        //Sept is always first month of a season
        $seasons = DBX::getRows('select YEAR(market_date) as seasonstartyear from markets where MONTH(market_date) = 9 '); 

        // If season is set then Get the list of markets 
        if($season = $this->_get_set_season() ){
            $this->data['season'] = $this->_get_season_markets($season);

        } else {
            die ("season is NOT YET SET - should have been set in ADMIN CONSTRUCTOR!!!!");
        }

        $this->data['seasons'] = $seasons;
        $this->render('markets');
    }

    function _get_season_markets($season){
        $sql = '    select * from markets where market_date 
        BETWEEN "'.$season.'-09-01" AND
        "'.($season + 1).'-06-01" order by market_date asc';

        //TWS::pr($sql);
        $markets= DBX::getRows($sql,'market_id');
        DBX::abortOnError();
        return $markets;
    }

 



  

    

    function locations($action=false){

        if($_POST){
            //TWS::pr($_POST);exit;
            if(strtolower($_POST['aga']) == 'update' ){
                if(TWS::isIterable($_POST['locations'])){
                    foreach($_POST['locations'] as $k => $name){
                        if(empty($name)){
                            // Delete maybe
                        } else {
                            if($k == 0){
                                // Add a new location
                                DBX::insert('locations',array('name'=>$name));
                            } else {
                                // Update the location
                                DBX::update('locations',array('name'=>$name),'WHERE location_id='.$k);
                            }
                        }
                    }
                }
                $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=locations';
                header('location: ' . $url);
                exit;
            }
        }
        $this->data['locations'] = DBX::getRows('select * from locations order by `name` asc');
        $this->render('locations');
    }

    /* SITES FUNCTIONS */
    function sites($action=false) {
        // if ACTION is set then determine handler and call that
        $action                = !empty($action) ? $action : 'list';
        $action_handler_method = __FUNCTION__ . '_' . $action;
        $this->$action_handler_method();
    }

    function sites_list(){

        $this->data['sites'] = DBX::getRows('select * from sites ');
        $this->data['locations'] = $this->_get_locations();
        $this->render('sites_list');
    }

    function sites_edit(){

        $table         = 'sites'; // database table we are working with

        // Determine contact ID
        $contactId = isSet($_GET['site_id']) ? (int) $_GET['site_id'] : 0; // 0 = create new
        //$rid = isSet($_POST['rid']) ? (int) $_POST['rid'] : 0; // Post form record id

        $locations = $this->_get_locations();

        $elements = array
        (

            'location_id' => array
            (
                'id' => 'approval_status',
                'field_group' => '',
                'type' => 'select',
                'name' => 'location_id',
                'label' => 'Location',
                'options' => $locations ,
                'attributes' => ' required '
            ),
            'note' => array
            (
                'id' => 'note',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'note',
                'label' => 'Note'
            ),
            'site_reference' => array
            (
                'id' => 'site_reference',
                'field_group' => '',
                'type' => 'text',
                'name' => 'site_reference',
                'label' => 'Site reference (eg R43)',
                'validation_rule' => 'not-empty',
                'validation_message' => 'Site reference isnot valid'
            ),
            'b' => array
            (
                'id' => 'b',
                'type' => 'submit',
                'name' => 'b',
                'value' => 'Save',
                'attributes' => ' class="btn btn-primary" '
            )
        );
        $action   = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier) . '?ag=sites&aga=edit&site_id=' . $siteId;
        $formname = 'edit_site';  
        $f = new TWSForm($action, $formname, 'post');
        foreach($elements as $e){
            $f->addElement($e);
        }


        $e = array(
            'id' => 'site_id',
            'type' => 'hidden',
            'name' => 'site_id',
            'value' => $siteId

        );
        $f->addElement($e);
        if($siteId > 0){ // add a delete button if we have a contact
            $e = array
            (
                'id' =>'bb',
                'type' => 'submit',
                'name' => 'bb',
                'value' => 'Delete',
                'attributes' => ' class="btn" onclick="return confirm(\'Are you sure you want to delete?\')"'
            );
            $f->addElement($e);

        }







        // Now process request


        if ($_POST) {

            // check for DELETE
            if(TWS::requestVar('formname') ==$formname && TWS::requestVar('bb')=='Delete' && TWS::requestVar('site_id') > 0){
                $siteId=TWS::requestVar('site_id'); // defaults to post var 
                $this->_delete_site($siteId);
                TWS::flash('message','Site has been deleted');
                TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED
                $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=sites&aga=list';
                header('location: ' . $url);
                exit;

            }

            if ($f->run()) {
                if ($f->isNotValid()) {
                    // Re display form with validation errors
                    //echo ' errors process form';

                    header('location:' . $url . '');
                    exit;
                } else {
                    // Okay to process post
                    $data                = array();
                    $data['location_id']        = $_POST['location_id'];
                    $data['note']      = $_POST['note'];
                    $data['site_reference']       = $_POST['site_reference'];



                    if ($siteId > 0) {
                        // update record


                        DBX::update($table, $data, 'WHERE site_id='.$siteId);
                        DBX::abortOnError();

                        TWS::flash('message', 'Site has been updated');
                    } else {
                        // insert new record
                        //TWS::pr('inserting record', 1);

                        $insertId = DBX::insert($table, $data);
                        DBX::abortOnError();

                        TWS::flash('message', 'Site has been added');

                        if (!$insertId) {
                            TWS::flash('error','Database error: failed to insert new information into ' . $table . ' table');
                        } else {
                            $contactId = TWS::modx()->db->getInsertId();
                        }
                    }
                    TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED
                    $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=sites&aga=list';
                    header('location: ' . $url);
                    exit;
                }
            }
        }



        $this->data['sites'] = array();

        if ($siteId > 0) {
            // retrieve contact data if post formdata not set
            //TWS::pr(TWS::getFlash('formdata'));exit;
            $formdata = TWS::getFlash('formdata');
            if ($formdata && $formdata['formname'] == $formname) {
                // dont load data from db as we are editing the stuff on formdata
            } else {
                $sql = 'select * from ' . $table . ' where site_id=' . $contactId;
                //TWS::pr($sql,1);         
                TWS::flash('formdata', DBX::getRow($sql));
            }


        }

        $this->data['f'] =& $f;
        $this->render('sites_edit');
    }

    function _get_locations() {
        $res = DBX::getRows('select * from locations order by `name` asc','location_id');
        $locations = array();
        if(TWS::isIterable($res)){
            foreach($res as $l){
                $locations[$l['location_id']] = $l['name'];
            }
        }
        return $locations;

    }

    function _get_contacts_list($status=false) {

        if($status){
            $where = ' WHERE `status`="'.$status.'" ';
        } else {
            $where = '';
        }

        $res = DBX::getRows('select * from contacts ' .$where.' order by `lastname` asc','contact_id');
        DBX::abortOnError();

        $result = array();
        if(TWS::isIterable($res)){
            foreach($res as $r){
                $result[$r['contact_id']] = $r['lastname'] . ', ' . $r['firstname'];
            }
        }
        return $result;

    }

    function _get_category_options() {



        $res = DBX::getRows('select * from categories ');
        DBX::abortOnError();

        $result = array();
        if(TWS::isIterable($res)){
            foreach($res as $r){
                $result[$r['category_id']] = $r['description'];
            }
        }
        return $result;

    }

    function _get_stalltype_options() {



        $res = DBX::getRows('select * from stalltypes');
        DBX::abortOnError();

        $result = array();
        if(TWS::isIterable($res)){
            foreach($res as $r){
                $result[$r['stalltype_id']] = $r['description'];
            }
        }
        return $result;

    }

    function _delete_site($contactId){
        // for now just set the status to deleted
        //DBX::query('UPDATE contacts set `status` = "Deleted" WHERE contact_id='.$contactId);
        DBX::abortOnError();


        // should probably cancell any market_sites for future markets too
    }

    /* End Sites */   

    /* CONTACTS FUNCTIONS */
    function contacts($action=false) {
        // if ACTION is set then determine handler and call that
        $action                = !empty($action) ? $action : 'list';
        $action_handler_method = __FUNCTION__ . '_' . $action;
        $this->$action_handler_method();
    }

    function contacts_list(){

        $this->data['contacts'] = DBX::getRows('select * from contacts where `status`="Active"');
        $this->render('contacts_list');
    }

    function contacts_edit(){

        $table         = 'contacts'; // database table we are working with

        // Determine contact ID
        $contactId = isSet($_GET['contact_id']) ? (int) $_GET['contact_id'] : 0; // 0 = create new
        //$rid = isSet($_POST['rid']) ? (int) $_POST['rid'] : 0; // Post form record id


        $elements = $this->formdefs['contact'];
        $action   = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier) . '?ag=contacts&aga=edit&contact_id=' . $contactId;
        $formname = 'edit_contact';  
        $f = new TWSForm($action, $formname, 'post');
        foreach($elements as $e){
            $f->addElement($e);
        }


        $e = array(
            'id' => 'contact_id',
            'type' => 'hidden',
            'name' => 'contact_id',
            'value' => $contactId

        );
        $f->addElement($e);
        if($contactId > 0){ // add a delete button if we have a contact
            $e = array
            (
                'id' =>'bb',
                'type' => 'submit',
                'name' => 'bb',
                'value' => 'Delete',
                'attributes' => ' class="btn" onclick="return confirm(\'Are you sure you want to delete?\')"'
            );
            $f->addElement($e);

        }







        // Now process request


        if ($_POST) {

            // check for DELETE
            if(TWS::requestVar('formname') ==$formname && TWS::requestVar('bb')=='Delete' && TWS::requestVar('contact_id') > 0){
                $contactid=TWS::requestVar('contact_id'); // defaults to post var 
                $this->_delete_contact($contactId);
                TWS::flash('message','Contact has been deleted');
                TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED
                $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=contacts&aga=list';
                header('location: ' . $url);
                exit;

            }

            if ($f->run()) {
                if ($f->isNotValid()) {
                    // Re display form with validation errors
                    //echo ' errors process form';

                    header('location:' . $url . '');
                    exit;
                } else {
                    // Okay to process post
                    $data                = array();
                    $data['salutation']        = $_POST['salutation'];
                    $data['firstname']      = $_POST['firstname'];
                    $data['lastname']       = $_POST['lastname'];
                    $data['address1'] = $_POST['address1'];
                    $data['address2']   = $_POST['address2'];
                    $data['city']      = $_POST['city'];
                    $data['postcode']      = $_POST['postcode'];
                    $data['phone1']      = $_POST['phone1'];
                    $data['phone2']      = $_POST['phone2'];
                    $data['email']      = $_POST['email'];
                    $data['notes']      = $_POST['notes'];


                    if ($contactId > 0) {
                        // update record


                        DBX::update($table, $data, 'WHERE contact_id='.$contactId);
                        DBX::abortOnError();

                        TWS::flash('message', 'Contact has been updated');
                    } else {
                        // insert new record
                        //TWS::pr('inserting record', 1);
                        $data['status']='Active';
                        $insertId = DBX::insert($table, $data);
                        DBX::abortOnError();

                        TWS::flash('message', 'Contact has been added');

                        if (!$insertId) {
                            TWS::flash('error','Database error: failed to insert new information into ' . $table . ' table');
                        } else {
                            $contactId = TWS::modx()->db->getInsertId();
                        }
                    }
                    TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED
                    $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=contacts&aga=list';
                    header('location: ' . $url);
                    exit;
                }
            }
        }



        $this->data['contact'] = array();

        if ($contactId > 0) {
            // retrieve contact data if post formdata not set
            //TWS::pr(TWS::getFlash('formdata'));exit;
            $formdata = TWS::getFlash('formdata');
            if ($formdata && $formdata['formname'] == $formname) {
                // dont load data from db as we are editing the stuff on formdata
            } else {
                $sql = 'select * from ' . $table . ' where contact_id=' . $contactId;
                //TWS::pr($sql,1);         
                TWS::flash('formdata', DBX::getRow($sql));
            }


        }

        $this->data['f'] =& $f;
        $this->render('contacts_edit');
    }

    function _delete_contact($contactId){
        // for now just set the status to deleted
        DBX::query('UPDATE contacts set `status` = "Deleted" WHERE contact_id='.$contactId);
        DBX::abortOnError();


        // should probably cancell any market_sites for future markets too
    }

    /* End Contcts */   

    /* STALLS FUNCTIONS */
    function stalls($action=false) {
        // if ACTION is set then determine handler and call that
        $action                = !empty($action) ? $action : 'list';
        $action_handler_method = __FUNCTION__ . '_' . $action;
        $this->$action_handler_method();
    }

    function stalls_edit_ajax(){
        $this->ajax = true;

        $this->stalls_edit(); // call normal handler

        echo ' okay';
        exit;
    }

    function stalls_site_bookings_ajax(){
        //TWS::pr($_POST);

        $stallId = $_POST['stall_id'];
        $marketSiteIds = $_POST['site_ids'];

        if(isSet($_POST['site_fee'])){
            $site_fee = $_POST['site_fee'];
        } else {
            $site_fee = true; // will default to standard fee
        }

        if(TWS::isIterable($marketSiteIds)){
            foreach($marketSiteIds as $marketId => $siteIds){
                $sql = 'SELECT * from market_sites where status ="Active" and market_id='.$marketId.' and stall_id='.$stallId;
                $currentMarketSites = DBX::getRows($sql,'site_id');
                DBX::abortOnError();

                // check if any current sites should be cancelled
                if(TWS::isIterable($currentMarketSites)){
                    foreach($currentMarketSites as $cms){
                        if(!in_array($cms['site_id'], $siteIds)){ // cancel the market_site

                            $data = array(  
                                'note'=>' cancelled via site bookings page',
                                'status'=>'Cancelled');

                            DBX::update('market_sites',$data, ' where market_site_id='.$cms['market_site_id']);
                        }
                    }
                }

                // now check in any new sites should be added
                if(TWS::isIterable($siteIds)){
                    foreach($siteIds as $siteId){
                        if(! isSet($currentMarketSites[$siteId])){
                            // create the market site
                            if(! $this->_create_market_site($marketId,$siteId,$stallId,'new site allocation',$site_fee)){
                                echo ' Could not allocate site';
                            }
                        }
                    }

                }


                //TWS::pr($currentMarketSites);
                //TWS::pr($siteIds);



            }
        }

        // current Market Sites for stall
        exit;
    }


    function _get_stall_allocation_requests($stallId){
        $allocations=array();
        $markets = $this->markets;
        foreach($markets as $k=>$m){
            $sql = 'SELECT * FROM market_allocation_requests MAR where market_id='.$k.' and stall_id='.$stallId;

            $res = DBX::getRow($sql);
            DBX::abortOnError();
            $allocations[$k]=$res;
        }


        return $allocations;      
    }
    function _get_stall_info($stallId){
        $sql = '    select stalls.*,stalltypes.std_site_fee as std_site_fee from stalls 
        JOIN stalltypes on stalltypes.stalltype_id=stalls.stalltype_id 
        where stalls.stall_id='.$stallId;
        $stall = DBX::getRow($sql);



        $sql = 'select contact_id from stallholders where stall_id='.$stallId;
        $contactRes = DBX::getRows($sql);
        $contacts=array();
        if(TWS::isIterable($contactRes)){
            foreach($contactRes as $c){
                $contacts[$c['contact_id']] = $c['contact_id'];
            }
        }
        $stall['contact_ids']= $contacts;
        return $stall;

    }

    function stalls_list(){



        if(TWS::requestVar('contact_id','get')){
            $sql = 'select  s.*,ST.description as stalltype from stalls as s 
            join stallholders as sh ON sh.stall_id=s.stall_id
            join stalltypes as ST on ST.stalltype_id=s.stalltype_id
            where s.`status`!="Inactive" AND sh.contact_id='.TWS::requestVar('contact_id','get').' ';
        } else {
            $sql = 'select  s.*,ST.description as stalltype from stalls as s 
            join stallholders as sh ON sh.stall_id=s.stall_id
            left join stalltypes as ST on ST.stalltype_id=s.stalltype_id
            where 1';
        }
        $stalls = DBX::getRows($sql);
        DBX::abortOnError();

        //TWS::pr($sql);
        // get contact(s) for stall
        if(TWS::isIterable($stalls)){
            foreach($stalls as &$s){
                $sql = '    select c.firstname,c.lastname from contacts as c
                join stallholders as sh on sh.contact_id=c.contact_id
                where sh.stall_id='.$s['stall_id'];

                $s['stallholders'] = DBX::getRows($sql);
                DBX::abortOnError();



                $s['permanent_sites'] = $this->_get_stall_permanent_sites($s['stall_id']);

            }
        }

        $this->data['stalls'] = $stalls;
        $this->render('stalls_list');
    }

    /**
    * Gets a list of any permanent sites currently allocated to a stall
    * 
    */
    function _get_stall_permanent_sites($stallId){
        $pSites=array();
        $markets = $this->markets;
        if(TWS::isIterable($markets)){
            foreach($markets as $m){
                // gather and permenannt site allocations to this stall
                $sql = '    select MS.site_id,S.site_reference from market_sites MS 
                JOIN sites S on S.site_id=MS.site_id
                where MS.stalltype_id=1 and MS.market_id='.$m['market_id'].' and MS.stall_id='.$stallId;
                $res = DBX::getRows($sql);
                DBX::abortOnError();
                if($res){
                    if(TWS::isIterable($res)){
                        foreach($res as $r){
                            $pSites[$r['site_id']] = $r['site_reference'];
                        }
                    }
                }

            }
        }
        return $pSites;
    }

    function stalls_edit(){

        $table         = 'stalls'; // database table we are working with

        // Determine contact ID
        $stallId = isSet($_GET['stall_id']) ? (int) $_GET['stall_id'] : 0; // 0 = create new
        //$rid = isSet($_POST['rid']) ? (int) $_POST['rid'] : 0; // Post form record id


        $this->data['form_elements'] =array
        (

            'name' => array
            (
                'id' => 'name',
                'field_group' => '',
                'type' => 'text',
                'name' => 'name',
                'label' => 'Stall name',
                'validation_rule' => '',
                'validation_message' => 'Stall name cannot be empty',
                'attributes' => ''

            ),
            'category' => array(
                'id' => 'category_ids',
                'type' => 'multi_select',
                'name' => 'category_ids',
                'label' => 'Category(s)',
                'multi_select_size' => 5,
                'options' =>  $this->_get_category_options()

            ),
            'contacts' => array(
                'id' => 'contact_ids',
                'type' => 'multi_select',
                'name' => 'contact_ids',
                'label' => 'Contact(s)',
                'multi_select_size' => 3,
                'options' =>  $this->_get_contacts_list('Active')

            ),
            'stalltype' => array(
                'id' => 'stalltype_id',
                'type' => 'select',
                'name' => 'stalltype_id',
                'label' => 'Stall booking type',
                'options' =>  $this->_get_stalltype_options()

            ),
            'description' => array
            (
                'id' => 'description',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'description',
                'validation_rule' => '',
                'validation_message' => 'Description cannot be empty',
                'label' => 'Description',
                'attributes' => ''

            ),
            'status' => array
            (
                'id' => 'status',
                'field_group' => '',
                'type' => 'select',
                'name' => 'status',
                'label' => 'Status',
                'options' => array
                (
                    'New request' => 'New request',
                    'Under consideration' => 'Under consideration',
                    'Rejected' => 'Rejected',
                    'Active' => 'Active',
                    'Inactive' => 'Inactive'
                ),
                'attributes' => ' required '
            ),
            'notes' => array
            (
                'id' => 'notes',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'notes',
                'label' => 'Notes'
            ),
            'stall_id' => array(
                'id' => 'stall_id',
                'type' => 'hidden',
                'name' => 'stall_id',
                'value' => $stallId

            ),
            'save_button' => array
            (
                'id' => 'b',
                'type' => 'submit',
                'name' => 'b',
                'value' => 'Save',
                'attributes' => ' class="btn btn-primary" '
            ),
            'delete_button' => array
            (
                'id' =>'bb',
                'type' => 'submit',
                'name' => 'bb',
                'value' => 'Delete',
                'attributes' => ' class="btn" onclick="return confirm(\'Are you sure you want to delete?\')"'
            )
        );



        $action   = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier) . '?ag=stalls&aga=edit&stall_id=' . $stallId;
        $formname = 'edit_stall';  
        $f = new TWSForm($action, $formname, 'post');
        foreach($this->data['form_elements'] as $e){
            $f->addElement($e);
        }
        $this->data['f'] =&$f;

        // Now process request

        if ($_POST) {

            // check for DELETE
            if(TWS::requestVar('formname') ==$formname && TWS::requestVar('bb')=='Delete' && TWS::requestVar('contact_id') > 0){

                die('Delete not yet implemented');
                $contactid=TWS::requestVar('contact_id'); // defaults to post var 
                $this->_delete_stall($contactId);
                $this->_delete_stall_allocations($contactId);
                $this->_delete_stall_allocation_requests($contactId);

                TWS::flash('message','stall has been deleted');
                TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED
                $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=stalls&aga=list';
                header('location: ' . $url);
                exit;

            }

            if ($f->run()) {
                if ($f->isNotValid()) {
                    // Re display form with validation errors
                    //echo ' errors process form';

                    header('location:' . $url . '');
                    exit;
                } else {
                    // Okay to process post
                    $data                = array();
                    $data['name']        = $_POST['name'];
                    $data['description']      = $_POST['description'];
                    $data['status']       = $_POST['status'];
                    $data['notes']      = $_POST['notes'];
                    $data['stalltype_id']      = $_POST['stalltype_id'];


                    if ($stallId > 0) {
                        // update record


                        DBX::update($table, $data, 'WHERE stall_id='.$stallId);                      
                        DBX::abortOnError();


                        // now update stall contact(s)
                        DBX::query('delete from stallholders where stall_id='.$stallId); // remove old entries
                        if(TWS::isIterable(TWS::requestVar('contact_ids'))){
                            foreach(TWS::requestVar('contact_ids') as $contact){
                                DBX::insert('stallholders',array('stall_id'=>$stallId,'contact_id'=>$contact));
                                DBX::abortOnError();
                            }
                        }


                        TWS::flash('message', 'Stall has been updated');
                    } else {
                        // insert new record
                        //TWS::pr('inserting record', 1);
                        $insertId = DBX::insert($table, $data);
                        DBX::abortOnError();

                        // now update stall contact(s)
                        if(TWS::isIterable(TWS::requestVar('contact_ids'))){
                            foreach(TWS::requestVar('contact_ids') as $contactId){
                                DBX::insert('stallholders',array('stall_id'=>$insertId,'contact_id'=>$contactId));
                                DBX::abortOnError();
                            }
                        }


                        TWS::flash('message', 'Stall has been added');

                        if (!$insertId) {
                            TWS::flash('error','Database error: failed to insert new information into ' . $table . ' table');
                        } else {
                            $stallId = $insertId;
                        }
                    }
                    $this->_update_allocation_requests($stallId,$_POST['allocation_request']);// process checkboxes


                    TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED

                    // if request is ajax then just return a error status - if not ajax reload the page
                    if($this->ajax){
                        return;
                    } else {
                        $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=stalls&aga=list';
                        header('location: ' . $url);
                        exit;
                    }



                }
            }
        }





        if ($stallId > 0) {
            // retrieve contact data if post formdata not set
            //TWS::pr(TWS::getFlash('formdata'));exit;
            $formdata = TWS::getFlash('formdata');
            if ($formdata && $formdata['formname'] == $formname) {
                // dont load data from db as we are editing the stuff on formdata
            } else {  
                //TWS::pr($this->_get_stall_info($stallId));

                TWS::flash('formdata',$this->_get_stall_info($stallId));

            }

        }

        // Current Stall sites
        $markets = $this->markets;

        /*
        foreach($markets as $m){
        $this->data['stall_sites'][$m['market_id']] = $this->_get_stall_market_sites($m['market_id'],$stallId);

        // Currently avialable sites
        $this->data['available_sites'][$m['market_id']] = $this->_get_available_market_sites($m['market_id']);
        }
        */
        //TWS::pr($this->data['stall_sites']);
        //TWS::pr($this->data['available_sites']);
        $this->data['allocation_requests']= $this->_get_stall_allocation_requests($stallId);
        $this->data['stall']= $this->_get_stall_info($stallId);
        $this->data['permanent_sites']=$this->_get_stall_permanent_sites($stallId);
        $this->data['markets']= $markets;
        $this->render('stalls_edit');
    }

    /**
    * Used to check in num is odd or not
    * odd stalltypes are PERMANENT and even rare casual
    * 
    * @param mixed $num
    */
    function _is_odd($num){
        $num = (int) $num;
        return $num % 2;
    }

    /**
    * Update Permamnet stalls allocation requests 
    * ONLY CALLED FROM STALLS_EDIT
    * 
    * @param mixed $stallId
    * @param mixed $requests
    */
    function _update_allocation_requests($stallId,$requests){
        $stall = DBX::getRow('select * from stalls where stall_id='.$stallId);
        DBX::abortOnError();
        if(!$stall) return false; // stall does not exist ???

        $current_open_requests = $this->_get_allocation_requests(); // for set season for ALL stalls
        

        // first check if there are any new requests
        if(TWS::isIterable($requests)){
            foreach($requests as $marketId){ 
                if(! isSet($current_open_requests[$marketId][$stallId])){ // this is a new request for this market
                    // check if this stall already has a market_site allocated 
                    $res = DBX::getRow('select * from market_sites where market_id='.$marketId .' and stall_id='.$stallId);
                    DBX::abortOnError();
                    if(!$res){ // stall does not have a site for this market
                        // check if anyone else has ACTIVE  allocation of  this market_site - it should only be a casual if anything
                        $res2 = DBX::getRows('select * from market_sites where market_id='.$marketId.' and stall_id !='.$stallId .' and `status`="Active" ');
                        if ($res2){ // damn we have a potentil conflict!!
                            // should only be one if any but safer to useRows incase the system has hicupped and we have more than one Active!!!
                            if(TWS::isIterable($res2)){
                                foreach($res2 as $r2){
                                    if ($this->_is_odd($r2['stalltype_id'])){ // check if it is a permanent stalltype
                                        // This is NOT GOOD as we already have a permanent allocation for another stall!
                                        TWS::flash('message','There seems to be a different permanent stall (stall_id='.$r2['stall_id'].') allocated to this stalls previously not-required site');
                                        $this->redirectAndExit(); // to same page but with GET not POST

                                    } else {
                                        // okay a casual has been found squatting on our previous not required perm site.

                                        // first we move them OFF and recreate an alloc_request for them
                                        // and reverse the transaction cost if there was one

                                        $this->_remove_and_reallocate_casual_site($r2['market_site_id']); // note deletes market_site_record.

                                        // now allocate our perm site to us and enter the transaction cost

                                        // Do the allocation for the perm site 
                                        $data['stall_id']=$stallId;
                                        $data['market_id']=$marketId;
                                        $data['stalltype_id']=$stall['stalltype_id'];
                                        $data['status']='Active';
                                        $marketSiteId = DBX::insert('market_sites',$data);
                                        DBX::abortOnError();

                                        // remove alloc request
                                        $this->_allocation_done($marketSiteId);


                                        $this->_charge_for_market_site_allocation($marketSiteId,'allocating my previously not required site - note we moved casual off to make room!');



                                    }
                                }
                            }

                        } // end of resolving conflict


                        // add the new allocation_request
                        $data= array(
                            'market_id'=>$marketId,
                            'stall_id'=>$stallId
                        );
                        DBX::insert('market_allocation_requests',$data);
                        DBX::abortOnError();
                    } else {
                        // There is a previous market_site entry for THIS stall - so now what???
                        // if status of previous request is Not Required AND it is in the future then ....
                        if($res['status'] == 'Not required' && $this->_market_in_future($res['market_id'])){ 
                            $sql = 'update market_sites set `status`="Active" where market_site_id='.$res['market_site_id'];
                            DBX::query($sql);
                            DBX::abortOnError();

                            // update allocation_request to show allocation has been made
                            $this->_allocation_done($res['market_site_id']);

                            // add a tranaction for the cost of new site allocation
                            $this->_charge_for_market_site_allocation($res['market_site_id'],'allocation of previously Not required market site');
                        }

                        // check if any other stall has been allocated the site
                        // if they have and if it is a casual then requeue casual request and
                        // give stall to the perm
                    }

                } else {
                    // we already have this request 
                    unset($current_open_requests[$marketId][$stallId]);// cross it off the list
                }
            }
        }


        // now check if any remaining  allocation requests and delete as no longer wanted
        if(TWS::isIterable($current_open_requests)){
            foreach($current_open_requests as $marketId=> $cr){
                if(isSet($cr[$stallId])){

                    // if market is in the future then mark as Not required and credit if applicable
                    // $thi->set_ar_not_required()
                    DBX::query('DELETE from market_allocation_requests where stall_id='.$stallId .' and market_id='.$marketId );
                    DBX::abortOnError();
                }


            }
        }


    }

    /**
    * cancels the allocation request by updating the allocated_market_site_id 
    * 
    * @param mixed $marketSiteId
    */
    function _allocation_done($marketSiteId) {

        $ms = DBX::getRow('select * from market_sites where market_site_id='.$marketSiteId);


        $sql = 'Update market_allocation_requests 
        set allocated_market_site_id='.$marketSiteId.',
        `modified` = '.DBX::quote(gmdate("Y-m-d H:i:s", time()) ). ' 
        where market_id='.$ms['market_id'] .' 
        and stall_id='.$ms['stall_id'];
        DBX::query($sql);
        DBX::abortOnError();
    }

    function _remove_and_reallocate_casual_site($marketSiteId){
        // TODO  4 -o owner -c task: Add this function to remove a casual from a site
        $ms = DBX::getRow('select * from market_sites where market_site_id='.$marketSiteId);
        DBX::abortOnError();
        $this->_remove_casual_booking($ms['market_id'],$ms['site_id'],$ms['stall_id']);

        return true; 
    }

    function _market_in_future($marketId){
        return ($this->markets[$marketId]['market_date'] >= $this->nowDate);
    }

    function _get_stall_market_sites($marketId,$stallId=false){
        $sql = '
        SELECT S.*,L.`name` as location,MS.`status`
        from market_sites as MS 
        JOIN sites as S on S.site_id= MS.site_id 
        JOIN locations L on L.location_id=S.location_id
        where MS.market_id='.$marketId;
        if($stallId){
            $sql .= ' and MS.stall_id='.$stallId;
        } 



        $rs = DBX::getRows($sql);
        DBX::abortOnError();
        $result=array();
        if(TWS::isIterable($rs)){
            foreach($rs as $r){
                $result[$r['site_id']]=$r;
            }
        }
        return $result;

    }

    function _get_available_market_sites($marketId){
        //TWS::pr($marketId);
        $sql = 'select * from market_sites where market_id='.$marketId .' AND `status`="Active"';
        $allocatedSites = DBX::getRows($sql,'site_id');
        DBX::abortOnError();
        //TWS::pr($allocatedSites);

        $sql =' select site_id,site_reference, locations.`name` as location from sites join locations on locations.location_id=sites.location_id order by site_id asc';
        $allSites= DBX::getRows($sql,'site_id');
        DBX::abortOnError();
        //TWS::pr($allSites);


        if(TWS::isIterable($allocatedSites)){
            foreach($allocatedSites as $k=>$v){
                unset($allSites[$k]);
            }
        }

        //TWS::pr($allSites);

        return $allSites;
    }

    function _return_ajax_error($msg,$data){
        header('Content-Type: application/json');
        $rdata['status']=500;// error code
        $rdata['error_msg']=$msg;
        $rdata['data']=$data;
        echo json_encode($rdata,JSON_FORCE_OBJECT);
        exit;
    }
    function _return_ajax($status=500,$msg,$data){
        header('Content-Type: application/json');
        $rdata['status']=$status;// status code 200=okay, cant proces error, others defined to specific meaning
        $rdata['msg']=$msg;
        $rdata['data']=$data;
        echo json_encode($rdata,JSON_FORCE_OBJECT);
        exit;
    }

    function _remove_casual_booking($marketId,$siteId,$stallId){
        $done = false;
        // get the current alloc record
        $where = ' where market_id='.$marketId.' and stall_id='.$stallId;


        $res = DBX::getRow('select * from market_sites '.$where .' and site_id='.$siteId);
        if($res){

            DBX::begin();
            // reset the market_allocations_table
            $sql = '    update market_allocation_requests set allocated_market_site_id=0 '.$where;

            DBX::query($sql);
            if(DBX::error()){
                //$this->_return_ajax_error('error 1 '.$sql,$_POST);
                $error=true;
            }
            // Delete transaction 
            $sql = 'delete from transactions where market_site_id='.$res['market_site_id'];
            DBX::query($sql);
            if(DBX::error()){
                //$this->_return_ajax_error('error 2 '.$sql.' DBERROR: '.DBX::error(),$_POST);
                $error=true;
            }
            // finally delete the market_site itself
            $sql = 'delete from market_sites where market_site_id='.$res['market_site_id'];
            DBX::query($sql);
            if(DBX::error()){
                //$this->_return_ajax_error('error 3 '.$sql,$_POST);
                $error=true;
            }
            if($error){

                DBX::rollback(); 
                //$this->_return_ajax_error('rollback',$_POST);
                $done = false;
            } else {
                //$this->_return_ajax_error('commit',$_POST);
                DBX::commit();
                $done=true;
            }

        }
        return $done;
    }

    function _issue_cancellation_credit_for_market_site($marketSiteId=false, $notation='site cancellation credit'){
        if(!$marketSiteId) return false; // must give us a ms id

        $marketSite = DBX::getRow('select * from market_sites where market_site_id='.$marketSiteId);
        if(!$marketSite) return false; // strange - ms not found

        $stalltype=DBX::getRow('select * from stalltypes where stalltype_id='.$marketSite['stalltype_id']);

        if($marketSite['status'] == 'Cancelled'){
            $tdata=array();
            $tdata['stall_id']= $marketSite['stall_id'];
            $tdata['market_site_id']= $marketSiteId;
            $tdata['transaction_type']= 1; // a credit
            $tdata['amount']= $stalltype['cancellation_credit'];
            $tdata['note']=  'Market date:'.$this->markets[$marketSite['market_id']]['market_date'] ." \n". $notation;
            if($tdata['amount'] > 0){
                $tid = DBX::insert('transactions',$tdata);
                DBX::abortOnError();
            }
        } elseif ($marketSite['status'] == 'Not required'){
            $tdata=array();
            $tdata['stall_id']= $marketSite['stall_id'];
            $tdata['market_site_id']= $marketSiteId;
            $tdata['transaction_type']= 1; // a credit
            $tdata['amount']= $stalltype['std_site_fee'];
            $tdata['note']=  'Market date:'.$this->markets[$marketSite['market_id']]['market_date'] ." \n". $notation;
            if($tdata['amount'] > 0){
                $tid = DBX::insert('transactions',$tdata);
                DBX::abortOnError();
            }

        }


        return $tid; 
    }

    /**
    * if appropriate creates a transaction for the allocation of a site
    *  Charge is based on stalltype
    * 
    * @param mixed $marketSiteId
    */
    function _charge_for_market_site_allocation($marketSiteId=false, $notation='std site charge'){
        if(!$marketSiteId) return false; // must give us a ms id

        $marketSite = DBX::getRow('select * from market_sites where market_site_id='.$marketSiteId);
        if(!$marketSite) return false; // strange - ms not found

        $stalltype=DBX::getRow('select * from stalltypes where stalltype_id='.$marketSite['stalltype_id']);

        $tdata=array();
        $tdata['stall_id']= $marketSite['stall_id'];
        $tdata['market_site_id']= $marketSiteId;
        $tdata['transaction_type']= -1; // a charge
        $tdata['amount']= $stalltype['std_site_fee'];
        $tdata['note']=  'Market date:'.$this->markets[$marketSite['market_id']]['market_date'] ." \n". $notation;
        if($tdata['amount'] > 0){
            $tid = DBX::insert('transactions',$tdata);
            DBX::abortOnError();
        }

        return $tid; 

    }

    /* ALLOCATE SITE FUNCTIONS */
    function site_allocation($action=false) {

        if($_POST){

            if(strtolower( $action ) == 'remove_casual_ajax'){
                $marketId = (int) $_POST['market_id'];
                $siteId = (int)$_POST['site_id'];
                $stallId = (int)$_POST['stall_id'];

                if($this->_remove_casual_booking($marketId,$siteId ,$stallId )){
                    $data['status']=200;
                    header('Content-Type: application/json');
                    echo json_encode($_POST,JSON_FORCE_OBJECT);
                    exit;
                } else {
                    $this->_return_ajax_error('Remove casual booking failed',$_POST);
                }
            }// end remove re-queue casual


            //$data = array('a'=>1223,'b'=>456,'c'=>'this is c');


            $markets = $this->markets;

            // check that we are not allocating to past markets
            $now = $this->nowDate;


            //$data['data'] = $_POST;

            if($_POST['stalltype_id'] == 1){ // permanent site allocation

                if(TWS::isIterable($markets)){

                    $data=array();// return arry of market_site_updates
                    // Note if a perm site replaces a casual site then the js client needs to know
                    // about both additions and deletions to its market_sites data
                    $data['allocations']=array();
                    $data['deallocations']=array();

                    // Posted data

                    $stallId=$_POST['stall_id'];
                    $siteId = $_POST['site_id'];
                    $selectedMarketId= $_POST['market_id']; // the market where the alloc request was dropped
                    $data=array();
                    // get any conflicts
                    $conflicts = $this->_get_booking_conflicts($siteId,$stallId);
                    //$this->_return_ajax(301,'Detected permanent booking conflict',$conflicts);
                    // we need to check if any casual bookings in future and de-allocated them   
                    if(TWS::isIterable($conflicts['casual'])){
                        // process them away!
                        foreach($conflicts['casual'] as $marketSite){
                            // notify client to also remove
                            $data['deallocations'][][ $marketSite['market_id'] ][ $marketSite['site_id']]=$marketSite; 
                            $this->_remove_casual_booking($marketSite['market_id'],$marketSite['site_id'],$marketSite['stall_id']);

                            //i.e put them back into allocation_requests and remove cost transactions
                        } 
                    }

                    // we need to check if their are any perm bookings for other stalls in the future
                    // if there are then we need to abort                
                    if(TWS::isIterable($conflicts['permanent'])){
                        $this->_return_ajax(301,'Detected permanent booking conflict',$conflicts);
                    }




                    // get list of what client has asked for
                    $allocationRequests=$this->_get_stall_allocation_requests($stallId);

                    // a few constant values for allocation inserts below
                    $d['stall_id'] = $stallId;
                    $d['site_id'] = $siteId;
                    $d['stalltype_id']=$_POST['stalltype_id'];


                    foreach($markets as $m){

                        $dd=array();   
                        // Check if client requires site or not for this market             
                        if(!isSet($allocationRequests[$m['market_id']]['market_id'])){
                            // no allocation requested this market;
                            //$d['status']='Not required';

                            // continue and dont create a Not required MS
                            continue; // next market


                        } else {
                            $d['status']='Active';
                        }

                        // check date is in the future - we dont allocate in the past!!!
                        // $selectedMarketId > $m['market_id'] ) ||
                        if ($m['market_date'] < $now){
                            //$this->_return_ajax_error('DEBUG selectedMarketId='.$selectedMarketId.' now= '.$now,$m);

                            //continue; // skip as in past UNCOMMENT ONCE TESTING DONE
                        }

                        $d['market_id'] = $m['market_id'];// 

                        // paranoid - check if an active allocation already exists for this stall
                        // or maybe cancle any non-permamnet conflicting allocations found

                        $market_site_id=DBX::insert('market_sites',$d);
                        DBX::abortOnError();

                        $dd=$d;

                        $dd['market_site_id']=$market_site_id;
                        //$this->_return_ajax_error('DEBUG'.DBX::showError(),$dd);
                        $data['allocations'][]=$dd;


                        if(DBX::error() ){
                            $this->_return_ajax_error('could not save permanent market site'.DBX::showError(),$d);
                        } 



                        if($d['status'] != 'Not required'){
                            // add the transaction
                            $this->_charge_for_market_site_allocation($market_site_id);


                            // remove pending alocation request 
                            $this->_complete_allocation_request($m['market_id'],$stallId,$market_site_id);


                        }


                    }
                    $this->_return_ajax(200,'Perm sites allocated',$data);
                }


            } else if($_POST['stalltype_id'] == 2){ // Casual siteallocation
                $marketId=$_POST['market_id'];
                $d=$_POST;
                // check casual allocation is for a future date - else die!!
                if ($now > $markets[$marketId]['market_date']){
                    $this->_return_ajax_error('cant allocate to passed markets!',$_POST);
                }

                $d['market_site_id']=DBX::insert('market_sites',$d);
                if(DBX::error() || !$d['market_site_id']){
                    $this->_return_ajax_error('could not save casual market site',$d);
                } 
                // now  update the market_allocations_request table withthe allocated market_site_id
                $this->_complete_allocation_request($d['market_id'],$d['stall_id'],$d['market_site_id']);


                // enter the cost transaction

                $stalltype=DBX::getRow('select * from stalltypes where stalltype_id='.$d['stalltype_id']);
                $tdata['stall_id']= $d['stall_id'];
                $tdata['market_site_id']= $d['market_site_id'];
                $tdata['transaction_type']= -1; // a charge
                $tdata['amount']= $stalltype['std_site_fee'];
                $tdata['note']= 'Casual site allocation for market on ' .$markets[$marketId]['market_date'];
                $tid = DBX::insert('transactions',$tdata);

                $data['transaction_data']=$tdata;
                $data['data']['allocations'][]=$d;



                if(DBX::error() || !$tid){
                    $this->_return_ajax_error('transaction failed',$data);
                }



            } else {
                $this->_return_ajax_error('could not save STALLTYPE_ID not supported (yet)',$data['data']);
            }

            // if $data['data']['market_site_id'] is not set then we have an issue


            $data['status']=200;
            header('Content-Type: application/json');
            echo json_encode($data,JSON_FORCE_OBJECT);
            exit;




        }

        $markets = $this->markets;


        /* if(isSet($_GET['market_id']) && (int) $_GET['market_id'] > 0){
        $marketId = (int) $_GET['market_id'];
        } else {
        $now= date('Y-m-d');
        foreach($markets as $m){
        if($m['market_date'] > $now){
        $next_market = $m;
        $marketId=$m['market_id'];
        break;
        }
        }
        }
        */

        //TWS::pr(json_encode($this->_get_market_site_data()));

        $this->data['season']=$this->season;
        $this->data['sites'] = json_encode($this->_get_sites(),JSON_FORCE_OBJECT);
        $this->data['market_sites']= json_encode($this->_get_market_site_data(),JSON_FORCE_OBJECT);
        $this->data['stalls']= json_encode($this->_get_market_stall_data(),JSON_FORCE_OBJECT);
        //$this->data['selected_market_id'] =$marketId;    
        $this->data['markets'] =& $markets;
        // Stalls waiting to be allocated

        //TWS::pr($this->_get_allocation_requests(true));

        $this->data['allocation_requests'] = $this->_get_allocation_requests(false);// true limits to future only alloc req

        $this->render('site_allocation');
    }

    function _get_market_permanent_sites($marketId){
        return $this->_get_stall_market_sites($marketId);
    }
    /**
    * Returns a list of all the sites 
    * 
    */
    function _get_sites(){
        return DBX::getRows('select * from sites order by site_reference asc','site_id');
    }

    function _get_market_site_data(){


        $sql = '    select market_id,site_id,stall_id,stalltype_id,`status` 
        from market_sites where status="Active" and market_id=';// where status="Active"
        $sql2 = '    select market_site_id,market_id,site_id,stall_id,stalltype_id,`status` 
        from market_sites where status !="Active" and market_id=';// where status="Active"
        foreach($this->markets as $m){
            $res = DBX::getRows($sql . $m['market_id'],'site_id');
            DBX::abortOnError();
            $res2 = DBX::getRows($sql2 . $m['market_id'],'site_id');
            DBX::abortOnError();

            $ms[$m['market_id']]['active'] =$res;
            $ms[$m['market_id']]['not_active'] =$res2;

        }

        //TWS::pr(__LINE__);
        //TWS::pr($ms);
        return $ms; 
    }

    function _get_current_season_markets(){
        return $this->_get_season_markets($this->getCurrentSeason());
    }

    /**
    * Gets the stall and stall holedr data for the allocation page app
    * return any stall data dfor the current season
    * 
    */
    function _get_market_stall_data(){
        $markets = $this->markets;
        if(TWS::isIterable($markets)){
            $sql = '    select ST.*,concat(C.lastname,",", C.firstname) as contact
            from market_sites MS 
            join stalls ST on ST.stall_id=MS.stall_id
            join stallholders SH on SH.stall_id=MS.stall_id
            join contacts C on C.contact_id=SH.contact_id
            where MS.`status`="Active" and market_id=';
            foreach($markets as $m){
                $res = DBX::getRows($sql . $m['market_id'],'stall_id');
                DBX::abortOnError();

                // single and double quotes screw up the jso_encode js variables 
                if(TWS::isIterable($res)){
                    foreach($res as &$r){
                        $r['name']= htmlspecialchars($r['name'],ENT_QUOTES);
                        $r['description']= htmlspecialchars($r['description'],ENT_QUOTES);
                        $r['contact']= htmlspecialchars($r['contact'],ENT_QUOTES);
                    }
                }

                $ms[$m['market_id']] =$res;
            }
        }
        //TWS::pr($ms);
        return $ms; 

    }

    /**
    * Gets a list of stalls wanting a site allocation 
    * for the selected season of markets
    * 
    * @param mixed $marketId
    */
    function _get_allocation_requests($futureOnly=false){
        $markets = $this->markets; 

        $now = $this->nowDate;

        if(TWS::isIterable($markets)){
            foreach($markets as $m){
                if($futureOnly && ($now >= $m['market_date'] )){
                    continue; // its in the past so skip it
                }

                $sql = '    
                SELECT ST.*,ST.name as contact
                from stalls ST
                JOIN stallholders SH on SH.stall_id=ST.stall_id
                JOIN contacts C on C.contact_id=SH.contact_id
                JOIN market_allocation_requests MAR on MAR.stall_id=ST.stall_id
                where MAR.market_id='.$m['market_id'].' AND allocated_market_site_id=0 ';
                $res[$m['market_id']]=DBX::getRows($sql,'stall_id');
                DBX::abortOnError();

                if(TWS::isIterable($res)){
                    foreach($res[$m['market_id']] as &$r){
                        $r['name']= htmlspecialchars($r['name'],ENT_QUOTES);
                        $r['description']= htmlspecialchars($r['description'],ENT_QUOTES);
                        $r['contact']= htmlspecialchars($r['contact'],ENT_QUOTES);
                    }
                }
            }
        }




        return $res;

    }

    /**
    * used to check if there are any conflicts for a permananet
    * booking for $stallId from NOW to the end of the current_set_season
    * 
    * Returns an array of any conflicts found OR false if none found
    * 
    * @param mixed $stallId
    */
    function _get_booking_conflicts($siteId,$stallId=0){
        // return if no stalId or stallTypes is not an array of the stallTypes being checked out
        $stallId = (int) $stallId;
        $siteId = (int) $siteId;

        if(! $siteId || !$stallId ) {return false;}

        $futureMarkets = $this->pastAndFutureMarkets['future'];

        $conflicts['permanent']=array();//init return array
        $conflicts['casual'] = array();

        // Permenant conflicts
        $sql = '    SELECT * from market_sites 
        where  market_id IN('.implode(',',array_keys($futureMarkets)).') 
        and stall_id !='.$stallId . ' and site_id='.$siteId .' and `status` = "Active"';

        $res = DBX::getRows($sql);
        DBX::abortOnError();

        if(TWS::isIterable($res)){
            foreach($res as $r){
                $stallType=$r['stalltype_id'] % 2;
                if( $stallType == 0){ // Even stall_type_id(s) are casual
                    $conflicts['casual'][$r['market_site_id']]=$r;
                } else if ($stallType == 1) {// Odd stalltype_id(s) are permanent
                    $conflicts['permanent'][$r['market_site_id']]=$r;
                }
            }
        }

        //DEBUG
        //$this->_return_ajax_error('conflicts'.DBX::showError(),$conflicts);

        return $conflicts;

    }

    function _complete_allocation_request($marketId,$stallId,$marketSiteId){
        $res = DBX::getRow('select * from market_allocation_requests where market_id='.$marketId.' and stall_id='.$stallId);

        DBX::abortOnError();

        if($res){
            $res['allocated_market_site_id'] = $marketSiteId;
            DBX::update('market_allocation_requests',$res, ' where market_allocation_request_id='.$res['market_allocation_request_id']);
            DBX::abortOnError();
        }
    }

    function stall_update(){


        /*
        $_GET has a list of maket_identifier with the updted status 
        plus the stall_id

        m156    Active
        m157    Active
        m158    Active
        m159    Active
        m160    Active
        m161    Active
        m162    Active
        m163    Active
        m164    Active
        stall_id    1290
        */

        $ms = array();
        foreach($this->markets as $m){


            if(! isSet($_GET['m'.$m['market_id']])){
                continue;
            }
            $status = $_GET['m'.$m['market_id']];
            $stallId=$_GET['stall_id'];
            $siteId=$_GET['site_id'];



            // get current state IMPORTANT - there is likely No record for Not required marketss !!
            $res = DBX::getRow('select * from market_sites where market_id='.$m['market_id'].' and stall_id='.$stallId);

            if(!$res){ // ms was  previously not required 
                // if it is still not required we can do nothing
                if($status =='Not required'){
                    continue; // skip to next market - nothing to do
                } else {
                    // create a Not required record because we need and then  let the logic below work out what to do
                    $stall = $this->_get_stall_info($stallId);

                    $data = array(
                        'market_id'=>$m['market_id'],
                        'site_id'=>$siteId,
                        'stall_id'=>$stallId,
                        'stalltype_id'=>$stall['stalltype_id'],
                        'status'=>'Not required'
                    );
                    $result = DBX::insert('market_sites',$data);
                    DBX::abortOnError();

                    // now get the record back to use below.
                    $res = DBX::getRow('select * from market_sites where market_id='.$m['market_id'].' and stall_id='.$stallId);
                }
            }



            DBX::query('update market_sites set `status`="'.$status.'" where market_id='.$m['market_id'].' and stall_id='.$stallId);

            $newData=DBX::getRow('select * from market_sites where market_id='.$m['market_id'].' and stall_id='.$stallId);



            if($res['status']== 'Active'){ // booking used to be Active
                switch($status){// status is what it is being updated to
                    case 'Not required':
                        // issue credit 
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation='site Not required credit'); 

                        // remove request from market_allocation_request
                        DBX::query('DELETE from market_allocation_requests where stall_id='.$res['stall_id'].' and market_id='.$res['market_id'] );
                        DBX::abortOnError();
                        
                        // remove the MS
                        DBX::query('DELETE from market_sites where stall_id='.$res['stall_id'].' and market_id='.$res['market_id'] );
                        DBX::abortOnError();
                        
                        break;
                    case 'Cancelled':
                        // issue partial credit
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation='site cancellation credit');
                        break;

                }
            }
            if($res['status']== 'Not required'){ // booking used to be Not required
                switch($status){// status is what it is being updated to
                    case 'Active':
                        // charge for site 
                        $this->_charge_for_market_site_allocation($res['market_site_id'], $notation='std site charge, going from Not required to Active');
                        break;
                    case 'Cancelled':
                        // charge for site and issue partial credit ????
                        $this->_charge_for_market_site_allocation($res['market_site_id'], $notation='std site charge, going from Not required to Active so we can cancel - Cancellation credit to follow');
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation='site cancellation credit');
                        break;

                }
            }
            if($res['status']== 'No show' || $res['status']== 'Cancelled late'){ // booking used to be Not required
                switch($status){// status is what it is being updated to
                    case 'Active':
                        // no charge required as already charged
                        break;
                    case 'Cancelled':
                        // issue the cancellation credit  ????
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation='site cancellation credit');
                        break;

                }
            }




            if(strtolower($newData['status']) == 'active'){
                $ms[$m['market_id']]['active'][$res['site_id']] = $newData;
            } else {
                $ms[$m['market_id']]['not_active'][$res['site_id']] = $newData;
            }
        }






        $data['status']=200;
        $data['data']=$ms;
        header('Content-Type: application/json');
        echo json_encode($data,JSON_FORCE_OBJECT);
        exit;

    }


}
?>