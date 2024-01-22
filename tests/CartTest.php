<?php

namespace App\Tests;

use App\Services\CurrencyRatesLoader;
use App\Services\CartCalculator;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use App\Requests\CartRequest;

class NewsletterGeneratorTest extends KernelTestCase
{
    public function testRatesLoader(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $service = $container->get(CurrencyRatesLoader::class);
        $rates = $service->load();

        $this->assertIsArray($rates);
        $this->assertArrayHasKey('EUR', $rates);
    }

    public function testCalculator(): void
    {
        self::bootKernel();

        $container = static::getContainer();

        $service = $container->get(CartCalculator::class);

        $request = new CartRequest();
        $request->checkoutCurrency = "USD";
        $request->items = [
            [
                'price' => 12.0,
                'quantity' => 1,
                'currency' => 'USD'
            ]
        ];

        $value = $service->calculateCartTotalByRequest($request);

        $this->assertEquals(12.0, $value);
    }
}