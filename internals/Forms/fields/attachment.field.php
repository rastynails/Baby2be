<?php

class field_attachment extends fieldType_file
{
    public function setup(SK_Form $form)
    {
        $this->multifile = true;
        $this->max_files_num = 50;
        $this->max_file_size = 10 * 1024 * 1024;
        
        $this->default_allowed_extensions = array (
            'txt', 'doc', 'docx', 'sql', 'csv', 'doc', 'docx', 
            'jpg', 'jpeg', 'png', 'gif', 'bmp', 'psd', 'ai', 'rtf', 
            'avi', 'wmv', 'mp3', '3gp', 'flv', 'mkv', 'mpeg', 'mpg', 'swf',
            'zip', 'gz', '.tgz', 'gzip', '7z', 'bzip2', 'rar'
        );
        
        parent::setup($form);
    }
    
    public function preview( SK_TemporaryFile $tmp_file )
    {
        $src = $tmp_file->getURL();
        $label = $tmp_file->getFileName();
        
        $size = round($tmp_file->getSize() / 1024, 2);
        
        $output = <<<EOT
<div class="af_attachment_item">
    <span class="af_attachment_label">
        $label ($size KB)
    </span>
    <a class="delete_file_btn lbutton" href="javascript://">delete</a>
</div>
EOT;
        return $output;
    }
}