<?php 

namespace Core;

class Validation 
{
    private $fields;
    private $errors=[];
    private $breakSwitch=false;


    public function validate(array $data)
    {
        foreach($data as $name => $value)
        {
            $roles = explode("|",$value);
            foreach($roles as $role)
            {
                switch ($role) 
                {
                    case 'required':
                        $this->valRequired($name,$_REQUEST[$name]);
                        break;
                    case 'string':
                        $this->valString($name,$_REQUEST[$name]);
                        break;
                    case 'email':
                        $this->valEmail($name,$_REQUEST[$name]);
                        break;
                    case 'number':
                        $this->valNumber($name,$_REQUEST[$name]);
                        break;
                    case 'float':
                        $this->valFloat($name,$_REQUEST[$name]);
                        break;
                    case 'file':
                        $this->valFile($name);
                        break;
                }

                if(preg_match('/^min:[1-9]+$/',$role))
                {
                    $this->valMin($name,$_REQUEST[$name],$role);
                }

                if(preg_match('/^max:[1-9]+$/',$role))
                {
                    $this->valMin($name,$_REQUEST[$name],$role);
                }


                if($this->breakSwitch)
                {
                    $this->breakSwitch=false;
                    break;
                }
            }
        }
    }


    /**
     * check if field has data or not 
     * @param $field => name if field 
     * @param $value => value of filed 
     * @return mixid
     */
    private function valRequired($field,$value)
    {
        $value = trim($value);
        if(strlen($value) == 0)
        {
            $this->errors[] = $field . " : Is Required ";
            $this->breakSwitch = true;
        }

    }


    // check if value is string or not 
    private function valString($field,$value)
    {
        if (!preg_match('/^[A-Za-z0-9 _-]*$/', $value)) 
        {
            $this->errors[] = $field . " : Must Be String ";
            $this->breakSwitch = true;
        }
    }

    // check from minimum value 
    private function valMin($field,$value,$role)
    {
        $r = explode(":",$role);
        $value = trim($value);
        $n = (int) $r[1];
        if(strlen($value) < $n)
        {
            $this->errors[] = $field . " : Must Be Grater Than : {$n} Chars ";
            $this->breakSwitch = true;
        }
    }

    // check from maximum value 
    private function valMax($field,$value,$role)
    {
        $r = explode(":",$role);
        $val = trim($r[0]);
        $n = (int) $r[1];
        if(strlen($val) < $n)
        {
            $this->errors[] = $field . " : Must Be Smaller Than : {$n} Chars ";
            $this->breakSwitch = true;
        }
    }



    // check from email
    private function valEmail($field,$value)
    {
        if(!filter_var($value,FILTER_VALIDATE_EMAIL))
        {
            $this->errors[] = $field . " : Must Be an Email";
            $this->breakSwitch = true;
        }
    }

    // check from number
    private function valNumber($field,$value)
    {
        if(!is_int($value))
        {
            $this->errors[] = $field . " : Must Be Number";
            $this->breakSwitch = true;
        }
    }


    // check from float
    private function valFloat($field,$value)
    {
        if(!is_float($value))
        {
            $this->errors[] = $field . " : Must Be Float Number";
            $this->breakSwitch = true;
        }
    }



    // check from file
    private function valFile($field)
    {
        if(!isset($_FILES[$field]['name']))
        {
            $this->errors[] = $field . " : Is Required";
            $this->breakSwitch = true;
        }
    }






    // check if validation is clear or not
    public function check()
    {
        if(count($this->errors))
        {
            return false;
        }
        else 
        {
            return true;
        }
    }

    // show errors of validation
    public function getErrors()
    {
        return $this->errors;
    }

}