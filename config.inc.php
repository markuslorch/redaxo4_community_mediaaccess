<?php
/**
* Plugin Media-Access
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
* @version 1.0
* 
* Some parts are inspired from rexseo - thx jeandeluxe ;)
*/

/*
 * Options
 */

// --- DYN
$REX['ADDON']['community']['plugin_mediaaccess']['xsendfile'] = 0;
$REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext'] = "jpeg,jpg,png,gif,ico,css,js,swf";
// --- /DYN

## hidden option :)
$REX['ADDON']['community']['plugin_mediaaccess']['request']['file'] = 'file';

// --- END OF CONFIG ---
// --- DON'T CHANGE ANYTHING BELOW THIS LINE ---

/*
* Loading Plugin
*/
if ($REX["REDAXO"] && $REX['USER'])
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
  
  ## Register extension Point for rex_com_mediaaccess function
  rex_register_extension('ADDONS_INCLUDED', 'rex_com_mediaaccess_EP');

  ## register extension points if needed
  $unsecure_fileext = explode(',',$REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext']);
  $image_fileext = array('jpeg', 'jpg', 'gif', 'png');
  if(count(array_intersect($image_fileext, $unsecure_fileext)) < count($image_fileext))
  {
    rex_register_extension('IMAGE_SEND', 'rex_com_mediaaccess_EP_images'); //Image-Manager & Image-Manager EP
    rex_register_extension('IMAGE_RESIZE_SEND', 'rex_com_mediaaccess_EP_images'); //Image-Resize
  }
}
