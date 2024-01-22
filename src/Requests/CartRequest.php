<?php

namespace App\Requests;

use Symfony\Component\Validator\Mapping\ClassMetadata;
use Symfony\Component\Validator\Constraints as Assert;

class CartRequest
{
    public array $items;

    public string $checkoutCurrency;

    public static function loadValidatorMetadata(ClassMetadata $metadata): void
    {
        $metadata->addPropertyConstraint('items', new Assert\NotBlank());
        $metadata->addPropertyConstraint('items', new Assert\Type('array'));
        $metadata->addPropertyConstraint('items', new Assert\All([
            new Assert\Collection([
                'fields' => [
                    'currency' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'string']),
                        new Assert\Length(3),
                    ],
                    'price' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'float']),
                    ],
                    'quantity' => [
                        new Assert\NotBlank(),
                        new Assert\Type(['type' => 'int']),
                    ],
                ],
            ]),
        ]));

        $metadata->addPropertyConstraint('checkoutCurrency', new Assert\NotBlank());
        $metadata->addPropertyConstraint('checkoutCurrency', new Assert\Length(3));
        $metadata->addPropertyConstraint('checkoutCurrency', new Assert\Type('string'));
    }
}
