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

namespace Pmclain\Twilio\Controller\Adminhtml\Log;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Ui\Component\MassAction\Filter;
use Pmclain\Twilio\Model\LogRepository;
use Pmclain\Twilio\Model\ResourceModel\Log\CollectionFactory;
use Magento\Framework\Controller\ResultFactory;

class MassDelete extends Action
{
    /**
     * Authorization level of a basic admin session
     * @see _isAllowed()
     */
    const ADMIN_RESOURCE = 'Pmclain_Twilio::sms';

    protected $_filter;

    protected $_logRepository;

    protected $_collectionFactory;

    public function __construct(
        Context $context,
        Filter $filter,
        LogRepository $logRepository,
        CollectionFactory $collectionFactory
    ) {
        $this->_filter = $filter;
        $this->_logRepository = $logRepository;
        $this->_collectionFactory = $collectionFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $collection = $this->_filter->getCollection($this->_collectionFactory->create());

        $count = $collection->getSize();

        foreach ($collection->getAllIds() as $logId) {
            $this->_logRepository->delete($logId);
        }

        $this->messageManager->addSuccessMessage($count . __(' log item(s) have been deleted.'));

        $resultRedirect = $this->resultRedirectFactory->create();
        return $resultRedirect->setPath('twilio/usage/');
    }
}
