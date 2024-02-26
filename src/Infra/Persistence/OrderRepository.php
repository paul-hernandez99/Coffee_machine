<?php

namespace Pdpaola\CoffeeMachine\Infra\Persistence;

class OrderRepository
{
    protected $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function savePrices() {
        try {
            $createTableSql = "CREATE TABLE IF NOT EXISTS drink_prices (
                drink_type VARCHAR(255) NOT NULL,
                price DECIMAL(10, 2) NOT NULL,
                PRIMARY KEY (drink_type)
            )";
            
            $this->pdo->exec($createTableSql);

            $insertSql = "INSERT INTO drink_prices (drink_type, price) VALUES
            ('tea', 0.4),
            ('coffee', 0.5),
            ('chocolate', 0.6)
            ON DUPLICATE KEY UPDATE price=VALUES(price)";

            $this->pdo->exec($insertSql);

        } catch (PDOException $e) {
            error_log('Database error: ' . $e->getMessage());
            echo "An error occurred while processing your order. Please try again later.";
        }
        
    }

    public function saveOrder($order)
    {
        try {
        
            $stmt = $this->pdo->prepare('INSERT INTO orders (drink_type, sugars, stick, extra_hot) VALUES (:drink_type, :sugars, :stick, :extra_hot)');
            $stmt->execute([
                'drink_type' => $order->type,
                'sugars' => $order->sugars,
                'stick' => $order->stick ? 1 : 0, // Assuming $stick is a boolean, convert to 1 or 0 for the database.
                'extra_hot' => $order->extraHot ? 1 : 0, // Same as above, assuming $extraHot is a boolean.
            ]);
        
            // Optionally, confirm the insert operation was successful
            if ($stmt->rowCount() > 0) {
                // Insert was successful
                echo "Order saved successfully.";
            } else {
                // Handle the case where the insert didn't happen, e.g., due to constraints
                echo "Failed to save the order.";
            }
        } catch (PDOException $e) {
            // Handle any errors that occur during the database operations
            // Log the error message or display a user-friendly message
            // For example, log the error and display a generic error message
            error_log('Database error: ' . $e->getMessage());
            echo "An error occurred while processing your order. Please try again later.";
        }
        
    }

    public function drinkMoney() {

        try {
            $stmt = $this->pdo->query(
                "SELECT o.drink_type, SUM(p.price) AS Money
                FROM orders o
                JOIN drink_prices p ON o.drink_type = p.drink_type
                GROUP BY o.drink_type"
            );
        
            $results = $stmt->fetchAll(\PDO::FETCH_ASSOC);
        
            return $results;
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    }
}
