<?php


namespace Xigen\QuoteCleaner\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Clean console class
 */
class Clean extends Command
{
    const CLEAN_ARGUMENT = 'clean';
    const LIMIT_OPTION = 'limit';

    /**
     * @var \Psr\Log\LoggerInterface
     */
    private $logger;

    /**
     * @var \Magento\Framework\App\State
     */
    private $state;

    /**
     * @var \Magento\Framework\Stdlib\DateTime\DateTime
     */
    private $dateTime;

    /**
     * @var \Xigen\QuoteCleaner\Helper\Cleaner
     */
    private $cleanerHelper;

    /**
     * @var InputInterface
     */
    protected $input;

    /**
     * @var OutputInterface
     */
    protected $output;

    /**
     * Clean constructor.
     * @param \Psr\Log\LoggerInterface $logger
     * @param \Magento\Framework\App\State $state
     * @param \Magento\Framework\Stdlib\DateTime\DateTime $dateTime
     * @param \Xigen\QuoteCleaner\Helper\Cleaner $cleanerHelper
     */
    public function __construct(
        \Psr\Log\LoggerInterface $logger,
        \Magento\Framework\App\State $state,
        \Magento\Framework\Stdlib\DateTime\DateTime $dateTime,
        \Xigen\QuoteCleaner\Helper\Cleaner $cleanerHelper
    ) {
        $this->logger = $logger;
        $this->state = $state;
        $this->dateTime = $dateTime;
        $this->cleanerHelper = $cleanerHelper;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output
    ) {
        $this->input = $input;
        $this->output = $output;

        $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_GLOBAL);

        $clean = $input->getArgument(self::CLEAN_ARGUMENT) ?: false;

        if ($clean) {
            $this->output->writeln((string) __('[%1] Start', $this->dateTime->gmtDate()));
            $this->output->writeln('Cleaning customer quotes');
            $result = $this->cleanerHelper->cleanCustomerQuotes();
            $this->output->writeln((string) __('Result: in %1 cleaned %2 customer quotes', $result['quote_duration'],
                $result['quote_count']));
            $this->output->writeln('Cleaning anonymous quotes');
            $result = $this->cleanerHelper->cleanAnonymousQuotes();
            $this->output->writeln((string) __('Result: in %1 cleaned %2 anonymous quotes', $result['quote_duration'],
                $result['quote_count']));
            $this->output->writeln((string) __('[%1] Finish', $this->dateTime->gmtDate()));
        }
    }

    /**
     * {@inheritdoc}
     * xigen:quote:cleaner [-l|--limit [LIMIT]] [--] <clean>.
     */
    protected function configure()
    {
        $this->setName('xigen:quote:cleaner');
        $this->setDescription('Clean old quote from Magento');
        $this->setDefinition([
            new InputArgument(self::CLEAN_ARGUMENT, InputArgument::REQUIRED, 'Clean'),
            new InputOption(self::LIMIT_OPTION, '-l', InputOption::VALUE_OPTIONAL, 'Limit'),
        ]);
        parent::configure();
    }
}
