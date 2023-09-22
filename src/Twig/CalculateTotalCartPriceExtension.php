<?php
namespace App\Twig;

use App\Service\CalculateTotalCartPriceService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CalculateTotalCartPriceExtension extends AbstractExtension
{
    private $calculateTotalCartPriceService;

    public function __construct(CalculateTotalCartPriceService $calculateTotalCartPriceService)
    {
        $this->calculateTotalCartPriceService = $calculateTotalCartPriceService;
    }

    public function getFilters(): array
    {
        return [
            new TwigFilter('calculateTotalCartPrice', [$this, 'calculateTotalCartPrice']),
        ];
    }

    public function calculateTotalCartPrice(array $cartItems): float
    {
        dump('Calculating total cart price...');  // Debug output
        return $this->calculateTotalCartPriceService->calculateTotalCartPrice($cartItems);
    }
}
