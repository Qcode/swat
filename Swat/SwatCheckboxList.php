<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatOptionControl.php';
require_once 'Swat/SwatHtmlTag.php';
require_once 'Swat/SwatCheckAll.php';
require_once 'Swat/SwatState.php';
require_once 'Swat/SwatString.php';
require_once 'Swat/exceptions/SwatException.php';

/**
 * A checkbox list widget
 *
 * @package   Swat
 * @copyright 2005-2012 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatCheckboxList extends SwatOptionControl implements SwatState
{
	// {{{ public properties

	/**
	 * List values
	 *
	 * The values of the selected items.
	 *
	 * @var array
	 */
	public $values = array();

	/**
	 * Whether to show the check all box
	 *
	 * @var boolean
	 */
	public $show_check_all = true;

	/**
	 * Defines the columns in which this list is displayed
	 *
	 * If unspecified, the list will be displayed in one column.
	 *
	 * Each column is displayed using a separate XHTML unordered list
	 * (<code>&lt;ul&gt;</code>). If the value is an integer it specifies
	 * the number of columns to display. If the value is an array of
	 * integers it specifies the number of checkboxes to display in each
	 * column.
	 *
	 * @var integer or array of integers
	 */
	public $columns = 1;

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new checkbox list
	 *
	 * @param string $id a non-visible unique id for this widget.
	 *
	 * @see SwatWidget::__construct()
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);
		$this->requires_id = true;
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializes this checkbox list
	 *
	 * @throws SwatException if there are duplicate values in the options array
	 */
	public function init()
	{
		parent::init();

		// checks to see if there are duplicate values in the options array
		$options_count =  array();
		foreach ($this->getOptions() as $option)
			$options_count[] = $option->value;

		foreach ((array_count_values($options_count)) as $count) {
			if ($count > 1)
				throw new SwatException(sprintf('Duplicate option values '.
					'found in %s', $this->id));
		}
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this checkbox list
	 *
	 * The check-all widget is only displayed if more than one checkable item
	 * is displayed.
	 */
	public function display(SwatDisplayContext $context)
	{
		$options = $this->getOptions();

		if (!$this->visible || count($options) == 0) {
			return;
		}

		parent::display($context);

		// outer div is required because the check-all widget is outside the
		// unordered list
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = $this->getCSSClassString();
		$div_tag->open($context);

		$current_column = 1;
		$current_option = 0;
		$columns = (is_array($this->columns)) ? $this->columns : array(ceil(
			count($options) / (($this->columns > 0) ? $this->columns : 1)));

		$multiple_columns = (count($options) > $columns[0]);
		$maximum_options  = array_shift($columns);

		$ul_tag = new SwatHtmlTag('ul');
		if ($multiple_columns) {
			$ul_tag->id = sprintf('%s_column_%s', $this->id, $current_column);
			$ul_tag->class = 'swat-checkbox-list-column';
		}
		$ul_tag->open($context);

		foreach ($options as $index => $option) {
			if ($current_option == $maximum_options) {
				$ul_tag->close($context);

				$current_column++;
				$current_option  = 0;
				$maximum_options = (count($columns) > 0) ?
					array_shift($columns) : $maximum_options;

				$ul_tag->id = sprintf(
					'%s_column_%s',
					$this->id,
					$current_column
				);

				$ul_tag->class = 'swat-checkbox-list-column';

				$ul_tag->open($context);
			}

			$current_option++;
			$this->displayOption($context, $option, $index);
		}

		$ul_tag->close($context);

		// Show clear div if columns are used
		if ($multiple_columns) {
			$context->out('<div class="swat-clear"></div>');
		}

		// Show the check all if more than one checkable item is displayed.
		$check_all = $this->getCompositeWidget('check_all');
		$check_all->visible = $this->show_check_all && (count($options) > 1);
		$check_all->display($context);

		$div_tag->close($context);

		$context->addYUI('event');
		$context->addStyleSheet('packages/swat/styles/swat.css');
		$context->addScript('packages/swat/javascript/swat-checkbox-list.js');
		$context->addInlineScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function process()

	/**
	 * Processes this checkbox list widget
	 */
	public function process()
	{
		if (!$this->getForm()->isSubmitted())
			return;

		parent::process();

		$this->processValues();

		if ($this->required && count($this->values) == 0
			&& $this->isSensitive()) {

			$message = Swat::_('The %s field is required.');
			$this->addMessage(new SwatMessage($message, 'error'));
		}
	}

	// }}}
	// {{{ public function reset()

	/**
	 * Reset this checkbox list.
	 *
	 * Reset the list to its default state. This is useful to call from a
	 * display() method when persistence is not desired.
	 */
	public function reset()
	{
		$this->values = array();
	}

	// }}}
	// {{{ public function setState()

	/**
	 * Sets the current state of this checkbox list
	 *
	 * @param array $state the new state of this checkbox list.
	 *
	 * @see SwatState::setState()
	 */
	public function setState($state)
	{
		$this->values = $state;
	}

	// }}}
	// {{{ public function getState()

	/**
	 * Gets the current state of this checkbox list
	 *
	 * @return array the current state of this checkbox list.
	 *
	 * @see SwatState::getState()
	 */
	public function getState()
	{
		return $this->values;
	}

	// }}}
	// {{{ protected function processValues()

	/**
	 * Processes the values of this checkbox list from raw form data
	 */
	protected function processValues()
	{
		$form = $this->getForm();
		$data = &$form->getFormData();

		if (isset($data[$this->id])) {
			if (is_array($data[$this->id])) {
				$this->values = $data[$this->id];
			} elseif ($data[$this->id] != '') {
				$this->values = array($data[$this->id]);
			} else {
				$this->values = array();
			}
		} else {
			$this->values = array();
		}
	}

	// }}}
	// {{{ protected function displayOption()

	/**
	 * Helper method to display a single option of this checkbox list
	 *
	 * @param SwatOption $option the option to display.
	 * @param integer $index a numeric index indicating which option is being
	 *                        displayed.
	 */
	protected function displayOption(SwatDisplayContext $context,
		SwatOption $option, $index)
	{
		$input_tag = new SwatHtmlTag('input');
		$input_tag->type = 'checkbox';
		$input_tag->name = $this->id.'['.$index.']';
		$input_tag->value = (string)$option->value;
		$input_tag->id = $this->id.'_'.$index;
		$input_tag->removeAttribute('checked');

		if (in_array($option->value, $this->values)) {
			$input_tag->checked = 'checked';
		}

		if (!$this->isSensitive()) {
			$input_tag->disabled = 'disabled';
		}

		$label_tag = new SwatHtmlTag('label');
		$label_tag->class = 'swat-control';
		$label_tag->for = $this->id.'_'.$index;
		$label_tag->setContent($option->title, $option->content_type);

		$li_tag = $this->getLiTag($option);

		$li_tag->open($context);
		$context->out('<span class="swat-checkbox-wrapper">');
		$input_tag->display($context);
		$context->out('<span class="swat-checkbox-shim"></span>');
		$context->out('</span>');
		$label_tag->display($context);
		$li_tag->close($context);
	}

	// }}}
	// {{{ protected function getLiTag()

	protected function getLiTag($option)
	{
		$tag = new SwatHtmlTag('li');

		return $tag;
	}

	// }}}
	// {{{ protected function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript for this checkbox list
	 *
	 * @return string the inline JavaScript for this checkbox list.
	 */
	protected function getInlineJavaScript()
	{
		$javascript = sprintf(
			'var %s_obj = new %s(%s);',
			$this->id,
			$this->getJavaScriptClassName(),
			SwatString::quoteJavaScriptString($this->id));

		// set check-all controller if it is visible
		$check_all = $this->getCompositeWidget('check_all');
		if ($check_all->visible) {
			$javascript.= sprintf(
				"\n%s_obj.setController(%s_obj);",
				$check_all->id,
				$this->id);
		}

		return $javascript;
	}

	// }}}
	// {{{ protected function getJavaScriptClassName()

	/**
	 * Get the name of the JavaScript class for this widget
	 *
	 * @return string JavaScript class name.
	 */
	protected function getJavaScriptClassName()
	{
		return 'SwatCheckboxList';
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this checkbox list
	 *
	 * @return array the array of CSS classes that are applied to this checkbox
	 *                list.
	 */
	protected function getCSSClassNames()
	{
		$classes = array('swat-checkbox-list');
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
	// {{{ protected function createCompositeWidgets()

	/**
	 * Creates and adds composite widgets of this widget
	 *
	 * Created composite widgets should be added in this method using
	 * {@link SwatWidget::addCompositeWidget()}.
	 */
	protected function createCompositeWidgets()
	{
		$this->addCompositeWidget(new SwatCheckAll(), 'check_all');
	}

	// }}}
}

?>
