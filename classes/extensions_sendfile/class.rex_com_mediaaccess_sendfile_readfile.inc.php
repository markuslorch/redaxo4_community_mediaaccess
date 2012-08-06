<?php
/**
 * Plugin Media-Access - rex_com_mediaaccess_sendfile_readfile class
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
*/

class rex_com_mediaaccess_sendfile_readfile extends rex_com_mediaaccess_sendfile
{
  function rex_com_mediaaccess_sendfile_readfile($oomedia)
  {
    global $REX;
    
    $this->MEDIA = $oomedia;
    
    $this->filepath = $REX['MEDIAFOLDER'];
    $this->filename = $this->MEDIA->getFileName();
    $this->fullpath = $this->filepath.'/'.$this->filename;
  }

  function send()
  {
    header("Pragma: public");
    header("Expires: 0");
    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
    header("Content-Type: application/force-download");
    header("Content-Type: application/octet-stream");
    header("Content-Type: application/download");
    header("Content-Transfer-Encoding: binary");
    header("Content-Length: ".$this->MEDIA->getSize());
    header("Content-Disposition: attachment; filename=".$this->filename.";");
    
    @readfile($this->fullpath);
  }

  function getName()
  {
    return 'readfile()';
  }

  function getDescription()
  {
    return 'Sends file via php function readfile() to browser';
  }
}