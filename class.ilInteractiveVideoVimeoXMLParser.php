<?php
require_once 'Customizing/global/plugins/Services/Repository/RepositoryObject/InteractiveVideo/classes/class.ilInteractiveVideoXMLParser.php';

/**
 * Class ilInteractiveVideoVimeoXMLParser
 */
class ilInteractiveVideoVimeoXMLParser extends ilInteractiveVideoXMLParser
{
	/**
	 * @var ilInteractiveVideoVimeo
	 */
	protected $vimeo_obj;
	

	/**
	 * @param ilInteractiveVideoVimeo $vimeo_obj
	 * @param                      $xmlFile
	 */
	public function __construct($vimeo_obj, $xmlFile)
	{
		$this->vimeo_obj = $vimeo_obj;
		$this->setHandlers($xmlFile);
	}

	/**
	 * @param $xmlParser
	 * @param $tagName
	 * @param $tagAttributes
	 */
	public function handlerBeginTag($xmlParser, $tagName, $tagAttributes): void
	{
		switch($tagName)
		{
			case 'VimeoId':
			case 'VideoSourceObject':
				$this->cdata = '';
				break;
		}
	}

	/**
	 * @param $xmlParser
	 * @param $tagName
	 */
	public function handlerEndTag($xmlParser, $tagName): void
	{
		switch($tagName)
		{
			case 'VimeoId':
				$this->vimeo_obj->setVimeoId(trim($this->cdata));
				break;
			case 'VideoSourceObject':
				$tmp = $this->cdata;
				break;
		}
	}

	private function fetchAttribute($attributes, $name)
	{
		if( isset($attributes[$name]) )
		{
			return $attributes[$name];
		}
		return null;
	}

	/**
	 * @param $xmlParser
	 */
	public function setHandlers($a_xml_parser): void
	{
		xml_set_object($a_xml_parser, $this);
		xml_set_element_handler($a_xml_parser, 'handlerBeginTag', 'handlerEndTag');
		xml_set_character_data_handler($a_xml_parser, 'handlerCharacterData');
	}

}
