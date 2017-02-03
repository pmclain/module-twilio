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

namespace Pmclain\Twilio\Controller\Adminhtml\Shipment;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Sales\Api\ShipmentRepositoryInterface;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\InputException;
use Pmclain\Twilio\Model\Adapter\Order\Shipment as ShipmentAdapter;

class Send extends Action
{
  /** @var \Magento\Sales\Api\ShipmentRepositoryInterface */
  protected $_shipmentRepository;

  /** @var \Pmclain\Twilio\Model\Adapter\Order\Shipment */
  protected $_shipmentAdapter;

  public function __construct(
    ShipmentAdapter $shipmentAdapter,
    ShipmentRepositoryInterface $shipmentRepository,
    Context $context
  ) {
    parent::__construct($context);
    $this->_shipmentAdapter = $shipmentAdapter;
    $this->_shipmentRepository = $shipmentRepository;
  }

  public function execute() {
    $shipment = $this->_initShipment();
    if($shipment) {
      $resultRedirect = $this->resultRedirectFactory->create()->setPath(
        'sales/shipment/view',
        ['shipment_id' => $shipment->getEntityId()]
      );

      if($shipment->getShippingAddress()->getSmsAlert()) {
        $result = $this->_shipmentAdapter->sendOrderSms($shipment);
        $this->messageManager->addSuccessMessage(__('The SMS has been sent.'));

        return $resultRedirect;
      }

      $this->messageManager->addErrorMessage(__('The shipping telephone number did not opt-in for SMS notifications.'));

      return $resultRedirect;
    }

    return $this->resultRedirectFactory->create()->setPath('sales/shipment/*');
  }

  /**
   * @return false|\Magento\Sales\Model\Order\Shipment
   */
  public function _initShipment() {
    $id = $this->getRequest()->getParam('id');
    try {
      $shipment = $this->_shipmentRepository->get($id);
    }catch (NoSuchEntityException $e) {
      $this->messageManager->addErrorMessage(__('This shipment no longer exists.'));
      return false;
    }catch (InputException $e) {
      $this->messageManager->addErrorMessage(__('This shipment no longer exists.'));
      return false;
    }

    return $shipment;
  }
}