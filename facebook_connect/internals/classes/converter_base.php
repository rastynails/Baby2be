<?php

abstract class FBC_ConverterBase
{
    abstract public function convert($question, $fbField, $value);
}