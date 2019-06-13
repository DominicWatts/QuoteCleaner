<?php


namespace Xigen\QuoteCleaner\Cron;

/**
 * Cleaner cron class
 */
class Cleaner
{
    protected $logger;

    /**
     * Constructor
     *
     * @param \Psr\Log\LoggerInterface $logger
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Xigen\QuoteCleaner\Helper\Cleaner $cleanerHelper
    ) {
        $this->logger = $logger;
        $this->cleanerHelper = $cleanerHelper;
    }

    /**
     * Execute the cron
     *
     * @return void
     */
    public function execute()
    {
        // $this->logger->addInfo("Cronjob Cleaner is executed.");
    }
}
