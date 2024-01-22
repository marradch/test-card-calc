<?php

namespace App\Commands;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Services\CurrencyRatesLoader;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
        $cache = new FilesystemAdapter();

        $cache->delete('currency_rates');
        $cache->get('currency_rates', function (ItemInterface $item): ?array {
            $item->expiresAfter(3600);

            try {
                return $this->loader->load();
            } catch (\Exception $e) {
                $this->logger->error('Currency rate loading error:' . $e->getMessage());
            }
        });

        return Command::SUCCESS;
    }
}
