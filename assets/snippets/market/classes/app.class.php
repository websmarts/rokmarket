<?php class App 
{
    var $requestActions;// list of actions supported
    var $viewDir;
    var $data;



    function __construct() {

        $this->App(); }

    function App()
    {

    }

    function redirectAndExit($url=false){
        
        if(! $url){
            // redirect to same page
            $url = $_SERVER['REQUEST_URI'];
        }
        
        header('Location: '.$url);
        exit;
        
    }


    function render($view)
    {
        if (empty($view))
            die('no view file passed to render()');

        $viewFile=$this->viewDir . $view . '.php';

        if (file_exists($viewFile))
        {      
            $o='';
            include ($viewFile);
            echo $o;

            $this->viewRendered=true; // so we dont do multiple renders
        }
        else
        {
            die('failed to include viewfile:' . $viewFile);
        }
    }
    
    
}
?>