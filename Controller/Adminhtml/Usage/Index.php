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

namespace Pmclain\Twilio\Controller\Adminhtml\Usage;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\View\Result\PageFactory;

class Index extends Action
{
  /** @var \Magento\Framework\View\Result\PageFactory */
  protected $resultPageFactory;

  public function __construct(
    Context $context,
    PageFactory $pageFactory
  ) {
    parent::__construct($context);
    $this->resultPageFactory = $pageFactory;
  }

  public function execute() {
    $resultPage = $this->resultPageFactory->create();
    $resultPage->getConfig()->getTitle()->prepend(__('Twilio SMS Usage'));

    return $resultPage;
  }
}