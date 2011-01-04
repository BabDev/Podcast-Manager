<?php 
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );

class PodcastModelFiles extends JModel {
	private $pagination = null;
	private $filelist = null;
	private $folder = null;
	private $data = null;
	private $podcasts = null;
	private $hasSpaces = false;
	
	private function &getFilelist() {
		if(!$this->filelist) {
			$folder = $this->getFolder();
			
			if(!JFolder::exists($folder)) {
				// TODO: handle error when mediapath isn't a folder
				$this->filelist = array();
			} else {
				$this->filelist = JFolder::files($folder);
			}
		}
		
		return $this->filelist;
	}
	
	private function &getPodcasts()
	{
		if (!$this->podcasts) {
			$query = "SELECT podcast_id,filename FROM #__podcast";
			$this->podcasts = $this->_getList($query);
			
			if (!count($this->podcasts)) {
				$this->podcasts = array();
			}
		}
		
		return $this->podcasts;
	}

	public function getFolder() {
		if(!$this->folder) {
			jimport('joomla.filesystem.path');
			jimport('joomla.filesystem.folder');

			$params =& JComponentHelper::getParams('com_podcast');
			$mediapath = $params->get('mediapath', 'components/com_podcast/media');

			$this->folder = JPATH_ROOT . DS . JFolder::makeSafe(JPath::clean($mediapath));
		}

		return $this->folder;
	}

	public function &getData() {
		$pagination = $this->getPagination();
		$files =& $this->getAllData();
		$files = array_slice($files, $pagination->limitstart, $pagination->limit);
		return $files;
	}

	private function getTotal() {
		$data =& $this->getAllData();
		return count($data);
	}
	
	public function getPagination() {
		if(!$this->pagination) {
			jimport('joomla.html.pagination');
			global $mainframe;
			$this->pagination = new JPagination($this->getTotal(), JRequest::getVar('limitstart', 0), JRequest::getVar('limit', $mainframe->getCfg('list_limit')));
		}

		return $this->pagination;
	}
	
	public function getHasSpaces()
	{
		if (!$this->data) {
			$this->getAllData();
		}
		
		return $this->hasSpaces;
	}

	private function &getAllData() {
		if($this->data)
			return $this->data;

		global $mainframe, $option;

		$files =& $this->getFilelist();
		$podcasts =& $this->getPodcasts();
		$data = array();

		foreach($files as $filename) {
			$file = new stdClass();
			$file->filename = $filename;
			$file->published = false;
			$file->hasMetadata = false;
			$file->id = 0;
			$file->hasSpaces = false;
			
			if (JString::stristr($filename, ' ')) {
				$this->hasSpaces = true;
				$file->hasSpaces = true;
			}

			$data[$filename] = $file;
		}
		
		// merge in metadata with no corresponding files on filesystem
		foreach ($podcasts as $podcast) {			
			if (!isset($data[$podcast->filename])) {
				$file = new stdClass();
				$file->filename = $podcast->filename;
				$file->published = false;
				$file->hasMetadata = false;
				$file->id = 0;
				$file->hasSpaces = false;

				if (JString::stristr($podcast->filename, ' ')) {
					$this->hasSpaces = true;
					$file->hasSpaces = true;
				}

				$data[$podcast->filename] = $file;
			}
		}
		
		$date =& JFactory::getDate();
		$now = $date->toMySQL();
		$nullDate = $this->_db->Quote($this->_db->getNullDate());
		
		$query = "SELECT id,introtext FROM #__content"
		. "\n WHERE state = '1' AND introtext LIKE '%{enclose%}%'"
		. "\n AND access = 0"
		. "\n AND ( publish_up = $nullDate OR publish_up <= '$now' )"
		. "\n AND ( publish_down = $nullDate OR publish_down >= '$now' );";

		$articles = $this->_getList($query);
		foreach($articles as &$row) {
			preg_match('/\{enclose\s(.*)\}/', $row->introtext, $matches);
			
			// get the filename, ignore filesize and mimetype
			$pieces = explode(' ', $matches[1]);
			$filename = $pieces[0];
			
			if(!isset($data[$filename])) // file has probably been deleted
				continue;
			
			$podcast = $data[$filename];

			$podcast->published = true;
			$podcast->articleId = $row->id;
		}

		foreach($podcasts as &$row) {
			if(!isset($data[$row->filename]))
				continue;
				
			$data[$row->filename]->hasMetadata = true;
			$data[$row->filename]->id = $row->podcast_id;
		}

		$this->data =& $data;

		// filters
		$filter_published = $mainframe->getUserStateFromRequest($option . 'filter_published', 'filter_published', '*', 'word');
		$filter_metadata = $mainframe->getUserStateFromRequest($option . 'filter_metadata', 'filter_metadata', '*', 'word');

		$published = $filter_published == 'on';
		$unpublished = $filter_published == 'off';
		if(!$published && !$unpublished) // no filtering on published
			$published = $unpublished = true;

		$metadata = $filter_metadata == 'on';
		$nometadata = $filter_metadata == 'off';
		if(!$metadata && !$nometadata) // no filtering on metadata
			$metadata = $nometadata = true;

		if($published && $unpublished && $metadata && $nometadata) // no filtering
			return $data;

		$keys = array_keys($data);
		foreach($keys as $key) {
			$file = $data[$key];

			if($file->published) {
				if(!$published)
					unset($data[$key]);
			} else {
				if(!$unpublished)
					unset($data[$key]);
			}

			if($file->hasMetadata) {
				if(!$metadata)
					unset($data[$key]);
			} else {
				if(!$nometadata)
					unset($data[$key]);
			}
		}

		return $data;
	}
}

?>
