<?php
class FBC_FieldDao extends FBC_BaseDao
{
    public function getDtoClassName()
    {
        return 'FBC_Field';
    }

    public function getTableName()
    {
        return DB_TBL_PREFIX . 'fbconnect_field';
    }
    
    /**
     * 
     * @param $question
     * @return FBC_Field
     */
    public function findByQuestion($question)
    {
        $query = SK_MySQL::placeholder("SELECT * FROM `" . $this->getTableName() . "` WHERE `question`='?'", $question);
        
        return $this->fetchObject($query);
    }
    
    public function findListByQuestionList($questionList)
    {
        $sqlList = implode(',', $questionList);
        $query = "SELECT * FROM `" . $this->getTableName() . "` WHERE `question` IN ($sqlList)";
        
        return $this->fetchObjectArray($query);
    }
    
    public function deleteByQuestion($question)
    {
        $query = SK_MySQL::placeholder("DELETE FROM `" . $this->getTableName() . "` WHERE `question`='?'", $question);
        
        SK_MySQL::query($query);
    }
}