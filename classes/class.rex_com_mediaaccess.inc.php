<?php
/**
* Plugin Media-Access - rex_com_mediaaccess class
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
*/

class rex_com_mediaaccess
{
  var $MEDIA;
  var $extension_sendfile;

  function rex_com_mediaaccess($oomedia)
  {
    global $REX;
        
    $this->MEDIA = $oomedia;
    $this->setExtensionSendfile($REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile']);
  }

  function getMediaByFilename($filename)
  {
    $oomedia = OOMedia::getMediaByFileName($filename);

    return new rex_com_mediaaccess($oomedia);
  }
  
  function setExtensionSendfile($classname)
  {
    $this->extension_sendfile = $classname;
  }
  
  function getExtensionSendfile()
  {
    global $REX;
    
    foreach($REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile_dir'] as $path)
      if(@include_once($path.'class.'.$this->extension_sendfile.'.inc.php'))
        return new $this->extension_sendfile($this->MEDIA);
  }

  function send()
  {
    global $REX;
    
    require_once $REX["INCLUDE_PATH"].'/addons/community/plugins/mediaaccess/classes/extensions_sendfile/class.'.$this->extension_sendfile.'.inc.php';
    
    while(ob_get_level())
      ob_end_clean();
    
    $sendclass = $this->getExtensionSendfile();
    $sendclass->send();
    
    exit;
  }

  function checkPerm()
  {
    global $REX;
    
    ## if no access rule - grant access
    if($this->MEDIA->getValue('med_com_mediaaccess_comusers') == '' || $this->MEDIA->getValue('med_com_mediaaccess_comusers') == '||')
      if($this->MEDIA->getValue('med_com_groups') == '' || $this->MEDIA->getValue('med_com_groups') == '||')
        return true;

    ## true if user is in one or more required groups
    if(isset($REX['COM_USER']))
    {
      if($this->MEDIA->getValue('med_com_mediaaccess_comusers') != '' && $this->MEDIA->getValue('med_com_mediaaccess_comusers') != '||')
        return true;
      
      $media_groups = explode("|",$this->MEDIA->getValue('med_com_groups'));
      $user_groups = explode(",",$REX["COM_USER"]->getValue("rex_com_group"));

      foreach($media_groups as $group)
        if($group != "" && in_array($group,$user_groups))
          return true;
    }
    
    return false;
  }
  
}