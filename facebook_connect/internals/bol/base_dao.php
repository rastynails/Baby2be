<?php

abstract class FBC_BaseDao
{
    abstract public function getDtoClassName();
    abstract public function getTableName();

    public function findAll()
    {
        $query = "SELECT * FROM `" . $this->getTableName() . "`";

        return $this->fetchObjectArray($query);
    }

    public function save($dto)
    {
        $tmp = array();
        foreach ($dto as $key => $value)
        {
	    if ( $value === null )
	    {
		$tmp[] = "`$key` = NULL";
	    }
	    else
	    {
		$tmp[] = SK_MySQL::placeholder("`$key` = '?'", $value);
	    }
        }

        $setSql = implode(', ', $tmp);

        if ($dto->id > 0)
        {
            SK_MySQL::query(
            	SK_MySQL::placeholder("UPDATE `" . $this->getTableName() . "` SET $setSql WHERE `id`=?", $dto->id));
        }
        else
        {
            SK_MySQL::query("INSERT INTO `" . $this->getTableName() . "` SET $setSql");
            $dto->id = SK_MySQL::insert_id();
        }

        return $dto;
    }

    protected function fetchObjectArray($query)
    {
        $r = SK_MySQL::query($query);
        $out = array();
        while ($item = $r->fetch_object($this->getDtoClassName()))
        {
            $out[] = $item;
        }

        return $out;
    }

    protected function fetchArray($query)
    {
        $r = SK_MySQL::query($query);
        $out = array();
        while ($item = $r->fetch_assoc())
        {
            $out[] = $item;
        }

        return $out;
    }

    protected function fetchObject($query)
    {
        return SK_MySQL::query($query)->fetch_object($this->getDtoClassName());
    }
}