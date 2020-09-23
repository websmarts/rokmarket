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
class Admin extends App
{
    public $error;
    public $ajax; // true if ajax request
    public $months = array(
        1 => 'jan',
        2 => 'feb',
        3 => 'mar',
        4 => 'apr',
        5 => 'may',
        6 => 'jun',
        7 => 'jul',
        8 => 'aug',
        9 => 'sep',
        10 => 'oct',
        11 => 'nov',
        12 => 'dec',
    );
    public $nowtime; // time in seconds for NOW
    public $nowDate; // NOW in 'Y-M-d' format
    public $season; // the start Year of selected season
    public $markets; // an array of markets in the selected season
    public $openMarkets; // season markets that are open
    public $closedMarkets; // season markets that are closed
    public $pastAndFutureMarkets; // markets keyed by ['past'] and ['future']
    public function __construct($config = false)
    {
        if (!$config) {
            die('no config files present');
        } else {
            $this->config = $config;
        }
        $this->viewDir = MODX_BASE_PATH . TWSAPP_DIR . 'views/admin/';
        /**
         * the REQUEST params that determine what method gets called
         */
        $this->requestMethod = isset($_REQUEST['ag']) && !empty($_REQUEST['ag']) ? $_REQUEST['ag'] : 'markets';
        $this->requestMethodAction = isset($_REQUEST['aga']) && !empty($_REQUEST['aga']) ? $_REQUEST['aga'] : '';
        /**
         * Check if ajax request
         */
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            /* special ajax here */
            $this->ajax = true;
        }
        //date_default_timezone_set ( 'Australia/Melbourne' );
        $this->nowtime = $this->_get_nowtime();
        $this->nowDate = date('Y-m-d', $this->_get_nowtime());
        //TWS::pr(date('Y-m-d H:i:s'));
        // Check if this and next season are loaded into the database - if not do it
        $this->_check_and_create_seasons();
        // Check if any SEASON has been set yet for application to work with
        // if not the set current season
        // sets $this->season
        if (!$this->_get_set_season()) {
            $this->_set_selected_season($this->_get_current_season()); // default to the NOW season
        }
        // markets for set season
        $this->markets = $this->_get_season_markets($this->_get_set_season());
        // Setup arrays of open and closed season markets
        foreach ($this->markets as $mid => $m) {
            if ($m['status'] == 'Open') {
                $this->openMarkets[$mid] = $m;
            } else {
                $this->closedMarkets[$mid] = $m;
            }
        }
        // past and future markets
        //arry['past']=arry of past markets
        // arry['future'] array of future markets
        $this->pastAndFutureMarkets['future'] = array();
        $this->pastAndFutureMarkets['past'] = array();
        foreach ($this->markets as $m) {
            if ($m['market_date'] < $this->nowDate) {
                $this->pastAndFutureMarkets['past'][$m['market_id']] = $m;
            } else {
                $this->pastAndFutureMarkets['future'][$m['market_id']] = $m;
            }
        }
        $this->baseUrl = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier);
        // Good place to add any formdef arrays - see bbsn i=for examples if needed
        $this->formdefs['contact'] = array(
            'firstname' => array(
                'id' => 'firstname',
                'field_group' => '',
                'type' => 'text',
                'name' => 'firstname',
                'label' => 'First name',
                'validation_rule' => 'not-empty',
                'validation_message' => 'First name cannot be empty',
                'attributes' => ' required ',
            ),
            'lastname' => array(
                'id' => 'lastname',
                'field_group' => '',
                'type' => 'text',
                'name' => 'lastname',
                'validation_rule' => 'not-empty',
                'validation_message' => 'Last name cannot be empty',
                'label' => 'Last name',
                'attributes' => ' required ',
            ),
            'firstname2' => array(
                'id' => 'firstname2',
                'field_group' => '',
                'type' => 'text',
                'name' => 'firstname2',
                'label' => 'First name - alternate contact',
                'validation_rule' => '',
                'validation_message' => '',
                'attributes' => '',
            ),
            'lastname2' => array(
                'id' => 'lastname2',
                'field_group' => '',
                'type' => 'text',
                'name' => 'lastname2',
                'validation_rule' => '',
                'validation_message' => '',
                'label' => 'Last name - alternate contact',
                'attributes' => '',
            ),
            'address1' => array(
                'id' => 'address1',
                'field_group' => '',
                'type' => 'text',
                'name' => 'address1',
                'label' => 'Address1',
            ),
            'address2' => array(
                'id' => 'address2',
                'field_group' => '',
                'type' => 'text',
                'name' => 'address2',
                'label' => 'Address2',
            ),
            'city' => array(
                'id' => 'city',
                'field_group' => '',
                'type' => 'text',
                'name' => 'city',
                'label' => 'City',
            ),
            'postcode' => array(
                'id' => 'postcode',
                'field_group' => '',
                'type' => 'text',
                'name' => 'postcode',
                'label' => 'Postcode',
            ),
            'phone' => array(
                'id' => 'phone',
                'field_group' => '',
                'type' => 'text',
                'name' => 'phone',
                'label' => 'Phone',
            ),
            'mobile' => array(
                'id' => 'mobile',
                'field_group' => '',
                'type' => 'text',
                'name' => 'mobile',
                'label' => 'Mobile',
            ),
            'email' => array(
                'id' => 'email',
                'field_group' => '',
                'type' => 'text',
                'name' => 'email',
                'label' => 'Email',
            ),
            'notes' => array(
                'id' => 'notes',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'notes',
                'label' => 'Notes',
            ),
            'status' => array(
                'id' => 'status',
                'field_group' => '',
                'type' => 'select',
                'name' => 'status',
                'label' => 'Status',
                'options' => array(
                    'Active' => 'Active',
                    'Archive' => 'Archive',
                ),
                'attributes' => ' required ',
            ),
            'b' => array(
                'id' => 'b',
                'type' => 'submit',
                'name' => 'b',
                'value' => 'Save',
                'attributes' => ' class="btn btn-primary" ',
            ),
        );
        if (!$this->ajax && $_GET['ag'] !== 'reports') {
            $this->menu(); // echo the menu, if not ajax request
        }
        /**
         * Call the request handler
         */
        $method = $this->requestMethod;
        $action = $this->requestMethodAction;
        $this->$method($action);
    }
    /**
     * This method outputs the html menu for  App
     *
     * @param mixed $actionGroup
     */
    public function menu($actionGroup = '')
    {
        $url = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier);
        $menu = '    <div id="appmenu">';
        $menu .= '<ul class="nav nav-tabs">';
        $menu .= '<li><span style="font-size:18pt;color:#777">&nbsp; ' . $this->season .'-'.($this->season + 1) . '&nbsp;</span>&nbsp;</li>';
        //$menu .= '<li ' . ($actionGroup == 'import' ? 'class="active"' : '') . '><a href="' . $url . '?ag=import&aga=">Importer</a></li>';
        $menu .= '<li ' . ($actionGroup == 'markets' ? 'class="active"' : '') . '><a href="' . $url . '?ag=markets">Season</a></li>';
        //$menu .= '<li ' . ($actionGroup == 'locations' ? 'class="active"' : '') . '><a href="' . $url . '?ag=locations">Locations</a></li>';
        //$menu .= '<li ' . ($actionGroup == 'sites' ? 'class="active"' : '') . '><a href="' . $url . '?ag=sites">Sites</a></li>';
        $menu .= '<li ' . ($actionGroup == 'contacts' ? 'class="active"' : '') . '><a href="' . $url . '?ag=contacts">Contacts</a></li>';
        $menu .= '<li ' . ($actionGroup == 'stalls' ? 'class="active"' : '') . '><a href="' . $url . '?ag=stalls">Stalls</a></li>';
        //$menu .= '<li ' . ($actionGroup == 'cancellations' ? 'class="active"' : '') . '><a href="' . $url . '?ag=cancellations">Cancellations</a></li>';
        $menu .= '<li ' . ($actionGroup == 'markets_view' ? 'class="active"' : '') . '><a href="' . $url . '?ag=markets_view">Markets</a></li>';
        // $menu .= '<li ' . ($actionGroup == 'finances' ? 'class="active"' : '') . '><a href="' . $url . '?ag=finances">Finance reports</a></li>';
        $menu .= '<li ' . ($actionGroup == 'notes' ? 'class="active"' : '') . '><a href="' . $url . '?ag=notes">Market Notes</a></li>';
        $menu .= '<li ' . ($actionGroup == 'collections' ? 'class="active"' : '') . '><a href="' . $url . '?ag=collections">Season Collections</a></li>';
        $menu .= '<li ' . ($actionGroup == 'reports' ? 'class="active"' : '') . '><a href="' . $url . '?ag=reports">Export Season Data</a></li>';
        $menu .= '</ul>';
        $menu .= ' </div>';
        echo $menu;
    }
    public function setup()
    {
        $this->render('setup');
    }
    public function _get_nowtime()
    {
        //DateTimezone set in admin.php to Aust/Melb
        return time();
    }
    public function _get_set_season()
    {
        if (isset($_SESSION[TWS_APP_SESSION_DATAKEY]['season']) && preg_match('/\d{4}/', $_SESSION[TWS_APP_SESSION_DATAKEY]['season'])) {
            $this->season = $_SESSION[TWS_APP_SESSION_DATAKEY]['season'];
            return $this->season;
        } else {
            return false;
        }
    }
    public function notes($action = false)
    {
        if (!$action) {
            if ($_POST) {
                // update notes page
                $data['content'] = $_POST['content'];
                $data['id'] = 1;
                DBX::update('notes', $data);
            }
            // show notes page
            $this->data['note'] = DBX::getRow('select content,modified from notes where id=1');
            $this->render('notes');
        }
    }
    public function markets($action = false)
    {
        if ($_POST) {
            if ($action == 'setseason') {
                $this->_set_selected_season($_POST['seasonstartyear']);
                // Save the stall charges info for the season
                //TWS::pr($_POST,1);
                if (strtolower($_POST['b']) == 'update') {
                    if (TWS::isIterable($_POST['std_site_fee'])) {
                        foreach ($_POST['std_site_fee'] as $stalltypeId => $fee) {
                            $data = array(
                                'std_site_fee' => (float)$fee,
                                'prompt_payment_site_fee' => (float)$_POST['prompt_payment_site_fee'][$stalltypeId],
                                'cancellation_credit' => (float)$_POST['cancellation_credit'][$stalltypeId],
                            );
                            //TWS::pr($data,1);
                            DBX::update('stalltypes', $data, ' where stalltype_id=' . $stalltypeId . ' and season=' . $this->season);
                            DBX::abortOnError();
                        }
                        // $message = "Informastion has been updated";
                    }
                }
                //exit;
                // TWS::flash('message', $message);
                // TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED
                $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=markets';
                header('location: ' . $url);
                exit;
            }
            // TWS::pr($_GET);
            // TWS::pr($action);
            // TWS::pr($_POST);exit;
            // Check if request is calling for  cancelled  action
            // Hack added in Oct 2017 to handle when planned markets are cancelled
            if (strtolower($_POST['b']) == 'cancel' && $_POST['confirm_cancellation'] == 'cancel market') {
                $marketId = isset($_GET['market_id']) && (int)$_GET['market_id'] > 0 ? (int)$_GET['market_id'] : 0;
                if ($action == 'cancel' && $marketId) {
                    // die('going to cancel market ' . $marketId);exit;
                    // toggle Active / Cancelled  status for the market
                    $market = DBX::getRow('SELECT `status` from markets where market_id=' . $marketId);
                    if ($market['status'] == 'Open') {
                        DBX::update('markets', array('status' => 'Cancelled'), 'WHERE market_id=' . $marketId);
                        // Now delete all entries form the market_sites table for this cancelled market
                        DBX::delete('market_sites', array('market_id' => $marketId));
                    }
                    // $message = "Market has been cancelled";
                }
            }
            $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=markets';
            header('location: ' . $url);
            exit;
        }
        // Check if request is calling for  statustoggle action
        $marketId = isset($_GET['market_id']) && (int)$_GET['market_id'] > 0 ? (int)$_GET['market_id'] : 0;
        if ($action == 'statustoggle' && $marketId) {
            // toggle Active / Cancelled  status for the market
            $market = DBX::getRow('SELECT `status` from markets where market_id=' . $marketId);
            //TWS::pr($market);exit;
            if ($market['status'] == 'Cancelled') {
                // do nothing ??
            } else if ($market['status'] == 'Open') {
                DBX::update('markets', array('status' => 'Closed'), 'WHERE market_id=' . $marketId);
            } else {
                DBX::update('markets', array('status' => 'Open'), 'WHERE market_id=' . $marketId);
            }
            $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=markets';
            header('location: ' . $url);
            exit;
        }
        //Sept is always first month of a season
        $seasons = DBX::getRows('select YEAR(market_date) as seasonstartyear from markets where MONTH(market_date) = 9 ');
        // If season is set then Get the list of markets
        if ($this->season) {
            $this->data['season'] = $this->_get_season_markets($this->season);
        } else {
            die("season is NOT YET SET - should have been set in ADMIN CONSTRUCTOR!!!!");
        }
        $this->data['stalltypes'] = $this->_get_stalltypes($this->season);
        $this->data['seasons'] = $seasons;
        $this->render('markets');
    }
    public function _get_season_markets($season)
    {
        $sql = '    select * from markets
        where market_date BETWEEN "' . $season . '-09-01"
        AND"' . ($season + 1) . '-06-01" order by market_date asc';
        //TWS::pr($sql);
        $markets = DBX::getRows($sql, 'market_id');
        // add a few useful extra tid bits
        foreach ($markets as &$m) {
            $time = strtotime($m['market_date']);
            $m['F'] = date('F', $time);
            $m['M'] = date('M', $time);
            $m['Y'] = date('Y', $time);
        }
        DBX::abortOnError();
        return $markets;
    }
    public function _check_and_create_seasons()
    {
        // Get the current list of markets
        $today = date('Y-m-d', $this->nowtime);
        $nowYear = date('Y', $this->nowtime);
        $nowMonth = date('n', $this->nowtime); // numeric month
        $lastMarket = DBX::getRow('select market_date from markets order by market_date desc limit 1 ');
        if (!$lastMarket) {
            // markets table is empty !
            // create the current season
            if ($nowMonth < 6) {
                $startYear = $nowYear - 1;
            } else {
                $startYear = $nowYear;
            }
            $this->_addMarketSeason($startYear);
            $this->_set_selected_season($startYear);
        } else {
            // Check if lastMarket is in next season and if not then add a new season
            $lastMarketYear = substr($lastMarket['market_date'], 0, 4);
            if (($nowMonth < 6 && (($lastMarketYear - $nowYear) < 1)) || ($nowMonth > 5 && (($lastMarketYear - $nowYear) < 2))) {
                // then last_market year should be nowYear plus one
                $this->_addMarketSeason($lastMarketYear);
            }
        }
    }
    public function _get_current_season()
    {
        $nowYear = date('Y', $this->nowtime);
        $nowMonth = date('n', $this->nowtime); // numeric month
        if ($nowMonth < 6) {
            $seasonStartYear = $nowYear - 1;
        } else {
            $seasonStartYear = $nowYear;
        }
        return $seasonStartYear;
    }
    public function _set_selected_season($year)
    {
        if (preg_match('/\d{4}/', $year)) {
            $_SESSION[TWS_APP_SESSION_DATAKEY]['season'] = $year;
            $this->season = $year;
        } else {
            die(__FUNCTION__ . ' called with invalid YEAR =' . $year);
        }
        // check if stalltypes have already been set for this season
        $sql = 'select * from stalltypes where season=' . $this->season;
        $res = DBX::getRows($sql);
        if (count($res) != 5) {
            // season data should be five records so looks like we have not set any yet.
            $res = DBX::getRows('select * from stalltypes where season=0');
            DBX::abortOnError();
            foreach ($res as $r) {
                $r['season'] = $this->season;
                unset($r['id']);
                DBX::insert('stalltypes', $r);
                DBX::abortOnError();
            }
        }
        return $year; // may as well return set season
    }
    public function _addMarketSeason($startYear)
    {
        foreach ($this->config['market_months_startyear'] as $m) {
            $date = date('Y-m-d', strtotime($this->config['market_month_day'] . ' ' . $this->months[$m] . ' ' . $startYear));
            if (!DBX::insert('markets', array('market_date' => $date, 'status' => 'Closed'))) {
                TWS::pr(DBX::showError());
            }
        }
        $nextYear = ++$startYear;
        foreach ($this->config['market_months_finishyear'] as $m) {
            $date = date('Y-m-d', strtotime('+1 week sat ' . $this->months[$m] . ' ' . $nextYear));
            if (!DBX::insert('markets', array('market_date' => $date, 'status' => 'Closed'))) {
                TWS::pr(DBX::showError());
            }
        }
    }
    public function locations($action = false)
    {
        if ($_POST) {
            //TWS::pr($_POST);exit;
            if (strtolower($_POST['aga']) == 'update') {
                if (TWS::isIterable($_POST['locations'])) {
                    foreach ($_POST['locations'] as $k => $name) {
                        if (empty($name)) {
                            // Delete maybe
                        } else {
                            if ($k == 0) {
                                // Add a new location
                                DBX::insert('locations', array('name' => $name));
                            } else {
                                // Update the location
                                DBX::update('locations', array('name' => $name), 'WHERE location_id=' . $k);
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
    public function sites($action = false)
    {
        // if ACTION is set then determine handler and call that
        $action = !empty($action) ? $action : 'list';
        $action_handler_method = __FUNCTION__ . '_' . $action;
        $this->$action_handler_method();
    }
    public function sites_list()
    {
        $this->data['sites'] = DBX::getRows('select * from sites ');
        $this->data['locations'] = $this->_get_locations();
        $this->render('sites_list');
    }
    public function sites_edit()
    {
        $table = 'sites'; // database table we are working with
        // Determine contact ID
        $contactId = isset($_GET['site_id']) ? (int)$_GET['site_id'] : 0; // 0 = create new
        //$rid = isSet($_POST['rid']) ? (int) $_POST['rid'] : 0; // Post form record id
        $locations = $this->_get_locations();
        $elements = array(
            'location_id' => array(
                'id' => 'approval_status',
                'field_group' => '',
                'type' => 'select',
                'name' => 'location_id',
                'label' => 'Location',
                'options' => $locations,
                'attributes' => ' required ',
            ),
            'note' => array(
                'id' => 'note',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'note',
                'label' => 'Note',
            ),
            'site_reference' => array(
                'id' => 'site_reference',
                'field_group' => '',
                'type' => 'text',
                'name' => 'site_reference',
                'label' => 'Site reference (eg R43)',
                'validation_rule' => 'not-empty',
                'validation_message' => 'Site reference isnot valid',
            ),
            'b' => array(
                'id' => 'b',
                'type' => 'submit',
                'name' => 'b',
                'value' => 'Save',
                'attributes' => ' class="btn btn-primary" ',
            ),
        );
        $action = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier) . '?ag=sites&aga=edit&site_id=' . $siteId;
        $formname = 'edit_site';
        $f = new TWSForm($action, $formname, 'post');
        foreach ($elements as $e) {
            $f->addElement($e);
        }
        $e = array(
            'id' => 'site_id',
            'type' => 'hidden',
            'name' => 'site_id',
            'value' => $siteId,
        );
        $f->addElement($e);
        if ($siteId > 0) {
            // add a delete button if we have a contact
            $e = array(
                'id' => 'bb',
                'type' => 'submit',
                'name' => 'bb',
                'value' => 'Delete',
                'attributes' => ' class="btn" onclick="return confirm(\'Are you sure you want to delete?\')"',
            );
            $f->addElement($e);
        }
        // Now process request
        if ($_POST) {
            // check for DELETE
            if (TWS::requestVar('formname') == $formname && TWS::requestVar('bb') == 'Delete' && TWS::requestVar('site_id') > 0) {
                $siteId = TWS::requestVar('site_id'); // defaults to post var
                $this->_delete_site($siteId);
                TWS::flash('message', 'Site has been deleted');
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
                    $data = array();
                    $data['location_id'] = $_POST['location_id'];
                    $data['note'] = $_POST['note'];
                    $data['site_reference'] = $_POST['site_reference'];
                    if ($siteId > 0) {
                        // update record
                        DBX::update($table, $data, 'WHERE site_id=' . $siteId);
                        DBX::abortOnError();
                        TWS::flash('message', 'Site has been updated');
                    } else {
                        // insert new record
                        //TWS::pr('inserting record', 1);
                        $insertId = DBX::insert($table, $data);
                        DBX::abortOnError();
                        TWS::flash('message', 'Site has been added');
                        if (!$insertId) {
                            TWS::flash('error', 'Database error: failed to insert new information into ' . $table . ' table');
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
        $this->data['f'] = &$f;
        $this->render('sites_edit');
    }
    public function _get_locations()
    {
        $res = DBX::getRows('select * from locations order by `name` asc', 'location_id');
        $locations = array();
        if (TWS::isIterable($res)) {
            foreach ($res as $l) {
                $locations[$l['location_id']] = $l['name'];
            }
        }
        return $locations;
    }
    public function _get_contacts_list($status = false)
    {
        if ($status) {
            $where = ' WHERE `status`="' . $status . '" ';
        } else {
            $where = '';
        }
        $res = DBX::getRows('select * from contacts ' . $where . ' order by `lastname` asc', 'contact_id');
        DBX::abortOnError();
        $result = array(0 => '-- select --');
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                $result[$r['contact_id']] = $r['lastname'] . ', ' . $r['firstname'];
            }
        }
        return $result;
    }
    /**
     * Get a list of sites for the season that dont have a perm stalltype_id assocaited with them (in the future)
     * unles they are for the chosen stall
     *
     */
    public function _get_permanent_sites_available($stallId)
    {
        $sites = $this->_get_sites();
        /*if(PAST_LOCKED){// need to look at whole seasom
        $markets = $this->markets;
        } else { // just look at future markets
        $markets = $this->pastAndFutureMarkets['future'];
         */
        //$markets = $this->markets;
        $markets = $this->openMarkets; // changed to check only open markets instead of ALL markets 12/01/2015 re tony hosenans issue
        //TWS::pr($markets);
        //TWS::pr($stallId);
        //TWS::pr($futureMarkets);
        //TWS::pr(implode(',',array_keys($futureMarkets)));
        if (!TWS::isIterable($markets)) {
            return array();
        }
        $sql = '
        SELECT  site_id,stall_id,stalltype_id from market_sites
        WHERE
        market_id IN(' . implode(',', array_keys($markets)) . ')';
        //TWS::pr($sql);
        $res = DBX::getRows($sql);
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                if ($this->_is_odd($r['stalltype_id'])) {
                    // only want permenant market_site allocations
                    if (isset($sites[$r['site_id']]) && ($r['stall_id'] != $stallId)) {
                        unset($sites[$r['site_id']]);
                    }
                }
            }
        }
        //TWS::pr($res);
        //TWS::pr($sites);
        $result = array();
        if (TWS::isIterable($sites)) {
            foreach ($sites as $s) {
                $result[$s['site_id']] = $s['site_reference'];
            }
        }
        // sort result bu site_ref (alpha asc
        uasort($result, 'sort_sites');
        //TWS::pr($result);
        return $result;
    }
    public function _get_category_options()
    {
        $res = DBX::getRows('select * from categories ');
        DBX::abortOnError();
        $result = array();
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                $result[$r['category_id']] = $r['description'];
            }
        }
        return $result;
    }
    public function _was_get_stalltypes()
    {
        $res = DBX::getRows('select * from stalltypes', 'stalltype_id');
        DBX::abortOnError();
        return $res;
    }
    public function _get_stalltypes()
    {
        // stalltype data with season = 0 forms the default values
        $sql = 'select * from stalltypes where season = ' . $this->season;
        $res = DBX::getRows($sql); // there should be 10
        $n = 0;
        $st = array();
        foreach ($res as $r) {
            $r['stalltype_id'] = ++$n;
            $st[$n] = $r;
        }
        return $st;
    }
    public function _get_stalltype_options()
    {
        $res = $this->_get_stalltypes();
        $result = array();
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                $result[$r['stalltype_id']] = $r['description'];
            }
        }
        return $result;
    }
    /* End Sites */
    /* CONTACTS FUNCTIONS */
    public function contacts($action = false)
    {
        // if ACTION is set then determine handler and call that
        $action = !empty($action) ? $action : 'list';
        $action_handler_method = __FUNCTION__ . '_' . $action;
        $this->$action_handler_method();
    }
    public function contacts_list()
    {
        $this->data['result'] = DBX::getRows('select * from contacts where 1 order by lastname asc '); //`status`="Active"
        $this->render('contacts_list');
    }
    public function contacts_edit()
    {
        $table = 'contacts'; // database table we are working with
        // Determine contact ID
        $contactId = isset($_GET['contact_id']) ? (int)$_GET['contact_id'] : 0; // 0 = create new
        //$rid = isSet($_POST['rid']) ? (int) $_POST['rid'] : 0; // Post form record id
        $elements = $this->formdefs['contact'];
        $action = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier) . '?ag=contacts&aga=edit&contact_id=' . $contactId;
        $formname = 'edit_contact';
        $f = new TWSForm($action, $formname, 'post');
        foreach ($elements as $e) {
            $f->addElement($e);
        }
        $e = array(
            'id' => 'contact_id',
            'type' => 'hidden',
            'name' => 'contact_id',
            'value' => $contactId,
        );
        $f->addElement($e);
        /*
        if($contactId > 0){ // add a delete button if we have a contact
        $e = array
        (
        'id' =>'bb',
        'type' => 'submit',
        'name' => 'bb',
        'value' => 'Delete',
        'attributes' => ' class="btn" onclick="return confirm(\'Are you sure you want to delete contact?\')"'
        );
        $f->addElement($e);
        }
         */
        // Now process request
        if ($_POST) {
            // check for DELETE
            if (TWS::requestVar('formname') == $formname && TWS::requestVar('bb') == 'Delete' && TWS::requestVar('contact_id') > 0) {
                $contactid = TWS::requestVar('contact_id'); // defaults to post var
                $this->_delete_contact($contactId);
                TWS::flash('message', 'Contact has been deleted');
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
                    $data = array();
                    $data['salutation'] = $_POST['salutation'];
                    $data['firstname'] = $_POST['firstname'];
                    $data['lastname'] = $_POST['lastname'];
                    $data['firstname2'] = $_POST['firstname2'];
                    $data['lastname2'] = $_POST['lastname2'];
                    $data['address1'] = $_POST['address1'];
                    $data['address2'] = $_POST['address2'];
                    $data['city'] = $_POST['city'];
                    $data['postcode'] = $_POST['postcode'];
                    $data['phone'] = $_POST['phone'];
                    $data['mobile'] = $_POST['mobile'];
                    $data['email'] = $_POST['email'];
                    $data['notes'] = $_POST['notes'];
                    $data['status'] = $_POST['status'];
                    if ($contactId > 0) {
                        // update record
                        DBX::update($table, $data, 'WHERE contact_id=' . $contactId);
                        DBX::abortOnError();
                        // if status set to Archive we need to deactivate any stalls and remove any site bookings or allocation requests
                        if ($data['status'] != "Active") {
                            // Deactivate stall(s)
                            $sql = '    select stall_id from stallholders where contact_id=' . $contactId;
                            $stalls = DBX::getRows($sql);
                            DBX::abortOnError();
                            if (TWS::isIterable($stalls)) {
                                foreach ($stalls as $s) {
                                    // set stall status to inactive
                                    DBX::query('UPDATE stalls set `status`="Inactive" where stall_id=' . $s['stall_id']);
                                    DBX::abortOnError();
                                    // Remove any permanent site allocations
                                    DBX::query('delete from stall_sites where stall_id=' . $s['stall_id']);
                                    DBX::abortOnError();
                                    // Remove any casual requests
                                    DBX::query('DELETE from market_allocation_requests where stall_id=' . $s['stall_id']);
                                    DBX::abortOnError();
                                    // Remove any allocated Market Sites in future
                                    $futureMarketIds = implode(',', array_keys($this->pastAndFutureMarkets['future']));
                                    if (!empty($futureMarketIds)) {
                                        $sql = '    DELETE from market_sites where stall_id=' . $s['stall_id'] . '
                                                AND market_id IN (' . $futureMarketIds . ')';
                                        DBX::query($sql);
                                        DBX::abortOnError();
                                    }
                                }
                            }
                        }
                        TWS::flash('message', 'Contact has been updated');
                    } else {
                        // insert new record
                        //TWS::pr('inserting record', 1);
                        //$data['status']='Active';
                        $insertId = DBX::insert($table, $data);
                        DBX::abortOnError();
                        TWS::flash('message', 'Contact has been added');
                        if (!$insertId) {
                            TWS::flash('error', 'Database error: failed to insert new information into ' . $table . ' table');
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
        $this->data['f'] = &$f;
        $this->render('contacts_edit');
    }
    public function _delete_contact($contactId)
    {
        // for now just set the status to deleted
        DBX::query('UPDATE contacts set `status` = "Deleted" WHERE contact_id=' . $contactId);
        DBX::abortOnError();
        DBX::query('Delete form satallholders where contact_id=' . $contactTd);
        DBX::abortOnError();
        // should probably cancell any market_sites for future markets too??
    }
    /* End Contcts */
    /* STALLS FUNCTIONS */
    public function stalls($action = false)
    {
        // if ACTION is set then determine handler and call that
        $action = !empty($action) ? $action : 'list';
        $action_handler_method = __FUNCTION__ . '_' . $action;
        $this->$action_handler_method();
    }
    /*
    function stalls_edit_ajax(){
    $this->ajax = true;
    $this->stalls_edit(); // call normal handler
    echo ' okay';
    exit;
    }
     */
    /*
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
     */
    public function _get_stall_allocation_requests($stallId)
    {
        $allocations = array();
        $markets = $this->markets;
        foreach ($markets as $k => $m) {
            $sql = 'SELECT * FROM market_allocation_requests MAR where market_id=' . $k . ' and stall_id=' . $stallId;
            $res = DBX::getRow($sql);
            DBX::abortOnError();
            $allocations[$k] = $res;
        }
        return $allocations;
    }
    /**
     * Get a list of casual requests for stall
     * to display on the stall edit page
     * Adds an F if freebee to the number so it
     * can be displayed in the request input box
     */
    public function _get_number_of_stall_casual_requests($stallId)
    {
        $sql = '    select * from market_allocation_requests
        where market_id IN(' . implode(',', array_keys($this->markets)) . ')
        and stall_id=' . $stallId;
        $res = DBX::getRows($sql);
        DBX::abortOnError();
        $requests = array();
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                $a = $requests[$r['market_id']];
                $aStr = preg_replace("/[0-9]/", '', $a);
                // if F then we can ignore $r['freebee]
                //if empty check if freebee is > 0 and set to F
                if ($aStr == 'F') {
                    // do nothing
                } else if ($r['is_freebee'] > 0) {
                    $aStr = 'F';
                }
                $aNum = preg_replace("/[^0-9]/", '', $a);
                $requests[$r['market_id']] = ++$aNum . $aStr;
                $aNum++;
            }
        }
        return $requests;
    }
    /**
     * Gets a comprehensive list  of  infor about a stall
     *
     *
     * @param mixed $stallId
     */
    public function _get_stall_info($stallId)
    {
        $sql = '    select stalls.* from stalls
        where stalls.stall_id=' . $stallId;
        $stall = DBX::getRow($sql);
        $stall['main_contact_id'] = array();
        $sql = '
        select C.* from contacts C
        join stallholders  SH on C.contact_id=SH.contact_id
        where SH.primary_contact=1 and SH.stall_id=' . $stallId;
        $rs = DBX::getRow($sql);
        DBX::abortOnError();
        if ($rs) {
            $stall['main_contact_id'] = $rs['contact_id'];
        }
        $stall['main_contact'] = $rs;
        $stall['contact_ids'] = array();
        $sql = '
        select C.* from contacts C
        join stallholders  SH on C.contact_id=SH.contact_id
        where SH.primary_contact=0 and SH.stall_id=' . $stallId;
        $contactRes = DBX::getRows($sql);
        if (TWS::isIterable($contactRes)) {
            foreach ($contactRes as $c) {
                $stall['contact_ids'][$c['contact_id']] = $c['contact_id'];
            }
        }
        $stall['contacts'] = $contactRes;
        // get any assigned perm season sites
        /*
        $sql = 'select DISTINCT site_id,stalltype_id from market_sites where stall_id='.$stallId .'
        and market_id IN ('.implode(',',array_keys($this->markets)).')
        and NOT isNull(site_id)';
        $res = DBX::getRows($sql);
        DBX::abortOnError();
        //TWS::pr($res);
        $sites = array();
        if(TWS::isIterable($res)){
        foreach($res as $s){
        if($this->_is_odd($s['stalltype_id'])){
        $sites[$s['site_id']]= $s['site_id'];
        }
        }
        }
         */
        $sql = 'select site_id from stall_sites where stall_id=' . $stallId;
        $rs = DBX::getRows($sql, 'site_id');
        DBX::abortOnError();
        $sites = array();
        if (TWS::isIterable($rs)) {
            foreach ($rs as $siteId => $arr) {
                $sites[$siteId] = $siteId;
            }
        }
        $stall['permanent_site_ids'] = $sites;
        //TWS::pr($stall); exit;
        // get season pass issued attribute for current season
        $stall['season_pass'] = DBX::getRow('select season_pass_issued from season_attributes where stall_id=' . $stallId . ' AND season=' . $this->season);
        if ($stall['season_pass']['season_pass_issued'] == 0) {
            unset($stall['season_pass']);
        }
        return $stall;
    }
    public function _update_casual_stall_attendance($stallId, $attendance)
    {
        // get list of market_sites actually allocated - these are the only ones we update
        // Note cancel and Cancel late are dealt with by removing the request itself
        $sql = '
        select * FROM market_sites
        where stall_id=' . $stallId . '
        AND stalltype_id IN (2,4)
        AND market_id IN (' . implode(',', array_keys($this->markets)) . ')';
        $mss = DBX::getRows($sql);
        DBX::abortOnError();
        if ($mss && TWS::isIterable($mss)) {
            foreach ($mss as $ms) {
                $status = $attendance['casual'][$ms['market_id']];
                if (!in_array($status, array('Active', 'Cancelled', 'Cancelled late', ' No show'))) {
                    continue; // ignore if status is not set sensibly
                }
                $sql = 'UPDATE market_sites set `status`=' . DBX::quote($status) . ' WHERE market_site_id=' . $ms['market_site_id'];
                DBX::query($sql);
                DBX::abortOnError();
            }
        }
    }
    public function _update_permanent_stall_attendance($stallId, $sites = false, $attendance = false)
    {
        //TWS::pr($stallId);
        //TWS::pr($sites);
        //TWS::pr($attendance);
        //exit;
        // do perms first - no real reason for this
        if (TWS::isIterable($attendance['permanent'])) {
            foreach ($attendance['permanent'] as $marketId => $status) {
                if (isset($this->closedMarkets[$marketId])) {
                    continue; // skip any closed markets
                }
                if (!in_array($status, array('Active', 'Cancelled', 'Cancelled late', 'No show', 'Closed'))) {
                    continue;
                }
                $sql = '
                UPDATE market_sites set `status`=' . DBX::quote($status) . '
                WHERE market_id=' . $marketId . '
                AND stall_id =' . $stallId . '
                AND site_id IN (' . implode(',', $sites) . ')
                AND stalltype_id IN (1,3,5)';
                //TWS::pr($sql);
                DBX::query($sql);
                DBX::abortOnError();
            }
        }
    }
    /**
     * Stall edit calls this to update a stalls site allocations in MS Tablet
     * .
     *
     * @param mixed $stallId
     * @param mixed $siteRequests
     */
    public function _update_stall_marketsites($stallId, $siteRequests)
    {
        // Note siteRequests is limited to open markets ONLY
        // get the stall info
        $stall = $this->_get_stall_info($stallId);
        // determin stalltype
        if (1 == $stall['community_stall_free']) {
            $stallTypeId = 5;
        } else if (1 == $stall['community_stall']) {
            $stallTypeId = 3;
        } else {
            $stallTypeId = 1;
        }
        // get list of current Permenant market_sites for the Season
        $currentSites = $this->_get_stall_permanent_bookings($stallId); // OPEN & CLOSED MARKETS
        if (TWS::isIterable($currentSites)) {
            foreach ($currentSites as $marketSiteId => $cs) {
                if (isset($this->closedMarkets[$cs['market_id']])) {
                    unset($currentSites[$marketSiteId]); //
                    continue;
                }
                // check if current site is still in requests
                if (isset($siteRequests[$cs['market_id']][$cs['site_id']])) {
                    unset($siteRequests[$cs['market_id']][$cs['site_id']]); // remove request as we have it
                    // Update existing site with current stalldata - stalltype
                    DBX::query('update market_sites set stalltype_id=' . $stallTypeId . ' where market_site_id=' . $marketSiteId);
                    DBX::abortOnError();
                    unset($currentSites[$marketSiteId]); // mark it off the done list
                }
            }
        }
        // delete any currentSites we have left now as their is no request for them anymore
        if (TWS::isIterable($currentSites)) {
            foreach ($currentSites as $marketSiteId => $csArr) {
                $sql = 'delete from market_sites where market_site_id=' . $marketSiteId . ' and `status` != "Closed" ';
                DBX::query($sql);
                DBX::abortOnError();
            }
        }
        // Add any left in siteRequests as they are wanted
        if (TWS::isIterable($siteRequests)) {
            foreach ($siteRequests as $marketId => $srArr) {
                if (TWS::isIterable($srArr)) {
                    foreach ($srArr as $siteId => $v) {
                        $data = array();
                        $data['market_id'] = $marketId;
                        $data['site_id'] = $siteId;
                        $data['stall_id'] = $stallId;
                        $data['stalltype_id'] = $stallTypeId;
                        $data['status'] = 'Active';
                        // get any squatter
                        $squatter = DBX::getRow('select * from market_sites where NOT isNull(stall_id) and stall_id !=' . $stallId . '  and market_id=' . $data['market_id'] . ' and site_id=' . $data['site_id']);
                        DBX::abortOnError();
                        if ($squatter) {
                            // for now delete squatter
                            DBX::query('delete from market_sites where market_site_id=' . $squatter['market_site_id']);
                            DBX::abortOnError();
                            // reset any MARs that were allocated this market_site_id
                            DBX::query('update market_allocation_requests set allocated_market_site_id=0 where allocated_market_site_id=' . $squatter['market_site_id']);
                            DBX::abortOnError();
                        }
                        // insert the site request
                        DBX::insert('market_sites', $data);
                        DBX::abortOnError();
                    }
                }
            }
        }
    }
    public function stalls_list()
    {
        if (TWS::requestVar('contact_id', 'get')) {
            $sql = 'select  s.* from stalls as s
            join stallholders as sh ON sh.stall_id=s.stall_id
            join contacts as c on c.contact_id = sh.contact_id
            where sh.contact_id=' . TWS::requestVar('contact_id', 'get') . ' ';
        } else {
            $sql = 'select  s.* from stalls as s
            join stallholders as sh ON sh.stall_id=s.stall_id
            join contacts as c on c.contact_id = sh.contact_id
            where 1';
        }
        $sql .= ' ORDER BY c.lastname ASC';
        $stalls = DBX::getRows($sql);
        DBX::abortOnError();
        //TWS::pr($sql);
        // get contact(s) for stall
        if (TWS::isIterable($stalls)) {
            foreach ($stalls as &$s) {
                $sql = '    select c.firstname,c.lastname from contacts as c
                join stallholders as sh on sh.contact_id=c.contact_id
                where sh.stall_id=' . $s['stall_id'] . '
                ORDER BY c.lastname asc';
                $s['stallholders'] = DBX::getRows($sql);
                DBX::abortOnError();
                // determine stalltype by looking to see if any
                //entries in MS for PERMs or MAR for casual
                $s['stalltype'] = '';
                $sql = ' select DISTINCT stall_id from market_sites
                where stall_id=' . $s['stall_id'] . '
                and `status`="Active"
                AND stalltype_id IN(1,3,5)
                AND market_id IN(' . implode(',', array_keys($this->markets)) . ')';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                if ($rs['stall_id'] == $s['stall_id']) {
                    $s['stalltype'] .= 'P';
                }
                $sql = ' select DISTINCT stall_id from market_allocation_requests
                where stall_id=' . $s['stall_id'] . '
                AND market_id IN(' . implode(',', array_keys($this->markets)) . ')';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                if ($rs['stall_id'] == $s['stall_id']) {
                    $s['stalltype'] .= 'C';
                }
                /*
                // performance ranking
                $sql = ' select count(*) as booked from market_sites
                where stall_id='.$s['stall_id'].'
                AND market_id IN('.implode(',',array_keys($this->markets)).')';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                if($rs && $rs['booked'] > 0){// more than zero attendaces booked in season
                $booked = $rs['booked'];
                //ATTENDED
                $sql = ' select count(*) as attended from market_sites
                where stall_id='.$s['stall_id'].'
                and `status` IN ("Active")
                AND market_id IN('.implode(',',array_keys($this->markets)).')';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                $attended = $rs['attended'];
                //CANCELLED
                $sql = ' select count(*) as cancelled from market_sites
                where stall_id='.$s['stall_id'].'
                and `status` IN ("Cancelled")
                AND market_id IN('.implode(',',array_keys($this->markets)).')';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                $cancelled = $rs['cancelled'];
                //CANCELLED LATE
                $sql = ' select count(*) as cancelled_late from market_sites
                where stall_id='.$s['stall_id'].'
                and `status` IN ("Cancelled late")
                AND market_id IN('.implode(',',array_keys($this->markets)).')';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                $cancelled_late = $rs['cancelled_late'];
                //CANCELLED LATE
                $sql = ' select count(*) as noshow from market_sites
                where stall_id='.$s['stall_id'].'
                and `status` IN ("No show")
                AND market_id IN('.implode(',',array_keys($this->markets)).')';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                $noshow = $rs['noshow'];
                if($attended == $booked)
                $rank= $attended/$booked -($cancelled_late/($booked-$))
                }
                 */
                //$s['permanent_sites'] = $this->_get_stall_permanent_sites($s['stall_id']);
            }
        }
        $this->data['stalls'] = $stalls;
        $this->render('stalls_list');
    }
    public function _get_stall_casual_bookings($stallId)
    {
        $sql = '
        select
        MS.market_site_id,
        MS.market_id,
        MS.site_id,
        MS.stalltype_id,
        MS.`status`,
        S.site_reference
        from market_sites MS
        JOIN sites S on MS.site_id=S.site_id
        where MS.market_id IN(' . implode(',', array_keys($this->markets)) . ')
        and MS.stall_id=' . $stallId . '
        and MS.stalltype_id IN(2,4)
        and NOT isNull(MS.site_id)';
        //TWS::pr($sql);
        $bookings = array();
        $res = DBX::getRows($sql);
        DBX::abortOnError();
        if ($res && TWS::isIterable($res)) {
            foreach ($res as $r) {
                $bookings[$r['market_site_id']] = $r;
            }
        }
        return $bookings;
    }
    /**
     * Get a list of bookings for stall in current season
     */
    public function _get_stall_permanent_bookings($stallId)
    {
        $sql = 'select  market_site_id, market_id,site_id,stalltype_id,`status` from market_sites
        where market_id IN(' . implode(',', array_keys($this->markets)) . ')
        and stall_id=' . $stallId . '
        and stalltype_id IN(1,3,5)
        and NOT isNull(site_id)';
        //TWS::pr($sql);
        $bookings = array();
        $res = DBX::getRows($sql);
        DBX::abortOnError();
        if ($res && TWS::isIterable($res)) {
            foreach ($res as $r) {
                $bookings[$r['market_site_id']] = $r;
            }
        }
        return $bookings;
    }
    public function stalls_edit()
    {
        $table = 'stalls'; // database table we are working with
        // Determine contact ID
        $stallId = isset($_GET['stall_id']) ? (int)$_GET['stall_id'] : 0; // 0 = create new
        //$rid = isSet($_POST['rid']) ? (int) $_POST['rid'] : 0; // Post form record id
        $this->data['form_elements'] = array(
            'name' => array(
                'id' => 'name',
                'field_group' => '',
                'type' => 'text',
                'name' => 'name',
                'label' => 'Stall name',
                'validation_rule' => '',
                'validation_message' => 'Stall name cannot be empty',
                'attributes' => '',
            ),
            'category' => array(
                'id' => 'category_ids',
                'type' => 'multi_select',
                'name' => 'category_ids',
                'label' => 'Category(s)',
                'multi_select_size' => 5,
                'options' => $this->_get_category_options(),
            ),
            'main_contact' => array(
                'id' => 'main_contact_id',
                'type' => 'select',
                'name' => 'main_contact_id',
                'label' => 'Contact',
                'options' => $this->_get_contacts_list('Active'),
            ) /*,
            'contacts' => array(
            'id' => 'contact_ids',
            'type' => 'multi_select',
            'name' => 'contact_ids',
            'label' => 'Other contact(s)',
            'multi_select_size' => 3,
            'options' =>  $this->_get_contacts_list('Active')
            ),
            'mailname' => array
            (
            'id' => 'mailname',
            'field_group' => '',
            'type' => 'text',
            'name' => 'mailname',
            'label' => 'Mailout addressee(s)'
            )*/,
            'permanent_site_ids' => array(
                'id' => 'permanent_site_ids',
                'type' => 'multi_select',
                'name' => 'permanent_site_ids',
                'label' => 'Permanent site(s)',
                'multi_select_size' => 4,
                'options' => $this->_get_permanent_sites_available($stallId),
                'attributes' => ' style="width: 10em" ',
            ),
            'community_stall' => array(
                'id' => 'community_stall',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'community_stall',
                'label' => 'Community stall (discount rate)',
            ),
            'community_stall_free' => array(
                'id' => 'community_stall_free',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'community_stall_free',
                'label' => 'Community stall FREE',
            ),
            'relinquish_site' => array(
                'id' => 'relinquish_site',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'relinquish_site',
                'label' => 'Relinquish site(s) - deletes all permanent bookings for future markets',
            ),
            'application_form_on_file' => array(
                'id' => 'application_form_on_file',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'application_form_on_file',
                'label' => 'Application form on file',
            ),
            'foodseller' => array(
                'id' => 'foodseller',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'foodseller',
                'label' => 'Foodseller',
            ),
            'council_registration' => array(
                'id' => 'council_registration',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'council_registration',
                'label' => 'Council registration',
            ),
            'insurance' => array(
                'id' => 'insurance',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'insurance',
                'label' => 'Insurance',
            ),
            'season_pass' => array(
                'id' => 'season_pass',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'season_pass',
                'label' => 'Season pass issued',
            ),
            'prompt_payment_discount' => array(
                'id' => 'prompt_payment_discount',
                'field_group' => '',
                'type' => 'checkbox',
                'name' => 'prompt_payment_discount',
                'label' => 'Prompt payment discount',
            ),
            'description' => array(
                'id' => 'description',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'description',
                'validation_rule' => '',
                'validation_message' => 'Description cannot be empty',
                'label' => 'Description',
                'attributes' => '',
            ),
            'status' => array(
                'id' => 'status',
                'field_group' => '',
                'type' => 'select',
                'name' => 'status',
                'label' => 'Status',
                'options' => array(
                    'New request' => 'New request',
                    'Under consideration' => 'Under consideration',
                    'Rejected' => 'Rejected',
                    'Active' => 'Active',
                    'Inactive' => 'Inactive',
                ),
                'attributes' => ' required ',
            ),
            'notes' => array(
                'id' => 'notes',
                'field_group' => '',
                'type' => 'textarea',
                'name' => 'notes',
                'label' => 'Notes',
            ),
            'stall_id' => array(
                'id' => 'stall_id',
                'type' => 'hidden',
                'name' => 'stall_id',
                'value' => $stallId,
            ),
            'save_button' => array(
                'id' => 'b',
                'type' => 'submit',
                'name' => 'b',
                'value' => 'Save',
                'attributes' => ' class="btn btn-primary" style="width:300px;" ',
            ),
            'delete_button' => array(
                'id' => 'bb',
                'type' => 'submit',
                'name' => 'bb',
                'value' => 'Delete',
                'attributes' => ' class="btn" onclick="return confirm(\'Are you sure you want to delete?\')"',
            ),
        );
        $action = TWS::modx()->makeUrl(TWS::modx()->documentIdentifier) . '?ag=stalls&aga=edit&stall_id=' . $stallId;
        $formname = 'edit_stall';
        $f = new TWSForm($action, $formname, 'post');
        foreach ($this->data['form_elements'] as $e) {
            $f->addElement($e);
        }
        $this->data['f'] = &$f;
        // Now process request
        if ($_POST) {
            //TWS::pr($_POST); exit;
            // check for DELETE
            if (TWS::requestVar('formname') == $formname && TWS::requestVar('bb') == 'Delete' && TWS::requestVar('contact_id') > 0) {
                die('Delete not yet implemented');
                $contactid = TWS::requestVar('contact_id'); // defaults to post var
                $this->_delete_stall($contactId);
                $this->_delete_stall_allocations($contactId);
                $this->_delete_stall_allocation_requests($contactId);
                TWS::flash('message', 'stall has been deleted');
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
                    $data = array();
                    $data['name'] = $_POST['name'];
                    $data['description'] = $_POST['description'];
                    $data['status'] = $_POST['status'];
                    $data['notes'] = $_POST['notes'];
                    $data['mailout_addressee'] = $_POST['mailname'];
                    // CHECKBOXES
                    $checkboxes = array(
                        'community_stall',
                        'community_stall_free',
                        'application_form_on_file',
                        'foodseller',
                        'council_registration',
                        'insurance',
                        'prompt_payment_discount',
                        'season_pass',
                    );
                    foreach ($checkboxes as $cb) {
                        $data[$cb] = isset($_POST[$cb]) ? 1 : 0;
                    }
                    //Reliquish_site checbox - not saved to DB just used to flag behaviour
                    $relinquishSite = isset($_POST['relinquish_site']) ? 1 : 0;
                    //TWS::pr($data); exit;
                    if ($stallId > 0) {
                        // update record
                        DBX::update($table, $data, 'WHERE stall_id=' . $stallId);
                        DBX::abortOnError();
                        // now update stall contact(s)
                        DBX::query('delete from stallholders where stall_id=' . $stallId); // remove old entries
                        $mainContactId = (int)$_POST['main_contact_id'];
                        if ($mainContactId > 0) {
                            DBX::insert('stallholders', array('stall_id' => $stallId, 'contact_id' => $mainContactId, 'primary_contact' => 1));
                        }
                        if (TWS::isIterable(TWS::requestVar('contact_ids'))) {
                            foreach (TWS::requestVar('contact_ids') as $contact) {
                                DBX::insert('stallholders', array('stall_id' => $stallId, 'contact_id' => $contact, 'primary_contact' => 0));
                            }
                        }
                        // update season attributes eg season_pass issued
                        $seasonAttributes = DBX::getRow('select * from season_attributes where stall_id=' . $stallId . ' and season=' . $this->season);
                        if ($data['season_pass'] != $seasonAttributes['season_pass_issued']) {
                            // if record exists update it
                            if ($seasonAttributes['id'] > 0) {
                                // record exists so just update data
                                $seasonAttributes['season_pass_issued'] = $data['season_pass'];
                                DBX::update('season_attributes', $seasonAttributes);
                                DBX::abortOnError();
                            } else {
                                // insert new record
                                $aData['season'] = $this->season;
                                $aData['season_pass_issued'] = $data['season_pass'];
                                $aData['stall_id'] = $stallId;
                                DBX::insert('season_attributes', $aData);
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
                        $mainContactId = (int)$_POST['main_contact_id'];
                        if ($mainContactId > 0) {
                            DBX::insert('stallholders', array('stall_id' => $insertId, 'contact_id' => $mainContactId, 'primary_contact' => 1));
                        }
                        if (TWS::isIterable(TWS::requestVar('contact_ids'))) {
                            foreach (TWS::requestVar('contact_ids') as $contactId) {
                                DBX::insert('stallholders', array('stall_id' => $insertId, 'contact_id' => $contactId, 'primary_contact' => 0));
                                DBX::abortOnError();
                            }
                        }
                        TWS::flash('message', 'Stall has been added');
                        if (!$insertId) {
                            TWS::flash('error', 'Database error: failed to insert new information into ' . $table . ' table');
                        } else {
                            $stallId = $insertId;
                        }
                    }
                    // Save allocated perm sites to stall_site table
                    $rs = DBX::query('delete from stall_sites where stall_id=' . $stallId);
                    DBX::abortOnError();
                    if (1 == $data['community_stall_free']) {
                        $stallTypeId = 5;
                    } else if (1 == $data['community_stall']) {
                        $stallTypeId = 3;
                    } else {
                        $stallTypeId = 1;
                    }
                    //create permanent site requests array FOR OPEN SEASON MARKETS --  CLOSED MARKETS ARE LOCKED!!!
                    $siteRequests = array();
                    if (1 != $relinquishSite) {
                        if (TWS::isIterable($_POST['permanent_site_ids'])) {
                            if (TWS::isIterable($_POST['permanent_allocation_request'])) {
                                foreach ($_POST['permanent_allocation_request'] as $marketId => $v) {
                                    foreach ($_POST['permanent_site_ids'] as $siteId) {
                                        if ($this->markets[$marketId]['status'] == 'Open') {
                                            $siteRequests[$marketId][$siteId] = true;
                                        }
                                    }
                                }
                            }
                        }
                        // Check if any attendance is required at any open markets -
                        if (isset($_POST['permanent_site_ids'])) {
                            if (TWS::isIterable($_POST['permanent_site_ids'])) {
                                foreach ($_POST['permanent_site_ids'] as $siteId) {
                                    DBX::insert('stall_sites', array('stall_id' => $stallId, 'site_id' => $siteId, 'stalltype_id' => $stallTypeId));
                                    DBX::abortOnError();
                                }
                            }
                        }
                    } else {
                        // relinquish the site from the stall
                        // delete sites from stall_sites table
                        DBX::query('delete from stall_sites where stall_id=' . $stallId);
                        DBX::abortOnError();
                        // delete any MS from future markets
                        $futureMarkets = $this->pastAndFutureMarkets['future']; //[$m['market_id']]=$m;
                        if (TWS::isIterable($futureMarkets)) {
                            foreach ($futureMarkets as $marketId => $v) {
                                $sql = 'delete from market_sites
                                where stall_id=' . $stallId . '
                                and stalltype_id IN(1,3,5)
                                and market_id=' . $marketId;
                                DBX::query($sql);
                                DBX::abortOnError();
                            }
                        }
                    }
                    // update show_location_list
                    DBX::query('delete from show_location_list where stall_id=' . $stallId);
                    if (TWS::isIterable($_POST['show_location'])) {
                        foreach ($_POST['show_location'] as $marketId => $v) {
                            if ($v == 'on') {
                                DBX::query('INSERT INTO show_location_list (stall_id,market_id) VALUES (' . $stallId . ',' . $marketId . ')');
                            }
                        }
                    }
                    $this->_update_stall_marketsites($stallId, $siteRequests); // process permanent checkboxes
                    $this->_update_allocation_requests($stallId, $_POST['casual_allocation_request']); // process casual requests
                    if (isset($_POST['permanent_site_ids']) && isset($_POST['attendance'])) { // maynot be if stall is not active
                        $this->_update_permanent_stall_attendance($stallId, $_POST['permanent_site_ids'], $_POST['attendance']); //
                    }
                    if (isset($_POST['attendance'])) {
                        $this->_update_casual_stall_attendance($stallId, $_POST['attendance']); //
                    }
                    // check stall status finally check if stall status has ben set to anything other than Active and
                    // close the stall down if it has
                    if ($data['status'] != 'Active') {
                        // close down the stall
                        // Remove any permanent site ids
                        $rs = DBX::query('delete from stall_sites where stall_id=' . $stallId);
                        DBX::abortOnError();
                        // set any open and future markets in MS to Closed
                        if (TWS::isIterable($this->pastAndFutureMarkets['future'])) {
                            foreach ($this->pastAndFutureMarkets['future'] as $fm) {
                                if (isset($this->openMarkets[$fm['market_id']])) {
                                    $sql = 'UPDATE market_sites set `status`="Closed"
                                    where market_id=' . $fm['market_id'] . '
                                    AND stall_id=' . $stallId;
                                    DBX::query($sql);
                                    DBX::abortOnError();
                                }
                            }
                        }
                        // remove any pending market_allocation_requests
                        $sql = 'delete from market_allocation_requests where stall_id=' . $stallId;
                        DBX::query($sql);
                        DBX::abortOnError();
                    }
                    TWS::clearFlashFormdata(); // all saved so now clear NO LONGER SUPPPORTED
                    // if request is ajax then just return a error status - if not ajax reload the page
                    if ($this->ajax) {
                        return;
                    } else {
                        $url = TWS::modx()->MakeUrl(TWS::modx()->documentIdentifier) . '?ag=stalls&aga=edit&stall_id=' . $stallId;
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
                TWS::flash('formdata', $this->_get_stall_info($stallId));
                //TWS::pr($_SESSION);
            }
        }
        /*
        foreach($markets as $m){
        $this->data['stall_sites'][$m['market_id']] = $this->_get_stall_market_sites($m['market_id'],$stallId);
        // Currently avialable sites
        $this->data['available_sites'][$m['market_id']] = $this->_get_available_market_sites($m['market_id']);
        }
         */
        //TWS::pr($this->data['stall_sites']);
        //TWS::pr($this->data['available_sites']);
        $this->data['allocation_requests'] = $this->_get_stall_allocation_requests($stallId);
        $this->data['stall'] = $this->_get_stall_info($stallId);
        $this->data['permanent_bookings'] = $this->_get_stall_permanent_bookings($stallId);
        $this->data['casual_bookings'] = $this->_get_stall_casual_bookings($stallId);
        $this->data['casual_requests'] = $this->_get_number_of_stall_casual_requests($stallId);
        $this->data['markets'] = $this->markets;
        $this->data['closed_markets'] = $this->closedMarkets;
        $this->data['open_markets'] = $this->openMarkets;
        $this->data['pastAndFutureMarkets'] = $this->pastAndFutureMarkets;
        // stall site costs past and future
        $this->data['charges'] = $this->_get_stall_charges($stallId); // charges for the current season
        $this->data['payments'] = $this->_get_stall_payments($stallId); // list of all payment records
        $this->data['primary_contact'] = $this->_get_stall_primary_contact($stallId);
        $this->data['show_locations'] = $this->_get_show_location_list($stallId);
        $this->render('stalls_edit');
    }
    public function _get_show_location_list($stallId)
    {
        $sql = ' select * from show_location_list where market_id  IN(' . implode(',', array_keys($this->markets)) . ') and stall_id=' . $stallId;
        $res = DBX::getRows($sql, 'market_id');
        return $res;
    }
    public function _get_stall_primary_contact($stallId)
    {
        $sql = 'select C.* from stallholders SH
                JOIN contacts C on C.contact_id=SH.contact_id
                WHERE SH.primary_contact > 0
                and SH.stall_id=' . $stallId . ' LIMIT 1';
        $res = DBX::getRow($sql);
        DBX::abortOnError();
        return $res;
    }
    public function _get_stall_payments($stallId)
    {
        $sql = 'SELECT * from transactions
        where stall_id=' . $stallId . '
        and season=' . $this->_get_set_season() . '
        order by transaction_date asc';
        $payments = DBX::getRows($sql, 'id');
        DBX::abortOnError();
        return $payments;
    }
    public function _get_season_credit_total($stallId)
    {
        $credits = $this->_get_stall_payments($stallId);
        $credit = 0;
        if (TWS::isIterable($credits)) {
            foreach ($credits as $k => $v) {
                $credit += $v['amount'];
            }
        }
        return $credit;
    }
    /**
     * Get a list of charges for the current season
     * sort by market
     */
    public function _get_stall_charges($stallId)
    {
        if (!$stallId) {
            return 0;
        }


        // Set to false if you want the market collection sheet
        // to show the amount to collect for the entire season - this is normal behaviour. 
        // However if you are in covid type times and only want to collect what is due
        // up until the current market day then set $covid = true


        $covid = COVID_MODE;

        // Market_id is not always present eg when displaying edit stall screen
        // a market_id would not make sense bacuase it deals with the entire season.

        if(!$covid  || !isSet($_GET['market_id'])) { 
            $sql = '
            SELECT
            MS.market_id,
            MS.site_id,
            MS.stalltype_id,
            MS.`status`,
            S.prompt_payment_discount,
            ST.std_site_fee,
            ST.prompt_payment_site_fee,
            ST.cancellation_credit
            FROM market_sites as MS
            JOIN stalls as S on S.stall_id=MS.stall_id
            JOIN stalltypes ST on ST.stalltype_id=MS.stalltype_id
            WHERE MS.market_id IN (' . implode(',', array_keys($this->markets)) . ')
            AND MS.stalltype_id=ST.stalltype_id
            AND ST.season=' . $this->season . '
            AND MS.stall_id = ' . $stallId . '
            AND MS.`status` IN("Active","Cancelled","Cancelled late","No show","Closed") ';

        }

        

        // Covid version of query only calcs charges due up until the 
        // current market rather than for the whole season
        

        if ($covid && isSet($_GET['market_id'])) {

            $soFarMarkets = array();
            foreach($this->markets as $key => $m){
                if($key > $_GET['market_id']){
                    continue;
                }
                $soFarMarkets[$key] = $m;
            }
            // TWS::pr($this->markets);
            // TWS::pr($soFarMarkets);
            // die('done at line 1852');


        $sql = '
        SELECT
        MS.market_id,
        MS.site_id,
        MS.stalltype_id,
        MS.`status`,
        S.prompt_payment_discount,
        ST.std_site_fee,
        ST.prompt_payment_site_fee,
        ST.cancellation_credit
        FROM market_sites as MS
        JOIN stalls as S on S.stall_id=MS.stall_id
        JOIN stalltypes ST on ST.stalltype_id=MS.stalltype_id
        WHERE MS.market_id IN (' . implode(',', array_keys($soFarMarkets)) . ')
        AND MS.stalltype_id=ST.stalltype_id
        AND ST.season=' . $this->season . '
        AND MS.stall_id = ' . $stallId . '
        AND MS.`status` IN("Active","Cancelled","Cancelled late","No show","Closed") ';
        }
        //  TWS::pr($sql);

        $res = DBX::getRows($sql);
        DBX::abortOnError();
        $charges = 0;
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                $fee = 0;
                if ($r['stalltype_id'] == 1) {
                    // permanent
                    if ($r['prompt_payment_discount']) {
                        $fee = $r['prompt_payment_site_fee'];
                    } else {
                        $fee = $r['std_site_fee'];
                    }
                    // cancelled  only incurrs a partial charge
                    if (strtoupper($r['status']) == 'CANCELLED' || strtoupper($r['status']) == 'CLOSED') {
                        $fee -= $r['cancellation_credit'];
                    }
                } elseif ($r['stalltype_id'] == 2) {
// casual(2)
                    if (strtoupper($r['status']) == 'ACTIVE') {
                        $fee += $r['std_site_fee'];
                    }
                } elseif ($r['stalltype_id'] == 3) {
// community perm
                    $fee = $r['std_site_fee'];
                    if (strtoupper($r['status']) == 'CANCELLED') {
                        $fee -= $r['cancellation_credit'];
                    }
                } else {
                    $fee = 0; // its a freebee
                }
                $charges += $fee;
            }
        }
        return ($charges);
    }
    /**
     * Used to check in num is odd or not
     * odd stalltypes are PERMANENT and even rare casual
     *
     * @param mixed $num
     */
    public function _is_odd($num)
    {
        $num = (int)$num;
        return $num % 2;
    }
    /**
     * Update Permamnet stalls allocation requests
     * ONLY CALLED FROM STALLS_EDIT
     *
     * @param mixed $stallId
     * @param mixed $requests
     */
    public function _update_allocation_requests($stallId, $requests)
    {
        $stall = $this->_get_stall_info($stallId);
        DBX::abortOnError();
        if (!$stall) {
            return false;
        }
        // stall does not exist ???
        $markets = $this->markets;
        // only allow updates to future markets
        $sql = '    SELECT * FROM market_allocation_requests
        WHERE market_id IN (' . implode(',', array_keys($markets)) . ')
        AND stall_id=' . $stallId;
        $res = DBX::getRows($sql); // for set season for ALL stalls
        $currentRequests = array();
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                $currentRequests[$r['market_id']][] = $r;
            }
        }
        //TWS::pr($requests);
        //TWS::pr($currentRequests); exit;
        // first check if there are any new requests
        if (TWS::isIterable($requests)) {
            foreach ($requests as $marketId => $req) {
                //$req = something like 1 or 1F or 2 or 2F
                // determine how many casual sites are being requested
                // note $req holds this info but can take  forms like 1= one site, 2F= two free sites
                $req = trim($req); // remove white space
                $freebee = preg_replace("/[0-9]/", '', $req); // get the string info
                $freebee = strtolower($freebee) == 'f' ? 1 : 0;
                $number = preg_replace("/[^0-9]/", '', $req);
                $requested = isset($_POST['requested_datetime'][$marketId]) ? $_POST['requested_datetime'][$marketId] : '';
                while ($number > 0) {
                    if (TWS::isIterable($currentRequests[$marketId])) {
                        foreach ($currentRequests[$marketId] as $k => $r) {
                            //check if $r matches what we want
                            if ($r['market_id'] == $marketId && $r['stall_id'] == $stallId) {
                                // we have this one so remove it from the list
                                // But before we do we may need to change its stalltype_id if
                                // the alloc request freebee status has been changed
                                unset($currentRequests[$marketId][$k]);
                                if ($r['is_freebee'] != $freebee) {
                                    $is_freebee = $freebee > 0 ? 1 : 0;
                                    DBX::query('update market_allocation_requests set is_freebee=' . $is_freebee . ' where market_allocation_request_id=' . $r['market_allocation_request_id']);
                                    DBX::abortOnError();
                                    //if there is a MS for this MAR then update MS.stalltype_id as well
                                    if ($r['allocated_market_site_id'] != 0) {
                                        // there is an MS record that needs updating
                                        if ($is_freebee == 0) {
                                            $stallTypeId = 2; // regular casual
                                        } else {
                                            $stallTypeId = 4; // Community casual (freebee)
                                        }
                                        DBX::query('UPDATE market_sites set stalltype_id=' . $stallTypeId . ' where market_site_id=' . $r['allocated_market_site_id']);
                                        DBX::abortOnError();
                                    }
                                }
                                $number--;
                                break; // Very important to take a break
                            }
                            //if yes then set flag = true;
                            // if not delete the currentRequest
                        }
                    } else {
                        // no current requests so just add
                        $data = array(
                            'market_id' => $marketId,
                            'stall_id' => $stallId,
                            'allocated_market_site_id' => 0,
                            'is_freebee' => $freebee,
                        );
                        DBX::insert('market_allocation_requests', $data);
                        DBX::abortOnError();
                        $number--;
                    }
                }
                // update MAR for this market_id for this stallId with $requested info
                if ($requested) {
                    DBX::query('UPDATE market_allocation_requests set requested="' . $requested . '" where market_id=' . $marketId . ' and stall_id=' . $stallId);
                    DBX::abortOnError();
                }
            }
        }
        //All requests should now be in MAR
        // delete any excess left in $currentRequests
        if (TWS::isIterable($currentRequests)) {
            foreach ($currentRequests as $marketId => $reqArr) {
                if (TWS::isIterable($reqArr)) {
                    foreach ($reqArr as $cr) {
                        if (strtoupper($this->markets[$cr['market_id']]['status']) != 'CLOSED') {
                            DBX::query('delete from market_allocation_requests where market_allocation_request_id=' . $cr['market_allocation_request_id']);
                            DBX::abortOnError();
                            // Delete any MS that had been allocated
                            if ($cr['allocated_market_site_id'] > 0) {
                                DBX::query('delete from market_sites where market_site_id=' . $cr['allocated_market_site_id']);
                                DBX::abortOnError();
                            }
                        }
                    }
                }
            }
        }
        // All done
    }
    public function _get_stall_market_sites($marketId, $stallId = false)
    {
        $sql = '
        SELECT S.*,L.`name` as location,MS.`status`
        from market_sites as MS
        JOIN sites as S on S.site_id= MS.site_id
        JOIN locations L on L.location_id=S.location_id
        where MS.market_id=' . $marketId;
        if ($stallId) {
            $sql .= ' and MS.stall_id=' . $stallId;
        }
        $rs = DBX::getRows($sql);
        DBX::abortOnError();
        $result = array();
        if (TWS::isIterable($rs)) {
            foreach ($rs as $r) {
                $result[$r['site_id']] = $r;
            }
        }
        return $result;
    }
    public function _get_available_market_sites($marketId)
    {
        //TWS::pr($marketId);
        $sql = 'select * from market_sites where market_id=' . $marketId . ' AND `status`="Active"';
        $allocatedSites = DBX::getRows($sql, 'site_id');
        DBX::abortOnError();
        //TWS::pr($allocatedSites);
        $sql = ' select site_id,site_reference, locations.`name` as location from sites join locations on locations.location_id=sites.location_id order by site_id asc';
        $allSites = DBX::getRows($sql, 'site_id');
        DBX::abortOnError();
        //TWS::pr($allSites);
        if (TWS::isIterable($allocatedSites)) {
            foreach ($allocatedSites as $k => $v) {
                unset($allSites[$k]);
            }
        }
        //TWS::pr($allSites);
        return $allSites;
    }
    public function _return_ajax_error($msg, $data)
    {
        header('Content-Type: application/json');
        $rdata['status'] = 500; // error code
        $rdata['error_msg'] = $msg;
        $rdata['data'] = $data;
        echo json_encode($rdata, JSON_FORCE_OBJECT);
        exit;
    }
    public function _return_ajax($status = 500, $msg, $data)
    {
        header('Content-Type: application/json');
        $rdata['status'] = $status; // status code 200=okay, cant proces error, others defined to specific meaning
        $rdata['msg'] = $msg;
        $rdata['data'] = $data;
        echo json_encode($rdata, JSON_FORCE_OBJECT);
        exit;
    }
    public function _remove_casual_booking($marketId, $siteId, $stallId)
    {
        $done = false;
        // get the current alloc record
        $where = ' where market_id=' . $marketId . ' and stall_id=' . $stallId;
        $res = DBX::getRow('select * from market_sites ' . $where . ' and site_id=' . $siteId);
        if ($res) {
            DBX::begin();
            // reset the market_allocations_table
            $sql = '    update market_allocation_requests set allocated_market_site_id=0 ' . $where;
            DBX::query($sql);
            if (DBX::error()) {
                //$this->_return_ajax_error('error 1 '.$sql,$_POST);
                $error = true;
            }
            // Delete transaction
            $sql = 'delete from transactions where market_site_id=' . $res['market_site_id'];
            DBX::query($sql);
            if (DBX::error()) {
                //$this->_return_ajax_error('error 2 '.$sql.' DBERROR: '.DBX::error(),$_POST);
                $error = true;
            }
            // finally delete the market_site itself
            $sql = 'delete from market_sites where market_site_id=' . $res['market_site_id'];
            DBX::query($sql);
            if (DBX::error()) {
                //$this->_return_ajax_error('error 3 '.$sql,$_POST);
                $error = true;
            }
            if ($error) {
                DBX::rollback();
                //$this->_return_ajax_error('rollback',$_POST);
                $done = false;
            } else {
                //$this->_return_ajax_error('commit',$_POST);
                DBX::commit();
                $done = true;
            }
        }
        return $done;
    }
    public function _issue_cancellation_credit_for_market_site($marketSiteId = false, $notation = 'site cancellation credit')
    {
        if (!$marketSiteId) {
            return false;
        }
        // must give us a ms id
        $marketSite = DBX::getRow('select * from market_sites where market_site_id=' . $marketSiteId);
        if (!$marketSite) {
            return false;
        }
        // strange - ms not found
        $stalltype = DBX::getRow('select * from stalltypes where stalltype_id=' . $marketSite['stalltype_id'] . ' AND season=' . $this->season);
        if ($marketSite['status'] == 'Cancelled') {
            $tdata = array();
            $tdata['stall_id'] = $marketSite['stall_id'];
            $tdata['market_site_id'] = $marketSiteId;
            $tdata['transaction_type'] = 1; // a credit
            $tdata['amount'] = $stalltype['cancellation_credit'];
            $tdata['note'] = 'Market date:' . $this->markets[$marketSite['market_id']]['market_date'] . " \n" . $notation;
            if ($tdata['amount'] > 0) {
                $tid = DBX::insert('transactions', $tdata);
                DBX::abortOnError();
            }
        } elseif ($marketSite['status'] == 'Not required') {
            $tdata = array();
            $tdata['stall_id'] = $marketSite['stall_id'];
            $tdata['market_site_id'] = $marketSiteId;
            $tdata['transaction_type'] = 1; // a credit
            $tdata['amount'] = $stalltype['std_site_fee'];
            $tdata['note'] = 'Market date:' . $this->markets[$marketSite['market_id']]['market_date'] . " \n" . $notation;
            if ($tdata['amount'] > 0) {
                $tid = DBX::insert('transactions', $tdata);
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
    public function _charge_for_market_site_allocation($marketSiteId = false, $notation = 'std site charge')
    {
        if (!$marketSiteId) {
            return false;
        }
        // must give us a ms id
        $marketSite = DBX::getRow('select * from market_sites where market_site_id=' . $marketSiteId);
        if (!$marketSite) {
            return false;
        }
        // strange - ms not found
        $stalltype = DBX::getRow('select * from stalltypes where stalltype_id=' . $marketSite['stalltype_id'] . ' AND season=' . $this->season);
        $tdata = array();
        $tdata['stall_id'] = $marketSite['stall_id'];
        $tdata['market_site_id'] = $marketSiteId;
        $tdata['transaction_type'] = -1; // a charge
        $tdata['amount'] = $stalltype['std_site_fee'];
        $tdata['note'] = 'Market date:' . $this->markets[$marketSite['market_id']]['market_date'] . " \n" . $notation;
        if ($tdata['amount'] > 0) {
            $tid = DBX::insert('transactions', $tdata);
            DBX::abortOnError();
        }
        return $tid;
    }
    /**
     * Move a casual  from one site to another vacant one
     *
     */
    public function site_reallocate()
    {
        $newSiteId = $_POST['new_site_id'];
        $oldSiteId = $_POST['old_site_id'];
        $marketId = $_POST['market_id'];
        // first update MS by changing to new site
        DBX::query(' UPDATE market_sites set site_id=' . $newSiteId . '
            WHERE market_id=' . $marketId . '
            AND site_id=' . $oldSiteId . '
            AND `status`="Active"');
        DBX::abortOnError();
        // return updated MS data and for market
        // get the updated list of allocation requests and return
        $marketSites = $this->_get_market_site_data();
        $data['data']['market_sites'] = $marketSites[$marketId];
        $data['status'] = 200;
        header('Content-Type: application/json');
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit;
    }
    public function site_deallocate()
    {
        $sql = '
        select market_site_id from market_sites
        where market_id=' . $_POST['market_id'] . '
        and site_id=' . $_POST['site_id'] . '
        and stall_id=' . $_POST['stall_id'] . '
        and `status`="Active"';
        $ms = DBX::getRow($sql);
        DBX::abortOnError();
        $sql = '
        update market_allocation_requests
        set allocated_market_site_id=0
        where allocated_market_site_id=' . $ms['market_site_id'];
        DBX::query($sql);
        DBX::abortOnError();
        // remove the MS
        DBX::query('delete from market_sites
            where market_id=' . $_POST['market_id'] . '
            and site_id=' . $_POST['site_id'] . '
            and stall_id=' . $_POST['stall_id']);
        DBX::abortOnError();
        // get the updated allocation requests
        $mar = $this->_get_allocation_requests();
        $data['data'] = $mar[$_POST['market_id']]; // only return MARs for THIS market_id (save bandwidth??)
        $data['status'] = 200;
        header('Content-Type: application/json');
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit;
    }
    /* ALLOCATE SITE FUNCTIONS */
    public function site_allocation($action = false)
    {
        if ($_POST) {
            /*
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
             */
            //$data = array('a'=>1223,'b'=>456,'c'=>'this is c');
            //$data['data'] = $_POST;
            // Casual siteallocation
            //get the MAR record
            $mar = DBX::getRow('select * from market_allocation_requests where market_allocation_request_id=' . $_POST['mar_id']);
            // check casual allocation is for a future date - else die!!
            if (PAST_LOCKED && $this->nowDate > $this->markets[$mar['market_id']]['market_date']) {
                $this->_return_ajax_error('POST IS LOCKED cant allocate to passed markets while !', $_POST);
            }
            $d['site_id'] = $_POST['site_id'];
            $d['stall_id'] = $mar['stall_id'];
            $d['market_id'] = $mar['market_id'];
            $d['status'] = 'Active';
            // determine stalltype_id for MS table - it is either casual(2) ir casual freebee (4))
            if ($mar['is_freebee'] > 0) {
                $d['stalltype_id'] = 4; // free casual
            } else {
                $d['stalltype_id'] = 2; //regular casual
            }
            $d['market_site_id'] = DBX::insert('market_sites', $d);
            if (DBX::error() || !$d['market_site_id']) {
                $this->_return_ajax_error('could not save casual market site', $d);
            }
            // now  update the market_allocations_request table withthe allocated market_site_id
            $mar['allocated_market_site_id'] = $d['market_site_id'];
            DBX::update('market_allocation_requests', $mar, ' where market_allocation_request_id=' . $mar['market_allocation_request_id']);
            DBX::abortOnError();
            // get the updated list of allocation requests and return
            $marketAllocationRequests = $this->_get_allocation_requests();
            $data['data']['allocation_requests'] = $marketAllocationRequests[$mar['market_id']];
            $stalls = $this->_get_market_stall_data();
            $data['data']['stalls'] = $stalls[$mar['market_id']];
            // if $data['data']['market_site_id'] is not set then we have an issue
            $data['status'] = 200;
            header('Content-Type: application/json');
            echo json_encode($data, JSON_FORCE_OBJECT);
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
        //TWS::pr(json_encode($this->_get_sites(),JSON_FORCE_OBJECT));
        $this->data['season'] = $this->season;
        $this->data['sites'] = json_encode($this->_get_sites(), JSON_FORCE_OBJECT);
        $this->data['market_sites'] = json_encode($this->_get_market_site_data(), JSON_FORCE_OBJECT);
        $this->data['stalls'] = json_encode($this->_get_market_stall_data(), JSON_FORCE_OBJECT);
        //$this->data['selected_market_id'] =$marketId;
        $this->data['markets'] = &$markets;
        // Stalls waiting to be allocated
        /*
        //TWS::pr($this->_get_allocation_requests(true));
         */
        $this->data['allocation_requests'] = $this->_get_allocation_requests(); // true limits to future only alloc req
        $this->data['stalltypes'] = json_encode($this->_get_stalltypes(), JSON_FORCE_OBJECT); // stalltype info
        $this->render('site_allocation');
    }
    /**
     * Returns a list of all the sites
     *
     */
    public function _get_sites()
    {
        return DBX::getRows('select * from sites order by site_reference asc', 'site_id');
    }
    public function _get_market_site_data($marketId = false)
    {
// if false get for all markets in season
        $sql = '
        SELECT MS.market_id,MS.site_id,MS.stall_id,MS.stalltype_id,MS.note,MS.`status`,sites.site_reference
        FROM market_sites MS
        JOIN stalls S on S.stall_id=MS.stall_id
        JOIN sites on sites.site_id=MS.site_id
        WHERE MS.`status`="Active"
        AND S.`status`="Active"
        AND MS.market_id='; // where status="Active"
        /*$sql2 = '    select market_site_id,market_id,site_id,stall_id,stalltype_id,`status`
        from market_sites where status !="Active" and market_id=';// where status="Active"
         */
        foreach ($this->markets as $m) {
            if ($marketId && $marketId != $m['market_id']) {
                continue;
            }
            $res = DBX::getRows($sql . $m['market_id'], 'site_id');
            DBX::abortOnError();
            //$res2 = DBX::getRows($sql2 . $m['market_id'],'site_id');
            // DBX::abortOnError();
            if (!$marketId) {
                $ms[$m['market_id']]['active'] = $res;
            } else {
                $ms = $res;
            }
            //$ms[$m['market_id']]['not_active'] =$res2;
        }
        //TWS::pr(__LINE__);
        //TWS::pr($ms);
        return $ms;
    }
    /**
     * Gets the stall and stall holedr data for the allocation page app
     * return any stall data for the current season
     *
     */
    public function _get_market_stall_data($marketId = false)
    {
        $markets = $this->markets;
        if (TWS::isIterable($markets)) {
            $sql = '    select ST.*,concat(C.lastname,",", C.firstname) as contact
            from market_sites MS
            join stalls ST on ST.stall_id=MS.stall_id
            join stallholders SH on SH.stall_id=MS.stall_id
            join contacts C on C.contact_id=SH.contact_id
            where MS.`status`="Active" and market_id=';
            foreach ($markets as $m) {
                if ($marketId && $marketId != $m['market_id']) {
                    continue; // skip if single market mode and no match
                }
                $res = DBX::getRows($sql . $m['market_id'], 'stall_id');
                DBX::abortOnError();
                // single and double quotes screw up the jso_encode js variables
                if (TWS::isIterable($res)) {
                    foreach ($res as &$r) {
                        $r['name'] = htmlspecialchars($r['name'], ENT_QUOTES);
                        $r['description'] = htmlspecialchars($r['description'], ENT_QUOTES);
                        $r['contact'] = htmlspecialchars($r['contact'], ENT_QUOTES);
                        if ($marketId) {
                            $r['stalltype'] = '';
                            $sql = ' select DISTINCT stall_id from market_sites
                            where stall_id=' . $r['stall_id'] . '
                            and `status`="Active"
                            AND stalltype_id IN(1,3,5)
                            AND market_id IN(' . implode(',', array_keys($this->markets)) . ')';
                            $rs = DBX::getRow($sql);
                            DBX::abortOnError();
                            if ($rs['stall_id'] == $r['stall_id']) {
                                $r['stalltype'] .= 'P';
                            }
                            $sql = ' select DISTINCT stall_id from market_allocation_requests
                            where stall_id=' . $r['stall_id'] . '
                            AND market_id IN(' . implode(',', array_keys($this->markets)) . ')';
                            $rs = DBX::getRow($sql);
                            DBX::abortOnError();
                            if ($rs['stall_id'] == $r['stall_id']) {
                                $r['stalltype'] .= 'C';
                            }
                        }
                    }
                }
                if (!$marketId) {
                    $ms[$m['market_id']] = $res;
                } else {
                    $ms = $res;
                }
            }
        }
        //TWS::pr($ms);
        return $ms;
    }
    /**
     * Get a list of future bookings for a stall
     */
    public function _get_future_stall_bookings($stallId, $siteId, $marketId)
    {
        $res = array('permanent' => array(), 'casual' => array()); // init return array
        if (TWS::isIterable($this->markets)) {
            foreach ($this->markets as $m) {
                if ($m['market_id'] <= $marketId) {
                    continue;
                }
                // Permanents determined from MS
                $sql = '    SELECT `status` from market_sites
                WHERE market_id=' . $m['market_id'] . '
                AND stall_id=' . $stallId . '
                AND site_id=' . $siteId . '
                AND stalltype_id IN(1,3,5)';
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                if ($rs) {
                    $res['permanent'][$m['market_id']] = $rs['status'];
                }
                // Casual requests - check MARs table
                $sql = ' SELECT allocated_market_site_id from market_allocation_requests
                WHERE market_id=' . $m['market_id'] . '
                AND stall_id=' . $stallId;
                $rs = DBX::getRow($sql);
                DBX::abortOnError();
                if ($rs) {
                    $res['casual'][$m['market_id']] = $rs['allocated_market_site_id'];
                }
            }
        }
        //TWS::pr($res);
        return $res;
    }
    /**
     * Gets a list of stalls wanting a site allocation
     * for the selected season of markets
     *
     * @param mixed $marketId
     */
    public function _get_allocation_requests()
    {
        if (0) {
            $markets = $this->pastAndFutureMarkets['future'];
        } else {
            $markets = $this->markets;
        }
        $res = array();
        if (TWS::isIterable($markets)) {
            foreach ($markets as $m) {
                $sql = '
                SELECT ST.*,MAR.is_freebee as freebee,MAR.market_allocation_request_id
                from stalls ST
                JOIN market_allocation_requests MAR on MAR.stall_id=ST.stall_id
                where MAR.market_id=' . $m['market_id'] . ' AND allocated_market_site_id=0 ';
                $rs = DBX::getRows($sql, 'market_allocation_request_id');
                DBX::abortOnError();
                if (TWS::isIterable($rs)) {
                    foreach ($rs as $marId => &$r) {
                        $r['name'] = htmlspecialchars($r['name'], ENT_QUOTES);
                        $r['description'] = htmlspecialchars($r['description'], ENT_QUOTES);
                        //$r['contact']= htmlspecialchars($r['contact'],ENT_QUOTES);
                    }
                }
                $res[$m['market_id']] = $rs;
            }
        }
        return $res;
    }
    public function _complete_allocation_request($marketId, $stallId, $marketSiteId)
    {
        $res = DBX::getRow('select * from market_allocation_requests where market_id=' . $marketId . ' and stall_id=' . $stallId);
        DBX::abortOnError();
        if ($res) {
            $res['allocated_market_site_id'] = $marketSiteId;
            DBX::update('market_allocation_requests', $res, ' where market_allocation_request_id=' . $res['market_allocation_request_id']);
            DBX::abortOnError();
        }
    }
    public function stall_update()
    {
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
        foreach ($this->markets as $m) {
            if (!isset($_GET['m' . $m['market_id']])) {
                continue;
            }
            $status = $_GET['m' . $m['market_id']];
            $stallId = $_GET['stall_id'];
            $siteId = $_GET['site_id'];
            // get current state IMPORTANT - there is likely No record for Not required marketss !!
            $res = DBX::getRow('select * from market_sites where market_id=' . $m['market_id'] . ' and stall_id=' . $stallId);
            if (!$res) {
                // ms was  previously not required
                // if it is still not required we can do nothing
                if ($status == 'Not required') {
                    continue; // skip to next market - nothing to do
                } else {
                    // create a Not required record because we need and then  let the logic below work out what to do
                    $stall = $this->_get_stall_info($stallId);
                    $data = array(
                        'market_id' => $m['market_id'],
                        'site_id' => $siteId,
                        'stall_id' => $stallId,
                        'stalltype_id' => $stall['stalltype_id'],
                        'status' => 'Not required',
                    );
                    $result = DBX::insert('market_sites', $data);
                    DBX::abortOnError();
                    // now get the record back to use below.
                    $res = DBX::getRow('select * from market_sites where market_id=' . $m['market_id'] . ' and stall_id=' . $stallId);
                }
            }
            DBX::query('update market_sites set `status`="' . $status . '" where market_id=' . $m['market_id'] . ' and stall_id=' . $stallId);
            $newData = DBX::getRow('select * from market_sites where market_id=' . $m['market_id'] . ' and stall_id=' . $stallId);
            if ($res['status'] == 'Active') {
                // booking used to be Active
                switch ($status) {
                    // status is what it is being updated to
                    case 'Not required':
                        // issue credit
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation = 'site Not required credit');
                        // remove request from market_allocation_request
                        DBX::query('DELETE from market_allocation_requests where stall_id=' . $res['stall_id'] . ' and market_id=' . $res['market_id']);
                        DBX::abortOnError();
                        // remove the MS
                        DBX::query('DELETE from market_sites where stall_id=' . $res['stall_id'] . ' and market_id=' . $res['market_id']);
                        DBX::abortOnError();
                        break;
                    case 'Cancelled':
                        // issue partial credit
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation = 'site cancellation credit');
                        break;
                }
            }
            if ($res['status'] == 'Not required') {
                // booking used to be Not required
                switch ($status) {
                    // status is what it is being updated to
                    case 'Active':
                        // charge for site
                        $this->_charge_for_market_site_allocation($res['market_site_id'], $notation = 'std site charge, going from Not required to Active');
                        break;
                    case 'Cancelled':
                        // charge for site and issue partial credit ????
                        $this->_charge_for_market_site_allocation($res['market_site_id'], $notation = 'std site charge, going from Not required to Active so we can cancel - Cancellation credit to follow');
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation = 'site cancellation credit');
                        break;
                }
            }
            if ($res['status'] == 'No show' || $res['status'] == 'Cancelled late') {
                // booking used to be Not required
                switch ($status) {
                    // status is what it is being updated to
                    case 'Active':
                        // no charge required as already charged
                        break;
                    case 'Cancelled':
                        // issue the cancellation credit  ????
                        $this->_issue_cancellation_credit_for_market_site($res['market_site_id'], $notation = 'site cancellation credit');
                        break;
                }
            }
            if (strtolower($newData['status']) == 'active') {
                $ms[$m['market_id']]['active'][$res['site_id']] = $newData;
            } else {
                $ms[$m['market_id']]['not_active'][$res['site_id']] = $newData;
            }
        }
        $data['status'] = 200;
        $data['data'] = $ms;
        header('Content-Type: application/json');
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit;
    }
    /**
     * Prints off the report that the market admin uses on the day!
     *
     */
    public function market_worksheet()
    {
        $marketId = $_GET['market_id'];
        if ($_POST) {
            if (TWS::isIterable($_POST['payment'])) {
                foreach ($_POST['payment'] as $stallId => $amount) {
                    // add the transaction
                    if ($amount && $stallId) {
                        $data = array(
                            'stall_id' => $stallId,
                            'amount' => $amount,
                            'note' => 'Market day collection',
                            'transaction_date' => date('Y-m-d H:i:s', $this->_get_nowtime()),
                            'transaction_type' => 1,
                            'season' => $this->season,
                        );
                        $tid = DBX::insert('transactions', $data);
                        DBX::abortOnError();
                    }
                }
            }
            $this->redirectAndExit();
        }
        $stalltypes = $this->_get_stalltypes();
        $stalls = $this->_get_market_stall_data($marketId);

        // TWS::pr($stalls);
        if (TWS::isIterable($stalls)) {
            foreach ($stalls as &$s) {
                // get the stall charges to see if they exceed the stall credi
                $s['charges'] = $this->_get_stall_charges($s['stall_id']);
                $paymentsArr = $this->_get_stall_payments($s['stall_id']); // list of all payment records
                if (TWS::isIterable($paymentsArr)) {
                    foreach ($paymentsArr as $p) {
                        $s['payments'] += $p['amount'];
                    }
                }
            }
        }
        // TWS::pr($stalls);
        $ms = $this->_get_market_site_data($marketId); // Market sites data
        //uasort($ms, 'sort_sites');
        // TWS::pr($ms);
        $result = array();
        foreach ($ms as $r) {
            $result[$r['site_reference']] = array(
                'site_reference' => $r['site_reference'],
                'stall_id' => $r['stall_id'],
                'name' => $stalls[$r['stall_id']]['name'],
                'contact' => $stalls[$r['stall_id']]['contact'],
                'description' => $stalls[$r['stall_id']]['description'],
                'note' => $r['note'],
                'credit' => number_format((float)$stalls[$r['stall_id']]['payments'] - (float)$stalls[$r['stall_id']]['charges'], 2),
                'stalltype' => $stalls[$r['stall_id']]['stalltype'],
                'future_bookings' => $this->_get_future_stall_bookings($r['stall_id'], $r['site_id'], $marketId),
            );
        }
        uksort($result, 'sort_sites');
        //TWS::pr($result);
        // future markets - ie an array of future market_id(s) for current season after $marketId
        $this->data['future_markets'] = $this->_get_future_market_ids($marketId);
        $this->data['result'] = &$result;
        $this->data['markets'] = $this->markets;
        $this->data['nowdate'] = $this->nowDate;
        $this->render('worksheet');
    }
    public function _get_future_market_ids($marketId)
    {
        $res = array();
        foreach ($this->markets as $m) {
            if ($marketId < $m['market_id']) {
                $res[$m['market_id']] = $m['market_id'];
            }
        }
        return $res;
    }
    public function payments($action = false)
    {
        if (strtoupper($action) == 'DELETE') {
            $transactionId = TWS::requestVar('tid', 'get');
            if ($transactionId > 0) {
                $rs = DBX::query('delete from transactions where id=' . $transactionId);
                DBX::abortOnError();
            }
        }
        if ($_POST) {
            $stallId = (int)$_POST['stall_id'];
            // if $_POST['amount'][0] THEN new payment
            $amount = (float)$_POST['amount'][0];
            if ($amount && $stallId) {
                $data = array(
                    'stall_id' => $stallId,
                    'amount' => $amount,
                    'note' => $_POST['note'][0],
                    'transaction_date' => $_POST['payment_date'][0],
                    'transaction_type' => $_POST['transaction_type'][0],
                    'season' => $this->_get_set_season(),
                );
                $tid = DBX::insert('transactions', $data);
                DBX::abortOnError();
                if (!$tid) {
                    TWS::flash('error', ' Error: - could not save payment');
                } else {
                    TWS::flash('message', ' Success: payment saved');
                }
                $this->redirectAndExit();
            }
        }
        $stallId = $_REQUEST['stall_id'];
        if (!$stallId) {
            TWS::flash('error', 'Invalid StallID ');
            $url = $this->baseUrl . '?ag=stalls';
            $this->redirectAndExit($url);
        }
        $this->data['result'] = $this->_get_stall_payments($stallId);
        $this->render('payments');
    }
    /**
     * reporting on income and expenses etc
     *
     */
    public function finances()
    {
        if ($_POST) {
            if ($reportStartDate = TWS::requestVar('report_start_date')) {
                TWS::sessionSet('report_start_date', $reportStartDate);
            }
            if ($reportEndDate = TWS::requestVar('report_end_date')) {
                TWS::sessionSet('report_end_date', $reportEndDate);
            }
            //  Filter checkboxes
            if ($showPayments = TWS::requestVar('show_payments')) {
                TWS::sessionSet('show_payments', $showPayments);
            } else {
                TWS::sessionSet('show_payments', false); // unset session var
            }
            if ($showCharges = TWS::requestVar('show_charges')) {
                TWS::sessionSet('show_charges', $showCharges);
            } else {
                TWS::sessionSet('show_charges', false); // unset session var
            }
            if ($showCredits = TWS::requestVar('show_credits')) {
                TWS::sessionSet('show_credits', $showCredits);
            } else {
                TWS::sessionSet('show_credits', false); // unset session var
            }
            $this->redirectAndExit();
        }
        $sql = '    select T.*,S.name as stallname,sites.site_reference,MS.market_id from transactions T
        JOIN stalls S on S.stall_id=T.stall_id
        LEFT JOIN market_sites MS on MS.market_site_id=T.market_site_id
        LEFT JOIN sites on sites.site_id=MS.site_id
        Where 1 ';
        // add start date condition if we have one
        if ($reportStartDate = TWS::sessionGet('report_start_date')) {
            $sql .= ' AND DATE_FORMAT(`transaction_date`,"%Y-%m-%d") >="' . $reportStartDate . '" ';
        }
        // add end date condition if we have one
        if ($reportEndDate = TWS::sessionGet('report_end_date')) {
            $sql .= ' AND DATE_FORMAT(`transaction_date`,"%Y-%m-%d") <="' . $reportEndDate . '" ';
        }
        // Transaction type filters
        $filters = array();
        if ($showPayments = TWS::sessionGet('show_payments')) {
            $filters[] = 1;
        }
        if ($showCharges = TWS::sessionGet('show_charges')) {
            $filters[] = -2;
        }
        if ($showCredits = TWS::sessionGet('show_credits')) {
            $filters[] = -1;
        }
        if (count($filters) > 0) {
            $sql .= ' AND transaction_type IN (' . implode(',', $filters) . ')';
        }
        //TWS::pr($sql);
        $this->data['showPayments'] = $showPayments;
        $this->data['showCharges'] = $showCharges;
        $this->data['showCredits'] = $showCredits;
        $this->data['reportStartDate'] = $reportStartDate;
        $this->data['reportEndDate'] = $reportEndDate;
        $this->data['result'] = DBX::getRows($sql);
        DBX::abortOnError();
        $this->render('finances');
    }
    public function collections()
    {

        if ($_POST) {
            if (strtolower($_POST['b']) == 'transfer previous season credits') {
                $this->transferPreviousSeasonCredits();
            }
            if ($reportStartDate = TWS::requestVar('report_start_date')) {
                TWS::sessionSet('report_start_date', $reportStartDate);
            }
            if ($reportEndDate = TWS::requestVar('report_end_date')) {
                TWS::sessionSet('report_end_date', $reportEndDate);
            }
            //  Filter checkboxes
            if ($showPayments = TWS::requestVar('show_payments')) {
                TWS::sessionSet('show_payments', $showPayments);
            } else {
                TWS::sessionSet('show_payments', false); // unset session var
            }
            if ($showCharges = TWS::requestVar('show_charges')) {
                TWS::sessionSet('show_charges', $showCharges);
            } else {
                TWS::sessionSet('show_charges', false); // unset session var
            }
            if ($showCredits = TWS::requestVar('show_credits')) {
                TWS::sessionSet('show_credits', $showCredits);
            } else {
                TWS::sessionSet('show_credits', false); // unset session var
            }
            $this->redirectAndExit();
        }
        // Not a POST so must be a GET request
        $sql = '    select T.*,S.name as stallname,sites.site_reference,MS.market_id from transactions T
        JOIN stalls S on S.stall_id=T.stall_id
        LEFT JOIN market_sites MS on MS.market_site_id=T.market_site_id
        LEFT JOIN sites on sites.site_id=MS.site_id
        Where 1 ';
        $sql = '    select T.*,S.name as stallname,sites.site_reference,MS.market_id
        from transactions T
        JOIN stalls S on S.stall_id=T.stall_id
        LEFT JOIN market_sites MS on MS.market_site_id=T.market_site_id
        LEFT JOIN sites on sites.site_id=MS.site_id
        Where T.season=' . $this->season . ' ';
        // add start date condition if we have one
        if ($reportStartDate = TWS::sessionGet('report_start_date')) {
            $sql .= ' AND DATE_FORMAT(`transaction_date`,"%Y-%m-%d") >="' . $reportStartDate . '" ';
        }
        // add end date condition if we have one
        if ($reportEndDate = TWS::sessionGet('report_end_date')) {
            $sql .= ' AND DATE_FORMAT(`transaction_date`,"%Y-%m-%d") <="' . $reportEndDate . '" ';
        }
        // Transaction type filters
        $filters = array();
        if ($showPayments = TWS::sessionGet('show_payments')) {
            $filters[] = 1;
        }
        if ($showCharges = TWS::sessionGet('show_charges')) {
            $filters[] = -2;
        }
        if ($showCredits = TWS::sessionGet('show_credits')) {
            $filters[] = -1;
        }
        if (count($filters) > 0) {
            $sql .= ' AND transaction_type IN (' . implode(',', $filters) . ')';
        }
        $sql .= ' ORDER BY transaction_date asc, stallname asc ';
        //TWS::pr($sql);
        $this->data['showPayments'] = $showPayments;
        $this->data['showCharges'] = $showCharges;
        $this->data['showCredits'] = $showCredits;
        $this->data['reportStartDate'] = $reportStartDate;
        $this->data['reportEndDate'] = $reportEndDate;
        $this->data['result'] = DBX::getRows($sql);
        DBX::abortOnError();
        $this->render('collections');
    }
    public function transferPreviousSeasonCredits()
    {
        // import last years credits
        // Set system to last season and markets to last season
        $this->_set_selected_season($this->_get_current_season() - 1); // default to Last season
        $this->markets = $this->_get_season_markets($this->_get_set_season());
        // get list of all stalls 
        $sql = 'select stall_id from stalls where `status` = "Active"';
        $res = DBX::getRows($sql);
        DBX::abortOnError();
        if (TWS::isIterable($res)) {
            foreach ($res as $r) {
                $carry = $this->_get_season_credit_total($r['stall_id']) - $this->_get_stall_charges($r['stall_id']);
                if ($carry > 0) {
                    $T[$r['stall_id']]['carry'] = $carry;
                    // add a balance out transaction with a negative balance value
                    $tdata = array();
                    $tdata['stall_id'] = $r['stall_id'];
                    $tdata['market_site_id'] = -1;
                    $tdata['transaction_type'] = 1; // a charge
                    $tdata['amount'] = (float)$carry * -1;
                    $tdata['note'] = 'Carry forward to next season';
                    $tdata['season'] = $this->_get_current_season() - 1;
                    if ($tdata['amount'] < 0) {
                        $tid = DBX::insert('transactions', $tdata);
                        DBX::abortOnError();
                        // add the transaction to carry the balance the next season
                        $tdata['market_site_id'] = -2; // just a flag
                        $tdata['amount'] = $carry;
                        $tdata['transaction_type'] = -1; // a credit
                        $tdata['season'] = $this->_get_current_season();
                        $tdata['note'] = 'Carry forward from last season';
                        $tid = DBX::insert('transactions', $tdata);
                        DBX::abortOnError();
                    }
                }
            }
        }
        // Tidy up - reset current season and markets
        $this->_set_selected_season($this->_get_current_season());
        $this->markets = $this->_get_season_markets($this->_get_set_season());
    }
    /**
     * AJAX/XHR GATEWAY
     */
    public function gateway()
    {
        if ($_POST) {
            switch ($_POST['action']) {
                case 'update_booking':
                    // check if the stall has multiple bookings and update all withthe same status BUT not NOTE
                    $ms = DBX::getRow('SELECT * from market_sites where market_site_id=' . (int)$_POST['market_site_id']);
                    $stallId = $ms['stall_id'];
                    $marketId = $ms['market_id'];
                    $stallTypeId = $ms['stalltype_id'];
                    // if permanent then update status for all stall sites at this market
                    // if casual then update status individually - just because we can!
                    if (in_array($stalltypeId, array(1, 3, 5))) {
                        // get all stallholder perm site bookings
                        $myMarketSites = DBX::getRows('SELECT market_site_id,stalltype_id from market_sites where market_id=' . $marketId . ' and stall_id=' . $stallId . ' and `status`="Active"', 'market_site_id');
                        DBX::abortOnError();
                    } else {
                        $myMarketSites[] = $ms; // use the actual casual site booking
                    }
                    if (TWS::isIterable($myMarketSites)) {
                        foreach ($myMarketSites as $m) {
                            $sql = '    UPDATE market_sites
                            SET
                            `note`=' . DBX::quote($_POST['note']) . ',
                            `status`=' . DBX::quote($_POST['status']) . '
                            where market_site_id =' . $m['market_site_id'];
                            DBX::query($sql);
                            DBX::abortOnError();
                            if (in_array($m['stalltype_id'], array(2, 4))) {
                                // its a casual so we delete the MS record
                                // if the status is cancelled, no  show
                            }
                        }
                    }
                    break;
                case 'allocate_site':
                    $marId = (int)$_POST['market_allocation_request_id'];
                    $siteId = (int)$_POST['site_id'];
                    $mar = DBX::getRow('select * from market_allocation_requests where market_allocation_request_id=' . $marId);
                    $stalltypeId = $mar['is_freebee'] > 0 ? 4 : 2;
                    $data = array(
                        'market_id' => $mar['market_id'],
                        'stall_id' => $mar['stall_id'],
                        'site_id' => $siteId,
                        'status' => 'Active',
                        'stalltype_id' => $stalltypeId,
                    );
                    $msInsertId = DBX::insert('market_sites', $data);
                    DBX::abortOnError();
                    if ($msInsertId) {
                        $sql = 'UPDATE market_allocation_requests set allocated_market_site_id=' . $msInsertId . ' where market_allocation_request_id=' . $marId;
                        DBX::query($sql);
                        DBX::abortOnError();
                    }
                    break;
                case 'move_stall':
                    $siteId = (int)$_POST['site_id'];
                    $msId = (int)$_POST['market_site_id'];
                    $sql = 'UPDATE market_sites set site_id=' . $siteId . ' where market_site_id=' . $msId;
                    DBX::query($sql);
                    break;
                case 'undo_booking':
                    // put MS back to MAR
                    DBX::query('DELETE from market_sites where market_site_id=' . (int)$_POST['market_site_id']);
                    DBX::query('UPDATE market_allocation_requests set allocated_market_site_id=0 where allocated_market_site_id=' . (int)$_POST['market_site_id']);
                    break;
            }
        }
        $sql = '
        select  S.`stall_id`,S.`name` as stallname,S.description,S.`status`, C.`firstname`,C.`lastname`
        from stalls S
        JOIN stallholders SH on SH.stall_id=S.stall_id
        JOIN contacts C ON C.contact_id=SH.contact_id
        WHERE S.status IN ("Active","Closed")
        AND SH.primary_contact > 0
        ORDER BY C.`lastname` ASC';
        $stalls = DBX::getRows($sql, 'stall_id');
        DBX::abortOnError();
        // Casual requests
        $sql = ' select MAR.market_allocation_request_id,MAR.stall_id,MAR.market_id,MAR.is_freebee,requested
        from market_allocation_requests MAR
        JOIN stalls ST ON ST.stall_id=MAR.stall_id
        WHERE allocated_market_site_id=0
        and market_id IN (' . implode(',', array_keys($this->markets)) . ')
        AND ST.status IN ("Active","Closed") ORDER BY requested ASC';
        $mars = DBX::getRows($sql);
        $casualRequests = array();
        if (TWS::isIterable($mars)) {
            foreach ($mars as $mar) {
                $casualRequests[$mar['stall_id']][$mar['market_id']][$mar['requested']] = array('market_allocation_request_id' => $mar['market_allocation_request_id'], 'is_freebe' => $mar['is_freebee']);
            }
        }
        //TWS::pr($mars);
        //TWS::pr($casualRequests);
        $sql = '
        SELECT market_site_id,market_id,stall_id,site_id,stalltype_id,`status`,market_id,note
        FROM market_sites  MS
        WHERE market_id IN (' . implode(',', array_keys($this->markets)) . ') ';
        $brs = DBX::getRows($sql, 'market_site_id');
        DBX::abortOnError();
        $bookings = array();
        $attendance = array();
        if (TWS::isIterable($brs)) {
            foreach ($brs as $b) {
                $attendance[$b['stall_id']][$b['market_id']][$b['site_id']] = $b['status'];
                if ($b['status'] == "Active") {
                    $bookings[$b['market_id']][$b['site_id']] = array(
                        'site_id' => $b['site_id'],
                        'note' => $b['note'],
                        'market_site_id' => $b['market_site_id'],
                        'stalltype_id' => $b['stalltype_id'],
                        'stall_id' => $b['stall_id'],
                        'status' => $b['status'],
                    );
                }
            }
        }
        // Casual Requests - value id is_freebee value 0 or 1 if freebee
        // $bookings[ $b['stall_id'] ][ $b['market_id'] ]['casual_requests']=$casualRequests[ $b['stall_id']  ][ $b['market_id'] ];
        $result = array();
        foreach ($stalls as &$stall) {
            $stall['attendance'] = $attendance[$stall['stall_id']];
            foreach ($this->markets as $m) {
                //$result['market_data'][$m['market_id']]['attendance'][$stall['stall_id']]=$attendance[$stall['stall_id']][$m['market_id']];
                if (isset($casualRequests[$stall['stall_id']][$m['market_id']])) {
                    $result['market_data'][$m['market_id']]['casual_requests'][$stall['stall_id']] = $casualRequests[$stall['stall_id']][$m['market_id']];
                }
                $result['market_data'][$m['market_id']]['bookings'] = $bookings[$m['market_id']];
            }
        }
        $sql = 'select * from sites ';
        $result['sites'] = DBX::getRows($sql, 'site_id');
        $sql = 'select site_id,site_reference from sites';
        $result['site_references'] = DBX::getRows($sql, 'site_reference');
        $sql = 'select * from stalltypes where season=' . $this->season;
        $result['stalltypes'] = DBX::getRows($sql, 'stalltype_id');
        $result['season'] = $this->_get_set_season();
        $result['markets'] = $this->markets;
        $result['stalls'] = $stalls;
        //TWS::pr($stalls);
        //TWS::pr($result);
        $data = array();
        $data['data'] = $result;
        $data['status'] = 200;
        header('Content-Type: application/json');
        echo json_encode($data, JSON_FORCE_OBJECT);
        exit;
    }
    /* Exports an excel spreadsheet with market data for current season */
    public function reports()
    {
        $sql = '
        SELECT ST.stall_id,ST.name as stallname, ST.description as stalldescription, ST.credit as stallcredit, C.*
        FROM contacts AS C
        JOIN stallholders AS STH ON STH.contact_id = C.contact_id
        JOIN stalls AS ST ON ST.stall_id=STH.stall_id
        WHERE
        ST.status="Active" AND
        STH.`primary_contact` > 0
        order by C.lastname asc';
        $stalls = DBX::getRows($sql, 'stall_id');
        //TWS::pr($stalls,1); exit;
        $STALLS = $stalls; // cant do foreach on stalls by reference RELIABLT!!!!
        foreach ($STALLS as $stallId => $s) {
            // Get markets attending
            $sql = 'SELECT sites.site_reference,MS.* from market_sites as MS
            JOIN sites on sites.site_id = MS.site_id
            where MS.stall_id=' .
                $s['stall_id'] . ' and MS.market_id IN (' . implode(',', array_keys($this->markets)) . ')';

            // The following line limits report to just permanents - without it casuals get includes
            //AND MS.stalltype_id IN (1,3,5)'; // 1=permanent 2=casual 3=community permanent  4=community casual 5=community permanent free
            //TWS::pr($sql); exit;
            $marketSitesAll = DBX::getRows($sql);
            // key by market_id and concatenate site_references if multiple site booked at a market
            $sites = array();
            foreach ($marketSitesAll as $m) {
                if (isset($sites[$s['stall_id']]) && !strstr($sites[$s['stall_id']]['site_reference'], $m['site_reference'])) {
                    // concat the site reference to the current one
                    $sites[$s['stall_id']]['site_reference'] .= ", " . $m['site_reference'];
                } else {
                    // First reference for market
                    $sites[$s['stall_id']] = $m;
                }
            }
            $stalls[$stallId]['sites'] = $sites;
            $marketSites = array();
            foreach ($marketSitesAll as $m) {
                $marketSites[$m['market_id']] = $m;
            }
            if (!empty($marketSites)) {
                $stalls[$stallId]['markets'] = $marketSites;
                // Get any payments received this season
                $sql = ' SELECT * from transactions where season="' . $this->season . '" and stall_id=' . $s['stall_id'];
                $transactions = DBX::getRows($sql);
                $stalls[$stallId]['transactions'] = $transactions;
                // get season_pass_issued attribute
                $sql = 'Select season_pass_issued from season_attributes where stall_id=' . $s['stall_id'] . ' and season_attributes.season=' . $this->season . ' LIMIT 1 ';
                $res = DBX::getRow($sql);
                $stalls[$stallId]['season_pass'] = $res['season_pass_issued'];
                $charges = $this->_get_stall_charges($stallId); // charges for the current season
                $payments = $this->_get_season_credit_total($stallId); // list of all payment records
                $stalls[$stallId]['estimate_owing'] = ($payments - $charges) > 0 ? ($payments - $charges) : 0;
            } else {
                unset($stalls[$s['stall_id']]);
            }
        }
        //TWS::pr($contacts) ;  exit;
        $data = '';
        $data .= "StallId" . "\t";
        $data .= "Stall Name" . "\t";
        $data .= "Firstname" . "\t";
        $data .= "Lastname" . "\t";
        $data .= "Address" . "\t";
        $data .= "City" . "\t";
        $data .= "Postcode" . "\t";
        $data .= "Phone" . "\t";
        $data .= "Mobile" . "\t";
        $data .= "Email" . "\t";
        $data .= "Sites" . "\t";
        $data .= "Description" . "\t";
        $data .= "Est Owing" . "\t";
        foreach ($this->markets as $m) {
            $data .= $m['market_date'] . "\t";
        }
        $data .= "Payments" . "\t";
        $data .= "Season pass" . "\r\n";
        //TWS::pr($stalls,1);exit;
        foreach ($stalls as $s) {
            //TWS::pr($s,1);
            $data .= $s['stall_id'];
            $data .= "\t" . $s['stallname'];
            $data .= "\t" . $s['firstname'];
            $data .= "\t" . $s['lastname'];
            $data .= "\t" . $s['address1'];
            $data .= "\t" . $s['city'];
            $data .= "\t" . $s['postcode'];
            $data .= "\t" . $s['phone'];
            $data .= "\t" . $s['mobile'];
            $data .= "\t" . $s['email'];
            if (isset($s['sites'][$s['stall_id']])) {
                $data .= "\t" . '"=""' . $s['sites'][$s['stall_id']]['site_reference'] . '"""';
            } else {
                $data .= "\t -";
            }
            $str = $s['stalldescription'];
            $str = preg_replace("/\t/", "\\t", $str);
            $str = preg_replace("/\r?\n/", "\\n", $str);
            $data .= "\t" . $str;
            $data .= "\t" . $s['estimate_owing'];
            foreach ($this->markets as $m) {
                if (isset($s['markets'][$m['market_id']])) {
                    $data .= "\t" . "A";
                } else {
                    $data .= "\t" . "-";
                }
            }
            $payments = 0;
            if (isset($s['transactions'])) {
                foreach ($s['transactions'] as $t) {
                    if ($t['transaction_type'] == 1) {
                        $payments += $t['amount'];
                    }
                }
            }
            $data .= "\t" . $payments;
            $data .= "\t" . $s['season_pass'];
            // end teh row
            $data .= "\r\n";
        }
        $filename = "market_data_" . $this->season . '_' . date('Ymd') . ".xls";
        header("Content-Disposition: attachment; filename=\"$filename\"");
        header("Content-Type: application/vnd.ms-excel");
        echo $data;
        exit;
    }
    /**
     * Show list of stallholders that need to be shown to their stall
     */
    public function market_show_location_list()
    {
        $marketId = $_GET['market_id'];
        $sql = '
            SELECT
            C.`firstname`,
            C.`lastname`,
            S.`name`,
            sites.`site_reference`
            FROM show_location_list SLL
            JOIN market_sites MS ON MS.market_id=SLL.market_id AND MS.stall_id=SLL.stall_id
            JOIN stalls S ON S.stall_id=MS.stall_id
            JOIN stallholders SH ON SH.stall_id=MS.stall_id
            JOIN contacts C ON C.contact_id = SH.contact_id
            JOIN sites ON sites.site_id=MS.site_id
            WHERE
            MS.`status` = "Active"
            AND SLL.market_id=' . $marketId . '
            AND SH.`primary_contact`> 0
            ORDER BY C.lastname asc';
        $this->data['result'] = DBX::getRows($sql);
        $this->render('show_location_list');
    }
    /**
     * GATEWAY FUNCTIONS
     */
    public function markets_view()
    {
        $this->render('markets_view');
    }
}
