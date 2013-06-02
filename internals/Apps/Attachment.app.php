<?php

class app_Attachment
{
    private static $cache;

    public static function add($entity, $entityId, SK_TemporaryFile $file)
    {
        $dto = new AttachmentDto();
        $dto->label = $file->getFileName();
        $dto->extension = $file->getExtension();
        $dto->entityId = $entityId;
        $dto->entity = $entity;
        $dto->size = $file->getSize();
        
        if (!self::save($dto))
        {
            return null;
        }
        
        $dir = self::getImageDir($dto);
        $file->move($dir);
        
        return $dto;
    }
    
    private static function getImageDir(AttachmentDto $dto)
    {
        return DIR_USERFILES . 'attachment_' . $dto->id . '.' . $dto->extension;
    }
    
    public static function getImageUrl($dto)
    {
        return URL_USERFILES . 'attachment_' . $dto->id . '.' . $dto->extension;
    }
    
    private static function save(AttachmentDto $dto)
    {
        $fields = array();
        foreach ($dto as $field => $value) 
        {
            if ($value !== null) {
                $fields[] = SK_MySQL::placeholder("`$field`='?'", $value);
            }
        }
        
        $query = "REPLACE INTO `" . TBL_ATTACHMENT . "` SET " . implode(', ', $fields);
        SK_MySQL::query($query);
        $dto->id = SK_MySQL::insert_id();
        
        return (bool) $dto->id;
    }
    
    public static function getList($entity, $entityId)
    {
        if ( !isset(self::$cache[$entity][$entityId]) )
        {
            $query = SK_MySQL::placeholder(
                "SELECT * FROM `" . TBL_ATTACHMENT . "` WHERE `entityId`=? AND `entity`='?'"
            , $entityId, $entity);

            $out = array();
            $result = SK_MySQL::query($query);
            while ($item = $result->fetch_object('AttachmentDto'))
            {
                $out[] = $item;
            }

            self::$cache[$entity][$entityId] = $out;
        }
        else
        {
            $out = self::$cache[$entity][$entityId];
        }

        return $out;
    }

    public static function getListByEntityIdList($entity, array $entityIdList)
    {
        if ( empty($entityIdList) )
        {
            return array();
        }

        $out = array();
        $notCachedList = array();
        foreach ( $entityIdList as $id )
        {
            if ( !isset(self::$cache[$entity][$id]) )
            {
                $notCachedList[$id] = $id;
                self::$cache[$entity][$id] = array();
            }
            else
            {
                $out[$id] = self::$cache[$id];
            }
        }

        if ( !empty($notCachedList) )
        {
            $query = SK_MySQL::placeholder(
                "SELECT * FROM `" . TBL_ATTACHMENT . "` WHERE `entityId` IN (?@) AND `entity`='?'"
            , $notCachedList, $entity);


            $result = SK_MySQL::query($query);
            while ($item = $result->fetch_object('AttachmentDto'))
            {
                $out[$item->entityId][] = $item;
                self::$cache[$entity][$item->entityId][] = $item;
            }
        }

        return $out;
    }

    public static function delete($id)
    {
        $query = SK_MySQL::placeholder(
            "DELETE FROM `" . TBL_ATTACHMENT . "` WHERE `id`=?"
        , $id);
        
        SK_MySQL::query($query);
    }
}

class AttachmentDto
{
    public $id;
    public $entity;
    public $entityId;
    public $label;
    public $extension;
    public $size;
}