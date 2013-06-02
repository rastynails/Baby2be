<?php

class app_TextFormatter
{
    const ENTITY_FORUM = 'forum';
    const ENTITY_MAILBOX = 'mailbox';
    const ENTITY_BLOG = 'blog';
    
    public static function addImage($profileId, $entity, SK_TemporaryFile $file, $label)
    {
        $dto = new TF_ImageDto();
        $dto->label = $label;
        $dto->extension = $file->getExtension();
        $dto->profileId = $profileId;
        $dto->entity = $entity;
        
        if (!self::saveImageDto($dto))
        {
            return null;
        }
        
        $dir = self::getImageDir($dto);
        $file->move($dir);
        
        app_Image::resize($dir, 700, 700);
        
        return $dto;
    }
    
    private static function saveImageDto(TF_ImageDto $dto)
    {
        $fields = array();
        foreach ($dto as $field => $value) 
        {
            if ($value !== null) {
                $fields[] = SK_MySQL::placeholder("`$field`='?'", $value);
            }
        }
        
        $query = "REPLACE INTO `" . TBL_TEXT_FORMATTER_IMAGE . "` SET " . implode(', ', $fields);
        SK_MySQL::query($query);
        $dto->id = SK_MySQL::insert_id();
        
        return (bool) $dto->id;
    }
    
    public static function getImageDir(TF_ImageDto $dto)
    {
        return DIR_USERFILES . 'text_formatter_image_' . $dto->id . '.' . $dto->extension;
    }
    
    public static function getImageUrl($dto)
    {
        return URL_USERFILES . 'text_formatter_image_' . $dto->id . '.' . $dto->extension;
    }
    
    public static function getImageList($profileId, $entity)
    {
        $query = SK_MySQL::placeholder(
            "SELECT * FROM `" . TBL_TEXT_FORMATTER_IMAGE . "` WHERE `profileId`=? AND `entity`='?'"
        , $profileId, $entity);
        
        $out = array();
        $result = SK_MySQL::query($query);
        while ($item = $result->fetch_object('TF_ImageDto'))
        {
            $out[] = $item; 
        }
        
        return $out;
    }
    
    public static function deleteImage($id)
    {
        $query = SK_MySQL::placeholder(
            "DELETE FROM `" . TBL_TEXT_FORMATTER_IMAGE . "` WHERE `id`=?"
        , $id);
        
        SK_MySQL::query($query);
    }
}

class TF_ImageDto
{
    public $id;
    public $profileId;
    public $label;
    public $entity;
    public $extension;
}