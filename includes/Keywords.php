<?php
class Keywords {
	public function get($id) {
		return self::get_by_id($id);
	}

	public function get_by_id($id) {
		$result = DigiplayDB::query("SELECT * FROM keywords WHERE id = ".$id);
		if(pg_num_rows($result)) {
			return pg_fetch_object($result,NULL,"Keyword");
		} else return false;
	}

	public function get_by_text($text) {
		$result = DigiplayDB::query("SELECT * FROM keywords WHERE name = '".$text."';");
		if(pg_num_rows($result)) {
			return pg_fetch_object($result,NULL,"Keyword");
		} else return false;
	}

	public function get_by_audio($audio) {
		$keywords = array();
		$result = DigiplayDB::query("SELECT keywords.* FROM keywords INNER JOIN audiokeywords ON (keywords.id = audiokeywords.keywordid) WHERE audiokeywords.audioid = ".$audio->get_id()); 
		while($object = pg_fetch_object($result,NULL,"Keyword"))
                 $keywords[] = $object;
    	return ((count($keywords) > 0)? $keywords : false);
	}
}