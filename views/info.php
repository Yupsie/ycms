<?php //	namespace Views;

/**
 *	class V_Info
 *	Load the main View, other Views can extend this class
 *
 *	@author Yupsie.eu
 *	@copyright Yupsie.eu (c) 18-04-2010
 *	@version 8.8.0
 */
class V_Info extends V_Main {

	/**
	 * 	function getTodos
	 * 
	 * 	@access private
	 * 	@param string sPath
	 * 	@return array
	 */
	private function getTodos($sPath) {
		$aTodos = array();
		$aItems = glob(rtrim($sPath, '/') . '/*');

		foreach ($aItems as $sItem) {

		   	if (is_file($sItem) && pathinfo($sItem, PATHINFO_EXTENSION) == 'php') {
		       	$aLines = file($sItem);

		       	foreach($aLines as $i => $sLine) {
		        	if (strpos($sLine, '@todo') !== false && strpos($sLine, 'strpos') === false && strpos($sLine, 'str_replace') === false) {
		              	$aTodos[$sItem][($i + 1)] = trim(str_replace(array('//', '@todo:'), '', $sLine));
		           	}
		       	}
		   	} 
		   	elseif (is_dir($sItem)) {
		       	$aTodos = array_merge($aTodos, $this->getTodos($sItem));
		       	continue;
		   	}
		}

		return $aTodos;
	}

	/**
	 *	function setContent
	 *
	 *	@access public
	 *	@param array aDataTables
	 *	@return void
	 */
	public function setContent($aDataTables = array()) {
		$oParsedown = new V_Parsedown_Main();
		$sText = file_get_contents('/var/sub/admin.yupsie.eu/README.md');

		$this->sData .= '
		<section id="info">
			<h2 style="font-variant:small-caps;">Info</h2>
			<article>
				<p>Klik op een van bovenstaande knoppen om de website te bewerken.</p>
			</article>
			<table>';

		foreach ($this->getTodos('/var/sub/admin.yupsie.eu/') as $sFile => $aFile) {
			foreach ($aFile as $iLine => $sLine) {
				$this->sData .= '
				<tr>
					<td>' . $sLine . '</td>
					<td style="text-align:right;">' . $sFile . ' : ' . $iLine . '</td>
				</tr>';
			}
		}

		$this->sData .= '
			</table>
			<table>
				<tr>
					<td>' . phpversion() . '</td>
				</tr>
			</table>
		</section>
		<section>' . preg_replace_callback('/<pre\b[^>]*lang="(.+?)"\s*(lines="([0-9,]*?)")?[^>]*>(.+?)<\/pre>/s', 'V_Main::stylizeCode', str_replace(array('<pre><code>', '</code>'), array('<pre lang="php">', ''), $oParsedown->text($sText))) . '</section>';
	}
}