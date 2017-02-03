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

namespace Pmclain\Twilio\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\Encryption\EncryptorInterface;

class Data extends AbstractHelper
{
  const GENERAL_CONFIG_PATH = 'sales_sms/general/';
  const ORDER_CONFIG_PATH = 'sales_sms/order/';
  const INVOICE_CONFIG_PATH = 'sales_sms/invoice/';
  const SHIPMENT_CONFIG_PATH = 'sales_sms/shipment/';

  /**
   * @var \Magento\Framework\Encryption\EncryptorInterface
   */
  protected $_encryptor;

  /**
   * @var \Magento\Framework\App\Config\ScopeConfigInterface
   */
  protected $scopeConfig;

  /**
   * Data constructor.
   * @param EncryptorInterface $encryptor
   * @param ScopeConfigInterface $scopeConfig
   */
  public function __construct(
    EncryptorInterface $encryptor,
    ScopeConfigInterface $scopeConfig
  ) {
    $this->_encryptor = $encryptor;
    $this->scopeConfig = $scopeConfig;
  }

  /**
   * @return bool
   */
  public function isTwilioEnabled() {
    if($this->scopeConfig->getValue(self::GENERAL_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)) {
      return true;
    }
    return false;
  }

  /**
   * @return string
   */
  public function getAccountSid() {
    return $this->scopeConfig->getValue(self::GENERAL_CONFIG_PATH . 'account_sid', ScopeInterface::SCOPE_STORE);
  }

  /**
   * @return string
   */
  public function getAccountAuthToken() {
    return $this->_encryptor->decrypt(
      $this->scopeConfig->getValue(self::GENERAL_CONFIG_PATH . 'auth_token', ScopeInterface::SCOPE_STORE)
    );
  }

  public function getTwilioPhone() {
    return $this->scopeConfig->getValue(self::GENERAL_CONFIG_PATH . 'twilio_phone', ScopeInterface::SCOPE_STORE);
  }

  /**
   * @return bool
   */
  public function isOrderMessageEnabled() {
    if($this->scopeConfig->getValue(self::ORDER_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)
      && $this->isTwilioEnabled()) {
      return true;
    }
    return false;
  }

  /**
   * @return string
   */
  public function getRawOrderMessage() {
    return $this->scopeConfig->getValue(self::ORDER_CONFIG_PATH . 'message', ScopeInterface::SCOPE_STORE);
  }

  /**
   * @return bool
   */
  public function isInvoiceMessageEnabled() {
    if($this->scopeConfig->getValue(self::INVOICE_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)
      && $this->isTwilioEnabled()) {
      return true;
    }
    return false;
  }

  /**
   * @return string
   */
  public function getRawInvoiceMessage() {
    return $this->scopeConfig->getValue(self::INVOICE_CONFIG_PATH . 'message', ScopeInterface::SCOPE_STORE);
  }

  /**
   * @return bool
   */
  public function isShipmentMessageEnabled() {
    if($this->scopeConfig->getValue(self::SHIPMENT_CONFIG_PATH . 'enabled', ScopeInterface::SCOPE_STORE)
      && $this->isTwilioEnabled()){
      return true;
    }
    return false;
  }

  /**
   * @return string
   */
  public function getRawShipmentMessage() {
    return $this->scopeConfig->getValue(self::SHIPMENT_CONFIG_PATH . 'message', ScopeInterface::SCOPE_STORE);
  }
}