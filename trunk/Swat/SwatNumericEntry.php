<?php

require_once 'Swat/SwatEntry.php';
require_once 'Swat/SwatString.php';

/**
 * Base class for numeric entry widgets
 *
 * @package   Swat
 * @copyright 2004-2006 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
abstract class SwatNumericEntry extends SwatEntry
{
	// {{{ public properties

	/**
	 * Show Thousands Seperator
	 *
	 * Whether or not to show a thousands separator (shown depending on
	 * locale). 
	 *
	 * @var boolean
	 */
	public $show_thousands_separator = true;

	/**
	 * The smallest valid number in this entry
	 *
	 * This is inclusive. If set to null, there is no minimum value.
	 *
	 * @var double
	 */
	public $minimum_value = null;

	/**
	 * The largest valid number in this entry
	 *
	 * This is inclusive. If set to null, there is no maximum value.
	 *
	 * @var double
	 */
	public $maximum_value = null;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new numeric entry widget
	 *
	 * Sets the input size to 10 by default.
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->size = 10;
	}

	// }}}
	// {{{ public function process()

	/**
	 * Checks the minimum and maximum values of this numeric entry widget
	 */
	public function process()
	{
		parent::process();

		$value = $this->getNumericValue();
		if ($value !== null) {
			if ($this->minimum_value !== null &&
				$value < $this->minimum_value) {
				$message = sprintf(
					$this->getValidationMessage('below-minimum'),
					SwatString::numberFormat($this->minimum_value));

				$this->addMessage(
					new SwatMessage($message, SwatMessage::ERROR));
			}

			if ($this->maximum_value !== null &&
				$value > $this->maximum_value) {
				$message = sprintf(
					$this->getValidationMessage('above-maximum'),
					SwatString::numberFormat($this->maximum_value));

				$this->addMessage(
					new SwatMessage($message, SwatMessage::ERROR));
			}
		}
	}

	// }}}
	// {{{ protected function getValidationMessage()

	/**
	 * Get validation message
	 *
	 * @see SwatEntry::getValidationMessage()
	 */
	protected function getValidationMessage($id)
	{
		switch ($id) {
		case 'below-minimum':
			return Swat::_('The %%s field must not be less than %s.');
		case 'above-maximum':
			return Swat::_('The %%s field must not be more than %s.');
		default:
			return parent::getValidationMessage($id);
		}
	}

	// }}}
	// {{{ abstract protected function getNumericValue()
	
	/**
	 * Gets the numeric value of this widget
	 *
	 * This allows each widget to parse raw values how they want to get numeric
	 * values.
	 *
	 * @return mixed the numeric value of this entry widget of null if no
	 *                numeric value is available.
	 */
	abstract protected function getNumericValue();

	// }}}
}

?>