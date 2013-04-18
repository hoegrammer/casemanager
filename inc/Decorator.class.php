<?php

class Decorator
{
	// create a link from a client name and id
	public function makeLink($id_client, $name_first, $name_last)
	{
		return "<a class='content_link' href='client_det.php?client=$id_client'>
			$name_first $name_last </a>";

	}

	// create two table cells from an amount and a boolean
	public function makeAmountAndBusPassCells($amount, $bus_pass)
	{
		// is it a dummy placeholder?
		if ($amount === null && $bus_pass === null) {
			return "<td>&nbsp;</td><td>&nbsp;</td>";
		}
		$html = "
			<td>&pound;$amount</td>
			<td><input type='checkbox' disabled = 'disabled'";
		if ($bus_pass) {
			$html .= "checked = 'checked'";
		} 
		$html .= " />";                                    
		return $html;
	}
}

?>
