$(document).ready(function() { 
    
// Table sorter - used on STALLS page
    $(function(){
        
        $('.datepicker').datepicker({
            dateFormat: "yy-mm-dd"
        });
        
        jQuery('.datetimepicker').datetimepicker({
          format: 'Y-m-d H:i'
        });


        // Check all perm booking checkboxes on stall edit form
        
        $('#select_all').on('click',function(){
            if(this.checked){
                $('.bookbox').each(function(){
                    this.checked = true;
                });
            }else{
                 $('.bookbox').each(function(){
                    this.checked = false;
                });
            }
        });
        
        $('.bookbox').on('click',function(){
            if($('.bookbox:checked').length == $('.bookbox').length){
                $('#select_all').prop('checked',true);
            }else{
                $('#select_all').prop('checked',false);
            }
        });

    });

});