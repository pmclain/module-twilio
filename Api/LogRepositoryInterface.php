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

namespace Pmclain\Twilio\Api;

interface LogRepositoryInterface
{
  public function save(\Pmclain\Twilio\Api\Data\LogInterface $log);

  public function getById($logId);

  public function getList(\Magento\Framework\Api\SearchCriteriaInterface $searchCriteria);

  public function delete($logId);
}