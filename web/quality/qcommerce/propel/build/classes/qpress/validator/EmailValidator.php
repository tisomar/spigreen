<?php
class EmailValidator implements BasicValidator
{
    public function isValid(ValidatorMap $map, $str)
    {
        return (filter_var($str, FILTER_VALIDATE_EMAIL) != false);
    }
}
