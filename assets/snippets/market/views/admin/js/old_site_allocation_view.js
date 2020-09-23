$(document).ready(function() { 


    /*
    * MAIN APPLICATION - SITE_ALLOCATION - CODE START
    */

    //console.log(market_sites);
    // select the market based on current date
    var today= new Date();
    var current_month = today.getMonth();
    var current_year = today.getYear();

    var months = {
        0:'January',
        1:'Febuary',
        2:'March',
        3:'April',
        4:'May',
        5:'June',
        6:'July',
        7:'August',
        8:'September',
        9:'October',
        10:'November',
        11:'December'
    };

    // DOM container for market selectors
    var $tab_container = $('#market_selector_tabs ul');

    //DOM Stall status table 
    var $stall_status_form = $('#stall_status_form');
    var $stall_status_thead = $('#stall_status thead');
    var $stall_status_tbody = $('#stall_status tbody');
    var $stall_status_tfoot = $('#stall_status tfoot');
    var $stall_status_update_button = $('#update_button');
    var $stall_status_remove_casual_button = $('#remove_casual_button');
    var $whiteboard=$('#whiteboard');
    var $stall_info= $('#stall_info');

    // DATA - ID of stall currently selected
    var selected_stall_id = 0; // stall on site clicked
    var selected_stall = {}; // selected stall object
    var selected_site_id = 0;  // site clicked
    var site_reference = 0;  // site clicked




    // create the sites_by_ref array so we can lookup a site by its site_reference easily
    var sites_by_ref = [];
    $.each(sites,function(index,value){
        sites_by_ref[value['site_reference']]=value;
    });
    
    /*
    $('#sitelayout').mousemove(function(event){
        $( "#log" ).text( (event.pageX - 227) + ", " + (event.pageY - 300));
       
    });
    */
    var $sitelayout = $('#sitelayout');
    $.each(sites,function(index,site){
        
        
        var site_id= 'site_'+site.site_reference;
        $sitelayout.append('<div id="'+site_id+'">'+site['site_reference']+'</div>');
        var styles = {
            'top':parseInt(site.layout_y) ,
            'left':parseInt(site.layout_x),
            'width': parseInt(site.layout_w),
            'height':parseInt(site.layout_h),       
        }
       // console.log(site);
       // console.log(styles);
        $('#'+site_id).css(styles);
    })


    /**
    * 
    *  write out season market tabs
    * 
    */
    function createMarketTabs(){
        var select_tabmonth=8; // default to sept
        if(current_month > 4 && current_month < 8){
            // outside season so select closest season month
            if(current_year > season){
                // select last month of season
                select_tabmonth=4;//May
            } else {
                //select first month of season
                select_tabmonth=8;//Sept
            }           
        } else {
            select_tabmonth=current_month;
        }

        var li_class='';
        $.each(markets, function(index,value){
            
            //var date = new Date(value['market_date']);// is market date really an okay format to feed Date with ??? 
            //var tabmonth = date.getMonth();
            var tabmonth =  value['market_date'].substring(5,7)-1;
            //console.log(tabmonth);


            $tab_container.append('<li><a  market_id="'+index+'">'+months[tabmonth] +'</a></li>');
            if( select_tabmonth == tabmonth){              
                selected_market_id = index;


            } 
        }); 


        // Now tabs are installed so add the click handler
        $tab_container.click(function(e){
            //e.preventDefault();
            $tab_container.find('li.active').removeClass('active'); // remove active

            selected_market_id = $(e.target).closest('a').attr('market_id');

            $(e.target).closest('li').addClass('active');
            show_market();


        }); 
    };


    /**
    * SHOW THE MARKET!!!
    * 
    */
    function show_market() {

        $('.site').removeClass('allocated permanent casual selected');


        $.each(market_sites[selected_market_id]['active'], function(index,value){
            try{
                divid = '#site_'+ sites[index]['site_reference'];
                if(value['status'] == "Active"){

                    if(value['stalltype_id'] == 1 ){
                        $(divid).addClass('allocated permanent');
                    } else if (value['stalltype_id'] == 2){
                        $(divid).addClass('allocated casual');
                    } else {
                        $(divid).addClass('allocated');
                    }

                } else {
                    $(divid).removeClass('allocated permanent');
                }
            } catch(err){

            }


        });

        try {
            var site_ref = sites[selected_site_id]['site_reference'];
            //console.log('trying to trigger click on '+'#site_'+site_ref);
            $('#site_'+site_ref).click();
        } catch(e){}


        update_allocation_requests();
    }

    /**
    * ATTACH HOVER HANDLER TO SITE DIVs so we can display 
    * stall info on the whiteboard when mouse hovers over site
    * 
    */
    $('.site').hover( function() {

        site_reference = $(this).attr('id').substring(5); // remove the site_ par
        site_id = sites_by_ref[site_reference]['site_id'];         

        try { // site may not have a stall on it 
            stall_id = market_sites[selected_market_id]['active'][site_id]['stall_id'];
        } catch(e){ // no stall on site

            return;
        }
        var position = $(this).position();
        var msg = site_reference + ' ' + stalls[selected_market_id][stall_id]['contact'];
        msg += ' ' + stalls[selected_market_id][stall_id]['description'] +'<br>Top:' + position.top + ' Left:' + position.left;

        $('#whiteboard').html(  msg  );

        },function() {
            $('#whiteboard').text('Hover over site for stallholder info');
        }
    );

    function clear_stall_info(){

        $stall_status_tbody.html('');
        $stall_status_thead.hide();
        $stall_status_tfoot.hide();

        selected_stall =0;


        $stall_info.text('No Stall Information');
        $whiteboard.text('No stall has been allocated to site: '+ site_reference);
    }

    /**
    * 
    * ATTACH CLICK EEVENT TO EACH SITE DIV
    * Used to update the allocations status radio buttons panel
    *  
    */



    $('.site').on('click',function(){

        //console.log('Click function triggered');
        //populate the allocation table with current stall 
        $('.site').removeClass('selected');

        $(this).addClass('selected');// maybe put a red border


        site_reference = $(this).attr('id').substring(5); // remove the site_ part
        var site_id = sites_by_ref[site_reference]['site_id']; 
        selected_site_id=site_id;



        // get current stall on site - MAY NOT ONE
        try{
            selected_stall = market_sites[selected_market_id]['active'][site_id];
            stall_id = selected_stall['stall_id'];
            var stalltype_id = selected_stall['stalltype_id'];
        } catch(e){
            // not an avtive site
            // Clear the allocations info
            //console.log('stall info not available!!');
            clear_stall_info();

            return;
        }

        // 'stall_id='+stall_id + 'site_id=' + site_id + ' '+ 

        // put some helpful text above the radio buttons
        $('#stall_info').html(site_reference + ' ' + stalls[selected_market_id][stall_id]['contact']  );


        // Iterate through the markets to get the history for this particulat stall


        var radio_options = ['Active','Not required','Cancelled','Cancelled late','No show'];
        $stall_status_tbody.html(''); // clear radio buttons
        $stall_status_thead.show('slow');
        $stall_status_tfoot.show('slow');


        if(! is_odd(selected_stall['stalltype_id']) ){// a casual booking
            $stall_status_remove_casual_button.show();
            var casual=true;
        } else {
            $stall_status_remove_casual_button.hide();
            var casual = false;
        }


        $.each(market_sites , function(index,value){

            console.log(index);
            if ('undefined' !== ( typeof value['active'][site_id] )&& value['active'][site_id]['stall_id'] == stall_id){
                console.log('Active -line 256');
                stall_status = value['active'][site_id]['status'] ;
            } else if ('undefined' !== typeof value['not_active'][site_id]   && value['not_active'][site_id]['stall_id'] == stall_id) {
                // Not Active
                console.log('not active - line 260');
                stall_status = value['not_active'][site_id]['status'];

            } else {
                // use a non existent status so no radio button is checked
                stall_status ='skip';
            } 
            console.log(stall_status);
            // write out radio buttons
            var radio_name = 'm'+index;

            if(selected_market_id == index){
                var trclass="current";
            } else {
                var trclass='';
            }
            var row = '<tr class="' + trclass +' ">';
            row += '<td>'+markets[index]['market_date']+'</td>';
            if( 'skip' == stall_status){// dont show radio buttons
                row +='<td colspan="5" class="na">-</td>';

            } else {

                $.each(radio_options, function(idx,opt){
                    checked = stall_status == opt ? ' checked ' : '';
                    row += '<td><input type="radio" name="'+radio_name+'" value="'+opt+'" '+ checked +' ></td>';

                });
            }

            row += '</tr>';

            $stall_status_tbody.append(row);
        });
    });;






    
    $('#reallocate_target').droppable({
        drop:handleReallocateDropEvent
    });
    function handleReallocateDropEvent(event,ui){
        alert('deallocate the casual site');
    }

    /**
    * ALLOCATIONS DROPPING
    * Make all the divs contained in div.block(s), i.e. site divs, droppable
    * 
    */
    $('div.block div:not(".allocated, .preallocated")').droppable({
        drop: handleDropEvent
    });
    /**
    * Process a site allocaion being dropped on site map 
    * 
    */
    function handleDropEvent( event, ui ) {
        var draggable = ui.draggable; // get a reference to the dragged item
        if( $(this).hasClass('permanent')){
            // Dont support droping on sites with a permanent stall booking
        } else {
            stall_id=draggable.attr('stall');
            site_reference = $(this).attr('id').substring(5); // remove the site_ part
            site_id = sites_by_ref[site_reference]['site_id'];
            allocate_site(site_id,stall_id);



        }
    }

    /**
    * ALLOCATIONS DRAGGING
    * Helper for the dragging allocations
    * it returns what elm actually gets dragged around the place
    * 
    */
    function myHelper( event ) {
        return '<div id="draggableHelper">X</div>';
    }

    /**
    * DATABASE UPDATE OF ALLOCATIONS
    * Allocates a stall from allocates_sites array 
    */
    function allocate_site(site_id,stall_id){
        // insert the request into market_sites
        ms = {     
            'market_id': selected_market_id,
            'site_id': site_id,
            'stall_id':stall_id,
            'stalltype_id':allocation_requests[selected_market_id][stall_id]['stalltype_id'],
            'status': 'Active'
        };



        // Do the ajax update request
        $.ajax({
            type: "POST",
            url: url + '?ag=site_allocation',
            data: ms ,
            context: document.body,
            dataType: "json"               
        })
        .done(function(data){
            console.log('after');

            if(data['status'] == 500){
                alert('Processing Error Occured');
                console.log(data);
            } else if (data['status'] == 301){
                alert('Site Booking Conflict - site can probably only accomodate a casual booking!');
                console.log(data);
            }
            else {
                //console.log(this);
                //console.log(data);

                //console.log('site_id='+ site_id);
                //console.log(market_sites[selected_market_id]['active']);



                //console.log(data);
                if(data['status'] == 500){
                    alert('Processing Error Occured');
                    console.log(data);
                } else if (data['status'] == 301){
                    alert('Booking conflict for stall detected');
                    console.log(data);
                }
                else {

                    //console.log(data);
                    if(data['status'] == 500){
                        alert('Processing Error Occured');
                        console.log(data);
                    } else if (data['status'] == 301){
                        alert('Booking conflict for stall detected');
                        console.log(data);
                    }
                    else {

                        console.log(data);
                        //console.log('site_id='+ site_id);
                        //console.log(market_sites[selected_market_id]['active']);
                        if(typeof data['data']['deallocations'] !='undefined' ){
                            console.log('deallocations');
                            console.log(data['data']['deallocations']);

                            $.each(data['data']['deallocations'],function(key,record){
                                console.log('deallocation record');

                                $.each(record,function(market_id,site){
                                    $.each(site,function(idx,ms){
                                        console.log(ms);

                                        if(typeof market_sites[ms['market_id']]['active'][ms['site_id']] != 'undefined'){
                                            delete market_sites[ms['market_id']]['active'][ms['site_id']];
                                        }
                                        if(typeof allocation_requests[ms['market_id']] != 'undefined'){
                                            // add the alloc request back
                                            allocation_requests[ms['market_id']][ms['stall_id']]=stalls[ms['market_id']][ms['stall_id']] ;
                                        }

                                    });

                                });



                            });

                        }

                        if(typeof data['data']['allocations'] !='undefined' ){


                            $.each(data['data']['allocations'],function(key,value){
                                console.log('allocation record');

                                console.log(value);
                                console.log(value['market_id']);
                                console.log(value['site_id']);
                                if(value['status'] =='Active'){
                                    console.log('Active');
                                    market_sites[value['market_id']]['active'][value['site_id']] = value;
                                    stalls[value['market_id']][value['stall_id']]=allocation_requests[value['market_id']][value['stall_id']];

                                } else {
                                    console.log('Not Active');

                                    market_sites[value['market_id']]['not_active'][value['site_id']] = value;
                                }
                                //  typeof allocation_requests[ value['market_id'] ][ value['stall_id'] ] != 'undefined'

                                if(typeof allocation_requests[ value['market_id'] ] != 'undefined' &&  typeof allocation_requests[ value['market_id'] ][ value['stall_id'] ] != 'undefined' ){
                                    delete allocation_requests[ value['market_id'] ][ value['stall_id']] ;
                                }







                            });
                        }



                        //console.log(stalls[selected_market_id][stall_id]);

                        selected_site_id=site_id;

                        show_market();



                    }






                }
            }

        });
    }


    /**
    * PROCESS ALLOCATION RADIO BUTTON PANEL
    * UPDATE DATABASE
    * 
    */
    $stall_status_form.submit(function(event) {
        event.preventDefault();// stop browser doing form post
        var data=$stall_status_form.serialize();
        data +='&stall_id='+selected_stall['stall_id'];

        // Do the ajax update request
        $.ajax({
            type: "GET",
            url: url + '?ag=stall_update',
            data: data ,
            context: document.body,
            dataType: "json"               
        })
        .done(function(data){
            console.log(data);
            console.log(selected_stall);
            $.each(market_sites,function(mkt_id,$z){
                
                // update which stalls are in NOW in market_sites['active']
                
                 if(typeof data['data'][mkt_id] === 'undefined'){
                     
                     // no data for this MS 
                     // Delete any active and non-active MS data
                      if(typeof data['data'][mkt_id]['active'][selected_stall['stall_id']] !== 'undefined'){
                          delete data['data'][mkt_id]['active'][selected_stall['stall_id']];
                      }
                       if(typeof data['data'][mkt_id]['not_active'][selected_stall['stall_id']] !== 'undefined'){
                          delete data['data'][mkt_id]['not_active'][selected_stall['stall_id']];
                      }
                     
                     return;
                 }

                if(typeof data['data'][mkt_id]['active'] !== 'undefined'){
                    $.each(data['data'][mkt_id]['active'],function(s_id,ms){
                    
                        // add to market_sites active if not there
                        market_sites[mkt_id]['active'][s_id]=ms;

                        // delete from market_sites inactive if there
                        if(typeof market_sites[mkt_id]['not_active'][s_id] != 'undefined'){
                            
                            delete market_sites[mkt_id]['not_active'][s_id];
                        }

                    });
                }

                if( typeof data['data'][mkt_id]['not_active'] !== 'undefined'){
                    console.log('adding a not-active here');
                    
                    $.each(data['data'][mkt_id]['not_active'],function(s_id,ms){
                        console.log(s_id);
                        console.log(ms);
                        // add to market_sites not_active if not there
                        console.log(ms.status);
                        if(ms.status != 'Not required'){
                            
                            market_sites[mkt_id]['not_active'][s_id]=ms;
                        } else if (typeof  market_sites[mkt_id]['not_active'][s_id] != 'undefined'){
                            console.log('deleteing the not required not active entry')
                            delete market_sites[mkt_id]['not_active'][s_id];
                        }
                        

                        // delete from market_sites active if there
                        if(typeof market_sites[mkt_id]['active'][s_id] != 'undefined'){
                            delete market_sites[mkt_id]['active'][s_id];
                        }

                    });
                }

            });



            // and market_sites['not_active] based on returned market_site status
            
        });



    });


    /**
    * REMOVE A CASUAL ALLOCATION
    * When the Remove Casual button is clicked
    */
    $stall_status_remove_casual_button.click(function(e){
        e.preventDefault();
        console.log('remove stall_id='+selected_stall['stall_id']+' from site='+selected_site_id);

        var data = {
            stall_id : selected_stall['stall_id'],
            site_id : selected_site_id,
            market_id: selected_market_id
        }


        // Do the ajax update request
        $.ajax({
            type: "POST",
            url: url +'?ag=site_allocation&aga=remove_casual',
            data: data                
        })
        .done(function(data){
            //console.log('ajax.done for removing casual');
            //console.log(data);
            if(data['status'] == 500){
                alert('Processing Error Occured');
            } else {
                //console.log('site_id='+ site_id);
                //console.log(selected_stall);
                removeCasualBooking(data['market_id'],data['site_id'],data['stall_id']);


                clear_stall_info();



                show_market();
            }
        });

    });

    // chech if numer is off - odd stalltype_id(s) are perm, even are casual
    function is_odd(num){
        return num % 2;
    }

    function removeCasualBooking(market_id,site_id,stall_id){
        //console.log('removeCasualBooking - market_id='+market_id+'  site_id='+site_id+'  stall_id='+stall_id)
        try{

            var booking = market_sites[market_id]['active'][site_id];

            if(! is_odd(booking['stalltype_id']) ){
                delete market_sites[market_id]['active'][site_id] ;
                // put stall back in alloc_request array
                allocation_requests[market_id][stall_id] = stalls[market_id][stall_id];

                // remove from stals array
                delete stalls[market_id][stall_id];

            }
        } catch(err) {
            console.log('removeCasualBooking not done due to error: '.err.message);
        }
    }

    /**
    * CREATE THE ALLOCATION REQUEST LIST
    * 
    */
    function update_allocation_requests(){
        // clear list to start
        $('#allocation_request_list').html('');

        var requests = allocation_requests[selected_market_id];
        if('undefined' !== typeof requests){
            $.each(requests, function(index, stall){
                add_allocation(stall)
            });
            $('.tooltips').tooltip();

            // attachh draggable handler to items
            $( ".draggable" ).draggable({
                //containment: '#container',
                cursor: 'move',
                helper: myHelper

            });
        }

        // Add an item to the alloc list
        function add_allocation(stall){
            var tpl = '<div class="alloc_cell"><div  class="ui-widget-content draggable center" stall="'+ stall['stall_id'] +'" >PC</div>';
            tpl += '<div style="float:left;width:140px;" class="tooltips"  title="'+ stall['description'] +'">'+ stall['stalltype_id'] + ' - ' + stall['contact'] + '</div></div>';
            $('#allocation_request_list').append(tpl);
        }
    }




    /**
    * APPLICATION SETUP COMPLETE - START THE APPLICATION
    * 
    */
    createMarketTabs();
    $tab_container.find("[market_id='"+selected_market_id+"']").trigger('click'); // highlight the selected tab

    $stall_status_thead.hide();
    $stall_status_tbody.text('');
    $stall_status_tfoot.hide();

    show_market(); // start er up!!!



}); 