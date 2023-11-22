<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/VideoSources/interface.ilInteractiveVideoSourceGUI.php';
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/VideoSources/plugin/InteractiveVideoVimeo/class.ilInteractiveVideoVimeo.php';

/**
 * Class ilInteractiveVideoVimeoGUI
 * @author Guido Vollbach <gvollbach@databay.de>
 */
class ilInteractiveVideoVimeoGUI implements ilInteractiveVideoSourceGUI
{
	const VIMEO_URL = 'https://vimeo.com/';

	/**
	 * @param ilRadioOption $option
	 * @param               $obj_id
	 * @return ilRadioOption
	 */
	public function getForm($option, $obj_id)
	{
		$vimeo_url = new ilTextInputGUI(ilInteractiveVideoPlugin::getInstance()->txt('meo_url'), ilInteractiveVideoVimeo::FORM_FIELD);
		$object = new ilInteractiveVideoVimeo();
		if($obj_id > 0) {
			$object->doReadVideoSource($obj_id);
		}
		$vimeo_url->setValue($object->getVimeoId());
		$vimeo_url->setInfo(ilInteractiveVideoPlugin::getInstance()->txt('meo_vimeo_info'));
		$option->addSubItem($vimeo_url);
		return $option;
	}

	/**
	 * @param ilPropertyFormGUI $form
	 * @return bool
	 */
	public function checkForm($form)
	{
		$value = ilUtil::stripSlashes($form->getInput(ilInteractiveVideoVimeo::FORM_FIELD));
		$vimeo_id = ilInteractiveVideoVimeo::getVimeoIdentifier($value);
		if($vimeo_id)
		{
			return true;
		}
		return false;
	}


	/**
	 * @param ilTemplate $tpl
	 * @return ilTemplate
	 */
	public function addPlayerElements($tpl)
	{
		$tpl->addJavaScript('Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/VideoSources/plugin/InteractiveVideoVimeo/js/jquery.InteractiveVideoVimeoPlayer.js');
		$tpl->addJavaScript('Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/VideoSources/plugin/InteractiveVideoVimeo/js/vimeo_player_api.js');
		return $tpl;
	}

	/**
	 * @param                       $player_id
	 * @param ilObjInteractiveVideo $obj
	 * @return ilTemplate
	 */
	public function getPlayer($player_id, $obj)
	{
		$player = new ilTemplate('Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/VideoSources/plugin/InteractiveVideoVimeo/tpl/tpl.video.html', false, false);
		$instance = new ilInteractiveVideoVimeo();
		$instance->doReadVideoSource($obj->getId());
		$player->setVariable('PLAYER_ID', $player_id);
		$player->setVariable('VIMEO_ID', $instance->getVimeoId());
		return $player;
	}

	/**
	 * @param array                 $a_values
	 * @param ilObjInteractiveVideo $obj
	 */
	public function getEditFormCustomValues(array &$a_values, $obj)
	{
		$instance = new ilInteractiveVideoVimeo();
		$instance->doReadVideoSource($obj->getId());
		$value = $instance->getVimeoId();
		if($value != '')
		{
			$value = self::VIMEO_URL . $value;
		}
		$a_values[ilInteractiveVideoVimeo::FORM_FIELD] = $value;
	}

	/**
	 * @param $form
	 */
	public function getConfigForm($form)
	{
	}

	/**
	 * @return boolean
	 */
	public function hasOwnConfigForm()
	{
		return false;
	}



}
