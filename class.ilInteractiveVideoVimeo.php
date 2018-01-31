<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/VideoSources/interface.ilInteractiveVideoSource.php';
/**
 * Class ilInteractiveVideoVimeo
 * @author Guido Vollbach <gvollbach@databay.de>
 */
class ilInteractiveVideoVimeo implements ilInteractiveVideoSource
{
	const FORM_FIELD = 'vimeo_url';

	const TABLE_NAME = 'rep_robj_xvid_vimeo';

	/**
	 * @var string
	 */
	protected $id;

	/**
	 * @var string
	 */
	protected $version;

	/**
	 * @var string
	 */
	protected $vimeo_id;

	/**
	 * ilInteractiveVideoYoutube constructor.
	 */
	public function __construct()
	{
		if (is_file(dirname(__FILE__) . '/version.php'))
		{
			include(dirname(__FILE__) . '/version.php');
			$this->version = $version;
			$this->id = $id;
		}
	}

	/**
	 * @param $obj_id
	 */
	public function doCreateVideoSource($obj_id)
	{
		$this->doUpdateVideoSource($obj_id);
	}

	/**
	 * @param $obj_id
	 */
	public function doReadVideoSource($obj_id)
	{
		global $ilDB;
		$result = $ilDB->query('SELECT vimeo_id FROM '.self::TABLE_NAME.' WHERE obj_id = '.$ilDB->quote($obj_id, 'integer'));
		$row = $ilDB->fetchAssoc($result);
		$this->setVimeoId($row['vimeo_id']);
	}

	/**
	 * @param $obj_id
	 */
	public function doDeleteVideoSource($obj_id)
	{
		$this->beforeDeleteVideoSource($obj_id);
	}

	/**
	 * @param $original_obj_id
	 * @param $new_obj_id
	 */
	public function doCloneVideoSource($original_obj_id, $new_obj_id)
	{
		$this->doReadVideoSource($original_obj_id);
		$this->saveData($new_obj_id, $this->getVimeoId());
	}

	/**
	 * @param $obj_id
	 * @param $vimeo_id
	 */
	protected function saveData($obj_id, $vimeo_id)
	{
		global $ilDB;
		$ilDB->insert(
			self::TABLE_NAME,
			array(
				'obj_id'     => array('integer', $obj_id),
				'vimeo_id' => array('text', $vimeo_id)
			)
		);
	}

	/**
	 * @param $obj_id
	 */
	public function beforeDeleteVideoSource($obj_id)
	{
		$this->removeEntryFromTable($obj_id);
	}

	/**
	 * @param $obj_id
	 */
	public function removeEntryFromTable($obj_id)
	{
		global $ilDB;
		$ilDB->manipulateF('DELETE FROM '.self::TABLE_NAME.' WHERE obj_id = %s',
			array('integer'), array($obj_id));
	}

	/**
	 * @param $obj_id
	 */
	public function doUpdateVideoSource($obj_id)
	{
		if(ilUtil::stripSlashes($_POST[self::FORM_FIELD]))
		{
			$vimeo_id = self::getVimeoIdentifier(ilUtil::stripSlashes($_POST[self::FORM_FIELD]));
		}
		else
		{
			$vimeo_id = $this->getVimeoId();
		}

		if($vimeo_id)
		{
			$this->removeEntryFromTable($obj_id);
			$this->setVimeoId($vimeo_id);
			$this->saveData($obj_id, $vimeo_id);
		}
	}

	/**
	 * @return string
	 */
	public function getClass()
	{
		return __CLASS__;
	}

	/**
	 * @return bool
	 */
	public function isFileBased()
	{
		return false;
	}

	/**
	 * @return ilInteractiveVideoVimeoGUI
	 */
	public function getGUIClass()
	{
		require_once dirname(__FILE__) . '/class.ilInteractiveVideoVimeoGUI.php';
		return new ilInteractiveVideoVimeoGUI();
	}

	/**
	 * @return string
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getClassPath()
	{
		return 'VideoSources/plugin/InteractiveVideoVimeo/class.ilInteractiveVideoVimeo.php';
	}

	/**
	 * @return string
	 */
	public function getVersion()
	{
		return $this->version;
	}

	/**
	 * @return string
	 */
	public function getVimeoId()
	{
		return $this->vimeo_id;
	}

	/**
	 * @param string $vimeo_id
	 */
	public function setVimeoId($vimeo_id)
	{
		$this->vimeo_id = $vimeo_id;
	}

	/**
	 * @param $obj_id
	 * @return string
	 */
	public function getPath($obj_id)
	{
		return '';
	}

	/**
	 * @param $value
	 * @return string | boolean
	 */
	public static function getVimeoIdentifier($value)
	{
		$regex = '#(https?://)?(www.)?(player.)?vimeo.com/([a-z]*/)*([0-9]{6,11})[?]?.*#';
		preg_match_all($regex, $value, $matches);
		if(sizeof($matches) >= 6 && array_key_exists(0, $matches[5]))
		{
			return $matches[5][0];
		}
		return false;
	}

	/**
	 * @param int $obj_id
	 * @param ilXmlWriter $xml_writer
	 * @param string $export_path
	 */
	public function doExportVideoSource($obj_id, $xml_writer, $export_path)
	{
		$this->doReadVideoSource($obj_id);
		$xml_writer->xmlElement('VimeoId', null, (string)$this->getVimeoId());
	}

	/**
	 *
	 */
	public function getVideoSourceImportParser()
	{
		require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/VideoSources/plugin/InteractiveVideoVimeo/class.ilInteractiveVideoVimeoXMLParser.php';
		return 'ilInteractiveVideoVimeoXMLParser';
	}

	/**
	 * @param $obj_id
	 * @param $import_dir
	 */
	public function afterImportParsing($obj_id, $import_dir)
	{

	}
}