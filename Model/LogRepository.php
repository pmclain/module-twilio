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

namespace Pmclain\Twilio\Model;

use Magento\Framework\Api\SortOrder;
use Magento\Framework\Exception\NoSuchEntityException;
use Pmclain\Twilio\Api\Data\LogSearchResultsInterfaceFactory;
use Pmclain\Twilio\Api\Data\LogInterface;
use Pmclain\Twilio\Model\LogFactory;
use Pmclain\Twilio\Api\LogRepositoryInterface;
use Pmclain\Twilio\Model\ResourceModel\Log as LogResource;
use Pmclain\Twilio\Model\ResourceModel\Log\CollectionFactory;

class LogRepository implements LogRepositoryInterface
{
  protected $_logResource;

  protected $_logFactory;

  protected $_collectionFactory;

  protected $_searchResultsFactory;

  public function __construct(
    LogResource $logResource,
    LogFactory $logFactory,
    CollectionFactory $collectionFactory,
    LogSearchResultsInterfaceFactory $logSearchResultsFactory
  ) {
    $this->_logResource = $logResource;
    $this->_logFactory = $logFactory;
    $this->_collectionFactory = $collectionFactory;
    $this->_searchResultsFactory = $logSearchResultsFactory;
  }

  public function save(LogInterface $log) {
    $this->_logResource->save($log);
    return $log->getId();
  }

  public function getById($logId) {
    $log = $this->_logFactory->create();
    $this->_logResource->load($log, $logId);
    if (!$log->getId()) {
      throw new NoSuchEntityException('Log entity does not exits.');
    }

    return $log;
  }

  public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria) {
    $collection = $this->_collectionFactory->create();
    foreach((array)$searchCriteria->getFilterGroups() as $group) {
      $this->addFilterGroupToCollection($group, $collection);
    }

    foreach((array)$searchCriteria->getSortOrders() as $sortOrder) {
      $field = $sortOrder->getField();
      $collection->addOrder(
        $field,
        $this->getDirection($sortOrder->getDirection())
      );
    }

    $collection->setCurPage($searchCriteria->getCurrentPage());
    $collection->setPageSize($searchCriteria->getPageSize());
    $collection->load();
    $searchResults = $this->_searchResultsFactory->create();
    $searchResults->setCriteria($searchCriteria);

    $logs = [];
    foreach($collection as $log) {
      $logs[] = $log;
    }

    $searchResults->setItems($logs);
    $searchResults->setTotalCount($collection->getSize());

    return $searchResults;
  }

  public function delete($logId) {
    $log = $this->_logFactory->create();
    $log->setId($logId);
    if($this->_logResource->delete($log)) {
      return true;
    }
    return false;
  }

  protected function addFilterGroupToCollection($group, $collection) {
    $fields = [];
    $conditions = [];

    foreach($group->getFilters() as $filter) {
      $condition = $filter->getConditionType() ? : 'eq';
      $field = $filter->getField();
      $value = $filter->getValue();
      $fields[] = $field;
      $conditions[] = [$condition => $value];
    }

    $collection->addFieldToFilter($fields, $conditions);
  }
}