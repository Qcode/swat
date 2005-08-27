<?php

require_once 'DemoPage.php';
require_once 'Swat/SwatTreeNode.php';

/**
 * A demo using checkboxes
 *
 * This page sets up a tree for use in the SwatCheckboxTree demo.
 *
 * @package   SwatDemo
 * @copyright 2005 silverorange
 * @license   http://www.gnu.org/copyleft/lesser.html LGPL License 2.1
 */
class Checkbox extends DemoPage
{
	public function initUI()
	{
		$tree = new SwatTreeNode(array('title' => 'test'));

		$apples = new SwatTreeNode(array('title' => 'Apple'));
		$apples->addChild(new SwatTreeNode(array('title' => 'Mackintosh', 'value' => 0)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Courtland', 'value' => 1)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Golden Delicious', 'value' => 2)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Fuji', 'value' => 3)));
		$apples->addChild(new SwatTreeNode(array('title' => 'Granny Smith', 'value' => 4)));
		
		$oranges = new SwatTreeNode(array('title' => 'Orange'));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Navel', 'value' => 5)));

		$blood_oranges = new SwatTreeNode(array('title' => 'Blood'));
		$blood_oranges->addChild(new SwatTreeNode(array('title' => 'Doble Fina', 'value' => 6)));
		$blood_oranges->addChild(new SwatTreeNode(array('title' => 'Entrefina', 'value' => 7)));
		$blood_oranges->addChild(new SwatTreeNode(array('title' => 'Sanguinelli', 'value' => 8)));
		$oranges->addChild($blood_oranges);

		$oranges->addChild(new SwatTreeNode(array('title' => 'Florida', 'value' => 9)));
		$oranges->addChild(new SwatTreeNode(array('title' => 'California', 'value' => 10)));
		$oranges->addChild(new SwatTreeNode(array('title' => 'Mandarin', 'value' => 11)));
		
		$tree->addChild($apples);
		$tree->addChild($oranges);

		$checkbox_tree = $this->ui->getWidget('checkbox_tree');
		$checkbox_tree->tree = $tree;
	}
}

?>
