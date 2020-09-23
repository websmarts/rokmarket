<?php
/**
$qx = TWS::requestVar(qx,'get','');

if($qx == "dbx"){
$sql = 'select stall_id, `name` from stalls';
$stalls = DBX::getRows($sql);
if(TWS::isIterable($stalls)){
foreach ($stalls as $s){
$newname =   ucwords(strtolower($s['name']));
echo $newname .'<br>';
DBX::query('update stalls set `name`="'.$newname.'" where stall_id='.$s[
'stall_id']);
}
}
exit;
}
 */

if ($_POST && !$_POST['b']) {
    die('posting access denied');
}

$o = '';
$pStalls = array();
$cStalls = array();
$months = array(
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

$missMonths = array(6, 7, 8);

$nowMonth = strtolower(strftime('%m')) + 0;
$nowYear = strftime('%Y');
$found = false;
$nextMarket = '';
$time = time();
// echo 'Time: ' . $time . '<br>';
for ($y = $nowYear; $y <= ($nowYear + 1); $y++) {

    for ($n = 1; $n <= 12; $n++) {

        if (in_array($n, $missMonths)) {
            continue;
        }

        TWS::pr($n, 0);

        $date = strtotime('+1 week sat ' . $months[$n] . ' ' . $y); // Market Days

        // Note =1 was updated to +2 on 12/10/2017 because the +1 figure produced a sun BEFORE the saturday !!!
        //$changeDate = strtotime('+2 week sun ' . $months[$n] . ' ' . $y); // Sunday after market day
        $changeDate = $date + 86400; // 86400 = seconds in a day

        // TWS::pr($date);
        // TWS::pr($changeDate);
        if ($time > $changeDate) {
            // // market has passed
            // echo 'cont -- M Has been= ' . strftime('%A  %d %B  %Y ', $date); # 02/28/10
            // echo '<br>Change date= ' . strftime('%A  %d %B  %Y ', $changeDate); # 02/28/10
            // echo '<br>Change month-year=' . $months[$n] . '-' . $y . '<br>';
            continue;
        } else {
            // echo 'N.M = ' . strftime('%A  %d %B  %Y ', $date); # 02/28/10
            $marketDate = strftime('%Y-%m-%d', $date);
            break (2);
        }

    }

}

// code above just gets next market date
$marketId = 0;
$sql = 'select market_id from markets where market_date="' . $marketDate . '" limit 1';
// TWS::pr($sql);
$market = DBX::getRow($sql);

// Get casual requests - not yet allocated a site
$sql = '    select stalls.*
            FROM market_allocation_requests MAR
            JOIN stalls on stalls.stall_id=MAR.stall_id
            WHERE MAR.allocated_market_site_id=0 AND MAR.market_id=' . $market['market_id'];
if ($sq = TWS::requestVar(sq, 'post', '')) {
    $sql .= ' and stalls.description like "%' . $sq . '%" ';
}
$cStalls = DBX::getRows($sql);
//TWS::pr($sql);
//TWS::pr($cStalls,'name');

// Get all stalls that have been allocated
$sql = '    SELECT DISTINCT stalls.stall_id,stalls.name,stalls.description,sites.site_reference from market_sites MS
            join stalls on MS.stall_id = stalls.stall_id
            join stall_sites on stalls.stall_id=stall_sites.stall_id
            join sites on sites.site_id=stall_sites.site_id
            where MS.market_id=' . $market['market_id'] . '
            and MS.stalltype_id IN (1,3,5)
            and stalls.`status`="Active" and MS.`status`="Active"';

// if search key then filter description by key
if ($sq = TWS::requestVar(sq, 'post', '')) {
    $sql .= ' and stalls.description like "%' . $sq . '%" ';
}
$sql .= '   order by stalls.name asc';
//TWS::pr($sql);
$pStalls = DBX::getRows($sql);
DBX::abortOnError();

// Added 7/12/2014 - to show casual site site refs
// Get casual sites that have been allocated
$sql = '    SELECT DISTINCT stalls.stall_id,stalls.name,stalls.description,sites.site_reference from market_sites MS
            join stalls on MS.stall_id = stalls.stall_id

            join sites on sites.site_id=MS.site_id
            where MS.market_id=' . $market['market_id'] . '
            and MS.stalltype_id IN (2,4,6)
            and stalls.`status`="Active" and MS.`status`="Active"';
if ($sq = TWS::requestVar(sq, 'post', '')) {
    $sql .= ' and stalls.description like "%' . $sq . '%" ';
}
$sql .= '   order by stalls.name asc';

$casStalls = DBX::getRows($sql);
DBX::abortOnError();

// Merge all search results
$stalls = array_merge($pStalls, $casStalls, $cStalls);

$n = count($stalls);
$suffix = $n == 1 ? '' : 's';

//$o .= '<p>For market being held on: '.strftime('%A  %d %B  %Y ', $date).'</p>';
$o .= '<p>' . $n . ' stall' . $suffix . ' are currently booked to attend the market being held on<br /> ' . strftime('%A  %d %B  %Y ', $date) . '. See stallholder details below.</p><br />';
$o .= '<form id="searchbox" method="post" action="" >';
$o .= 'Search stalls for <small>(eg plants )</small>:<input type="text" name="sq" value="' . $sq . '" >';
$o .= '<input type="submit" name="b" value="Go">';
$o .= '</form><br /><br />';

$done = array();
$url = $modx->makeUrl(48);
if (TWS::isIterable($stalls)) {
    foreach ($stalls as $s) {
        $name = trim($s['name']);
        if (!empty($name) && !$done[$name]) {
            $done[$name] = 1;
            $o .= '<div class="stallholder">' . "\n";
            $o .= '<h3>' . ucwords(strtolower($s['name'])) . '</h3>' . "\n";

            // Casuals may not have a site ref yet
            $siteRef = !empty($s['site_reference']) ? $s['site_reference'] : 'TBA';
            $o .= '<p class="sitenum" ><a href="' . $url . '" title="view maket layout map">Site # ' . $siteRef . '</a></p>';

            // highlight search term if one

            if ($sq = TWS::requestVar(sq, 'post', '')) {
                $desc = preg_replace('/' . $sq . '/i', '<strong>' . $sq . '</strong>', $s['description']);
            } else {
                $desc = $s['description'];
            }
            $o .= '<p class="desc">' . $desc . '</p>' . "\n";

            $o .= '</div>' . "\n";
        }

    }

} else {
    $o .= '<p>No stalls found for <b>' . $sq . '</b></p>';
}
echo $o;
