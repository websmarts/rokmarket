<?php


//$o .=  TWS::pr($_SESSION['myapp'],0) ; 
$o.='<div class="row">';
$o.='<div class="col-md-5 col-md-offset-1">';
$o .= $this->data['f']->render();
$o .= '</div>';
$o .= '</div>';
?>