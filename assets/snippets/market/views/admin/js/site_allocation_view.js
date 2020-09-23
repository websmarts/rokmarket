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

    var $whiteboard=$('#whiteboard');


    // DATA - ID of stall currently selected
    var selected_market_id;
    var selected_stall_id = 0; // stall on site clicked
    var selected_stall = {}; // selected stall object
    var selected_site_id = 0;  // site clicked
    var site_reference = 0;  // site clicked

    var sites_by_ref = {};// array so we can lookup a site by its site_reference easily




    $.each(sites,function(index,value){
        if(typeof value['site_reference'] !== 'undefined'  && typeof value['site_id'] !=='undefined'){
            sites_by_ref[value['site_reference']]=value;
        }

    });


    /*
    $('#sitelayout').mousemove(function(event){
    $( "#log" ).text( (event.pageX - 227) + ", " + (event.pageY - 300));

    });
    */
    var $sitelayout = $('#sitelayout');
    var scale = .75;
    $.each(sites,function(index,site){


        var site_id= 'site_'+site.site_reference;
        $sitelayout.append('<div id="'+site_id+'">'+site['site_reference']+'</div>');
        var styles = {
            'top':parseInt(site.layout_y) * scale,
            'left':parseInt(site.layout_x) * scale,
            'width': parseInt(site.layout_w) * scale,
            'height':parseInt(site.layout_h) * scale,       
        }
        // console.log(site);
        // console.log(styles);

        $('#'+site_id).css(styles).addClass("site");
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
            $('#worksheet_link').attr('href', url +'?ag=market_worksheet&market_id='+selected_market_id)

            $(e.target).closest('li').addClass('active');
            show_market();


        }); 
    };


    /**
    * SHOW THE MARKET!!!
    * 
    */
    function show_market() {

        $('.site').removeClass('allocated permanent casual allocated permanent casual permanent_community permanent_community_free casual_community selected');


        $.each(market_sites[selected_market_id]['active'], function(index,value){
            try{
                divid = '#site_'+ sites[index]['site_reference'];
                if(value['status'] == "Active"){

                    if(value['stalltype_id'] == 1 ){
                        $(divid).addClass('allocated permanent');
                    } else if (value['stalltype_id'] == 3){
                        $(divid).addClass('allocated permanent_community');
                    }else if (value['stalltype_id'] == 2){
                        $(divid).addClass('allocated casual draggable_site');
                    } else if (value['stalltype_id'] == 4){
                        $(divid).addClass('allocated casual_community draggable_site');
                    } else if (value['stalltype_id'] == 5){
                        $(divid).addClass('allocated permanent_community_free');
                    } else {
                        $(divid).addClass('allocated');
                    }

                } else {
                    $(divid).removeClass('allocated permanent casual permanent_community casual_community');
                }
            } catch(err){

            }
            // make our casual sites draggable so we can remove them if need be
            $( '.draggable_site').draggable({
                //containment: '#sitelayout',
                cursor: 'move',
                helper: 'clone'

            });

        });

        try {
            var site_ref = sites[selected_site_id]['site_reference'];
            //console.log('trying to trigger click on '+'#site_'+site_ref);
            //$('#site_'+site_ref).click();
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

        if(typeof market_sites[selected_market_id]['active'][site_id] !== 'undefined' ){
            stall_id = market_sites[selected_market_id]['active'][site_id]['stall_id'];

            if(typeof stalls[selected_market_id][stall_id] !== 'undefined'){
                var msg = '<span>'+site_reference + '</span><br /> ' + stalls[selected_market_id][stall_id]['name'];
                msg += '<br />' + stalls[selected_market_id][stall_id]['description']; 
            }

        } else {
            var msg = 'No stall allocated to this site';
        }


        $('#whiteboard').html(  msg  );

        },function() {
            $('#whiteboard').text('Hover over site for stallholder info');
        }
    );

    function clear_stall_info(){


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

            // MODAL dialog form
            popup_form_for_stall();
            /*
            $('#stall_name').html(stalls[selected_market_id][selected_stall['stall_id']]['name']);
            $('#stall_payment').val(25);
            $('stall_status').val('Active');
            $( "#dialog-form" ).dialog( "open" );
            */
        } catch(e){
            // not an avtive site
            // Clear the allocations info
            //console.log('stall info not available!!');
            clear_stall_info();

            return;
        }

        // 'stall_id='+stall_id + 'site_id=' + site_id + ' '+ 




    });


    $('#reallocate_target').droppable({
        drop: function(event,ui){
            var draggable = ui.draggable;
            var site_reference = draggable.attr('id').substring(5); // remove the site_ part
            var site_id = sites_by_ref[site_reference]['site_id']
            var stall_id = market_sites[selected_market_id]['active'][sites_by_ref[site_reference]['site_id']]['stall_id']; 

            var data = {
                'market_id': selected_market_id,
                'site_id': site_id,
                'stall_id': stall_id
            }
            //console.log(data); 

            $.ajax({
                type: "POST",
                url: url + '?ag=site_deallocate',
                data: data ,
                context: document.body,
                dataType: "json"               
            })
            .done(function(data){
                //console.log('deallocated site');
                //console.log(data);
                // remove from market_sites[active]

                allocation_requests[selected_market_id]=data['data'];
                delete market_sites[selected_market_id]['active'][site_id];
                
                // remove site_draggable class from 
                draggable.removeClass('draggable_site');

                show_market();
                // put back into allocation_requests
            });

        }
    });




    /**
    * ALLOCATIONS DROPPING
    * Make all the divs contained in div.block(s), i.e. site divs, droppable
    * 
    */
    $('.site').droppable({
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

            // target site_id
            var site_reference = $(this).attr('id').substring(5); // remove the site_ part 
            var site_id = sites_by_ref[site_reference]['site_id'];

            if(typeof draggable.attr('id') !== 'undefined' &&  draggable.attr('id').substring(0,5) == 'site_'){
                // drop is from another casual site
                var old_site_id = sites_by_ref[ draggable.attr('id').substring(5) ]['site_id'];
                reallocate_stall(old_site_id,site_id);
            } else {
                // drop is from alloc list  
                var mar_id=draggable.attr('mar_id');  
                allocate_site(site_id,mar_id);
            }      
        }
    }
    function reallocate_stall(old_site_id,new_site_id){
        //console.log('reallocte stall from site:'+old_site_id + ' to new_site_id:'+new_site_id );
        var data = {
            'market_id': selected_market_id,
            'old_site_id': old_site_id,
            'new_site_id': new_site_id
        }


        $.ajax({
            type: "POST",
            url: url + '?ag=site_reallocate',
            data: data ,
            context: document.body,
            dataType: "json"               
        })
        .done(function(data){
            //console.log('reallocated site');
            //console.log(data);
            // remove from market_sites[active]

            market_sites[selected_market_id]['active']=data['data']['market_sites']['active'];
            
            // remove draggable_site class from old site
            var old_site_reference = sites[old_site_id]['site_reference'];
            $('#site_'+old_site_reference).removeClass('draggable_site');

            show_market();
            // put back into allocation_requests
        });
    }

    /**
    * ALLOCATIONS DRAGGING
    * Helper for the dragging allocations
    * it returns what elm actually gets dragged around the place
    * 
    */
    function myHelper( event ) {
        return '<div id="draggableHelper">&nbsp;</div>';
    }

    /**
    * DATABASE UPDATE OF ALLOCATIONS
    * Allocates a stall from allocates_sites array 
    */
    function allocate_site(site_id,mar_id){
        // insert the request into market_sites
        var stallInfo = allocation_requests[selected_market_id][mar_id];
        var stalltype_id= stallInfo['freebee'] > 0 ? 4 : 2; // casual or community casual freebee

        var ms = {
            'market_id': selected_market_id,
            'site_id':site_id,
            'stall_id':stallInfo['stall_id'],
            'stalltype_id': stalltype_id,
            'status':'Active'
        };
        //console.log(site_id);
        //console.log(selected_market_id);
        //console.log(ms);

        market_sites[selected_market_id]['active'][site_id] = ms;

        var data = {  
            'mar_id': mar_id,
            'site_id': site_id
        };

        // Do the ajax update request
        $.ajax({
            type: "POST",
            url: url + '?ag=site_allocation',
            data: data ,
            context: document.body,
            dataType: "json"               
        })
        .done(function(data){
            //console.log('after');


            //console.log(this);
            //console.log(data);

            //console.log('site_id='+ site_id);
            //console.log(market_sites[selected_market_id]['active']);

            //console.log(data);
            if(data['status'] == 500){
                alert('Processing Error Occured');
                //console.log(data);
            } else {

                //console.log(data);
                if(data['status'] == 500){
                    alert('Processing Error Occured');
                    //console.log(data);
                } else if (data['status'] == 301){
                    alert('Booking conflict for stall detected');
                    //console.log(data);
                }
                else {
                    //console.log(data); // data contains an update list of allocation_request
                    //console.log('site_id='+ site_id);
                    allocation_requests[selected_market_id]=data['data']['allocation_requests'];
                    stalls[selected_market_id]=data['data']['stalls'];

                    // add the 

                    selected_site_id=site_id;
                    show_market();
                }

            }


        });
    }


    /**
    * PROCESS ALLOCATION RADIO BUTTON PANEL
    * UPDATE DATABASE
    * 
    */





    // chech if numer is off - odd stalltype_id(s) are perm, even are casual
    function is_odd(num){
        return num % 2;
    }



    /**
    * CREATE THE ALLOCATION REQUEST LIST
    * 
    */
    function update_allocation_requests(){
        // clear list to start
        $('#allocation_request_list').html('');

        var requests = allocation_requests[selected_market_id];
        //console.log('allocation requsts');
        //console.log(requests);

        if('undefined' !== typeof requests){
            $.each(requests, function(index, stallRequest){
                var booking_type;
                if(stallRequest.community_stall == "1"){
                    booking_type="C";
                } else {
                    booking_type="S";
                }
                if(stallRequest.freebee =="1"){
                    booking_type += "F";
                }


                add_allocation(stallRequest,booking_type);
            });
            $('.tooltips').tooltip();

            // attachh draggable handler to items
            $( ".draggable" ).draggable({
                //containment: '#sitelayout',
                cursor: 'move',
                helper: myHelper

            });
        }

        // Add an item to the alloc list
        function add_allocation(stallRequest,booking_type){
//stalls[selected_market_id][stallRequest['stall_id']]['name']
            var tpl = '<div class="alloc_cell"><div  class="ui-widget-content draggable center" mar_id="'+ stallRequest['market_allocation_request_id'] +'" >'+booking_type+'</div>';
            tpl += '<div style="float:left;width:140px;" class="tooltips"  title="'+ 'NAME' +' | ' +stallRequest['description']+'">' + stallRequest['stallname'] + '</div></div>';
            $('#allocation_request_list').append(tpl);
        }
    }

    /*
    *  POPUP FORM TO UPDATE STALL INFO
    */
    function popup_form_for_stall() {
        return;
        // set form values  
        //console.log(stalls); 
        //console.log(selected_stall);

        var stall_id =  selected_stall['stall_id'];
        var site_id =  selected_stall['site_id'];
        var siteref = sites[site_id]['site_reference']; // eg H23
        var stall_data = stalls[selected_market_id][stall_id];

        console.log(stall_data);

        if(stall_data['prompt_payment_discount'] > 0 ){
            var stall_fee = stalltypes[selected_stall['stalltype_id']]['prompt_payment_site_fee'];
        } else {
            var stall_fee = stalltypes[selected_stall['stalltype_id']]['std_site_fee']; 
        }


        //console.log();

        $('#stall_name').html(siteref + ': '+stalls[selected_market_id][stall_id]['name']);
        $('#stalltype').text(stalltypes[selected_stall['stalltype_id']]['description']);
        $('#stall_payment').val(stall_fee);
        $('stall_status').val('Active');
        $( "#dialog-form" ).dialog( "open" );
    }

    $( "#dialog-form" ).dialog({
        autoOpen: false,
        height: 300,
        width: 350,
        modal: true,
        buttons: {
            "Update": function() {
                var data = {
                    'market_id': selected_market_id,
                    'site_id': selected_site_id,
                    'stall_id':selected_stall['stall_id'],
                    'payment':$('#stall_payment').val(),
                    'status':$('#stall_status').val()
                    
                }
                //console.log('Update the selected site withthe following data');
                //console.log(data);
                $.ajax({
                    type: "POST",
                    url: url + '?ag=site_update',
                    data: data ,
                    context: document.body,
                    dataType: "json"               
                })
                .done(function(data){

                });

            },
            Cancel: function() {
                $( this ).dialog( "close" );
            }
        },
        close: function() {
            //allFields.val( "" ).removeClass( "ui-state-error" );
        }
    });




    /**
    * APPLICATION SETUP COMPLETE - START THE APPLICATION
    * 
    */
    createMarketTabs();
    $tab_container.find("[market_id='"+selected_market_id+"']").trigger('click'); // highlight the selected tab


    show_market(); // start er up!!!



}); 