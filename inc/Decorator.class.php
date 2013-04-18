<?php

class Decorator
{
	// create a link from a client name and id
	public function makeLink($id_client, $name_first, $name_last)
	{
		return "<a class='content_link' href='client_det.php?client=$id_client'>
			$name_first $name_last </a>";

	}
}

?>
