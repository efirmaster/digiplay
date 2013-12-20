<?php
/*
 * PHP Pagination Class
 * @author admin@catchmyfame.com - http://www.catchmyfame.com
 * @version 2.0.0
 * @date October 18, 2011
 * @copyright (c) admin@catchmyfame.com (www.catchmyfame.com)
 * @license CC Attribution-ShareAlike 3.0 Unported (CC BY-SA 3.0) - http://creativecommons.org/licenses/by-sa/3.0/
 */
class Paginator{
	var $items_per_page;
	var $items_total;
	var $current_page;
	var $num_pages;
	var $mid_range;
	var $low;
	var $limit;
	var $return;
	var $default_n;
	var $querystring;
	var $index;
	var $n_array;

	function Paginator()
	{
		$this->current_page = 1;
		$this->mid_range = 7;
		$this->n_array = array(10,25,50);
		$this->items_per_page = (!empty($_GET["n"])) ? $_GET["n"]:$this->default_n;
	}

	function paginate()
	{
		if(!isset($this->default_n)) $this->default_n=10;
		if(isset($_GET["n"]) && $_GET["n"] == "All")
		{
			$this->num_pages = 1;
//			$this->items_per_page = $this->default_n;
		}
		else
		{
			if(!is_numeric($this->items_per_page) OR $this->items_per_page <= 0) $this->items_per_page = $this->default_n;
			$this->num_pages = ceil($this->items_total/$this->items_per_page);
		}
		$this->current_page = (isset($_GET["p"])) ? (int) $_GET["p"] : 1 ; // must be numeric > 0
		$prev_page = $this->current_page-1;
		$next_page = $this->current_page+1;

		$this->querystring = urlencode($this->querystring);
		$this->index = urlencode($this->index);

		$this->return = "<ul class=\"pagination\">";

		$this->return .= ($this->current_page > 1 && $this->items_total >= 10) ? "<li class=\"prev\"><a href=\"$_SERVER[PHP_SELF]?q=$this->querystring&i=$this->index&p=$prev_page&n=$this->items_per_page\">&laquo; Previous</a></li> ":"<li class=\"prev disabled\"><a href=\"#\">&laquo; Previous</a></li> ";

		if($this->num_pages > 10)
		{
			$this->start_range = $this->current_page - floor($this->mid_range/2);
			$this->end_range = $this->current_page + floor($this->mid_range/2);

			if($this->start_range <= 0)
			{
				$this->end_range += abs($this->start_range)+1;
				$this->start_range = 1;
			}
			if($this->end_range > $this->num_pages)
			{
				$this->start_range -= $this->end_range-$this->num_pages;
				$this->end_range = $this->num_pages;
			}
			$this->range = range($this->start_range,$this->end_range);

			for($i=1;$i<=$this->num_pages;$i++)
			{
				if($this->range[0] > 2 && $i == $this->range[0]) $this->return .= "<li class=\"disabled\"><a href=\"#\"> ... </a></li>";
				// loop through all pages. if first, last, or in range, display
				if($i==1 || $i==$this->num_pages || in_array($i,$this->range))
				{
					$this->return .= ($i == $this->current_page && (isset($_GET["page"]) && $_GET["page"] != "All")) ? "<li class=\"active\"><a title=\"Go to page $i of $this->num_pages\" class=\"current\" href=\"#\">$i</a></li>":"<li><a class=\"paginate\" title=\"Go to page $i of $this->num_pages\" href=\"$_SERVER[PHP_SELF]?q=$this->querystring&p=$i&i=$this->index&n=$this->items_per_page\">$i</a></li>";
				}
				if($this->range[$this->mid_range-1] < $this->num_pages-1 && $i == $this->range[$this->mid_range-1]) $this->return .= "<li class=\"disabled\"><a href=\"#\"> ... </a></li>";
			}
		}
		else
		{
			for($i=1;$i<=$this->num_pages;$i++)
			{
				$this->return .= ($i == $this->current_page) ? "<li class=\"active\"><a href=\"#\">$i</a></li>":"<li><a href=\"$_SERVER[PHP_SELF]?q=$this->querystring&i=$this->index&p=$i&n=$this->items_per_page\">$i</a></li>";
			}
		}
		$this->return .= (($this->current_page < $this->num_pages && $this->items_total >= 10) && (isset($_GET["page"]) && $_GET["page"] != "All") && $this->current_page > 0) ? "<li class=\"next\"><a href=\"$_SERVER[PHP_SELF]?q=$this->querystring&i=$this->index&p=$next_page&n=$this->items_per_page\">Next &raquo;</a></li>\n":"<li class=\"next disabled\"><a href=\"#\">&raquo; Next</a></li>\n";
		$this->low = ($this->current_page <= 0) ? 0:($this->current_page-1) * $this->items_per_page;
		if($this->current_page <= 0) $this->items_per_page = 0;
		$this->limit = (isset($_GET["n"]) && $_GET["n"] != "All") ? "":" LIMIT $this->low,$this->items_per_page";
		$this->return .= "</ul>";
	}
	function display_items_per_page()
	{
		$items = "";
		if(!isset($_GET["n"])) $this->items_per_page = $this->default_n;
		foreach($this->n_array as $n_opt) $items .= ($n_opt == $this->items_per_page) ? "<option selected value=\"$n_opt\">$n_opt</option>\n":"<option value=\"$n_opt\">$n_opt</option>\n";
		return "<span>Items per page </span> <select name=\"items-per-page\" onchange=\"window.location=".$_SERVER["PHP_SELF"]."?q=".$this->querystring."&i=".$this->index."&p=1&n=\"+this[this.selectedIndex].value;return false\">".$items."</select>\n";
	}
	function display_jump_menu()
	{
		$option = "";
		for($i=1;$i<=$this->num_pages;$i++)
		{
			$option .= ($i==$this->current_page) ? "<option value=\"$i\" selected>$i</option>\n":"<option value=\"$i\">$i</option>\n";
		}
		return "<span>Page </span> <select name=\"page-select\" onchange=\"window.location=".$_SERVER["PHP_SELF"]."?q=".$this->querystring."&i=".$this->index."&p=\"+this[this.selectedIndex].value+\"&n=".$this->items_per_page."\";return false\">".$option."</select>\n";
	}
	function display_pages()
	{
		return $this->return;
	}
}