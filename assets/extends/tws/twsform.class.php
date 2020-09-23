<?php
    class TWSForm
    {

        var $formValidationError;
        var $layout = 'horizontal'; // bootstrap layout - horizontal, vertical, inline

        var $formAction;
        var $formName;
        var $formMethod;




        function __construct($action='',$formname='myForm',$method='post') { $this->TWSForm($action,$formname,$method); }

        function TWSForm($action='',$formname='myForm',$method='post')
        {

            // Save these for render(0 knows what to use
            $this->formAction = $action;
            $this->formMethod = $method;
            $this->formName = $formname;

            $this->supportedElementTypes=array
            (
                'hidden',
                'submit',
                'text',
                'textarea',
                'select',
                'multi_select',
                'checkbox',
                'date'
            );

            $this->validationRules=array
            (
                'not-empty' => '/.+/',
                'number' => '/^\d+$/',
                'email' =>
                '/\\A(?:^([a-z0-9][a-z0-9_\\-\\.\\+]*)@([a-z0-9][a-z0-9\\.\\-]{0,63}\\.(com|org|net|biz|info|name|net|pro|aero|coop|museum|[a-z]{2,4}))$)\\z/i',
                'year' => '/^[12][0-9]{3}$/',
                'number-not-zero' => '/^[1-9][0-9]*$/',
                'float' =>'/^(\d*\.\d+|\d+\.\d*|\d+)$/',
                'not-zero'=>'/[1-9]+/',
                'valid-date'=>'/^\d\d\/\d\d\/\d\d\d\d$/'
            );
        }



        function isNotValid() { return $this->formValidationError; }

        function  run()
        {
            $this->errors=array(); // init errors
            // Check the formname is ours, otherwise ignore //
            //$this->pr($_POST);exit;
            if ($_POST['formname'] != $this->formName)
            {
                return false; // Post is not from our form
            }

            $this->savePostToSession();

            $validation_message='';
            $this->formValidationError=false;

            foreach ($this->elements as $type => $eArr)
            {
                foreach ($eArr as $e)
                {
                    if (!empty($e['validation_rule']))
                    {

                        //$this->pr($e);

                        $value=$_POST[$e['name']];

                        if (is_array($value) && count($value) > 0)
                        { // multi selects have an array of values so validate each one
                            foreach ($value as $v)
                            {
                                $this->validate($e, $v);
                            }
                        }
                        else
                        {
                            $this->validate($e, $value);
                        }
                    }
                }
            }

            return true;
        }

        function validate($e, $value)
        {
            if (empty($e['validation_rule']))
                return;
            
           
            if(is_array($e['validation_rule'])){
                $oneflag = 0;
                foreach($e['validation_rule'] as $rule)
                {
                    if(!$oneflag && !preg_match($this->validationRules[$rule], $value)){
                        $this->errors[$e['name']]['error']=1;
                        $this->errors[$e['name']]['validation_message']=$e['validation_message'];
                        TWS::flash('validation_messages', $e['validation_message'] . '<br />' . "\n");
                        $this->formValidationError=true;
                        $oneflag=true;
                    }
                    
                    
                }
            } else {
                if(!preg_match($this->validationRules[$e['validation_rule']], $value)){
                    $this->errors[$e['name']]['error']=1;
                    $this->errors[$e['name']]['validation_message']=$e['validation_message'];
                    TWS::flash('validation_messages', $e['validation_message'] . '<br />' . "\n");
                    $this->formValidationError=true;
                }
            }


        }
       
        function getElements() { return $this->elements; }

        function addElement($e)
        {
            $e['type']=strtolower($e['type']);

            if (!in_array($e['type'], $this->supportedElementTypes))
            {
                die('element type: ' . $e['type'] . ' is not supported');
            }
            $this->elements[$e['type']][$e['id']]=$e;
        }

        function render_validation_massages()
        {
            $validationMessages=TWS::getFlash('validation_messages');

            if (!empty($validationMessages))
                return '<div class="form_validation_messages">FORM VALIDATION ERRORS<br />' . $validationMessages . '</div>'
                . "\n";
        }

        function render_form_open()
        {

            

            $o='<form class="form-horizontal" method="' . $this->formMethod . '" action="' . $this->formAction . '" id="'
            . $this->formName . '" >';
            $e=array
            (
                'type' => 'hidden',
                'name' => 'formname',
                'value' => $this->formName
            );

            $o.=$this->render_hidden($e);
            return $o;
        }

        function render_field_group($field_group_name,$alt='')
        {
            $o='';

            if (is_array($this->elements) && count($this->elements) > 0)
            {
                foreach ($this->elements as $type => $elements)
                {
                    foreach ($elements as $e)
                    {
                        if ($e['field_group'] == $field_group_name)
                        {
                            $o.=$this->render_element($e);
                        }
                    }
                }
            }
            if(empty($o))
                $o = $alt;// use alternative if nothing to render from element data
                
            return $o;
        }

        function render_form_close() { return '</form>' . "\n"; }

        function render_js_validator(){
            $o = '
            <script>
            $(document).ready(function(){
            $("#'.$this->formName.'").validate();
            });
            </script>';

            return $o;
        }

        function render_all_elements()
        {
            if (!is_array($this->elements))
            {
                return;
            }

            foreach ($this->elements as $type => $eArr)
            {
                // if no type given default to 'text'

                foreach ($eArr as $e)
                {
                    //$this->pr($e) ;

                    $o.=$this->render_element($e);
                }
            }
            return $o;
        }

        function render()
        {

            $o='';
            $o.=$this->render_validation_massages();
            $o.=$this->render_form_open( );
            $o.=$this->render_all_elements();

            $o.=$this->render_form_close();
            $o.=$this->render_js_validator();
            TWS::clearFlashData(); // form is rendered - time to clear flash data out
            return $o;
        }

        function _getFieldValue($e)
        {
            $val=TWS::getFlash('formdata', $e['name']);

            if ($val == false)
            { // no flash value
                if (isSet($e['value']))
                {
                    return $e['value'];
                }
            }
            else
            {
                return $val;
            }
        }

        function render_hidden($e)
        {
            $o='<input  type="hidden" name="' . $e['name'] . '" value="' . $this->_getFieldValue($e) . '" >';
            return $o;
        }

        function render_element($e)
        {
            $renderMethod='render_' . $e['type'];
            //$this->pr($renderMethod);
            $input=$this->$renderMethod($e);

            // Not all form elements get formatted wit TPL
            $selfRenderElements=array('checkbox');

            if (in_array($e['type'], $selfRenderElements))
            {
                return $input;
            }

            $tpl['horizontal']=
            <<<EOT
            
            <div class="form-group">
            <label  for="{$e['id']}">{$e['label']}</label>
            
            {$input}
            
            </div>
                      
EOT;

            return $tpl[$this->layout];
        }

        function render_date($e) { 
            return $this->render_text($e); 
        }

        function render_text($e)
        {
            // type is sett by e['type'] to allow this reneder to be used for date, email etc types
            $o = '<input class="form-control" type="' . $e['type'] . '" name="' . $e['name'] . '" id="' . $e['name'] . '" value="'
            . htmlspecialchars(TWS::getFlash('formdata', $e['name']),ENT_QUOTES) . '" ' . $e['attributes'] . ' >';
            
            if(isSet($e['help-inline']) && !empty($e['help-inline'])){
               $o .=  '<span class="help-inline">'.$e['help-inline'].'</span>';
            } 
            if( isSet($e['help-block']) && !empty($e['help-block']) ){
                $o .=  '<span class="help-block">'.$e['help-block'].'</span>';
            }
            return $o;

            
        }

        function render_submit($e)
        {
            $o='<input  type="submit" name="' . $e['name'] . '" value="' . $e['value'] . '"  ' . $e['attributes'] . ' >';
            return $o . "\n";
        }

        function render_textarea($e)
        {
            $o='<textarea class="form-control" name="' . $e['name'] . '" '.$e['attributes'].' >' .htmlspecialchars(TWS::getFlash('formdata', $e['name']),ENT_QUOTES) . '</textarea>';
            return $o . "\n";
        }

        function render_select($e)
        {
            $o='<select class="form-control" name="' . $e['name'] . '" '.$e['attributes'].' >' . "\n";

            if (isSet($e['options']) && is_array($e['options']) && count($e['options']) > 0)
            {
                foreach ($e['options'] as $k => $v)
                {
                    $selected = $k == TWS::getFlash('formdata', $e['name']) ? ' selected="selected" ' : '';
                    $o.='<option ' . $selected . ' value="' . $k . '" >' . $v . '</option>' . "\n";
                }
            }

            $o.='</select>';
            return $o . "\n";
        }

        function render_multi_select($e)
        {
            $size=
            isSet($e['multi_select_size']) && (int)$e['multi_select_size'] > 0 ? $e['multi_select_size'] : 3; // default
            $curVals=TWS::getFlash('formdata', $e['name']);
            //$this->pr($curVals); exit;
            $o='<select class="form-control" multiple="multiple" name="' . $e['name'] . '[]" size="' . $size . '" '.$e['attributes'].' >' . "\n";
            ;

            if (isSet($e['options']) && is_array($e['options']) && count($e['options']) > 0)
            {
                foreach ($e['options'] as $k => $v)
                {
                    if (is_array($curVals) && count($curVals) > 0)
                    {
                        $selected=in_array($k, $curVals) ? ' selected="selected" ' : '';
                    }

                    $o.='<option ' . $selected . ' value="' . $k . '" >' . $v . '</option>' . "\n";
                }
            }

            $o.='</select>';
            return $o . "\n";
        }

        function render_checkbox($e)
        {
            $curVal=TWS::getFlash('formdata', $e['name']);
            $checked = $curVal ? ' checked ':'';
            $tpl['horizontal']=
            <<<EOT
            
            <div class="form-group">
            <div class="checkbox">
            <label>
            <input  {$checked} type="checkbox" name="{$e['name']}" value="1" {$e['attributes']} >{$e['label']}
            </label>
            </div>
            </div>
                      
EOT;
            return $tpl[$this->layout];
        }

        function render_radio($e)
        {
            // TODO  4 -o owner -c task: Add RADIO form element render
            $o=$this->pr($e, 0);
            return $o;
        }

        function render_label($e = '')
        {
            if (isSet($e['label']) || !empty($e['label']))
            {
                $o='<label for="'.$e['id'].'">' . $e['label'] . '</label>';
                return $o . "\n";
            }
        }

        function savePostToSession() {

            TWS::flash('formdata', $_POST); }

        function pr($a, $print = 1)
        {
            $html='<pre class="prettyprint linenums" >';
            $html.=print_r($a, true);
            $html.='</pre>';

            if ($print)
            {
                echo $html;
            }
            else
            {
                return $html;
            }
        }
    }
?>