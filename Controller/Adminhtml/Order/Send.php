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

namespace Pmclain\Twilio\Controller\Adminhtml\Order;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\OrderRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Pmclain\Twilio\Model\Adapter\Order as OrderAdapter;

class Send extends Action
{
  /**
   * Authorization level of a basic admin session
   * @see _isAllowed()
   */
  const ADMIN_RESOURCE = 'Pmclain_Twilio::sms';

  /** @var \Magento\Sales\Api\OrderRepositoryInterface */
  protected $_orderRepository;

  /** @var \Pmclain\Twilio\Model\Adapter\Order */
  protected $_orderAdapter;

  public function __construct(
    OrderRepositoryInterface $orderRepository,
    OrderAdapter $orderAdapter,
    Context $context
  ) {
    parent::__construct($context);
    $this->_orderRepository = $orderRepository;
    $this->_orderAdapter = $orderAdapter;
  }

  public function execute() {
    $order = $this->_initOrder();
    if($order) {
      $resultRedirect = $this->resultRedirectFactory->create()->setPath(
        'sales/order/view',
        ['order_id' => $order->getEntityId()]
      );

      /** TODO: this is not clean */
      if(!$shippingAddress = $order->getShippingAddress()) {
        $this->messageManager->addErrorMessage(__('The order does not have a shipping address.'));

        return $resultRedirect;
      }

      if($shippingAddress->getSmsAlert()) {
        $result = $this->_orderAdapter->sendOrderSms($order);
        $this->messageManager->addSuccessMessage(__('The SMS has been sent.'));

        return $resultRedirect;
      }

      $this->messageManager->addErrorMessage(__('The shipping telephone number did not opt-in for SMS notifications.'));

      return $resultRedirect;
    }

    return $this->resultRedirectFactory->create()->setPath('sales/*/');
  }

  /**
   * @return false|\Magento\Sales\Model\Order
   */
  protected function _initOrder() {
    $id = $this->getRequest()->getParam('id');
    try {
      $order = $this->_orderRepository->get($id);
    } catch (NoSuchEntityException $e) {
      $this->messageManager->addErrorMessage(__('This order no longer exists.'));
      return false;
    } catch (InputException $e) {
      $this->messageManager->addErrorMessage(__('This order no longer exists.'));
      return false;
    }

    return $order;
  }
}