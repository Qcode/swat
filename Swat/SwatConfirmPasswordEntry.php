<?php

require_once 'Swat/SwatPasswordEntry.php';

/**
 * A password confirmation entry widget
 *
 * Automatically compares the value of the confirmation with the matching
 * password widget to see if they match.
 *
 * @package   Swat
 * @copyright 2004-2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatConfirmPasswordEntry extends SwatPasswordEntry
{
	/**
	 * A reference to the matching password widget
	 *
	 * @var SwatPasswordEntry
	 */
	public $password_widget = null;
	
	/**
	 * Checks to make sure passwords match
	 *
	 * Checks to make sure the values of the two password fields are the same.
	 * If an associated password widget is not set, an exception is thrown. If
	 * the passwords do not match, an error is added to this widget.
	 *
	 * @throws SwatException
	 */
	public function process()
	{
		parent::process();
		
		if ($this->password_widget === null)
			throw new SwatException(__CLASS__.
				'$this->password_widget is null. Expected a reference to a '.
				'SwatPasswordEntry.');

		if ($this->password_widget->value !== null) {
			if (strcmp($this->password_widget->value, $this->value) != 0) {
				$msg = Swat::_('Password and confirmation password do not match.');

				$this->addMessage(
					new SwatMessage($msg, SwatMessage::USER_ERROR));
			}
		}
	}
}

?>
