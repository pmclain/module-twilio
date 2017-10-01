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

use Magento\Framework\Exception\NoSuchEntityException;

interface LogRepositoryInterface
{
    public function save(\Pmclain\Twilio\Api\Data\LogInterface $log);

    /**
     * @param string|int $logId
     * @throws NoSuchEntityException
     * @return Data\LogInterface
     */
    public function getById($logId);

    /**
     * @param string $sid
     * @throws NoSuchEntityException
     * @return Data\LogInterface
     */
    public function getBySid($sid);

    public function getList(
        \Magento\Framework\Api\SearchCriteriaInterface $searchCriteria
    );

    public function delete($logId);
}
