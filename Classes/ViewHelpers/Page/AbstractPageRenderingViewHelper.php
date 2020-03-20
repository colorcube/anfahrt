<?php
namespace Colorcube\Anfahrt\ViewHelpers\Page;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2014 Helmut Hummel <helmut.hummel@typo3.org>
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *  A copy is found in the text file GPL.txt and important notices to the license
 *  from the author is found in LICENSE.txt distributed with these scripts.
 *
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

use TYPO3\CMS\Core\Page\PageRenderer;

/**
 * Class AbstractPageRenderingViewHelper
 */
abstract class AbstractPageRenderingViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

	/**
	 * @var PageRenderer
	 */
	protected $pageRenderer;

	/**
	 * @param PageRenderer $pageRenderer
	 */
	public function __construct(PageRenderer $pageRenderer = NULL) {
		$this->registerArgument('searchValues', 'array', 'Search values', FALSE);
		$this->registerArgument('replaceValues', 'array', 'Replacement values', FALSE);
		if (!isset($GLOBALS['TSFE'])) {
			return;
		}
		$this->pageRenderer = $pageRenderer ?: \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
	}

	/**
	 * Helper method which triggers the rendering of everything between the
	 * opening and the closing tag.
	 *
	 * @return mixed The finally rendered child nodes.
	 */
	public function renderChildren() {
		$renderedChildren = parent::renderChildren();
		if (is_string($renderedChildren) && !empty($this->arguments['searchValues']) && !empty($this->arguments['replaceValues'])) {
			$renderedChildren = str_replace($this->arguments['searchValues'], $this->arguments['replaceValues'], $renderedChildren);
		}
		return $renderedChildren;
	}

}
