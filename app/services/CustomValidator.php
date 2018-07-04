<?php
class CustomValidator extends \Illuminate\Validation\Validator
{
	public function validateLikeRestricted($attribute, $value, $parameters)
    {
        $res_words = array_filter($parameters);
        foreach($res_words as $word)
       	{
       		if(strpos($value, $word) !== false) return false;
		}
       	return true;
    }

    public function validateMatchRestricted($attribute, $value, $parameters)
    {
    	$res_words = array_filter($parameters);
       	return (!in_array($value, $res_words));
    }

    public function ValidateIsValidOldPassword($attribute, $value, $parameters)
	{
		$user = Sentry::getUser();
		$old_password = $user->password;
		$bba_token = $user->bba_token;
		$temp = md5($value. $bba_token);
		if(md5($value. $bba_token) != $old_password)
		{
			return false;
		}
		return true;
	}

	public function validateIsValidUserId($attribute, $value, $parameters)
	{
		if($parameters[0] > 0)
		{
			return true;
		}
		return false;
	}
}
