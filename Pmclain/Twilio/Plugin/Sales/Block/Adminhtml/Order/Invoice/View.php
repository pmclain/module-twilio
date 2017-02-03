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

namespace Pmclain\Twilio\Plugin\Sales\Block\Adminhtml\Order\Invoice;

use Pmclain\Twilio\Helper\Data;
use Magento\Sales\Block\Adminhtml\Order\Invoice\View as InvoiceView;
use Magento\Framework\UrlInterface;

class View
{
  /** @var \Pmclain\Twilio\Helper\Data */
  protected $_helper;

  /** @var \Magento\Framework\UrlInterface */
  protected $_urlBuilder;

  public function __construct(
    Data $helper,
    UrlInterface $url
  ) {
    $this->_helper = $helper;
    $this->_urlBuilder = $url;
  }

  public function beforeSetLayout(InvoiceView $view) {
    if(!$this->_helper->isInvoiceMessageEnabled()) { return; }

    $message = __('Are you sure you want to send a SMS to the customer?');
    $url = $this->_urlBuilder->getUrl('twilio/invoice/send', ['id' => $view->getInvoice()->getId()]);

    $view->addButton(
      'send_invoice_sms',
      [
        'label' => __('Send Invoice SMS'),
        'class' => 'send-sms',
        'onclick' => "confirmSetLocation('{$message}', '{$url}')"
      ]
    );
  }
}