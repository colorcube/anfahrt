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
 * Class FooterDataViewHelper
 *
 * @author Helmut Hummel <helmut.hummel@typo3.org>
 */
class FooterDataViewHelper extends \TYPO3\CMS\Fluid\Core\ViewHelper\AbstractViewHelper {

    /**
     * @var PageRenderer
     */
    protected $pageRenderer;

    /**
     * @param PageRenderer $pageRenderer
     */
    public function __construct(PageRenderer $pageRenderer = NULL) {
        if (!isset($GLOBALS['TSFE'])) {
            return;
        }
        $this->pageRenderer = $pageRenderer ?: \TYPO3\CMS\Core\Utility\GeneralUtility::makeInstance(\TYPO3\CMS\Core\Page\PageRenderer::class);
    }

	/**
	 * Renders HeaderData
	 *
	 * @return void
	*/
	public function render() {
		$this->pageRenderer->addFooterData($this->renderChildren());
	}

} 