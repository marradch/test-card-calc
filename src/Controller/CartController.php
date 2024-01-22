<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Requests\CartRequest;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use App\Services\CartCalculator;

class CartController extends AbstractController
{
    #[Route('/cart-calculate', name: 'app_cart', methods: ['POST'])]
    public function calculate(
        #[MapRequestPayload] CartRequest $cartRequest,
        CartCalculator $cartCalculator
    ): JsonResponse {

        try {
            $total = $cartCalculator->calculateCartTotalByRequest($cartRequest);
        } catch (\Exception $e) {
            return $this->json([
                'status' => 'error',
                'message' => $e->getMessage(),
            ]);
        }

        return $this->json([
            'status' => 'success',
            'data' => [
                $total,
                $cartRequest->checkoutCurrency,
            ]
        ]);
    }
}
