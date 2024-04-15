<?php
/**
 * Member shopping cart class
 *
 * @author      Stanley Sie <swookon@gmail.com>
 * @access      public
 * @version     Release: 1.0
 */

namespace Stanleysie\ShoppingCart;

use \PDO as PDO;
use \PDOException as PDOException;

/**
 * Class VisitorShoppingCart
 * Perform CRUD operations on the visitor shopping cart table.
 */
class VisitorShoppingCart
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * VisitorShoppingCart constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Add a product to the visitor's shopping cart.
     *
     * @param string $visitorId Visitor ID
     * @param int $productId Product ID
     * @param int $quantity Quantity of the product
     * @return bool True on success, False on failure
     */
    public function addProductToCart($visitorId, $productId, $quantity = 1)
    {
        try {
            $sql = <<<EOF
                INSERT INTO visitor_shopping_cart (visitor_id, product_id, quantity)
                VALUES (:visitor_id, :product_id, :quantity)
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_STR);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve all products in the visitor's shopping cart.
     *
     * @param string $visitorId Visitor ID
     * @return array Array of product data in the visitor's shopping cart
     */
    public function getProductsInCart($visitorId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM visitor_shopping_cart WHERE visitor_id = :visitor_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_STR);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Remove a product from the visitor's shopping cart.
     *
     * @param string $visitorId Visitor ID
     * @param int $productId Product ID
     * @return bool True on success, False on failure
     */
    public function removeProductFromCart($visitorId, $productId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM visitor_shopping_cart WHERE visitor_id = :visitor_id AND product_id = :product_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_STR);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Clear all products from the visitor's shopping cart.
     *
     * @param string $visitorId Visitor ID
     * @return bool True on success, False on failure
     */
    public function clearCart($visitorId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM visitor_shopping_cart WHERE visitor_id = :visitor_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_STR);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
