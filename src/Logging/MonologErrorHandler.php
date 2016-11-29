<?php

namespace SilverStripe\Logging;

use Psr\Log\LoggerInterface;
use Monolog\ErrorHandler;

/**
 * Simple adaptor to start Monolog\ErrorHandler
 */
class MonologErrorHandler
{
    private $logger;

    /**
     * Set the PSR-3 logger to send errors & exceptions to
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function start()
    {
        if (!$this->logger) {
            throw new \InvalidArgumentException("No Logger property passed to MonologErrorHandler."
                . "Is your Injector config correct?");
        }

        ErrorHandler::register($this->logger);
    }
}
