<?php

/**
 * Does the job on frontend
 */
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

/**
 * Checks perms for Image-Manager, Image-Manager EP and Image-Resize
 * @param array $params
 * @return boolean
 */
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