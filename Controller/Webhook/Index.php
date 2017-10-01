<?php

namespace Pmclain\Twilio\Controller\Webhook;

use Magento\Framework\App\Action\Action;
use Magento\Framework\App\Action\Context;
use Pmclain\Twilio\Api\LogRepositoryInterface;
use Magento\Framework\Stdlib\DateTime\DateTimeFactory;
use Magento\Framework\Controller\ResultFactory;

class Index extends Action
{
    /**
     * @var LogRepositoryInterface
     */
    protected $logRepository;

    /**
     * @var DateTimeFactory
     */
    protected $dateTimeFactory;

    /**
     * Index constructor.
     * @param Context $context
     * @param LogRepositoryInterface $logRepository
     * @param DateTimeFactory $dateTimeFactory
     */
    public function __construct(
        Context $context,
        LogRepositoryInterface $logRepository,
        DateTimeFactory $dateTimeFactory
    ) {
        parent::__construct($context);
        $this->logRepository = $logRepository;
        $this->dateTimeFactory = $dateTimeFactory;
    }

    /**
     * @return \Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        if (!$this->getRequest()->isPost()) {
            $resultRedirect = $this->resultRedirectFactory->create();

            return $resultRedirect->setUrl($this->_redirect->getRefererUrl());
        }

        $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);

        try {
            $log = $this->logRepository->getBySid($this->getRequest()->getParam('SmsSid'));
            $log->setResult($this->getRequest()->getParam('SmsStatus'));
            $log->setUpdatedAt($this->dateTimeFactory->create()->timestamp());
            $this->logRepository->save($log);

            $resultJson->setData(['message' => 'success']);
        } catch (\Exception $e) {
            $resultJson->setData(['message' => $e->getMessage()]);
        }

        return $resultJson;
    }
}
