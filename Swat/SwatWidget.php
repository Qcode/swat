<?php
/**
 * @package Swat
 * @license http://opensource.org/licenses/gpl-license.php GNU Public License
 * @copyright silverorange 2004
 */
require_once('Swat/SwatObject.php');

/**
 * Base class for all widgets.
 */
abstract class SwatWidget extends SwatObject {

	/**
	 * @var SwatContainer The widget which contains this widget.
	 */
	public $parent = null;

	/**
	 * @var string A non-visible textual name for this widget, or null.
	 */
	public $name;

	/**
	 * @param string A non-visible textual name for this widget.
	 */
	function __construct($name = null) {
		$this->name = $name;

		$this->init();
	}

	/**
	 * Display the widget.
	 *
	 * The widget displays itself as well as recursively displays any child widgets.
	 */
	abstract public function display();

	/**
	 * Display the widget with tidy HTML.
	 *
	 * this::display() is called and the output is cleaned by libtidy.
	 */
	public function displayTidy() {
		ob_start();
		$this->display();
		$buffer = ob_get_clean();
		/*
		$config = array('indent' => true,
		                'input-xml' => true,
		                'output-xml' => true,
		                'wrap' => 200);

		$tidy = tidy_parse_string($buffer, $config, 'UTF8');
		*/
		$tidy = ereg_replace("</?div[^<>]*>", "\n\\0\n", $buffer);
		$tidy = ereg_replace("\n\n", "\n", $tidy);
		echo $tidy;
	}

	/**
	 * Process the widget.
	 *
	 * After a form submit, the widget processes itself as well as recursively
	 * processes any child widgets.
	 */
	public function process() {

	}

	/**
	 * Initialize the widget.
	 *
	 * Run by the widget constructor.
	 */
	public function init() {

	}

	/**
	 * Gather error messages.
	 *
	 * Gather all error messages from children of this widget and this widget itself.
	 *
	 * @return array Array of SwatErrorMessage objects.
	 */
	abstract function gatherErrorMessages();

}

?>
