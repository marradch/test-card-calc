<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;
use App\Requests\CartRequest;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;

class CartController extends AbstractController
{
    #[Route('/cart-calculate', name: 'app_cart', methods: ['POST'])]
    public function calculate(#[MapRequestPayload] CartRequest $cartRequest): JsonResponse
    {
        return $this->json([
            'message' => 'Welcome to your new controller!',
            'path' => 'src/Controller/CartController.php',
        ]);
    }
}
