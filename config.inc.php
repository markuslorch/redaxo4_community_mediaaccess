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

$REX['ADDON']['community']['plugin_mediaaccess']['unsafe_fileext'] = 'jpg,jpeg,gif,png,ico,js,css,swf'; //seperate with coma, without dot
$REX['ADDON']['community']['plugin_mediaaccess']['xsendfile'] = false; // Activate this Option if you want use Apache mod_xsendfile to send files to browser
$REX['ADDON']['community']['plugin_mediaaccess']['request']['file'] = 'file';

// --- DON'T CHANGE THE FOLLOWING LINES ---
$REX['ADDON']['community']['plugin_mediaaccess']['unsafe_fileext'] = explode(',',$REX['ADDON']['community']['plugin_mediaaccess']['unsafe_fileext']);

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
  
  ## Check perms for Image-Manager
  function rex_com_mediaaccess_EP_images($params)
  {
    global $REX;
    
    if($params['extension_point'] == 'IMAGE_RESIZE_SEND')
      $file = $params['filename'];
    else
      $file = $params['img']['file'];
    
    ## get auth - isn't loaded yet
    require_once $REX["INCLUDE_PATH"]."/addons/community/plugins/auth/inc/auth.php"; 
    
    $media = rex_com_mediaaccess::getMediaByFilename($file);
    if($media->checkPerm())
      return true;

    return false;
  }
  
  ## register extension points if needed
  $image_fileext = array('jpeg', 'jpg', 'gif', 'png');
  if(count(array_intersect($image_fileext, $REX['ADDON']['community']['plugin_mediaaccess']['unsafe_fileext'])) < count($image_fileext))
  {
    rex_register_extension('IMAGE_SEND', 'rex_com_mediaaccess_EP_images'); //Image-Manager & Image-Manager EP
    rex_register_extension('IMAGE_RESIZE_SEND', 'rex_com_mediaaccess_EP_images'); //Image-Resize
  }
}
?>