<?php
/**
* Plugin Media-Access
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
* @version 1.0
*/

/*
 * Options
 */

$REX['ADDON']['community']['plugin_mediaaccess']['xsendfile'] = false; // Activate this Option if you want use Apache mod_xsendfile to send files to browser
$REX['ADDON']['community']['plugin_mediaaccess']['request']['file'] = 'file';

/*
* Loading Plugin
*/
if ($REX["REDAXO"] && $REX['USER'])
{
  ## Include lang files
  if(isset($I18N) && is_object($I18N))
  $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/mediaaccess/lang');

  //$REX['ADDON']['community']['SUBPAGES'][] = array('plugin.media_access','Media Access');
}
else
{
  ## only required in frontend
  include $REX["INCLUDE_PATH"]."/addons/community/plugins/mediaaccess/classes/class.rex_com_mediaaccess.inc.php";

  ## global function for ADDONS_INCLUDED Extension Point
  function rex_com_mediaaccess_EP()
  {
    global $REX;
  
    $file = rex_request($REX['ADDON']['community']['plugin_mediaaccess']['request']['file'], 'string');
  
    if($file)
    {
      $media = rex_com_mediaaccess::getMediaByFilename($file);
      $media->setXsendfile($REX['ADDON']['community']['plugin_mediaaccess']['xsendfile']);
  
      if($media->checkPerm())
        $media->send();
      else
        header('Location: '.rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_withoutperm'],'',array($REX['ADDON']['community']['plugin_auth']['request']['ref'] => urlencode($_SERVER['REQUEST_URI'])),'&'));
        //echo rex_getUrl($REX['ADDON']['community']['plugin_auth']['article_withoutperm'],'',array($REX['ADDON']['community']['plugin_auth']['request']['ref'] => urlencode($_SERVER['REQUEST_URI'])) );

      exit;
    }
  }
  
  ## Register extension Point for rex_com_mediaaccess function
  rex_register_extension('ADDONS_INCLUDED', 'rex_com_mediaaccess_EP');
}
?>