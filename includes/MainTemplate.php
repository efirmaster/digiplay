<?php
class MainTemplate implements Template{
	protected static $sidebar;
	protected static $menu;
	protected static $subtitle;
	protected static $masthead;
	protected static $summary;
	public static function set_subtitle($subtitle){
		self::$subtitle = $subtitle;
	}
	public static function set_masthead($masthead) {
		self::$masthead = $masthead;
	}
	public static function set_summary($summary) {
		self::$summary = $summary;
	}
	public static function set_sidebar($sidebar) {
		self::$sidebar = $sidebar;
	}
	public static function set_menu($menu) {
		self::$menu = $menu;
	}
	public static function print_page($content){
		if(strlen(SITE_PATH)>0){
			$sitePathArray = explode("/",SITE_PATH);
			for($i = 0; $i < count($sitePathArray); $i++){
				$file = SITE_FILE_PATH.implode("/",array_slice($sitePathArray,0,$i+1))."/sidebar.php";
				if(file_exists($file)){
					include($file);
					MainTemplate::set_sidebar(sidebar());
					MainTemplate::set_menu(menu());
				}
			}
			unset($sitePathArray,$i,$file);
		}

		$main_menu = new Menu;
		$main_menu->add_many(
			array("music","Music Library"),
			array("playlists","Playlists"),
			array("audiowalls","Audiowalls"),
			array("files","Files"),
			array("showplans","Show Planning"));
	if(Session::is_admin()) $main_menu->add("admin","Admin");
	
	$site_path_array = explode("/",SITE_PAGE);
	$main_menu->set_active($site_path_array[0]);
	
	header("Content-Type: text/html; charset=utf-8");
	$return = "<!DOCTYPE html> 
<html> 
	<head> 
		<title>RaW Digiplay";
	if(Output::get_title() != 'Untitled Page')
		$return .= " - ".Output::get_title();
	$return .= "</title> 
		<meta http-equiv=\"Content-Type\" content=\"text/html; charset=utf-8\">
		<script type=\"text/javascript\" src=\"//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js\"></script>
		";
		if(Session::is_developer()) {
			$return .= "<script type=\"text/javascript\" src=\"".SITE_LINK_REL."js/bootstrap.js\"></script>
		<link rel=\"stylesheet\" href=\"".SITE_LINK_REL."css/bootstrap.css\" />
		";
		} else {
			$return .= "<script type=\"text/javascript\" src=\"".SITE_LINK_REL."js/bootstrap.min.js\"></script>
		<link rel=\"stylesheet\" href=\"".SITE_LINK_REL."css/bootstrap.min.css\" />
		";
		}
	if(count(Output::get_less_stylesheets())>0) {
		foreach(Output::get_less_stylesheets() AS $src){
			$return .= "<link href=\"".$src."\" rel=\"stylesheet/less\"/>
		";
		}
		$return .= "<script type=\"text/javascript\" src=\"".SITE_LINK_REL."js/less-1.1.5.min.js\"></script>
		";
	}

	$return .="<link rel=\"stylesheet\" type=\"text/css\" href=\"".SITE_LINK_REL."css/dps.css\" />
		";
	if(count(Output::get_stylesheets())>0)
		foreach(Output::get_stylesheets() AS $src){
			$return .= "<link href=\"".$src."\" rel=\"stylesheet\" type=\"text/css\"/>
		";
		}
	if(count(Output::get_scripts())>0)
		foreach(Output::get_scripts() AS $src){
			$return .= "<script src=\"".$src."\" type=\"text/javascript\"></script>
		";
		}
	if(count(Output::get_feeds())>0)
		foreach(Output::get_feeds() AS $feed){
			$return .= "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"".$feed['title']."\" href=\"".$feed['url']."\" />
		";
		}
	$return .= "<script src=\"".SITE_LINK_REL."js/main.js\" type=\"text/javascript\"></script>
	</head>
	<body>
		<div class=\"navbar navbar-inverse navbar-fixed-top\">
			<div class=\"navbar-inner\">
				<div class=\"container\">
				<a class=\"brand\" href=\"".SITE_LINK_REL."\">Digiplay</a>"
					.$main_menu->output(SITE_LINK_REL,6,"nav");
					if(Session::is_user()) { $return .= "
						<ul class=\"nav pull-right\">
							<li>
								<form class=\"navbar-search pull-right\" action=\"".SITE_LINK_REL."music/search\" method=\"GET\">
            						<input type=\"text\" class=\"search-query\" style=\"width: 180px\" placeholder=\"Search Tracks\" name=\"q\" autocomplete=\"off\">
            					</form>
            				</li>
            				<li>
	          					<ul id=\"quick-search\" class=\"dropdown-menu pull-right\"></ul>
	          				</li>
	          			</ul>
	          		"; }
          			$return .= "
				</div>
			</div>
		</div>
		";

	if(isset(self::$masthead)) {
		$return .= "
		<div class=\"masthead\">
			<div class=\"intro\">
				<div class=\"container\">
					".self::$masthead."
				</div>
			</div>
		</div>";
	}

	if(isset(self::$summary)) {
		$return .= "
		<div class=\"summary\">
			<div class=\"container\">
				".self::$summary."
			</div>
		</div>";
	}

	$return .= "
		<div class=\"container\">";

	if(Output::get_title() != 'Untitled Page') {
		$return .= "
			<div class=\"page-header\">
				<h2>".Output::get_title();
				if(isset(self::$subtitle)) {
					$return .= " <small>".self::$subtitle."</small>";
				}
		$return .= "</h2>
			</div>";
	}

	$return .= "
			<div class=\"row\">";
	if (isset(self::$sidebar) || isset(self::$menu)){
		$return .= "
			<div class=\"span3\">";
		if(isset(self::$menu)) {
			$return .= "	
					<div class=\"well\" style=\"padding: 8px 0; margin-bottom: 0;\">".
					self::$menu."
					</div>";
		}
		if(isset(self::$sidebar)) {
			$return .= "	
					<div style=\"padding: 19px;\">".
					self::$sidebar."
					</div>";
		}
		$return .= "
			</div>
			<div class=\"span9\">";
	} else {
		$return .= "
				<div class=\"span12\">";
	}

	$return .= $content;

	$return .= "
				</div>
			</div>
		</div>";

	if(Session::is_user())
		$return .= "
		<div class=\"modal fade hide\" id=\"logout-modal\">
			<div class=\"modal-header\">
				<button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
				<h4>Log out?</h4>
			</div>
			<div class=\"modal-body\">
				You'll lose any unsaved changes on this page.
			</div>
			<div class=\"modal-footer\">
				<a class=\"btn btn-primary\" href=\"".SITE_LINK_REL."ajax/logout.php\">Yes, log out</a>
			</div>
		</div>";

	$return .= "
		<footer class=\"footer\">
			<div class=\"container\">
				<p class=\"pull-right\">
					<a href=\"".SITE_LINK_REL."\"><img src=\"".SITE_LINK_REL."img/footer_logo.png\" alt=\"RaW 1251AM\" /></a>
				</p>
				<p>";
	if(Session::is_user()) $return .= "Logged in as ".Session::get_username().". <a href=\"#\" data-toggle=\"modal\" data-target=\"#logout-modal\" data-backdrop=\"true\" data-keyboard=\"true\">Logout</a><br />";
	else $return .= "Not logged in<br />";
	$return .= "Copyright &copy; 2011-12 Radio Warwick
				</p>
			</div>
		</footer>
	</body> 
</html>";
	return $return;
	}

	public static function print_http_error($error){
		switch($error){
			case 401: return "<h2>Unauthorized</h2>\n<p>This page requires special permissions</p>"; break;
			case 404: return "<h2>Page not found</h2>\n<p>This page does not exist</p>"; break;
			case 410: return "<h2>This page has left us</h2>\n<p>We are sad.</p>"; break;
		}
	}

}

?>
