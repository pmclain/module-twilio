<?php
/**
 * Pmclain_Twilio extension
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the GPL v3 License
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * https://www.gnu.org/licenses/gpl.txt
 *
 * @category       Pmclain
 * @package        Twilio
 * @copyright      Copyright (c) 2017
 * @license        https://www.gnu.org/licenses/gpl.txt GPL v3 License
 */

namespace Pmclain\Twilio\Model\ResourceModel;

use Magento\Framework\Model\ResourceModel\Db\AbstractDb;

class Log extends AbstractDb
{
  /**
   * @return void
   */
  protected function _construct() {
    $this->_init('pmclain_twilio_log', 'id');
  }
}