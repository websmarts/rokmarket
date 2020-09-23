

$(document).ready(function() { 

    // Useful containers
    var $marketTabsDiv = $('#market_tabs');
    var $siteLayout = $('#sitelayout');

    
    
    // load the main data store
    var DS = function() {
        var ds={}; // the main data
        var state={}; // application state data

        return {
            getSeason: function() {
                return ds.season;
            },
            getMarkets: function(){
                return ds.markets;
            },
            setSelectedMarket: function(id){
                //console.log(id);
                //console.log('id',id,'ds.markets[id]',ds.markets[id]);
                state.selectedMarket = ds.markets[id];
                state.selectedMarketId=id;
            },
            getSelectedMarket: function(){
                return state.selectedMarket;
            },
            getSelectedMarketId: function(){
                 return state.selectedMarketId;
            },
            isMarketOpen: function(){

                return state.selectedMarket['status'] == "Open" ? true : false;
               
            },
            getBookings: function(){
                //console.log(state.selectedMarket.market_id);
                return ds.market_data[state.selectedMarket.market_id].bookings;
            },
            getBooking: function(siteId) { 
                return ds.market_data[state.selectedMarket.market_id].bookings[siteId];
            },
            setSelectedSiteId: function(siteId){
                state.selectedSiteId = siteId;
            },
            getSelectedSiteId: function(){
                return state.selectedSiteId;
            },
            getSiteStall: function(site_id){
                if(typeof(ds['market_data'][ state.selectedMarket['market_id']]['bookings'][site_id]) !=='undefined'){
                    var stallId=ds['market_data'][ state.selectedMarket['market_id']]['bookings'][site_id]['stall_id'];
                    return ds['stalls'][stallId];
                }
            },
            getSiteRequests: function(){
                return ds.market_data[state.selectedMarket.market_id]['casual_requests'];
            },
            allocateSite: function(siteId,marId){

                // insert the request into market_sites
                var data ={};
                data['site_id']=siteId;
                data['market_allocation_request_id']=marId;
                data['action']='allocate_site';

                $.ajax({
                    type: "POST",
                    url: url + '?ag=gateway',
                    data: data ,
                    //context: document.body,
                    dataType: "json"               
                })
                .done(function(data){
                    if(data['status'] == 500){
                        alert('Processing Error Occured');
                        //console.log(data);
                    } else {
                        ds=data['data'];
                        state.selectedSiteId = siteId;
                        refresh();

                    }
                });
            },
            moveStall: function(siteId,marketSiteId){ // move allocated casual stall to a new site
                var data ={};
                data['site_id']=siteId;
                data['market_site_id']=marketSiteId;
                data['action']='move_stall';

                $.ajax({
                    type: "POST",
                    url: url + '?ag=gateway',
                    data: data ,
                    //context: document.body,
                    dataType: "json"               
                })
                .done(function(data){
                    if(data['status'] == 500){
                        alert('Processing Error Occured');
                        //console.log(data);
                    } else {
                        ds=data['data'];
                        state.selectedSiteId = siteId;
                        refresh();

                    }
                });  
            },
            undoBooking: function(marketSiteId){
                var data ={};
                
                data['market_site_id']=marketSiteId;
                data['action']='undo_booking';

                $.ajax({
                    type: "POST",
                    url: url + '?ag=gateway',
                    data: data ,
                    //context: document.body,
                    dataType: "json"               
                })
                .done(function(data){
                    if(data['status'] == 500){
                        alert('Processing Error Occured');
                        //console.log(data);
                    } else {
                        ds=data['data'];
                        
                        refresh();

                    }
                });  
            },
            getDS: function(){
                return ds;
            },
            getSites: function(){
                return ds.sites;
            },
            getStalls: function(){
                return ds.stalls;
            },
            updateStall: function(note,status){ // updates the current selected stall
                var data = {
                    note: note,
                    status: status,
                    market_site_id: ds.market_data[state.selectedMarket.market_id].bookings[state.selectedSiteId]['market_site_id'],
                    action: 'update_booking'
                }

                $.ajax({
                    type: "POST",
                    url: url + '?ag=gateway',
                    data: data,
                    //context: document.body,
                    dataType: "json"               
                })
                .done(function(data){
                    ds=data['data']; 
                    
                    $('#p2').fadeIn();
                    showBookings();
                    
                });

            },

            init: function() {
                $.ajax({
                    type: "GET",
                    url: url + '?ag=gateway',
                    //context: document.body,
                    dataType: "json"               
                })
                .done(function(data){
                    ds=data['data'];
                    main();
                });


            }
        }
    }();
    DS.init(); // Get the data for the season markets

    function refresh() {
        //var a=0;
        //console.log(a++) ;
        updateWhiteboard(); // Season Market & status
        // console.log(a++) ;
        siteEventsHandler();
        // console.log(a++) ;
        showBookings(); // market bookings
        // console.log(a++) ;
        showRequests();
        // console.log(a++) ;
        

    }

    // ONCE DATA IS LOADED main(0 gets called to start up app)   
    function main() {
       //console.log(DS.getDS());

        marketTabs(); // market tabs
        //console.log('done tabs');
        layoutSites(); // site placement
        //console.log('done layout');
        refresh();
        //console.log('done refresh');
    }// End of MAIN



    // Add the market selector tabs
    function marketTabs(){
        var nowDate = new Date();
        var currentMonth = nowDate.getMonth() + 1; // we use 1-12 NOT 0-11 for month idx
        var currentYear = nowDate.getYear();
        var season = DS.getSeason();
        var markets = DS.getMarkets();

        //console.log('currentMonth',currentMonth);
        var selectTabMonth=currentMonth; // default current month
        if(currentMonth > 5 && currentMonth < 9){// outside season so select closest season month          
            if(currentYear > season){            
                selectTabMonth=5;// select last month of season i.e. May
            } else {        
                selectTabMonth=9;////select first month of season ie Sept
            }           
        } 
        
        // CREATE THE MENU TABS
        var html = '<ul class="nav nav-tabs">';
        $.each(markets, function(index,value){
            var tabMonth =  parseInt(value['market_date'].substring(5,7));
            var liClass = '';
            if( selectTabMonth == tabMonth){      
                DS.setSelectedMarket(index);
                $('#worksheet_link').attr('href', url +'?ag=market_worksheet&market_id='+index);
                $('#location_list_link').attr('href', url +'?ag=market_show_location_list&market_id='+index);
                var liClass=' class="active" ';
            } 
            html += '<li ' + liClass + ' ><a  market_id="'+index+'">'+value['F'] +'</a></li>';
        }); 
        html += '</ul>';
        $marketTabsDiv.append(html);

        // Now tabs are installed so add the click handler
        $marketTabsDiv.click(function(e){

            //e.preventDefault();
            $marketTabsDiv.find('li.active').removeClass('active'); // remove active

            var selectedMarketId = $(e.target).closest('a').attr('market_id');
            DS.setSelectedMarket(selectedMarketId);
            $('#worksheet_link').attr('href', url +'?ag=market_worksheet&market_id='+selectedMarketId);
            $('#location_list_link').attr('href', url +'?ag=market_show_location_list&market_id='+selectedMarketId);
            $(e.target).closest('li').addClass('active');


            if(!DS.isMarketOpen()){
                $('#p2').hide(); // hide the stall update panel if market is closed
            } else {
                $('#p2').show();
            }
            refresh(); // as the name says!

        });
        if(!DS.isMarketOpen()){
            $('#p2').hide(); // hide the stall update panel if market is closed
        } else {
            $('#p2').show();
        } 

    }; // END MARKET TABS CREATION



    function layoutSites() {


        var scale = .75;

        $.each(DS.getSites(),function(index,site){


            var id= 'site_'+ index; //site.site_reference;
            $siteLayout.append('<div id="'+id+'">'+site.site_reference+'</div>');
            var styles = {
                'top':parseInt(site.layout_y) * scale,
                'left':parseInt(site.layout_x) * scale,
                'width': parseInt(site.layout_w) * scale,
                'height':parseInt(site.layout_h) * scale,       
            }
            // console.log(site);
            // console.log(styles);

            $('#'+id).css(styles).addClass("site");
        })
    }

    function showBookings() {
        $('.site').removeClass('permanent permanent_community permanent_community_free casual casual_community draggable_site selected');
        

        //var market = DS.getSelectedMarket();
        //var stalls= DS.getMarketStalls(market.market_id)


        //console.log(market);
        //console.log(stalls);
        var bookings = DS.getBookings();
        if(typeof bookings == 'undefined' || bookings == null){ // no bookings
            return;
        }
        $.each(bookings, function(siteId,booking){

            if(booking.status =='Active'){

                var divId = '#site_'+ siteId;
                if(booking.stalltype_id == 1 ){
                    $(divId).addClass('permanent');
                } else if (booking.stalltype_id == 3){
                    $(divId).addClass('permanent_community');
                }else if (booking.stalltype_id == 2){
                    $(divId).addClass('casual draggable_site');
                } else if (booking.stalltype_id == 4){
                    $(divId).addClass('casual_community draggable_site');
                } else if (booking.stalltype_id == 5){
                    $(divId).addClass('permanent_community_free');
                } 

            } else {
                $(divId).removeClass('permanent permanent_community permanent_community_free casual casual_community  draggable_site selected');
            }

        });

        // make our casual sites draggable so we can remove them if need be
        $('.site').unbind("disable",1);
        
        $( '.site.draggable_site').draggable({
            //containment: '#sitelayout',
            cursor: 'move',
            helper: 'clone'


            

        });

        // Make our sites droppable
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
                var site_id = $(this).attr('id').substring(5); // remove the site_ part 

                if(typeof draggable.attr('id') !== 'undefined' &&  draggable.attr('id').substring(0,5) == 'site_'){
                    // drop is from another casual site
                    var old_site_id = draggable.attr('id').substring(5) ;
                    try{
                       var booking= DS.getBooking(old_site_id);
                       //console.log(booking);
                       DS.moveStall(site_id,booking['market_site_id']);
                       draggable.removeClass('draggable_site'); 
                      
                    } catch(e) {
                        
                    }
                    
                    //ui.draggable.draggable("destroy");
                    
                } else {
                    // drop is from alloc list  
                    var mar_id=draggable.attr('mar_id');  
                    DS.allocateSite(site_id,mar_id);
                    //alert('show bookings');

                }      
            }
        }





    } // END Display Bookings()

    // Drop casual stall to put back on alloc list
    $('.reallocate_target').droppable({
        drop: function(event,ui){
            var draggable = ui.draggable;
            var siteId = draggable.attr('id').substring(5); // remove the site_ part

            var booking = DS.getBooking(siteId);
            var marketSiteId=booking['market_site_id'];

            DS.undoBooking(marketSiteId);
            
            //draggable.draggable('disable');
            draggable.removeClass('draggable_site');
            //draggable.draggable("option","cancel",true);
        }

    });




    function updateWhiteboard(){
        var season = DS.getSeason();
        var market = DS.getSelectedMarket();
        //console.log('updateWhiteboard');
        //console.log(market);

        var html = '<p>'+ season + '-' + (parseInt(season) +1) + '<br>'+ market['F']+ '<br>'+ market['status']+ '</p>';
        $('#whiteboard').html(html);
    }

    function siteEventsHandler() {

        var bookings = DS.getBookings(); // for selected market
        var stalls =  DS.getStalls();
        var sites = DS.getSites(); 
        var markets =DS.getMarkets();  
        //add a hover handler for the the siteLayout container div
        $('.site').hover( function() {
            //var bookings = DS.getBookings(); // for selected market
            site_id = $(this).attr('id').substring(5); // remove the site_ par

            //console.log('Hovering over site_id='+site_id);

            //console.log('site_id='+site_id);
            //console.log(bookings);
            if(typeof bookings == 'undefined' || bookings == null){
                return;
            }

            if(typeof bookings[site_id] !== 'undefined'){
                stall_id = bookings[site_id]['stall_id'];
                //console.log('site_id',site_id)
                //console.log('Bookings for site',bookings[site_id]);
                // check if booking statu
               // console.log('stall_id='+stall_id);
                var msg='';
                msg += '<span class="site_reference">'+sites[site_id]['site_reference'] + ' </span>';
                msg += '<span  class="contact">' + stalls[stall_id]['firstname'] + ", " + stalls[stall_id]['lastname'] + '</span>';

                // if stallname and contact are basically the same then just display contact
                var testname = stalls[stall_id]['lastname'] + stalls[stall_id]['firstname'];
                var testname2 =stalls[stall_id]['firstname'] + stalls[stall_id]['lastname'];
                testname = testname.toLowerCase();
                testname2 = testname2.toLowerCase();  
                var testname3 = stalls[stall_id]['stallname'];
                testname3 = testname2.replace(',','').replace(' ','').toLowerCase();              
                if ( testname != testname3 && testname2 != testname3  ){
                    msg += '<span class="stallname">' + stalls[stall_id]['stallname']+'</span>';
                }

                msg += '<p>' + stalls[stall_id]['description']+'</p>'; 

                var attendance = getStallAttendance(stall_id);

                $('#p1 .attendance').html(attendance);


            } else {
                var msg = 'No stall allocated to  site '+site_id;
                $('#p1 .attendance').html('');
            }


            $('#p1 .note').html(  msg  );

            },function() {
                $('#p1 .note').text('Hover over site for stallholder info');
                $('#p1 .attendance').html('');
            }
        ); // END OF SITE HOVER EVENT HANDLER
        
        // SITE CLICK HANDLER       
        $('.site').on('click',function(){
            
            // Dont allow if market is closed
            if(! (  $(this).hasClass('permanent') 
                    || $(this).hasClass('permanent_community')
                    || $(this).hasClass('permanent_community_free')
                    || $(this).hasClass('casual_community') 
                    || $(this).hasClass('casual')) ){
               
                return;
            }


            //console.log('Click function triggered');
            //populate the allocation table with current stall 
            $('.site ').removeClass('selected');

            // clear the form area
            function clearForm(){
                $('#p2 .stallname').html('');
                $('#p2 .marketnote').html('');
                $('#p2 .attendance').html('');
                $('#p2 .form').html(''); 
            }

            if(!DS.isMarketOpen()){
                clearForm();

                $('#p2 .stallname').html( 'Market is CLOSED');
                return;
            }




            $(this).addClass('selected');// maybe put a red border


            var site_id = $(this).attr('id').substring(5); // remove the site_ part
            DS.setSelectedSiteId(site_id);
            var stall = DS.getSiteStall(site_id); // for current selected market
            //console.log(stall);
            var booking = DS.getBooking(site_id);
            
            
            var attendance = getStallAttendance(stall['stall_id']);

            if(booking['note'] == null){
                var note='';
            } else {
                var note = booking['note'];
            }
            $('#p2 .stallname').html( stall['stallname']);
            $('#p2 .marketnote').html('<textarea>' + note + '</textarea>');
            $('#p2 .attendance').html(attendance);

            // The update form
            var statusOpts = ['Active','Cancelled','Cancelled late','No show'];
            var form='';
            var status = '';
            $.each(statusOpts, function(i,o){
                if (booking['status'] == o ){
                    var checked = ' checked '
                    status = o;
                } else {
                    var checked = '';
                }

                form += '<input type="radio" name="status" value="' + o + '" ' + checked + '>' + o + '<br>';
            });
            //form += '<input type="text" name="payment" id="form_payment"> Payment <br>';
            form += '<button>Save</button> &nbsp;&nbsp;<a href="'+url +'?ag=stalls&aga=edit&stall_id='+stall['stall_id']+'" >view stall</a>';
            $('#p2 .form').html(form);

            //Add click handler to save button
            $('#p2 button').on('click', function(e){

                //console.log('you clicked the button for site_id=' + site_id);

                var new_note = $('#p2 textarea').val();
                var selected = $("#p2 input[type='radio'][name='status']:checked");
                if (selected.length > 0) {
                    var new_status = selected.val();
                }

                if(new_note != note || new_status != status){
                    // Make the ajax call to update 
                      var $p2= $('#p2');

                    // fade out
                    $p2.fadeOut( function(){
                        //clearForm();

                        //$p2.fadeIn();
                        //showBookings(); // sneak this in here ??
                    });
                    
                    DS.updateStall(new_note,new_status);
                    // Now what ??
                   
                }
            })


            // 'stall_id='+stall_id + 'site_id=' + site_id + ' '+ 




        });

        function getStallAttendance(stall_id){
            var attendance = '';
            // stalls[stall_id]['attendance'],function(mid,obj)
            
            if( typeof(stalls[stall_id]['attendance']) == 'undefined' || stalls[stall_id]['attendance'] == null  ){
                return 'no attendance record found';
            } 
            $.each(markets, function(mid,mdata){

                var a ='';   
                var st ='NR';
                               
                if( typeof(stalls[stall_id]['attendance'][mid]) !== 'undefined' && stalls[stall_id]['attendance'][mid] != null  ){                
                    var obj =stalls[stall_id]['attendance'][mid] ;
                    $.each(obj,function(sid,status){
                        if(sid != site_id || a.length > 1){
                            return true; // only get status for first site - assume same for multi sites
                        }
                        if(status =='Active'){
                            st = "a";
                        } else if (status=='Cancelled'){
                            st = 'c';
                        } else if (status=='Cancelled late'){
                            st ='CL';
                        } else if(status = 'No show'){
                            st ='NS';
                        } 

                        a +=   sites[sid]['site_reference'] + ' <br>'+st ;

                    });
                                        
                } else {
                    st='NA';
                    a='-';
                }
                 attendance += '<div class="'+st+'" >'+ a + '</div>';  
            });
            return attendance;
        }


    }

    /**
    * CREATE THE ALLOCATION REQUEST LIST
    * 
    */
    function showRequests() {
        
        $('#allocation_request_list').html('');

        if(!DS.isMarketOpen()){
            $('#allocation_requests').hide();
            return; // dont show requests for closed makets - Hmmm .. maybe show but dont allow alloc
        }
        $('#allocation_requests').show();
        var stalls = DS.getStalls();
        //console.log('Stalls',stalls);
        
        // clear list to start
        var markets = DS.getMarkets();
        var sites = DS.getSites();

        var requests = DS.getSiteRequests();
        var orderedRequests =[];
        
        if(typeof requests == 'undefined' || requests == null){ // no requests
            return;
        }
        //console.log('allocation requests');
        //console.log(requests);

        
        $.each(requests, function(stallId, requests){
            $.each(requests, function(n,req){
                //console.log(n);
                //console.log(req);
                //console.log(req['market_allocation_request_id']);
                orderedRequests.push( { 'requested':n,
                                        'is_freebe':req['is_freebe'],
                                        'market_allocation_request_id': req['market_allocation_request_id'],
                                        'stall_id': stallId
                
                });
                

            });

        });
        //console.log(orderedRequests);
        // sort by requested date
        orderedRequests.sort(function(a,b){
            return a.requested.localeCompare(b.requested); 
        })
        //console.log(orderedRequests);
        
        
        
        $.each(orderedRequests, function(key, req){
            //console.log(req);
            var stallId = req['stall_id'];
            var requested = req['requested'];
            
            var tpl = '<div class="alloc_cell">';
            tpl += '<div  class="ui-widget-content draggable center" mar_id="'+ req['market_allocation_request_id'] +'" >&nbsp;</div>';
            tpl += '<div class="item tooltips"  stall_id="'+stallId+'" requested="'+requested+'"  title="'  + stalls[stallId]['firstname'] + ' ' + stalls[stallId]['lastname'] + '"><a href="'+url+'?ag=stalls&aga=edit&stall_id='+stallId+'" >' + stalls[stallId]['stallname'] + '</a></div></div>';
            $('#allocation_request_list').append(tpl);
        });
        //$('.tooltips').tooltip();

        $('.alloc_cell .item').hover(
            function(e){
                 //$('#allocation_requests .stallinfo').html($(e.target).attr('title'));
                 var stallId=$(e.target).attr('stall_id');
                 if(typeof stallId == 'undefined'){
                     return;
                 }
                 
                 var stall=stalls[stallId];
                 if (typeof stall != 'undefined') {
                     var requested = $(e.target).attr('requested').substring(0,16);
                    $('#allocation_requests .stallinfo').html( requested + ' ' + stall['firstname'] + ' ' + stall['lastname'] + '<br />' + stall['description']) ;
                    
                    $('#allocation_requests .attendance').html(getStallAttendance(stallId)); 
                 }  
                 
            }, function(){
                $('#allocation_requests .stallinfo').html('');
                $('#allocation_requests .attendance').html('');
        });



        // attachh draggable handler to items
        $( ".draggable" ).draggable({
            //containment: '#sitelayout',
            cursor: 'move',
            helper: myHelper

        });

        function getStallAttendance(stall_id){

            var attendance = '';

            //console.log('Stall attendance',stalls[stall_id]['attendance']);

            // stalls[stall_id]['attendance'],function(mid,obj)
            if(typeof  stalls[stall_id] != 'undefined' && stalls[stall_id]['attendance'] != null){


                $.each(markets, function(mid,mdata){
                    var a ='';
                    var st ='NR'               
                    if( typeof stalls[stall_id]['attendance'][mid] !== 'undefined' ){
                        var obj =stalls[stall_id]['attendance'][mid] ;
                        $.each(obj,function(sid,status){
                            if(a.length > 1){

                                return true; // only get status for first site - assume same for multi sites
                            }
                            if(status =='Active'){
                                st = "a";
                            } else if (status=='Cancelled'){
                                st = 'c';
                            } else if (status=='Cancelled late'){
                                st ='CL';
                            } else if(status = 'No show'){
                                st ='NS';
                            }

                            a +=   sites[sid]['site_reference'] + ' <br>'+st ;

                        });                       
                    }

                    attendance += '<div class="'+st+'" >'+ a + '</div>';
                });
            } else {
                attendance='<span>No attendance record</span>';
            }
            return attendance;
        }


    }
    function myHelper( event ) {
        return '<div id="draggableHelper">&nbsp;</div>';
    }

}); // END DOCUMENT READY