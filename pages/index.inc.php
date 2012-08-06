<?php
$info = '';
$warning = '';
$xsendfile_checked = '';

/*
 * Update confic.inc.php
 */
if(rex_request("func","string")=="update")
{
  ## get request parameters
  $REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext'] = rex_request("unsecure_fileext","string");
  $REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile'] = rex_request("extension_sendfile","string");

  ## build new config content
  $content = '
$REX[\'ADDON\'][\'community\'][\'plugin_mediaaccess\'][\'extension_sendfile\'] = "'.$REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile'].'";
$REX[\'ADDON\'][\'community\'][\'plugin_mediaaccess\'][\'unsecure_fileext\'] = "'.$REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext'].'";
';

  ## update files
  if(rex_replace_dynamic_contents($REX['INCLUDE_PATH'].'/addons/community/plugins/mediaaccess/config.inc.php', $content) !== false)
    if(rex_com_mediaaccess_htaccess_update())
      echo rex_info($I18N->msg('com_mediaaccess_settings_update'));
    else
      echo rex_warning($I18N->msg('com_mediaaccess_htaccess_failupdate'));      
  else
    echo rex_warning($I18N->msg('com_mediaaccess_settings_failupdate'));
}

/*
 * Formular output
 */

## building Drop-Down
function dropdown_sendfile()
{
  global $REX;
  $out = '<select class="rex-form-select" name="extension_sendfile">';
  
  foreach(rex_com_mediaaccess_getExtensionsSendfile() as $class)
  {
    $selected = '';
    
    if($REX['ADDON']['community']['plugin_mediaaccess']['extension_sendfile'] == $class)
      $selected = 'selected="selected"';
    
    $out .= '<option value="'.$class.'" '.$selected.'>'.$class::getName().'</option>';
  }
  $out .= '</select>';
  
  return $out;
}

## building Sendfile Descriptions
function describe_sendfile()
{
  global $REX;
  $out = '';
  
  foreach(rex_com_mediaaccess_getExtensionsSendfile() as $class)
     $out .= '<p><strong>'.$class::getName().'</strong><br/>'.$class::getDescription().'</p>';
  
  return $out;
}

echo '
	<div class="rex-form" id="rex-form-system-setup">
  	<form action="index.php" method="post">
    	<input type="hidden" name="page" value="community" />
    	<input type="hidden" name="subpage" value="plugin.mediaaccess" />
    	<input type="hidden" name="func" value="update" />
		
			<div class="rex-area-col-2">
				<div class="rex-area-col-a">
	
					<h3 class="rex-hl2">'.$I18N->msg("description").'</h3>
	
					<div class="rex-area-content">
<p class="rex-tx1">'.$I18N->msg("com_mediaaccess_settings_description").'</p>
<h3 class="rex-hl3">'.$I18N->msg("com_mediaaccess_settings_unsecure_fileext").'</h3>
<p class="rex-tx1">'.$I18N->msg("com_mediaaccess_help_unsecure_fileext").'</p>
<h3 class="rex-hl3">'.$I18N->msg("com_mediaaccess_settings_sendfile").'</h3>
<p class="rex-tx1">'.$I18N->msg("com_mediaaccess_help_sendfile").'</p>
'.describe_sendfile().'
					</div>
				</div>
			
				<div class="rex-area-col-b">
					
					<h3 class="rex-hl2">'.$I18N->msg("com_mediaaccess_settings").'</h3>
					
					<div class="rex-area-content">
					
						<fieldset class="rex-form-col-1">
							<legend>'.$I18N->msg("com_mediaaccess_settings_config").'</legend>
							
							<div class="rex-form-wrapper">
							
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-appId">'.$I18N->msg("com_mediaaccess_settings_unsecure_fileext").'</label>
										<input class="rex-form-text" type="input" id="rex-form-unsecure_fileext" name="unsecure_fileext" value="'.$REX['ADDON']['community']['plugin_mediaaccess']['unsecure_fileext'].'" />
									</p>
								</div>
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-checkbox">
										<label for="rex-form-appSecret">'.$I18N->msg("com_mediaaccess_settings_sendfile").'</label>
										'.dropdown_sendfile().'
									</p>
								</div>

							</div>
							<div class="rex-form-wrapper">
								<div class="rex-form-row">
									<p class="rex-form-col-a rex-form-submit">
										<input type="submit" class="rex-form-submit" name="sendit" value="'.$I18N->msg("specials_update").'"'. rex_accesskey($I18N->msg('specials_update'), $REX['ACKEY']['SAVE']) .' />
									</p>
								</div>
							</div>
						</fieldset>
					</div> <!-- Ende rex-area-content //-->					
				</div> <!-- Ende rex-area-col-b //-->
			</div> <!-- Ende rex-area-col-2 //-->
			
		</form>
	</div>
  ';
