<?php

/* vim: set noexpandtab tabstop=4 shiftwidth=4 foldmethod=marker: */

require_once 'Swat/SwatWidget.php';
require_once 'Swat/SwatUIParent.php';
require_once 'Swat/SwatYUI.php';
require_once 'Swat/SwatNoteBookPage.php';
require_once 'Swat/exceptions/SwatInvalidClassException.php';

/**
 * Notebook widget for containing {@link SwatNoteBook} pages
 *
 * @package   Swat
 * @copyright 2007 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 * @see       SwatNoteBookPage
 */
class SwatNoteBook extends SwatWidget implements SwatUIParent
{
	// {{{ constants

	/**
	 * Top Position
	 *
	 * The constant used to represent tabs on top.
	 */
	const POSITION_TOP    = 1;

	/**
	 * Right Position
	 *
	 * The constant used to represent tabs on the right.
	 */
	const POSITION_RIGHT  = 2;

	/**
	 * Bottom Position
	 *
	 * The constant used to represent tabs on the bottom.
	 */
	const POSITION_BOTTOM = 3;

	/**
	 * Left Position
	 *
	 * The constant used to represent tabs on the left.
	 */
	const POSITION_LEFT   = 4;

	// }}}
	// {{{ public properties

	/**
	 * A value containing the desired position of the tabs
	 *
	 * @var integer 
	 */
	public $tab_position = self::POSITION_TOP;

	// }}}
	// {{{ protected properties

	/**
	 * Pages affixed to this widget
	 *
	 * @var array
 	 */
	protected $pages = array();

	// }}}
	// {{{ public function __construct()

	/**
	 * Creates a new SwatWidget
	 *
	 * @param string $id a non-visable unique id for this widget.
	 */
	public function __construct($id = null)
	{
		parent::__construct($id);

		$this->requires_id = true;

		$yui = new SwatYUI(array('tabview'));
		$this->html_head_entry_set->addEntrySet($yui->getHtmlHeadEntrySet());

		$this->addStyleSheet('packages/swat/styles/swat-note-book.css',
			Swat::PACKAGE_ID);
	}

	// }}}
	// {{{ public function addChild()

	/**
	 * Adds a {@link SwatNoteBookPage} to this notebook 
	 * 
	 * This method fulfills the {@link SwatUIParent} interface. It is used 
	 * by {@link SwatUI} when building a widget tree and should not need to be
	 * called elsewhere. To add a notebook page to a notebook, use 
	 * {@link SwatNoteBook::addPage()}.
	 *
	 * @param SwatNoteBookPage $child the notebook page to add.
	 *
	 * @throws SwatInvalidClassException if the given object is not an instance
	 *                                    of SwatNoteBookPage.
	 *
	 * @see SwatUIParent
	 * @see SwatNoteBook::addPage()
	 */
	public function addChild(SwatObject $child)
	{
		if ($child instanceof SwatNoteBookPage)
			$this->addPage($child);
		 else
			throw new SwatInvalidClassException(
				'Only SwatNoteBookPage objects may be nested within a '.
				'SwatNoteBook object.', 0, $child);
	}

	// }}}
	// {{{ public function addPage()

	/**
	 * Adds a {@link SwatNoteBookPage} to this notebook 
	 *
	 * @param SwatNoteBookPage $page the notebook page to add.
	 */
	public function addPage(SwatNoteBookPage $page)
	{
		$this->pages[] = $page;
		$page->parent = $this;
	}

	// }}}
	// {{{ public function init()

	/**
	 * Initializaes this notebook 
	 */
	public function init()
	{
		parent::init();
		foreach($this->pages as $page)
			$page->init();
	}

	// }}}
	// {{{ public function display()

	/**
	 * Displays this notebook
	 */
	public function display()
	{
		if (!$this->visible)
			return;

		$li_counter = 0;
		$div_tag = new SwatHtmlTag('div');
		$div_tag->id = $this->id;
		$div_tag->class = 'yui-navset';
		$div_tag->open();

		echo'<ul class="yui-nav">';
		foreach ($this->pages as $page)
		{
			$li_counter++;
			if ($li_counter == 1) {
				$li_tag = new SwatHtmlTag('li');
				$li_tag->class = 'selected';
			} else {
				$li_tag = new SwatHtmlTag('li');
			}

			$anchor_tag = new SwatHtmlTag('a');
			$anchor_tag->href = '#'.$page->id;

			$em_tag = new SwatHtmlTag('em');
			$em_tag->setContent($page->title);

			$li_tag->open();
			$anchor_tag->open();
			$em_tag->display();
			$anchor_tag->close();
			$li_tag->close();
		}
		echo'</ul>';

		echo '<div class="yui-content">';
		foreach ($this->pages as $page)
			$page->display();

		echo '</div>';
		$div_tag->close();
		Swat::displayInlineJavaScript($this->getInlineJavaScript());
	}

	// }}}
	// {{{ public function printWidgetTree()

	public function printWidgetTree()
	{
		echo get_class($this), ' ', $this->id;

		$children = $this->getChildren();
		if (count($children) > 0) {
			echo '<ul>';
			foreach ($children as $child) {
				echo '<li>';
				$child->printWidgetTree();
				echo '</li>';
			}
			echo '</ul>';
		}
	}

	// }}}
	// {{{ public function addMessage()

	/**
	 * Adds a message to this notebook
	 *
	 * @param SwatMessage the message to add.
	 *
	 * @see SwatMessage
	 */
	public function addMessage(SwatMessage $message)
	{
		$this->messages[] = $message;
	}

	// }}}
	// {{{ public function getMessages()

	/**
	 * Gets all messaages
	 *
	 * Gathers all messages from pages of this notebook and from this notebook
	 * itself.
	 *
	 * @return array an array of {@link SwatMessage} objects.
	 *
	 * @see SwatMessage
	 */
	public function getMessages()
	{
		$messages = $this->messages;

		foreach ($this->pages as $page)
			$messages = array_merge($messages, $page->getMessages());

		return $messages;
	}

	// }}}
	// {{{ public function hasMessage()

	/**
	 * Checks for the presence of messages
	 *
	 * @return boolean true if there is a message in the widget subtree
	 *                  starting with this notebook and false if there is not.
	 */
	public function hasMessage()
	{
		$has_message = false;

		foreach ($this->pages as $page) {
			if ($page->hasMessage()) {
				$has_message = true;
				break;
			}
		}

		return $has_message;
	}

	// }}}
	// {{{ public function getHtmlHeadEntrySet()

	/**
	 * Gets the SwatHtmlHeadEntry objects needed by this container
	 *
	 * @reutnr SwatHtmlHeadEntrySet the SwatHtmlHeadEntry objects needed by
	 * this container.
	 *
	 * @see SwatUIObject::getHtmlHeadEntrySet()
	 */
	public function getHtmlHeadEntrySet()
	{
		$set = parent::getHtmlHeadEntrySet();

			foreach ($this->pages as $child_widget)
			$set->addEntrySet($child_widget->getHtmlHeadEntrySet());

		return $set;
	}

	// }}}
	// {{{ public function getInlineJavaScript()

	/**
	 * Gets the inline JavaScript used by this notebook
	 *
	 * @return string the inline JavaScript used by this notebook.
	 */
	protected function getInlineJavaScript()
	{
		switch ($this->tab_position) {
		case self::POSITION_RIGHT:
			$position = 'right';
			break;
		case self::POSITION_LEFT:
			$position = 'left';
			break;
		case self::POSITION_BOTTOM:
			$position = 'bottom';
			break;
		case self::POSITION_TOP:
		default:
			$position = 'top';
			break;
		}

		return sprintf("var %s_obj = new YAHOO.widget.TabView(".
				"'%s', {orientation: '%s'});",
				$this->id, $this->id, $position);
	}

	// }}}
}

?>