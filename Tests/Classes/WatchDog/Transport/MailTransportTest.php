<?php

namespace DMK\Mklog\WatchDog\Transport;

use TYPO3\CMS\Core\Utility\GeneralUtility;

/***************************************************************
 * Copyright notice
 *
 * (c) 2016 DMK E-BUSINESS GmbH <dev@dmk-ebusiness.de>
 * All rights reserved
 *
 * This script is part of the TYPO3 project. The TYPO3 project is
 * free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * The GNU General Public License can be found at
 * http://www.gnu.org/copyleft/gpl.html.
 *
 * This script is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

if (!\class_exists('tx_rnbase')) {
    require_once \tx_rnbase_util_Extensions::extPath(
        'rn_base',
        'class.tx_rnbase.php'
    );
}
if (!\class_exists('DMK\\Mklog\\Tests\\BaseTestCase')) {
    require_once \tx_rnbase_util_Extensions::extPath(
        'mklog',
        'Tests/Classes/BaseTestCase.php'
    );
}

/**
 * Class SchedulerWatchDogTest.
 *
 * @author  Hannes Bochmann
 * @license http://www.gnu.org/licenses/lgpl.html
 *          GNU Lesser General Public License, version 3 or later
 */
class SchedulerWatchDogTest extends \DMK\Mklog\Tests\BaseTestCase
{
    protected function setUp()
    {
        parent::setUp();

        $GLOBALS['TYPO3_CONF_VARS']['SYS']['sitename'] = 'test site';
    }

    /**
     * @group unit
     * @test
     */
    public function testSendMailWithDefaultSubject()
    {
        $mailUtility = $this->getMock('tx_rnbase_util_Mail', ['send', 'setSubject']);
        $mailUtility->expects(self::once())
            ->method('setSubject')
            ->with('DevLog WatchDog on site test site');
        GeneralUtility::addInstance('tx_rnbase_util_Mail', $mailUtility);

        $mailTransport = GeneralUtility::makeInstance(MailTransport::class);
        $mailTransport->initialize(GeneralUtility::makeInstance(
            'Tx_Rnbase_Domain_Model_Data'
        ));

        $this->callInaccessibleMethod($mailTransport, 'sendMail', 'mail content');
    }

    /**
     * @group unit
     * @test
     */
    public function testSendMailWithSubjectFromOptions()
    {
        $mailUtility = $this->getMock('tx_rnbase_util_Mail', ['send', 'setSubject']);
        $mailUtility->expects(self::once())
            ->method('setSubject')
            ->with('test subject on test site');
        GeneralUtility::addInstance('tx_rnbase_util_Mail', $mailUtility);

        $mailTransport = GeneralUtility::makeInstance(MailTransport::class);
        $mailTransport->initialize(GeneralUtility::makeInstance(
            'Tx_Rnbase_Domain_Model_Data', ['mail_subject' => 'test subject on %s']
        ));

        $this->callInaccessibleMethod($mailTransport, 'sendMail', 'mail content');
    }
}
