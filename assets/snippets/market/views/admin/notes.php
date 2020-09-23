<?php

//$o .=  TWS::pr($this->data['note'],0) ; 

$o .= '<p>Last updated: '.date('j-m-Y H:i:s',strtotime($this->data['note']['modified'].' GMT')) .'</p>';
$o .= '<form method="post">
         <button type="submit" class="btn btn-primary">Save</button><br /><br />
         <textarea name="content" cols="80" rows="10">'.$this->data['note']['content'].'</textarea><br />
         
         <button type="submit" class="btn btn-primary">Save</button>
      </form> 
      <script src="//tinymce.cachefly.net/4.1/tinymce.min.js"></script>

      <script>
        tinymce.init({
        selector:"textarea",
        menubar: false,
        height: 350
        
        });
</script>
'
      

?>