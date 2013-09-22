<?php
class Archive {
	protected $id;
	protected $name;
	protected $localpath;
	protected $remotepath;
	
	public function get_id() { return $this->id; }
	public function get_name() { return $this->name; }
	public function get_localpath() { return $this->localpath; }
	public function get_remotepath() { return $this->remotepath; }
	public function get_total_space() { return disk_total_space($this->localpath); }
	public function get_free_space() { return disk_free_space($this->localpath); }
}
?>