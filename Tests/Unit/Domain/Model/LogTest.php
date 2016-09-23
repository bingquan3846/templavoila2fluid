<?php

namespace ZerosOnes\templavoila2fluid\Tests\Unit\Domain\Model;

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2016 Bingquan bao
 *
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
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

/**
 * Test case for class \ZerosOnes\templavoila2fluid\Domain\Model\Log.
 *
 * @copyright Copyright belongs to the respective authors
 * @license http://www.gnu.org/licenses/gpl.html GNU General Public License, version 3 or later
 *
 * @author Bingquan bao 
 */
class LogTest extends \TYPO3\CMS\Core\Tests\UnitTestCase {
	/**
	 * @var \ZerosOnes\templavoila2fluid\Domain\Model\Log
	 */
	protected $subject = NULL;

	public function setUp() {
		$this->subject = new \ZerosOnes\templavoila2fluid\Domain\Model\Log();
	}

	public function tearDown() {
		unset($this->subject);
	}

	/**
	 * @test
	 */
	public function getBodyContentReturnsInitialValueForString() {
		$this->assertSame(
			'',
			$this->subject->getBodyContent()
		);
	}

	/**
	 * @test
	 */
	public function setBodyContentForStringSetsBodyContent() {
		$this->subject->setBodyContent('Conceived at T3CON10');

		$this->assertAttributeEquals(
			'Conceived at T3CON10',
			'bodyContent',
			$this->subject
		);
	}
}
