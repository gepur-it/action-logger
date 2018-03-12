<?php
/**
 * @author Marina Mileva <m934222258@gmail.com>
 * @since 28.11.17
 */

namespace GepurIt\ActionLoggerBundle\Repository;

use GepurIt\ActionLoggerBundle\Document\LogRow;
use Doctrine\ODM\MongoDB\DocumentRepository;
use DateTime;

/**
 * Class LogRowRepository
 * @package ActionLoggerBundle\Repository
 */
class LogRowRepository extends DocumentRepository
{
    /**
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @param string $managerId
     * @return LogRow[]
     */
    public function findFinishedMailLogsByDate(DateTime $dateFrom, DateTime $dateTo, string $managerId)
    {
        $query = $this->createQueryBuilder()
            ->field('actionName')->in([LogRow::ACTION__MAIL_WITHOUT_ANSWER, LogRow::ACTION__MAIL_WITH_ANSWER])
            ->field('createdAt')->gte($dateFrom)->lte($dateTo)
            ->field('authorId')->equals($managerId)
            ->getQuery();

        return $query->toArray();
    }

    /**
     * @param string $emailId
     * @return LogRow|null
     */
    public function findTakeNewClientMailLogByEmailId($emailId): ?LogRow
    {
        $records =  $this->createQueryBuilder()
            ->field('actionName')->equals(LogRow::ACTION__TAKE_NEW_CLIENT_MAIL)
            ->field('actionData.email_id')->equals($emailId)
            ->limit(1)
            ->getQuery()->toArray();

        return array_pop($records);
    }

    /**
     * @param $emailAddress
     * @param string $userId
     * @return LogRow|null
     */
    public function findClosedClientMailByEmailAndUser($emailAddress, $userId): ?LogRow
    {
        $builder = $this->createQueryBuilder();
        $records =  $builder
            ->field('actionName')->equals(LogRow::ACTION__CLOSE_RELATION)
            ->field('actionData.email_address')->equals($emailAddress)
            ->field('actionData.manager_id')->equals($userId)
            ->limit(1)
            ->getQuery()->toArray();

        return array_pop($records);
    }

    /**
     * @param string $managerId
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return LogRow[]
     */
    public function findOutgoingLogsByManagerAndInterval(string $managerId, DateTime $dateFrom, DateTime $dateTo): array
    {
        $query = $this->createQueryBuilder()
            ->field('actionName')->in([LogRow::ACTION__NEW_OUTGOING_EMAIL, LogRow::ACTION__MAIL_WITH_ANSWER])
            ->field('createdAt')->gte($dateFrom)->lte($dateTo)
            ->field('authorId')->equals($managerId)
            ->getQuery();
        return $query->toArray();
    }

    /**
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return string[]
     */
    public function findActiveManagerIdsByInterval(DateTime $dateFrom, DateTime $dateTo): array
    {
        /** @var LogRow[] $query */
        $query = $this->createQueryBuilder()
            ->select('authorId')
            ->field('actionName')->in([LogRow::ACTION__NEW_OUTGOING_EMAIL, LogRow::ACTION__MAIL_WITH_ANSWER])
            ->field('createdAt')->gte($dateFrom)->lte($dateTo)
            ->getQuery()->toArray();
        $ids = [];
        foreach ($query as $row) {
            $ids[] = $row->getAuthorId();
        }

        return array_unique($ids);
    }

    /**
     * @param DateTime $dateFrom
     * @param DateTime $dateTo
     * @return array
     */
    public function findOutgoingLogsByInterval(DateTime $dateFrom, DateTime $dateTo): array
    {
        $query = $this->createQueryBuilder()
            ->field('actionName')->in([LogRow::ACTION__NEW_OUTGOING_EMAIL, LogRow::ACTION__MAIL_WITH_ANSWER])
            ->field('createdAt')->gte($dateFrom)->lte($dateTo)
            ->getQuery();
        return $query->toArray();
    }
}