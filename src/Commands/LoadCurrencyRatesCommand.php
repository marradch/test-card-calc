<?php

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Services\CurrencyRatesLoader;
use Psr\Log\LoggerInterface;

#[AsCommand(name: 'app:load-currency-rates')]
class LoadCurrencyRatesCommand extends Command
{
    public function __construct (
        protected LoggerInterface $logger,
        protected CurrencyRatesLoader $loader,
    ) {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        echo "start to load rates \n";
        try {
            $content = $this->loader->load();
        } catch (\Exception $e) {
            $this->logger->error('Currency rate loading error:' . $e->getMessage());

            return Command::FAILURE;
        }

        // ... put here the code to create the user

        // this method must return an integer number with the "exit status code"
        // of the command. You can also use these constants to make code more readable

        // return this if there was no problem running the command
        // (it's equivalent to returning int(0))
        return Command::SUCCESS;

        // or return this if some error happened during the execution
        // (it's equivalent to returning int(1))
        // return Command::FAILURE;

        // or return this to indicate incorrect command usage; e.g. invalid options
        // or missing arguments (it's equivalent to returning int(2))
        // return Command::INVALID
    }
}