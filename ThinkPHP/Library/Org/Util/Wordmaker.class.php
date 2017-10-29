<?php
/***********************************************************************
Class:        Mht File Maker  生成mht ,生成word 时用到
Version:      1.2 beta
***********************************************************************/
class Wordmaker{
    var $config = array();
    var $headers = array();
    var $headers_exists = array();
    var $files = array();
    var $boundary;
    var $dir_base;
    var $page_first;
 
    function MhtFile($config = array()){
 
    }
 
    function SetHeader($header){
        $this->headers[] = $header;
        $key = strtolower(substr($header, 0, strpos($header, ':')));
        $this->headers_exists[$key] = TRUE;
    }
 
    function SetFrom($from){
        $this->SetHeader("From: $from");
    }
 
    function SetSubject($subject){
        $this->SetHeader("Subject: $subject");
    }
 
    function SetDate($date = NULL, $istimestamp = FALSE){
        if ($date == NULL) {
            $date = time();
        }
        if ($istimestamp == TRUE) {
            $date = date('D, d M Y H:i:s O', $date);
        }
        $this->SetHeader("Date: $date");
    }
 
    function SetBoundary($boundary = NULL){
        if ($boundary == NULL) {
            $this->boundary = '--' . strtoupper(md5(mt_rand())) . '_MULTIPART_MIXED';
        } else {
            $this->boundary = $boundary;
        }
    }
 
    function SetBaseDir($dir){
        $this->dir_base = str_replace("\\", "/", realpath($dir));
    }
 
    function SetFirstPage($filename){
        $this->page_first = str_replace("\\", "/", realpath("{$this->dir_base}/$filename"));
    }
 
    function AutoAddFiles(){
        if (!isset($this->page_first)) {
            exit ('Not set the first page.');
        }
        $filepath = str_replace($this->dir_base, '', $this->page_first);
        $filepath = 'http://mhtfile' . $filepath;
        $this->AddFile($this->page_first, $filepath, NULL);
        $this->AddDir($this->dir_base);
    }
 
    function AddDir($dir){
        $handle_dir = opendir($dir);
        while ($filename = readdir($handle_dir)) {
            if (($filename!='.') && ($filename!='..') && ("$dir/$filename"!=$this->page_first)) {
                if (is_dir("$dir/$filename")) {
                    $this->AddDir("$dir/$filename");
                } elseif (is_file("$dir/$filename")) {
                    $filepath = str_replace($this->dir_base, '', "$dir/$filename");
                    $filepath = 'http://mhtfile' . $filepath;
                    $this->AddFile("$dir/$filename", $filepath, NULL);
                }
            }
        }
        closedir($handle_dir);
    }
 
    function AddFile($filename, $filepath = NULL, $encoding = NULL){
        if ($filepath == NULL) {
            $filepath = $filename;
        }
        $mimetype = $this->GetMimeType($filename);
        $filecont = file_get_contents($filename);
        $this->AddContents($filepath, $mimetype, $filecont, $encoding);
    }
 
    function AddContents($filepath, $mimetype, $filecont, $encoding = NULL){
        if ($encoding == NULL) {
            $filecont = chunk_split(base64_encode($filecont), 76);
            $encoding = 'base64';
        }
        $this->files[] = array('filepath' => $filepath,
                               'mimetype' => $mimetype,
                               'filecont' => $filecont,
                               'encoding' => $encoding);
    }
 
    function CheckHeaders(){
        if (!array_key_exists('date', $this->headers_exists)) {
            $this->SetDate(NULL, TRUE);
        }
        if ($this->boundary == NULL) {
            $this->SetBoundary();
        }
    }
 
    function CheckFiles(){
        if (count($this->files) == 0) {
            return FALSE;
        } else {
            return TRUE;
        }
    }
 
    function GetFile(){
        $this->CheckHeaders();
        if (!$this->CheckFiles()) {
            exit ('No file was added.');
        }
        $contents = implode("\r\n", $this->headers);
        $contents .= "\r\n";
        $contents .= "MIME-Version: 1.0\r\n";
        $contents .= "Content-Type: multipart/related;\r\n";
        $contents .= "\tboundary=\"{$this->boundary}\";\r\n";
        $contents .= "\ttype=\"" . $this->files[0]['mimetype'] . "\"\r\n";
        $contents .= "X-MimeOLE: Produced By Mht File Maker v1.0 beta\r\n";
        $contents .= "\r\n";
        $contents .= "This is a multi-part message in MIME format.\r\n";
        $contents .= "\r\n";
        foreach ($this->files as $file) {
            $contents .= "--{$this->boundary}\r\n";
            $contents .= "Content-Type: $file[mimetype]\r\n";
            $contents .= "Content-Transfer-Encoding: $file[encoding]\r\n";
            $contents .= "Content-Location: $file[filepath]\r\n";
            $contents .= "\r\n";
            $contents .= $file['filecont'];
            $contents .= "\r\n";
        }
        $contents .= "--{$this->boundary}--\r\n";
        return $contents;
    }
 
    function MakeFile($filename){
        $contents = $this->GetFile();
        $fp = fopen($filename, 'w');
        fwrite($fp, $contents);
        fclose($fp);
    }
 
    function GetMimeType($filename){
        $pathinfo = pathinfo($filename);
        switch ($pathinfo['extension']) {
            case 'htm': $mimetype = 'text/html'; break;
            case 'html': $mimetype = 'text/html'; break;
            case 'txt': $mimetype = 'text/plain'; break;
            case 'cgi': $mimetype = 'text/plain'; break;
            case 'php': $mimetype = 'text/plain'; break;
            case 'css': $mimetype = 'text/css'; break;
            case 'jpg': $mimetype = 'image/jpeg'; break;
            case 'jpeg': $mimetype = 'image/jpeg'; break;
            case 'jpe': $mimetype = 'image/jpeg'; break;
            case 'gif': $mimetype = 'image/gif'; break;
            case 'png': $mimetype = 'image/png'; break;
            default: $mimetype = 'application/octet-stream'; break;
        }
        return $mimetype;
    }
}
?>