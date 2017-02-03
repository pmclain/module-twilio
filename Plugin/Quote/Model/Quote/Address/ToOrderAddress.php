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

namespace Pmclain\Twilio\Plugin\Quote\Model\Quote\Address;

class ToOrderAddress
{
  public function aroundConvert(
    \Magento\Quote\Model\Quote\Address\ToOrderAddress $subject,
    \Closure $proceed,
    \Magento\Quote\Model\Quote\Address $address,
    $data = []
  ) {
    $result = $proceed($address, $data);

    if ($address->getSmsAlert()) {
      $result->setSmsAlert($address->getSmsAlert());
    }

    return $result;
  }
}