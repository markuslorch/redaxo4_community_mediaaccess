<?php
/**
 * Plugin Media-Access - rex_com_mediaaccess_sendfile_xsendfile class
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
*/

class rex_com_mediaaccess_sendfile_xsendfile extends rex_com_mediaaccess_sendfile
{
  function rex_com_mediaaccess_sendfile_xsendfile($oomedia)
  {
    global $REX;
    
    $this->MEDIA = $oomedia;
    
    $this->filepath = $REX['MEDIAFOLDER'];
    $this->filename = $this->MEDIA->getFileName();
    $this->fullpath = $this->filepath.'/'.$this->filename;
  }

  function send()
  {
    global $REX;

    header('Content-type: application/octet-stream');
    header('Content-disposition: attachment; filename="'.$this->filename.'"');
    header('X-SendFile: '.$this->fullpath);
  }

  function getName()
  {
    return 'Apache xsendfile';
  }

  function getDescription()
  {
    return '<i>Sends file via Apache mod xsendfile to Browser</i> / Diese Einstellung aktiviert die Unterstützung für XSendFile (z.B. mod_xsendfile für Apache). Aktivieren sie diese Option, wenn ihr Server dies unterstützt und Sie mit der Funktionsweise vertraut sind. Beachten Sie, dass XSendFile normalerweiße nicht zur Verfügung steht und eine Aktivierung dieser Option in diesem Fall die Auslieferungen von Dateien auf ihrer Seite verhindert.';
  }
}