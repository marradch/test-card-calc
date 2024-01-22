<?php

namespace App\Services;

use App\Requests\CartRequest;
use App\Services\CurrencyRatesLoader;
use Psr\Log\LoggerInterface;
use Symfony\Contracts\Cache\ItemInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class CartCalculator
{
    protected const BASE_CURRENCY = CurrencyEnum::USD->value;

    public function __construct (
        protected LoggerInterface $logger,
        protected CurrencyRatesLoader $loader,
    ) {}

    public function calculateCartTotalByRequest(CartRequest $cartRequest): float
    {
        $rates = $this->getRates();

        $result = 0.0;
        if ($cartRequest->checkoutCurrency === self::BASE_CURRENCY) {
            $result = $this->calculateCartTotalByRequestForBaseCurrency($cartRequest->items, $rates);
        } else {
            $result = $this->calculateCartTotalByRequestForNotBaseCurrency($cartRequest, $rates);
        }

        return $result;
    }

    protected function getRates(): ?array
    {
        $cache = new FilesystemAdapter();

        $rates = $cache->get('currency_rates', function (ItemInterface $item): ?array {
            $item->expiresAfter(3600);

            return $this->loader->load();
        });

        return $rates;
    }

    protected function calculateCartTotalByRequestForBaseCurrency(array $items, array $rates): float
    {
        $total = 0.0;

        foreach ($items as $item) {
            if ($item['currency'] === self::BASE_CURRENCY) {
                $total += $item['price'] * $item['quantity'];
            } else {
                if (!array_key_exists($item['currency'], $rates)) {
                    throw new \Exception("Item use not supported currency");
                }
                $rate = $rates[$item['currency']];
                $total += ($item['price'] / $rate) * $item['quantity'];
            }
        }

        return round($total, 2);
    }

    protected function calculateCartTotalByRequestForNotBaseCurrency(CartRequest $cartRequest, array $rates): float
    {
        $total = 0.0;

        if (!array_key_exists($cartRequest->checkoutCurrency, $rates)) {
            throw new \Exception("Target currency is not supported");
        }

        $targetCurrencyRate = $rates[$cartRequest->checkoutCurrency];

        foreach ($cartRequest->items as $item) {
            if ($item['currency'] === self::BASE_CURRENCY) {
                $total += $item['price'] * $targetCurrencyRate * $item['quantity'];
            } else if ($item['currency'] === $cartRequest->checkoutCurrency) {
                $total += $item['price'] * $item['quantity'];
            } else {
                if (!array_key_exists($item['currency'], $rates)) {
                    throw new \Exception("Item use not supported currency");
                }
                $rate = $rates[$item['currency']];
                $baseCurSum = $item['price'] / $rate;
                $total += $baseCurSum * $targetCurrencyRate * $item['quantity'];
            }
        }

        return round($total, 2);
    }
}
