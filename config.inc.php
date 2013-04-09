<?php
/**
* Plugin Media-Access
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
* @version 1.5
* 
* Some parts are inspired from rexseo - thx jeandeluxe ;)
*/

$REX['ADDON']['version']['mediaaccess'] = '1.5';
$REX['ADDON']['author']['mediaaccess'] = 'Markus Lorch';
$REX['ADDON']['supportpage']['mediaaccess'] = 'www.it-kult.de';

/*
 * Options
 */

// --- DYN
$REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile'] = "rex_com_mediaaccess_sendfile_readfile";
$REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext'] = "jpeg,jpg,png,gif,ico,css,js,swf";
// --- /DYN

## hidden options :)
$REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile_dir'] = array($REX["INCLUDE_PATH"].'/addons/community/plugins/mediaaccess/classes/extensions_sendfile/');
$REX['ADDON']['community']['plugin_mediaaccess']['request']['file'] = 'file';

// --- END OF CONFIG ---
// --- DON'T CHANGE ANYTHING BELOW THIS LINE ---


/*
* Loading Plugin
*/
## include abstract class for sendfile extensions
include $REX["INCLUDE_PATH"]."/addons/community/plugins/mediaaccess/classes/class.rex_com_mediaaccess_sendfile.inc.php";

if($REX["REDAXO"] && $REX['USER'])
{
  ## Include lang files
  if(isset($I18N) && is_object($I18N))
    $I18N->appendFile($REX['INCLUDE_PATH'].'/addons/community/plugins/mediaaccess/lang');

  ## Include libs
  require_once $REX['INCLUDE_PATH'].'/addons/community/plugins/mediaaccess/functions/function.utils.inc.php';

  ## register to community addon navigation
  $REX['ADDON']['community']['SUBPAGES'][] = array('plugin.mediaaccess','Mediaaccess');
}
else
{
  ## only required in frontend
  include $REX["INCLUDE_PATH"]."/addons/community/plugins/mediaaccess/classes/class.rex_com_mediaaccess.inc.php";
  include $REX['INCLUDE_PATH'].'/addons/community/plugins/mediaaccess/functions/function.extensions.inc.php';
  
  ## starts session if required
  if(session_id() == '')
    session_start();
  
  ## Register extension Point for rex_com_mediaaccess function
  if(isset($ADDONSsic['status']['rexseo']) && $ADDONSsic['status']['rexseo'])
    rex_register_extension('REXSEO_INCLUDED', 'rex_com_mediaaccess_EP');
  else
    rex_register_extension('ADDONS_INCLUDED', 'rex_com_mediaaccess_EP');

  ## register extension points if needed
  $unsecure_fileext = explode(',',$REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext']);
  $image_fileext = array('jpeg', 'jpg', 'gif', 'png');
  
  if(count(array_intersect($image_fileext, $unsecure_fileext)) < count($image_fileext) && (!isset($_SESSION[$REX['INSTNAME']]['UID']) || $_SESSION[$REX['INSTNAME']]['UID'] <= 0))
  {
    ## Image_Manager Hack
    rex_com_mediaaccess_ImageManager_checkPerm(rex_get('rex_img_file','string'), $ADDONSsic);
    ## rex_register_extension('IMAGE_SEND', 'rex_com_mediaaccess_EP_images'); //Image-Manager EP
    rex_register_extension('IMAGE_RESIZE_SEND', 'rex_com_mediaaccess_EP_images'); //Image-Resize
  }
}
