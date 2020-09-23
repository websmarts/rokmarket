<?php
//$o.=TWS::pr($this->data['allocation_requests'],false);

//$o.=TWS::pr($this->data['selected_market_id'],false);


TWS::pr($this->data['permanent_sites']);


$o .= '<script type="text/javascript">'."\n";
$o .= 'var url ="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier).'";'."\n";
$o .= 'var markets='.json_encode($this->data['markets'],JSON_FORCE_OBJECT).';'."\n";
$o .= 'var season='.$this->data['season'].';'."\n";
//$o .= 'var selected_market_id='.$this->data['selected_market_id'].';'."\n";
$o .= 'var stalltypes='.$this->data['stalltypes'].';'."\n";

$o .= 'var market_sites='.$this->data['market_sites'].';'."\n";
$o .= 'var sites='.$this->data['sites'].';'."\n";
$o .= 'var stalls='.$this->data['stalls'].';'."\n";
$o .= 'var allocation_requests='.json_encode($this->data['allocation_requests'],JSON_FORCE_OBJECT).';'."\n";
$o .= '</script>'."\n";
ob_start();
?>
<style>
    .clearboth { clear: both;}



    #sitelayout {position:relative;margin-left:5px;height:360px; width:660px;border:1px solid #999;background:#fff;z-index:10}
    #sitelayout div { position:absolute; border:1px solid #999;text-align:center;font-size:9px;}
    .site{background:#eee}
    #sitelayout div.site.selected{border:1px solid #000;}
    .site.permanent { background: #ffcc20}
    .site.permanent_community { background: #ffcc20}
    .site.permanent_community_free { background: #ffcc20}
    
    .site.casual { background: #88ff88}
    .site.casual_community { background: #4444ff;color:#fff}



    #market_selector_tabs{clear: both; height:40px; background: #0078bf;margin-bottom: 10px;}
    #market_selector_tabs h3 { float: left; margin: 5px;color:#ddd}
    #market_selector_tabs a {color: #fff;}
    #market_selector_tabs a:hover {color: #333;}
    #market_selector_tabs li.active a{color: #333;background:#ccf;}


    #whiteboard{
        position:absolute;
        left: 40px;
        top:3px;
        width:200px;
        min-height: 100px;
        padding:5px; 
        border:0px solid #888;
        background:#fff; 
        color: #333;
        border-radius: 4px;
    }
    #whiteboard span { font-size:16px;}

    .alloc_cell { clear:both; margin-bottom:5px;overflow: hidden;font-size:11px;cursor:crosshair}
    .draggable { width: 20px; height: 20px;  background: #00bf66; float: left; margin-right: 4px;text-align:center;color:#fff;}
    .draggable_site {cursor:crosshair}
    #draggableHelper { width: 30px; height: 30px; background: #00bf66; z-index:1000;cursor:crosshair}
    #list h3 {font-size:14px;font-weight: normal;background:#555; padding:10px; color:#fff;margin-top:0;}

</style>
<div id="market_selector_tabs">
    <h3><?php echo $this->data['season']?></h3>
    <ul class="nav nav-tabs">
    </ul>

</div>

<div id="list" style="float: right;margin-bottom:30px;width:200px;">

    <h3 id="reallocate_target">Casual site requests</h3>
    <div id="allocation_request_list"></div>
</div>
<p style="float:right;clear:right;"><a  id="worksheet_link" class="noprint btn btn-warning" >Market runsheet</a></p>

<div id="sitelayout"><div id="whiteboard"></div>

</div>

<div id="dialog-form" title="Update Stall">

<form id="siteUpdateForm">
<fieldset>
<div id="stall_name">Stall name</div>
<label for="name">Stall status</label>
<select name="stall_status" id="stall_status" class=" ui-widget-content ui-corner-all">
<option value="Active">Active</option>
<option value="No show">No show</option>
</select>
<p>&nbsp;</p>
<p id="stalltype"></p>
<label for="name">Payment</label>
<input type="text" name="payment" id="stall_payment" class="text ui-widget-content ui-corner-all">
</fieldset>
</form>
</div>


<script type="text/javascript"  src="<?php echo MODX_BASE_URL . TWSAPP_DIR .'views/admin/js/site_allocation_view.js';?>"> </script>
<!--<script type="text/javascript"  src="<?php echo MODX_BASE_URL . 'assets/snippets/market/views/admin/js/jquery.ui.touch-punch.min.js';?>"> </script>-->

<?php
$o .= ob_get_contents();
ob_end_clean();
?>