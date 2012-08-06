<?php

/**
 * Returns an Array with the Names of registered Sendfile Extensions Classes
 * @return array 
 */
function rex_com_mediaaccess_getExtensionsSendfile()
{
  global $REX;

  $classes = array();

  foreach($REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile_dir'] as $path)
  {
    if($dir = opendir($path))
    {
      while($file = readdir($dir))
      {
        if(!is_dir($file))
        {
          $classname = explode(".", $file);
          $class = $classname[1];

          if(file_exists($path.$file))
          {
            include_once($path.$file);
            $classes[] = $class;
          }
        }
      }
      closedir($dir);
    }
  }

  return $classes;
}


/**
 * Updates /files/.htaccess file according to user config
 * @return boolean
 */
function rex_com_mediaaccess_htaccess_update()
{
  global $REX;
  
  $unsecure_fileext = implode('|',explode(',',$REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext']));
  $get_varname = $REX['ADDON']['community']['plugin_mediaaccess']['request']['file'];
  
  ## build new content
  $new_content = '### MEDIAACCESS'.PHP_EOL;
  $new_content .= 'RewriteCond %{HTTPS} off'.PHP_EOL;
  $new_content .= 'RewriteCond %{REQUEST_URI} !files/.*/.*'.PHP_EOL;
  $new_content .= 'RewriteCond %{REQUEST_URI} !files/(.*).('.$unsecure_fileext.')$'.PHP_EOL;
  $new_content .= 'RewriteRule ^(.*)$ http://%{HTTP_HOST}/?'.$get_varname.'=\$1 [R=301,L]'.PHP_EOL;
  $new_content .= 'RewriteCond %{HTTPS} on'.PHP_EOL;
  $new_content .= 'RewriteCond %{REQUEST_URI} !files/.*/.*'.PHP_EOL;
  $new_content .= 'RewriteCond %{REQUEST_URI} !files/(.*).('.$unsecure_fileext.')$'.PHP_EOL;
  $new_content .= 'RewriteRule ^(.*)$ https://%{HTTP_HOST}/?'.$get_varname.'=\$1 [R=301,L]'.PHP_EOL;
  $new_content .= '### /MEDIAACCESS'.PHP_EOL;
  
  ## write to htaccess
  $path = $REX['HTDOCS_PATH'].'files/.htaccess';
  $old_content = rex_get_file_contents($path);
  
  if(preg_match("@(### MEDIAACCESS.*### /MEDIAACCESS)@s",$old_content) == 1)
  {  
    $new_content = preg_replace("@(### MEDIAACCESS.*### /MEDIAACCESS)@s", $new_content, $old_content);
    return rex_put_file_contents($path, $new_content);
  }
  
  return false;
}


/**
 * Copy File from source to target
 * returns true on success
 * @param string $file
 * @param string $source
 * @param string $target
 * @return boolean
 */
function rex_com_mediaccess_copyfile($file, $source, $target)
{
  global $REX;

  if(!rex_is_writable($target))
  {
    echo rex_warning('Keine Schreibrechte für das Verzeichnis "'.$target.'" !');
    return false;
  }

  if(!is_file($source.$file))
  {
    echo rex_warning('Datei "'.$source.$file.'" ist nicht vorhanden und kann nicht kopiert werden!');
    return false;
  }

  if(is_file($target.$file))
  {
    if(!rename($target.$file,$target.date("d.m.y_H.i.s_").$file))
    {
      echo rex_warning('Datei "'.$target.$file.'" konnte nicht umbenannt werden!');
      return false;
    }
  }

  if(!copy($source.$file,$target.$file))
  {
    echo rex_warning('Datei "'.$target.$file.'" konnte nicht geschrieben werden!');
    return false;
  }

  if(!chmod($target.$file,$REX['FILEPERM']))
  {
    echo rex_warning('Rechte für "'.$target.$file.'" konnten nicht gesetzt werden!');
    return false;
  }

  echo rex_info('Datei "'.$target.$file.'" wurde erfolgreich angelegt.');
  return true;
}