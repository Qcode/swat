<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/Swat.php';
require_once 'Swat/SwatHtmlHeadEntry.php';

/**
 * @package   Swat
 * @copyright 2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatInlineJavaScriptHtmlHeadEntry extends SwatHtmlHeadEntry
{
	// {{{ protected properties

	/**
	 * @var string
	 */
	protected $script;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new HTML head entry
	 *
	 * @param string  $script the script of this entry.
	 * @param integer $package_id the package id of the package this HTML head
	 *                             entry belongs to.
	 */
	public function __construct($script, $package_id = null)
	{
		parent::__construct(md5($script), $package_id);
		$this->script = $script;
	}

	// }}}
	// {{{ public function display()

	public function display($uri_prefix = '', $tag = null)
	{
		Swat::displayInlineJavaScript($this->script);
	}

	// }}}
	// {{{ public function displayInline()

	public function displayInline($path)
	{
		$this->display();
	}

	// }}}
}

?>
