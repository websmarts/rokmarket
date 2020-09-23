<?php 

$o = '<h3>Edit Product</h3>'; 


?>



<?php
    //$this->pr(TWS::getFlash('formdata'));
?>

<?php
    if($errors = TWS::getFlash('formerrors')){
        echo '<div class="flash_form_errors"><p>Form Errors</p><p>'.$errors.'</p></div>';
    } 
    $f = $this->data['f'];
      
    $o = $f->render(); 
?>
