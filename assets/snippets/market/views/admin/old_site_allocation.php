<?php

//$o.=TWS::pr($this->data['selected_market_id'],false);
$o .= '<select id="marketSelector" name="market_id" >';
foreach($this->data['markets'] as $m){
    $selected = $m['market_id'] == $this->data['selected_market_id'] ? ' selected ': '';
    $o .='<option '.$selected . ' value="'.$m['market_id'].'">'.$m['market_date'].'</option>';  
}
$o .= ' </select>';

//TWS::pr($this->data['permanent_sites']);
$o .= '<script type="text/javascript">';
$o .= 'var psites='.$this->data['permanent_sites'].';'."\n";
$o .= '</script>'."\n";
ob_start();
?>
<style>
    #container { position: relative; float: left; height: 500px; width: 840px; border:1px solid #888; padding: 10px;font-size:12pt ; font-family: arial}



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

    #block18 {position:absolute; top:221px;left:55px;  }
    #block19 {position:absolute; top:161px;left:90px;  }
    #block20 {position:absolute; top:95px;left:400px; width:50px;  }

    #block21 {position:absolute; top:221px;left:109px;  }

    #block22 {position:absolute; top:120px;left:279px;  }
    #block23 {position:absolute; top:63px;left:279px;  }
    #block24 {position:absolute; top:63px;left:339px;  }

    #siteB5 { margin-top:38px}
    #site4 {margin-left:4px;}
    #site22 {margin-bottom:23px;}


    .block{overflow:hidden}
    .block div {text-align:center; background:#ccffcc; border:1px solid #333;}
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




    .v0 { width:25px; height:38px}
    .v1 { width:30px; height:53px}

    .block div.allocated {background: #ffcc20;}
    .block div.preallocated {background: #00ff00;}
    #draggable { width: 20px; height: 20px;  background: #ffcccc; }
    #draggableHelper { width: 20px; height: 20px; background: yellow; }
</style>

<div id="container">

    <div id="block1" class="block left">
        <div id="site_B5" class="x1">B5</div>
        <div id="site_44" class="v0">44</div>
        <div id="site_45" class="v0">45</div>
        <div id="site_46" class="v1">46</div>
        <div id="site_47" class="v1">47</div> 
        <div id="site_48" class="v1">48</div>
        <div id="site_49" class="v1">49</div>
        <div id="site_50" class="v1">50</div>
        <div id="site_51" class="v1">51</div>
        <div id="site_68" class="v1">68</div>
        <div id="site_69" class="v1">69</div>
        <div id="site_70" class="v1">70</div>
        <div id="site_71" class="v1">71</div>
        <div id="site_93" class="v1">93</div>
        <div id="site_94" class="v1">94</div>
        <div id="site_95" class="v1">95</div>
    </div>

    <div id="block2" class="block" >
        <div id="site_43" class="x2">43</div>
        <div id="site_42" class="x2">42</div>
        <div id="site_41" class="x2">41</div>  
    </div>

    <div id="block3" class="block" >
        <div id="site_52" class="x2">52</div>
        <div id="site_53" class="x2">53</div>
        <div id="site_54" class="x2">54</div>  
    </div>
    <div id="block4" class="block" >
        <div id="site_67" class="x2">67</div>
        <div id="site_66" class="x2">66</div>
        <div id="site_65" class="x2">65</div>  
    </div>

    <div id="block5" class="block" >
        <div id="site_73" class="x2">73</div>
        <div id="site_74" class="x2">74</div>
        <div id="site_75" class="x2">75</div>  
    </div>



    <div id="block6" class="block" >
        <div id="site_92" class="x2">92</div>
        <div id="site_91" class="x2">91</div>
        <div id="site_90" class="x2">90</div>
        <div id="site_89" class="x2">89</div>
        <div id="site_88" class="x2">88</div>
        <div id="site_87" class="x2">87</div>
        <div id="site_86" class="x2">86</div>
        <div id="site_85" class="x2">85</div>
        <div id="site_84" class="x2">84</div>
    </div>

    <div id="block7" class="block" >
        <div id="site_77" class="x2">77</div>
        <div id="site_78" class="x2">78</div>
        <div id="site_79" class="x2">79</div>
        <div id="site_80" class="x2">80</div>    
    </div>

    <div id="block8" class="block" >
        <div id="site_64" class="x2">64</div>
        <div id="site_63" class="x2">63</div>
        <div id="site_62" class="x2">62</div>
        <div id="site_61" class="x2">61</div>
        <div id="site_60" class="x2">60</div>     
    </div>

    <div id="block9" class="block" >
        <div id="site_55" class="x2">55</div>
        <div id="site_56" class="x2">56</div>
        <div id="site_57" class="x2">57</div>
        <div id="site_58" class="x2">58</div>
        <div id="site_59" class="x2">59</div>     
    </div>

    <div id="block10" class="block left" >
        <div id="site_24" class="x3">24</div>
        <div id="site_40" class="x4">40</div>     
    </div>

    <div id="block11" class="block" >
        <div id="site_39" class="x2">39</div>
        <div id="site_38" class="x2">38</div>
        <div id="site_37" class="x2">37</div>
        <div id="site_36" class="x2">36</div>
        <div id="site_35" class="x2">35</div>     
    </div>

    <div id="block12" class="block" >
        <div id="site_25" class="x2">25</div>
        <div id="site_26" class="x2">26</div>
        <div id="site_27" class="x2">27</div>
        <div id="site_28" class="x2">28</div>
        <div id="site_29" class="x2">29</div>     
    </div>

    <div id="block13" class="block" >
        <div id="site_21" class="x2">21</div>
        <div id="site_20" class="x2">20</div>
        <div id="site_19" class="x2">19</div>
        <div id="site_18" class="x2">18</div>
        <div id="site_17" class="x2">17</div>
        <div id="site_16" class="x2">16</div>     
    </div>

    <div id="block14" class="block" >
        <div id="site_6" class="x2">6</div>
        <div id="site_7" class="x2">7</div>
        <div id="site_8" class="x2">8</div>
        <div id="site_9" class="x2">9</div>
        <div id="site_10" class="x2">10</div>
        <div id="site_11" class="x2">11</div>     
    </div>

    <div id="block15" class="block left" >
        <div id="site_12" class="x5">12</div>
        <div id="site_13" class="x5">13</div>
        <div id="site_14" class="x5">14</div>
        <div id="site_15" class="x5">15</div>     
    </div>

    <div id="block16" class="block left" >
        <div id="site_30" class="x6">30</div>
        <div id="site_31" class="x6">31</div>
        <div id="site_32" class="x6">32</div>       
    </div>

    <div id="block33" class="block " >
        <div id="site_33" class="x7">33</div>    
    </div>

    <div id="block34" class="block " >
        <div id="site_34" class="x8">34</div>    
    </div>

    <div id="block17" class="block left" >
        <div id="site_81" class="x9">81</div>
        <div id="site_82" class="x9">82</div>  
    </div>

    <div id="block18" class="block" >
        <div id="site_H24" class="x7">H24</div>
    </div>

    <div id="block19" class="block left" >
        <div id="site_1" class="x2">1</div>
        <div id="site_2" class="x2">2</div>
        <div id="site_3" class="x2">3</div>
        <div id="site_4" class="x11">4</div> 
        <div id="site_5" class="x11">5</div>        
    </div>

    <div id="block20" class="block right" >
        <div id="site_22" class="x12">22</div>
        <div id="site_23" class="x2">23</div>  
    </div>

    <div id="block21" class="block left" >
        <div id="site_H23" class="x10">H23</div>
        <div id="site_H22" class="x10">H22</div>  
    </div>


    <div id="block22" class="block" >
        <div id="site_B2" class="x13">B2</div>
    </div>

    <div id="block23" class="block" >
        <div id="site_B4" class="x13">B4</div>
    </div>

    <div id="block24" class="block" >
        <div id="site_B1" class="x13">B1</div>
    </div>

</div><!-- end container -->

<div id="list" style="float: right;">
    <h3>Sites to be allocated</h3>
    <div id="draggable" class="ui-widget-content" stall="345" >X</div>
</div>
<script type="text/javascript"> 

    $(document).ready(function() { 
        
        // change market date
        $('#marketSelector').on('change', function(){
            reload(this.value);
        });
        
        
        function reload(market_id){
            document.location='http://localhost/rokeby_market/market-admin?ag=site_allocation&market_id='+market_id;
        }

        $.each(psites, function(index,value){
            //console.log(value);
            divid = '#site_'+value['site_reference'];
            $(divid).addClass('allocated')
        })
        //$('#R93').addClass('allocated');

        // Draggable
        $( "#draggable" ).draggable({
            //containment: '#container',
            cursor: 'move',
            helper: myHelper

        });
        function myHelper( event ) {
            return '<div id="draggableHelper">X</div>';
        }

        // Droppable
        $('div.block div:not(".allocated, .preallocated")').droppable({
            drop: handleDropEvent
        });
        function handleDropEvent( event, ui ) {
            var draggable = ui.draggable;
            if( $(this).hasClass('allocated')){
                // do nothing
            } else {
                $(this).addClass('allocated');
                console.log($(this).attr('id') + ' has been allocated to stall ' + draggable.attr('stall')  );
            }

            
        }



    }); 

</script>

<?php
$o .= ob_get_contents();
ob_end_clean();
?>