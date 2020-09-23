<?php
//$o.=TWS::pr($this->data['allocation_requests'],false);

//$o.=TWS::pr($this->data['selected_market_id'],false);


//TWS::pr($this->data['permanent_sites']);
$o .= '<script type="text/javascript">'."\n";
$o .= 'var url ="'.TWS::modx()->makeUrl(TWS::modx()->documentIdentifier).'";'."\n";
$o .= 'var markets='.json_encode($this->data['markets'],JSON_FORCE_OBJECT).';'."\n";
$o .= 'var season='.$this->data['season'].';'."\n";
//$o .= 'var selected_market_id='.$this->data['selected_market_id'].';'."\n";

$o .= 'var market_sites='.$this->data['market_sites'].';'."\n";
$o .= 'var sites='.$this->data['sites'].';'."\n";
$o .= 'var stalls='.$this->data['stalls'].';'."\n";
$o .= 'var allocation_requests='.json_encode($this->data['allocation_requests'],JSON_FORCE_OBJECT).';'."\n";
$o .= '</script>'."\n";
ob_start();
?>
<style>
    .clearboth { clear: both;}

 




    #container1 { position: relative; float: left; height: 500px; width: 47px; border:1px solid #888; padding: 10px;font-size:10pt ; font-family: arial}

    #container2 { position: relative; float: left; height: 500px; width: 830px; border:1px solid #888; padding: 10px;font-size:10pt ; font-family: arial}



    #block1 {position:absolute; overflow:hidden;top:1px;left:325px;}

    #block2 {position:absolute; top:95px;left:450px;width:49px;  }
    #block3 {position:absolute; top:95px;left:549px;width:49px;  }
    #block4 {position:absolute; top:95px;left:598px;width:49px;  }
    #block5 {position:absolute; top:95px;left:695px;width:49px;  }
    #block6 {position:absolute; top:95px;left:770px;width:49px;  }

    #block7 {position:absolute; top:218px;left:695px;width:49px;  }
    #block8 {position:absolute; top:218px;left:598px;width:49px;  }
    #block9 {position:absolute; top:218px;left:549px;width:49px;  }
    #block10 {position:absolute; top:221px;left:402px;width:98px;  }
    #block11 {position:absolute; top:250px;left:450px;width:49px;  }
    #block12 {position:absolute; top:250px;left:401px;width:49px;  }
    #block13 {position:absolute; top:230px;left:285px;width:49px;  }
    #block14 {position:absolute; top:230px;left:236px;width:49px;  }



    #block15 {position:absolute; top:436px;left:200px;  }
    #block16 {position:absolute; top:436px;left:412px;  }


    #block33 {position:absolute; top:431px;left:526px;  }
    #block34 {position:absolute; top:414px;left:578px;  }
    #block17 {position:absolute; top:414px;left:655px;  }

    #block18 {position:absolute; top:221px;left:25px;  }
    #block19 {position:absolute; top:161px;left:90px;  }
    #block20 {position:absolute; top:95px;left:400px; width:50px;  }

    #block21 {position:absolute; top:221px;left:76px;  }

    #block22 {position:absolute; top:120px;left:279px;  }
    #block23 {position:absolute; top:63px;left:279px;  }
    #block24 {position:absolute; top:63px;left:339px;  }

    /* hall */
    #block65 {position:absolute; top:261px;left:25px;  }
    #block66 {position:absolute; top:300px;left:5px;  }
    #block67 {position:absolute; top:450px;left:15px; }
    #block68 {position:absolute; top:380px;left:125px; }
    #block69 {position:absolute; top:285px;left:125px; }
    #block70 {position:absolute; top:318px;left:65px; }
    #block71 {position:absolute; top:380px;left:155px; }
    #block72 {position:absolute; top:255px;left:155px; }
    #block73 {position:absolute; top:450px;left:105px; }


    #halloutline {position:absolute; top:253px;left:3px;  width:150px; height:225px; border:1px dotted #888;background: #eee}

    /* tennic court*/
    #block50{position:absolute; top:1px;left:1px;  }


    #siteB5 { margin-top:38px}
    #site4 {margin-left:4px;}
    #site22 {margin-bottom:23px;}


    .block{overflow:hidden}
    .block div {text-align:center; background:#ddd; border:1px solid #333;}
    .block.left div {float: left}
    .block.right div { float:right;}

    .x1 { width:44px; height:22px}
    .x2 { width:47px; height:28px}
    .x3 { width:32px; height:28px}
    .x4 { width:62px; height:28px}
    .x5 { width:44px; height:28px}
    .x6 { width:36px; height:28px}
    .x7 { width:50px; height:33px}
    .x8 { width:50px; height:50px}
    .x9 { width:53px; height:28px}
    .x10 { width:36px; height:23px}
    .x11 { width:30px; height:25px}
    .x12 { width:30px; height:35px}
    .x13 { width:35px; height:35px}

    .e1 {width:60px; height: 35px; background:#fff;font-size: small}




    .v0 { width:25px; height:38px;}
    .v1 { width:30px; height:53px}
    .v3 { width:25px; height:30px;}

    .block div.allocated {border-left: 1px solid #888;border-top: 1px solid #888}
    .block div.selected {border: 2px solid #000;}

    .block div.permanent {background: #ffcc22;}
    .block div.casual {background: #ccff22;}


    .block div.preallocated {background: #00ff00;}


    #market_selector_tabs{clear: both; height:40px; background: #0078bf;margin-bottom: 10px;}
    #market_selector_tabs h3 { float: left; margin: 5px;color:#ddd}
    #market_selector_tabs a {color: #fff;}
    #market_selector_tabs a:hover {color: #333;}
    #market_selector_tabs li.active a{color: #333;background:#ccf;}
    
    form#stall_status_form{padding:3px; border:1px solid #888}
    table#stall_status { width:100%;}
    td.na { background: #fff; text-align: center;}
    table#stall_status tfoot td {padding-top:6px;}
    table#stall_status tr.current {background:#ccf; font-weight: bold;}
    
    #stall_info{ background: #0078bf; color: #fff;padding: 3px;}
    #stall_status tbody {}
    #whiteboard{
        width:260px;
        min-height: 100px;
        padding:5px; 
        border:0px solid #888;
        background:#fff; 
        color: #333;
        border-radius: 10px;
    }
    
    .alloc_cell { clear:both; margin-bottom:5px;overflow: hidden;font-size:11px;}
    .draggable { width: 20px; height: 20px;  background: #00bf66; float: left; margin-right: 4px;text-align:center;color:#fff;}
    #draggableHelper { width: 20px; height: 20px; background: #00bf66; }

</style>
<div id="market_selector_tabs">
    <h3><?php echo $this->data['season']?></h3>
    <ul class="nav nav-tabs">
    </ul>

</div>
<div id="container1" style="clear: left;">

    <div id="block50" class="block" >
        <div id="site_TC1" class="x1 site" >TC1</div>
        <div id="site_TC2" class="x1 site">TC2</div>
        <div id="site_TC3" class="x1 site">TC3</div>
        <div id="site_TC4" class="x1 site">TC4</div>
        <div id="site_TC5" class="x1 site">TC5</div>
        <div id="site_TC6" class="x1 site">TC6</div>
        <div id="site_TC7" class="x1 site">TC7</div>
        <div id="site_TC8" class="x1 site">TC8</div>
        <div id="site_TC9" class="x1 site">TC9</div>
        <div id="site_TC10" class="x1 site">TC10</div>
        <div id="site_TC11" class="x1 site">TC11</div>
        <div id="site_TC12" class="x1 site">TC12</div>
        <div id="site_TC13" class="x1 site">TC13</div>
        <div id="site_TC14" class="x1 site">TC14</div>
        <div id="site_TC15" class="x1 site">TC15</div>
        <div id="site_TC16" class="x1 site">TC16</div>
        <div id="site_TC17" class="x1 site">TC17</div>
        <div id="site_TC18" class="x1 site">TC18</div>
        <div id="site_TC19" class="x1 site">TC19</div>
        <div id="site_TC20" class="x1 site">TC20</div>
        <div id="site_TC21" class="x1 site">TC21</div>
        <div id="site_TC22" class="x1 site">TC22</div>       
    </div>

</div>

<div id="container2">
    <div id="whiteboard"></div>
    <div id="halloutline"></div>

    <div id="block1" class="block left">
        <div id="site_B5" class="x1 site">B5</div>
        <div id="site_44" class="v0 site">44</div>
        <div id="site_45" class="v0 site">45</div>
        <div id="site_46" class="v1 site">46</div>
        <div id="site_47" class="v1 site">47</div> 
        <div id="site_48" class="v1 site">48</div>
        <div id="site_49" class="v1 site">49</div>
        <div id="site_50" class="v1 site">50</div>
        <div id="site_51" class="v1 site">51</div>
        <div id="site_68" class="v1 site">68</div>
        <div id="site_69" class="v1 site">69</div>
        <div id="site_70" class="v1 site">70</div>
        <div id="site_71" class="v1 site">71</div>
        <div id="site_93" class="v1 site">93</div>
        <div id="site_94" class="v1 site">94</div>
        <div id="site_95" class="v1 site">95</div>
    </div>

    <div id="block2" class="block" >
        <div id="site_43" class="x2 site">43</div>
        <div id="site_42" class="x2 site">42</div>
        <div id="site_41" class="x2 site">41</div>  
    </div>

    <div id="block3" class="block" >
        <div id="site_52" class="x2 site">52</div>
        <div id="site_53" class="x2 site">53</div>
        <div id="site_54" class="x2 site">54</div>  
    </div>
    <div id="block4" class="block" >
        <div id="site_67" class="x2 site">67</div>
        <div id="site_66" class="x2 site">66</div>
        <div id="site_65" class="x2 site">65</div>  
    </div>

    <div id="block5" class="block" >
        <div id="site_73" class="x2 site">73</div>
        <div id="site_74" class="x2 site">74</div>
        <div id="site_75" class="x2 site">75</div>  
    </div>



    <div id="block6" class="block" >
        <div id="site_92" class="x2 site">92</div>
        <div id="site_91" class="x2 site">91</div>
        <div id="site_90" class="x2 site">90</div>
        <div id="site_89" class="x2 site">89</div>
        <div id="site_88" class="x2 site">88</div>
        <div id="site_87" class="x2 site">87</div>
        <div id="site_86" class="x2 site">86</div>
        <div id="site_85" class="x2 site">85</div>
        <div id="site_84" class="x2 site">84</div>
    </div>

    <div id="block7" class="block" >
        <div id="site_77" class="x2 site">77</div>
        <div id="site_78" class="x2 site">78</div>
        <div id="site_79" class="x2 site">79</div>
        <div id="site_80" class="x2 site">80</div>    
    </div>

    <div id="block8" class="block" >
        <div id="site_64" class="x2 site">64</div>
        <div id="site_63" class="x2 site">63</div>
        <div id="site_62" class="x2 site">62</div>
        <div id="site_61" class="x2 site">61</div>
        <div id="site_60" class="x2 site">60</div>     
    </div>

    <div id="block9" class="block" >
        <div id="site_55" class="x2 site">55</div>
        <div id="site_56" class="x2 site">56</div>
        <div id="site_57" class="x2 site">57</div>
        <div id="site_58" class="x2 site">58</div>
        <div id="site_59" class="x2 site">59</div>     
    </div>

    <div id="block10" class="block left" >
        <div id="site_24" class="x3 site">24</div>
        <div id="site_40" class="x4 site">40</div>     
    </div>

    <div id="block11" class="block" >
        <div id="site_39" class="x2 site">39</div>
        <div id="site_38" class="x2 site">38</div>
        <div id="site_37" class="x2 site">37</div>
        <div id="site_36" class="x2 site">36</div>
        <div id="site_35" class="x2 site">35</div>     
    </div>

    <div id="block12" class="block" >
        <div id="site_25" class="x2 site">25</div>
        <div id="site_26" class="x2 site">26</div>
        <div id="site_27" class="x2 site">27</div>
        <div id="site_28" class="x2 site">28</div>
        <div id="site_29" class="x2 site">29</div>     
    </div>

    <div id="block13" class="block" >
        <div id="site_21" class="x2 site">21</div>
        <div id="site_20" class="x2 site">20</div>
        <div id="site_19" class="x2 site">19</div>
        <div id="site_18" class="x2 site">18</div>
        <div id="site_17" class="x2 site">17</div>
        <div id="site_16" class="x2 site">16</div>     
    </div>

    <div id="block14" class="block" >
        <div id="site_6" class="x2 site">6</div>
        <div id="site_7" class="x2 site">7</div>
        <div id="site_8" class="x2 site">8</div>
        <div id="site_9" class="x2 site">9</div>
        <div id="site_10" class="x2 site">10</div>
        <div id="site_11" class="x2 site">11</div>     
    </div>

    <div id="block15" class="block left" >
        <div id="site_12" class="x5 site">12</div>
        <div id="site_13" class="x5 site">13</div>
        <div id="site_14" class="x5 site">14</div>
        <div id="site_15" class="x5 site">15</div>     
    </div>

    <div id="block16" class="block left" >
        <div id="site_30" class="x6 site">30</div>
        <div id="site_31" class="x6 site">31</div>
        <div id="site_32" class="x6 site">32</div>       
    </div>

    <div id="block33" class="block " >
        <div id="site_33" class="x7 site">33</div>    
    </div>

    <div id="block34" class="block " >
        <div id="site_34" class="x8 site">34</div>    
    </div>

    <div id="block17" class="block left" >
        <div id="site_81" class="x9 site">81</div>
        <div id="site_82" class="x9 site">82</div>  
    </div>

    <div id="block18" class="block" >
        <div id="site_H24" class="x7 site">H24</div>
    </div>

    <div id="block19" class="block left" >
        <div id="site_1" class="x2 site">1</div>
        <div id="site_2" class="x2 site">2</div>
        <div id="site_3" class="x2 site">3</div>
        <div id="site_4" class="x11 site">4</div> 
        <div id="site_5" class="x11 site">5</div>        
    </div>

    <div id="block20" class="block right" >
        <div id="site_22" class="x12 site">22</div>
        <div id="site_23" class="x2 site">23</div>  
    </div>

    <div id="block21" class="block left" >
        <div id="site_H23" class="x10 site">H23</div>
        <div id="site_H22" class="x10 site">H22</div>  
    </div>


    <div id="block22" class="block" >
        <div id="site_B2" class="x13 site">B2</div>
    </div>

    <div id="block23" class="block" >
        <div id="site_B4" class="x13 site">B4</div>
    </div>

    <div id="block24" class="block" >
        <div id="site_B1" class="x13 site">B1</div>
    </div>

    <div id="block65" class="block left" >
        <div id="site_H2" class="x10 site">H2</div>
        <div id="site_H1" class="x10 site">H1</div>  
    </div>
    <div id="block66" class="block" >
        <div id="site_H3" class="v3 site">H3</div>
        <div id="site_H4" class="v3 site">H4</div>
        <div id="site_H5" class="v3 site">H5</div>
        <div id="site_H6" class="v3 site">H6</div>  
    </div>
    <div id="block67" class="block left" >
        <div id="site_H7" class="x10 site">H7</div>
    </div>
    <div id="block73" class="block left" >   
        <div id="site_H8" class="x10 site">H8</div>      
    </div>
    <div id="block68" class="block" >
        <div id="site_H10" class="v3 site">H10</div>
        <div id="site_H9" class="v3 site">H9</div>
    </div>
    <div id="block69" class="block" >
        <div id="site_H12" class="v3 site">H12</div>
        <div id="site_H11" class="v3 site">H11</div>     
    </div>
    <div id="block70" class="block" >
        <div id="site_H13" class="v3 site">H13</div>
        <div id="site_H14" class="v3 site">H14</div>
        <div id="site_H15" class="v3 site">H15</div>
    </div>
    <div id="block71" class="block" >
        <div id="site_H18" class="v3 site">H18</div>
        <div id="site_H17" class="v3 site">H17</div>
        <div id="site_H16" class="v3 site">H16</div>
    </div>
    <div id="block72" class="block" >
        <div id="site_H21" class="v3 site">H21</div>
        <div id="site_H20" class="v3 site">H20</div>
        <div id="site_H19" class="v3 site">H19</div>   
    </div>


</div><!-- end container -->
<div id="allocations" style="float:right;width:240px">
    <form id="stall_status_form">
    
        <div id="stall_info">Stall info</div>
        <table id="stall_status">
            <thead>
                <tr>
                    <th width="100">Market</th>
                    <th width="25">A</th>
                    <th width="25">NR</th>
                    <th width="25">C</th>
                    <th width="25">CL</th>
                    <th width="25">NS</th>
                </tr>
            </thead>
            <tbody>

            </tbody>
            <tfoot>
                <tr><td colspan="6" align="right">
                <button class="btn btn-danger btn-xs"  id="remove_casual_button" >Remove Casual</button>&nbsp;&nbsp;
                <button class="btn btn-success btn-xs"  id="stall_status_update_button" >Update</button>
                
                </td></tr>
            </tfoot>
        </table>
    </form>
</div><!-- end allocations div -->
<div id="list" style="float: left;margin-left: 10px;">
    <h3>Allocation requests</h3>
    <div id="allocation_request_list"></div>


</div>
<script type="text/javascript"  src="<?php echo MODX_BASE_URL . 'assets/snippets/market/views/admin/js/site_allocation_view.js';?>"> </script>


<?php
$o .= ob_get_contents();
ob_end_clean();
?>