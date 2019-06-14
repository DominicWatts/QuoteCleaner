<?php


namespace Xigen\QuoteCleaner\Cron;

/**
 * Cleaner cron class
 */
class Cleaner
{
    protected $logger;
    protected $cleanerHelper;
    protected $dateTime;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Xigen\QuoteCleaner\Helper\Cleaner $cleanerHelper,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
    ) {
        $this->logger = $logger;
        $this->cleanerHelper = $cleanerHelper;
        $this->dateTime = $dateTime;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        if ($this->cleanerHelper->getCron()) {
            $this->logger->addInfo((string) __('[%1] Cleaner Cronjob Start', $this->dateTime->gmtDate()));
            $this->logger->addInfo('Cleaning customer quotes');
            $result = $this->cleanerHelper->cleanCustomerQuotes();
            $this->logger->addInfo((string) __('Result: in %1 cleaned %2 customer quotes', $result['quote_duration'], $result['quote_count']));
            $this->logger->addInfo('Cleaning anonymous quotes');
            $result = $this->cleanerHelper->cleanAnonymousQuotes();
            $this->logger->addInfo((string) __('Result: in %1 cleaned %2 anonymous quotes', $result['quote_duration'], $result['quote_count']));
            $this->logger->addInfo((string) __('[%1] Cleaner Cronjob Finish', $this->dateTime->gmtDate()));
        }
    }
}
