<?php

class FBC_FC_Username extends FBC_ConverterBase
{
    private $tryCount = 1;

    public function convert($question, $fbField, $value)
    {
        $this->tryCount++;
        list($fn, $ln) = explode(' ', strtolower($value));
        $username = $fn . substr($ln, 0, 1);

        if ($this->isUsernameExists($username))
        {
            return $this->convert($question, $fbField, $username . $this->tryCount);
        }

        return $username;
    }

    private function isUsernameExists($username)
    {
        $query = SK_MySQL::placeholder("SELECT COUNT(*) FROM `" . TBL_PROFILE . "` WHERE `username`='?'", $username);

        return (bool) SK_MySQL::query($query)->fetch_cell();
    }
}

class FBC_FC_TextFieldConverter extends FBC_ConverterBase
{
    public function convert($question, $fbField, $value)
    {
        return $value;
    }
}

class FBC_FC_Date extends FBC_ConverterBase
{
    public function convert($question, $fbField, $value)
    {
        if (empty($value))
        {
            return null;
        }

        $date = explode('/', $value);
        $year = empty($date[2]) ? 0 : $date[2];
        // YYYY/MM/DD
        return $year . '/' . $date[0] . '/' . $date[1];
    }
}

class FBC_FC_Picture extends FBC_ConverterBase
{
    public function convert($question, $fbField, $value)
    {
        $dir = DIR_TMP_USERFILES;
        $fileName = $dir . 'tmp_pic_' . md5($question . $value . time()) . '.jpg';
        if (copy($value, $fileName))
        {
            return $fileName;
        }

        return null;
    }
}

class FBC_FC_Sex extends FBC_ConverterBase
{
    public function convert($question, $fbField, $value)
    {
        if (empty($value))
        {
            return 1;
        }

        $sexAliasing = SK_Config::section('facebook_connect')->sex_aliasing;

        return empty($sexAliasing->$value) ? 1 : $sexAliasing->$value;
    }
}

