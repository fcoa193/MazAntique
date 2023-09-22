<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\Product;


class CalculateTotalCartPriceService
{
    public function calculateTotalCartPrice(array $cartItems): float
    {
        dump('Service calculating total cart price...');  // Debug output

        $totalPrice = 0;
    
        foreach ($cartItems as $cartItem) {
            if ($cartItem instanceof Cart) {
                $product = $cartItem->getProduct();
                $quantity = $cartItem->getQuantity();
    
                // Debugging statements
                var_dump($product); // Check if $product is not null
                var_dump($quantity); // Check if $quantity is not null
    
                if ($product && $quantity) {
                    $productPrice = $product->getPrice();
                    var_dump($productPrice); // Check if $productPrice is correctly retrieved
    
                    $totalPrice += $productPrice * $quantity;
                }
            }
        }
    
        return $totalPrice;
    }
    
}