<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatDisclosure.php';
require_once 'Swat/SwatHtmlTag.php';

/**
 * A frame-like container to show and hide child widgets
 *
 * @package   Swat
 * @copyright 2006-2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class SwatFrameDisclosure extends SwatDisclosure
{
	// {{{ public function display()

	/**
	 * Displays this frame disclosure container
	 *
	 * Creates appropriate divs and outputs closed or opened based on the
	 * initial state.
	 *
	 * The disclosure is always displayed as opened in case the user has
	 * JavaScript turned off.
	 */
	public function display(SwatDisplayContext $context)
	{
		if (!$this->visible) {
			return;
		}

		SwatWidget::display($context);

		// default header level is h2
		$level = 2;
		$ancestor = $this->parent;

		// get appropriate header level, limit to h6
		while ($ancestor !== null && $level < 6) {
			if ($ancestor instanceof SwatFrame) {
				$level++;
			}

			$ancestor = $ancestor->parent;
		}

		$header_tag = new SwatHtmlTag('h'.$level);
		$header_tag->class = 'swat-frame-title';

		$control_div = $this->getControlDivTag();
		$span_tag = $this->getSpanTag();
		$input_tag = $this->getInputTag();
		$container_div = $this->getContainerDivTag();
		$container_div->class.= ' swat-frame-contents';
		$animate_div = $this->getAnimateDivTag();

		$control_div->open($context);

		$header_tag->open($context);
		$span_tag->display($context);
		$header_tag->close($context);

		$input_tag->display($context);

		$animate_div->open($context);
		echo '<div>';
		$container_div->open($context);
		$this->displayChildren($context);
		$container_div->close($context);
		echo '</div>';
		$animate_div->close($context);

		$control_div->close($context);

		$context->addStyleSheet(
			'packages/swat/styles/swat-frame-disclosure.css'
		);

		$context->addInlineScript($this->getInlineJavascript());
	}

	// }}}
	// {{{ protected function getContainerDivTag()

	protected function getContainerDivTag()
	{
		$div = new SwatHtmlTag('div');
		$div->class = 'swat-disclosure-container '.
			'swat-frame-disclosure-container';

		return $div;
	}

	// }}}
	// {{{ protected function getSpanTag()

	protected function getSpanTag()
	{
		$span_tag = parent::getSpanTag();
		$span_tag->class = null;
		return $span_tag;
	}

	// }}}
	// {{{ protected function getJavaScriptClass()

	/**
	 * Gets the name of the JavaScript class to instantiate for this disclosure
	 *
	 * Subclasses of this class may want to return a sub-class of the default
	 * JavaScript disclosure class.
	 *
	 * @return string the name of the JavaScript class to instantiate for this
	 *                 frame disclosure.
	 */
	protected function getJavaScriptClass()
	{
		return 'SwatFrameDisclosure';
	}

	// }}}
	// {{{ protected function getCSSClassNames()

	/**
	 * Gets the array of CSS classes that are applied to this disclosure
	 *
	 * @return array the array of CSS classes that are applied to this
	 *                disclosure.
	 */
	protected function getCSSClassNames()
	{
		$classes = array();
		$classes[] = 'swat-frame';
		$classes[] = 'swat-disclosure-control-opened';
		$classes[] = 'swat-frame-disclosure-control-opened';
		$classes[] = 'swat-frame-disclosure';
		$classes = array_merge($classes, parent::getCSSClassNames());
		return $classes;
	}

	// }}}
}

?>
