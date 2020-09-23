<?php
$o .= '<script type="text/javascript">'."\n";
$o .= 'var url ="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier).'";'."\n";
$o .= '</script>'."\n";
ob_start();
?>
<style>
    .clearboth { clear: both;}
    .ui-draggable-helper {
        z-index:1500;
        border:1px solid red;
    }

    #l_container{ float:left;width :968px; overflow:hidden; background:#eee;z-index:1000;}
    #sitelayout {position:relative;height:360px; width:660px;border:1px solid #999;background:#fff;z-index:0}
    #sitelayout div { position:absolute; border:1px solid #999;text-align:center;font-size:9px;}
    .site{background:#eee}
    #sitelayout div.site.selected{border:1px solid #000;}
    .site.permanent { background: #ffcc20}
    .site.permanent_community { background: #ffcc20}
    .site.permanent_community_free { background: #ffcc20}
    
    .site.casual { background: #88ff88}
    .site.casual_community { background: #88ff88 }
    
    

    #market_tabs{clear: both; height:40px; background: #0078bf;margin-bottom: 10px;}
    #market_tabs h3 { float: left; margin: 5px;color:#ddd}
    #market_tabs a {color: #fff;}
    #market_tabs a:hover {color: #333;}
    #market_tabs li.active a{color: #333;background:#ccf;}
    
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
    #whiteboard p {
        font-size:16px;
        font-weight: bold;
        color:#999;
    }
    
    
    #top_panel{overflow:hidden; font-size: 13px;margin-bottom: 5px;width:662px; border:0px solid red;}
    #p1 {
        position:relative;
        float:left;
        width:246px;
        border:1px solid #ccc;
        background:#eee;
        height:115px;
        
    }
    #p2{
        position:relative;
        float:left;
        margin-left:12px;
        width:402px;
        height:115px;
        border:1px solid #ccc; 
        background:#eee;
    }
    #p2 .form {
        margin-left:254px;
        border:0px solid red;
        padding:1px;
        min-height: 111px;
        background: #eee;
    }
    #p2 .stallname{
        float: left;
        height:20px;
        border: 0px solid green;
        padding:2px;
        overflow:hidden;
        font-size:11px;
        width:248px;
    }
    #p2 .marketnote{
        float: left;
        height:55px;
        border: 0px solid green;
        padding:2px;
        overflow:hidden;
        font-size:11px;
        width:248px;
    }
    #p2 .marketnote textarea {
        width: 100%;
    }
    
    
    #p1 .note {
        float: left;
        height:75px;
        border: 0px solid green;
        padding:2px;
        overflow:hidden;
        font-size:11px;
        width:254px;
    }
    
    .attendance {
        clear: left;
        float:left;
        min-height: 36px;
        font-size:11px;
        border: 0px solid #ccc;
       /* background: #ccc;  */
        width:246px;
        text-align:center;
    }
    .attendance div {
        float:left;
        
        height:34px;
        width:27px;
       
        border: 1px solid #ccc;       
    }
    .attendance p {
         height:36px;
         padding:0;
         margin:0;
    }
    .a { background: #fff;}
    .c {background: #99f; }
    .CL {background: #f44}
    .NS {background: #333; color: #fff}
    .NR {background: #fff; }
    
    #p2 #form_payment {
        width:3em;       
    }
    #p2 #form_note {
        width:100%;       
    }
    
    /* Allocation Requests */
    #allocation_requests {
      
      float:right;
      
      width:246px;
      background:#efefef;
      border:1px solid #ccc;
      
      
    }
  
    #allocation_requests h3 {
        color:#777;
       
        margin:0;
        padding:5px;
        z-index: 10;
    }
    #allocation_request_list {
        border: 0px solid #ccc;
        
        
    }
    .alloc_cell { clear:both; margin-bottom:5px;overflow: hidden;font-size:11px;}
    .alloc_cell .item { float:left; width:160px; padding:3px;}
    .alloc_cell .draggable { width: 15px; height: 15px;  background: #00bf66; float: left; margin-right: 4px;text-align:center;color:#fff;z-index:2000;}
    .draggable_site,.draggable {cursor:crosshair;z-index:1000}
    #draggableHelper { width: 15px; height: 15px; background: #00bf66; z-index:1000;cursor:crosshair}
    
    .stallinfo {
        height:32px;
        overflow:hidden;
        font-size: 11px;
        border-bottom: 1px dashed #ccc;    
    }
    
</style>

<h3 style="float:left; width:200px;">Markets</h3>
<a id="worksheet_link" href="#" style="padding-top:10px;float:right">Market sheet</a>
<a id="location_list_link" href="#" style="padding-top:10px;float:right;margin-right:30px;">Show location list</a>
<div id="market_tabs"></div>
<div id="l_container">
 <div id="allocation_requests" class="reallocate_target">
            <h3 id="reallocate_target" >Casual site requests</h3>
            <div class="stallinfo"></div>
            <div class="attendance"></div>
            <div id="allocation_request_list"></div>
        </div>   
        <div id="top_panel">
            <div id="p1">
                <div class="note">P1 notes</div>
                <div class="attendance">Attendance</div>
            </div>
            <div id="p2">        
                <div class="stallname"></div>
                <div class="marketnote"></div>   
                <div class="attendance"></div>
                <div class="form"></div>
            </div>
        </div>

        <div id="sitelayout"><div id="whiteboard"></div></div>   
 </div>  
    
<div id="r_container">
        
    </div>



<script type="text/javascript"  src="<?php echo MODX_BASE_URL . TWSAPP_DIR .'views/admin/js/markets.app.js';?>"> </script>


<?php
$o .= ob_get_contents();
ob_end_clean();
?>