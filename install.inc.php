<?php
/**
* Plugin Media-Access - install.inc.php
* @author m.lorch[at]it-kult[dot]de Markus Lorch
* @author <a href="http://www.it-kult.de">www.it-kult.de</a>
*/

$install_success = true;
$install_source_path = $REX['INCLUDE_PATH'].'/addons/community/plugins/mediaaccess/install/';

/*
 * Starte installation
 */

## checking dependencies
if(!$ADDONSsic['plugins']['community']['status']['group'])
{
  echo rex_warning('Es wird das Plugin "group" benötigt!');
  $install_success = false;
}

## creating meta-info fields
if($install_success)
{
  $install_field = array();
  $install_field[] = array('title'=>'translate:com_mediaaccess_legend','name'=>'med_com_mediaccess_legend','type'=>'12');
  $install_field[] = array('title'=>'translate:com_mediaaccess_legend_groupname','name'=>'med_com_groups','type'=>'3','attributes'=>'multiple=multiple', 'params'=>'select name as label,id from '.$REX['TABLE_PREFIX'].'com_group order by label');
  
  $install_field_counter = 100;
  
  foreach($install_field as $field)
  {
    $sql_field = rex_sql::factory();
    $sql_field->setTable($REX['TABLE_PREFIX'].'62_params');
      
    foreach($field as $index => $value)
      $sql_field->setValue($index,$value);
  
    $sql_field->setValue('prior',$install_field_counter);
    $sql_field->setValue('validate',NULL);
    $sql_field->addGlobalCreateFields();
  
    $sql = rex_sql::factory();
    $sql->setQuery('select * from '.$REX['TABLE_PREFIX'].'62_params where name="'.$field['name'].'"');
    if ($sql->getRows()==1)
    {
      $sql_field->setWhere('name="'.$field['name'].'"');
      $sql_field->update();
    }
    else
      $sql_field->insert();
    
    ## adding to file table
    $sql->setQuery('SHOW COLUMNS FROM '.$REX['TABLE_PREFIX'].'file LIKE "'.$field['name'].'"');
    if ($sql->getRows()==0)
      $sql->setQuery('ALTER TABLE `'.$REX['TABLE_PREFIX'].'file` ADD `'.$field['name'].'` VARCHAR(255) NOT NULL');
    
    $install_field_counter++;
  }
  
  ## Prio neu sortieren // Metainfo
  rex_organize_priorities($REX['TABLE_PREFIX']. '62_params', 'prior', 'name LIKE "med_%"', 'prior, updatedate', 'field_id');
}

## Copy .htacces file
if($install_success)
{
  require_once $REX['INCLUDE_PATH'].'/addons/community/plugins/mediaaccess/functions/function.utils.inc.php';
  $install_success = rex_com_mediaccess_copyfile('.htaccess', $install_source_path, $REX['HTDOCS_PATH'].'files/');
}

## finish
if(!$install_success)
  $REX['ADDON']['installmsg']['mediaaccess'] = "Bei der Installation sind Fehler aufgetreten";
else
  $REX['ADDON']['install']['mediaaccess'] = true;

?>
