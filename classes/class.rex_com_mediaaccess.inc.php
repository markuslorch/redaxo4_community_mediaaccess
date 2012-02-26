<?php
/**
* Plugin Media-Access - rex_com_mediaaccess class
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
*/

class rex_com_mediaaccess
{
  var $filename;
  var $filepath;
  var $fullpath;
  var $xsendfile = false;
  var $MEDIA;

  function rex_com_mediaaccess($oomedia)
  {
    global $REX;

    $this->MEDIA = $oomedia;

    $this->filepath = $REX['MEDIAFOLDER'];
    $this->filename = $this->MEDIA->getFileName();
    $this->fullpath = $this->filepath.'/'.$this->filename;
  }

  function getMediaByFilename($filename)
  {
    $oomedia = OOMedia::getMediaByFileName($filename);

    return new rex_com_mediaaccess($oomedia);
  }

  function send()
  {
    if($this->xsendfile)
    {
      header('Content-type: application/octet-stream');
      header('Content-disposition: attachment; filename="'.$this->filename.'"');
      header('X-SendFile: '.$this->fullpath);
    }
    else
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
    
    exit;
  }

  function checkPerm()
  {
    global $REX;
    
    ## if no access rule - grant access
    if($this->MEDIA->getValue('med_com_groups') == '' || $this->MEDIA->getValue('med_com_groups') == '||')
      return true;

    ## true if user is in one or more required groups
    if(isset($REX['COM_USER']))
    {
      $media_groups = explode("|",$this->MEDIA->getValue('med_com_groups'));
      $user_groups = explode(",",$REX["COM_USER"]->getValue("rex_com_group"));

      foreach($media_groups as $group)
        if($group != "" && in_array($group,$user_groups))
          return true;
    }

    return false;
  }

  /*
  * Use this option if mod_xsenfile on Apache is available
  */
  function setXsendfile($option = true)
  {
    $this->xsendfile = $option;
  }
}