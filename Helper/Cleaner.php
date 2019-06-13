<?php


namespace Xigen\QuoteCleaner\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

/**
 * Cleaner helper class
 */
class Cleaner extends AbstractHelper
{
    const CONFIG_XML_OLDER_THAN = 'quote_cleaner/quote_cleaner/clean_quoter_older_than';
    const CONFIG_XML_ANONYMOUS_OLDER_THAN = 'quote_cleaner/quote_cleaner/clean_anonymous_quotes_older_than';
    const CONFIG_XML_LIMIT = 'quote_cleaner/quote_cleaner/limit';

    private $logger;
    private $limit;
    private $connection;
    private $resource;
    private $startTime = null;
    private $endTime = null;
    private $report = [];

    /**
     * @param \Magento\Framework\App\Helper\Context $context
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\ResourceConnection $resource
    ) {
        $this->logger = $logger;
        $this->connection = $resource->getConnection();
        $this->resource = $resource;
        parent::__construct($context);
    }

    /**
     * Get quotes older than
     * @return string
     */
    public function getQuotesOlderThan()
    {
        return $this->scopeConfig->getValue(self::CONFIG_XML_OLDER_THAN, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get anonymous quotes older than
     * @return string
     */
    public function getAnonymousQuotesOlderThan()
    {
        return $this->scopeConfig->getValue(self::CONFIG_XML_ANONYMOUS_OLDER_THAN, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Get limit
     * @return string
     */
    public function getLimit()
    {
        return $this->scopeConfig->getValue(self::CONFIG_XML_LIMIT, ScopeInterface::SCOPE_STORE);
    }

    /**
     * Initiate variables
     * @return void
     */
    public function initiate()
    {
        $this->limit = min($this->getLimit(), 50000);
        $this->customerOlderThan = max($this->getQuotesOlderThan(), 7);
        $this->anonymousOlderThan = max($this->getAnonymousQuotesOlderThan(), 7);
        $this->tableName = $this->getTableName();
        $this->startTime = time();
        $this->report = [];
    }
    

    /**
     * Clean customer quotes
     * @return array
     */
    public function cleanCustomerQuotes()
    {
        $this->initiate();

        $select = $this->connection
            ->select()
            ->from($this->tableName)
            ->where('NOT ISNULL(customer_id) AND customer_id != 0')
            ->where('updated_at < DATE_SUB(NOW(), INTERVAL ? DAY)', $this->customerOlderThan)
            ->limit($this->limit);

        $report = $this->doDeleteFromSelect($select);
        return $report;
    }

    /**
     * Clean anonymous quotes
     * @return array
     */
    public function cleanAnonymousQuotes()
    {
        $this->initiate();

        $select = $this->connection
            ->select()
            ->from($this->tableName)
            ->where('ISNULL(customer_id) OR customer_id = 0')
            ->where('updated_at < DATE_SUB(NOW(), INTERVAL ? DAY)', $this->customerOlderThan)
            ->limit($this->limit);

        $report = $this->doDeleteFromSelect($select);
        return $report;
    }

    public function doDeleteFromSelect($select)
    {
        try {
            $this->report['quote_duration'] = time() - $this->startTime;
            // deliberate empty second argument
            $query = $this->connection->deleteFromSelect($select, []);
            $statement = $this->connection->query($query);
            $this->report['quote_count'] = $statement->rowCount();
        } catch (\Exception $e) {
            $this->logger->critical($e);
        }
        return $this->report;
    }

    /**
     * Get quote table name
     * @return string
     */
    public function getTableName()
    {
        return $this->resource->getTableName('quote');
    }
}
