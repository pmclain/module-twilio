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

namespace Pmclain\Twilio\Api\Data;

interface LogInterface
{
    public function getId();

    public function getEntityId();

    public function getEntityTypeId();

    public function getRecipientPhone();

    public function getIsError();

    public function getResult();

    public function getTimestamp();

    public function setId($id);

    public function setEntityId($entityId);

    public function setEntityTypeId($entityTypeId);

    public function setRecipientPhone($recipientPhone);

    public function setIsError($isError);

    public function setResult($result);
}
