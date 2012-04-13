<?php

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