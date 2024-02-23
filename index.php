<?php

$products = [
    new Product('Coca-cola', 1.50),
    new Product('Snickers', 1.20),
    new Product('Lay\'s', 2.00),
];

$machine = new Machine($products);
$machine->run();


class Product {
	public function __construct(public string $title, public float $price) {}
}

class Machine {

    private array $products;

    private static array $_coins = [1, 5, 10, 25, 50, 100];
    private int $_balance = 0;

	public function __construct(array $products)
    {
        $this->products = $products;
    }

    public function run()
    {
        echo " ***** Торговий автомат працює *****\n";

        while(true) {
            $commandKey = (int)readline("Оберіть дію: [1] - переглянути список товарів, [2] - обрати товар, [3] - завершити роботу автомата: ");
            switch ($commandKey) {
                case 1: $this->_showProductList(); break;
                case 2: $this->_showSelectionProductList(); break;
                case 3: $this->_showMachineClosed(); return;
            }
        }
    }

    private function _showProductList()
    {
        $str = "\nСписок товарів: ";
        foreach ($this->products as $product) {
            $str .= "\n - {$product->title} {$product->price}";
        }
        $str .= "\n";
        echo $str;
    }

    private function _showMachineClosed()
    {
        echo " ***** Роботу торгового автомата завершено *****";
    }

    private function _showSelectionProductList()
    {
        $promptMessage = "Оберіть продукт: ";
        foreach ($this->products as $key => $product) {
            $promptMessage .= "[$key] - {$product->title} {$product->price}, ";
        }
        $promptMessage .= ': ';

        while (true) {
            $productKey = (int)readline($promptMessage);
            if (isset($this->products[$productKey])) {
                break;
            }
        }

        $this->_showPayment($this->products[$productKey]);
    }

    private function _showPayment(Product $product)
    {
        while (true) {
            $promptMessage = "Баланс: " . self::getFloatBalance($this->_balance) . ". Товар: {$product->title}. Ціна: {$product->price}. Внесіть монету : ";
            $receivedCoin = (int)readline($promptMessage);
            $coinKey = array_search($receivedCoin, self::$_coins);
            if ($coinKey !== false) {
                $this->_balance += $receivedCoin;
                if ($this->_balance == self::getIntBalance($product->price)) {
                    echo "Дякуємо за покупку!\n";
                    $this->_balance = 0;
                    return;
                } else if ($this->_balance > self::getIntBalance($product->price)) {
                    echo "Дякуємо за покупку! Решта: " . (self::getFloatBalance($this->_balance) - $product->price) . ". Монети: " . self::getDeal($this->_balance - self::getIntBalance($product->price)) . "\n";
                    $this->_balance = 0;
                    return;
                } else  {
                    echo "Недостатньо коштів!\n";
                }
            } else {
                echo "Внесіть іншу монету!\n";
            }
        }
    }

    private static function getDeal(int $amount)
    {
        $coins = [100, 50, 25, 10, 5, 1];
        $usedCoins = [];
        for ($i = 0; $i < count($coins) && $amount > 0; $i++) {
            if ($coins[$i] <= $amount) {
                $usedCoins[$i] = intval($amount / $coins[$i]);
                $amount %= $coins[$i];
            }
        }

        $str = "";
        foreach ($usedCoins as $key => $item) {
            $str .= "{$item}x{$coins[$key]}, ";
        }

        return $str;
    }

    private static function getFloatBalance(int $balance)
    {
        return round($balance / 100, 2);
    }

    private static function getIntBalance(float $balance)
    {
        return round($balance * 100);
    }

}




