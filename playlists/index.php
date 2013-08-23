<?php

Output::set_title("Playlists");
Output::add_script(LINK_ABS."js/jquery-ui-1.10.3.custom.min.js");

MainTemplate::set_subtitle("View and edit music playlists");

echo("<script type=\"text/javascript\">
$(function() {
	$('.table-striped tbody').sortable({ 
		axis: 'y',
		handle: '.move',
		helper: function(e, tr){
			var originals = tr.children();
			var helper = tr.clone();
			helper.children().each(function(index) {
				$(this).width(originals.eq(index).width())
			});
			return helper;
		},
		update : function () { 
			$('.move').removeClass('.glyphicon-move').addClass('.glyphicon-refresh');
            $.ajax({
                type: 'POST',
                url: '".LINK_ABS."/ajax/update-playlist-sortorder.php',
                data: $('.sortorder').serialize(),
                success: function(data) {
                	if(data != 'success') {
                		$('.sortorder').before('".Bootstrap::alert_message_basic("error","'+data+'","Error!")."');
                		$('.alert-message').alert();
                	}
                	$('.move').removeClass('.glyphicon-refresh').addClass('.glyphicon-move');
                }
            });
        }
	}).disableSelection();

	$('.info').popover({
		'html': true, 
		'title': function() { 
			return($(this).parent().parent().find('.title').html()+' tracks')
		},
		'content': function() {
			return($(this).parent().find('.hover-info').html());
		}
	});
	if(window.location.hash == '#add') {
		$('#add').click();
	}
	$('a[href=\"../playlists/index.php#add\"]').click(function() {
		event.preventDefault();
		$('#add').click();
	})

});
</script>");

echo("<h3>Current playlists:</h3>");

echo("
<form class=\"sortorder\">
<div class=\"table-responsive\">
<table class=\"table table-striped\">
	<thead>
		<tr>
			<th class=\"icon\"></th>
			<th class=\"title\">Title</th>
			<th class=\"icon\">Items</th>
			");
if(Session::is_group_user("Playlist Admin")) {
	echo("
			<th class=\"icon\"></th>
			<th class=\"icon\"></th>
			<th class=\"icon\"></th>
	");
}

echo("
		</tr>
	</thead>
	<tbody>
");

foreach (Playlists::get_all() as $playlist) {
	echo("
		<tr>
			<td>
				<a href=\"#\" class=\"info\">
					".Bootstrap::glyphicon("info-sign")."
					<input type=\"hidden\" name=\"id[]\" value=\"".$playlist->get_id()."\">
				</a>
				<div class=\"hover-info\">
				");
	foreach($playlist->get_tracks() as $track) {
		echo("<strong>".$track->get_title()."</strong> by ".$track->get_artists_str()."<br />");
	}
	echo("
				</div>
			</td>
			<td class=\"title\">".$playlist->get_name()."</td>
			<td>".count($playlist->get_tracks())."</td>
	");
	if(Session::is_group_user("Playlist Admin")) {
		echo("
			<td>
				<a href=\"".LINK_ABS."playlists/detail/".$playlist->get_id()."\" title=\"View/Edit this playlist\" rel=\"twipsy\">
					".Bootstrap::glyphicon("pencil")."
				</a>
			</td>
			<td>
				<a href=\"#\" title=\"Delete this playlist\" rel=\"twipsy\">
					".Bootstrap::glyphicon("remove-sign")."
				</a>
			</td>
			<td>
				<a href=\"#\" class=\"move\">
					".Bootstrap::glyphicon("move move")."
				</a>
			</td>
		");
	}
	echo("
		</tr>");
}
echo("
	</tbody>
</table>
</div>
</form>
");

if(Session::is_group_user("Playlist Admin")) {
	echo("
<a href=\"#\" data-toggle=\"modal\" data-target=\"#addnew-modal\" data-backdrop=\"true\" data-keyboard=\"true\" id=\"add\">Add a new playlist &raquo;</a>
<div class=\"modal fade hide\" id=\"addnew-modal\">
	<div class=\"modal-header\">
		<a class=\"close\" data-dismiss=\"modal\">&times;</a>
		<h4>Add new playlist</h4>
	</div>
	<div class=\"modal-body\">
		<form class=\"form-horizontal\" action=\"\" method=\"POST\">
			<fieldset>
				<div class=\"control-group\">
					<label class=\"control-label\" for=\"name\">Name</label>
					<div class=\"controls\">
						<input type=\"text\" class=\"input-xlarge\" id=\"name\">
						<p class=\"help-block\">Enter a name for the new playlist.</p>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
	<div class=\"modal-footer\">
		<a class=\"btn btn-primary\" href=\"#\">Save</a>
		<a class=\"btn\" data-dismiss=\"modal\">Cancel</a>
	</div>
</div>");
}
?>