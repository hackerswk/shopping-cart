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
 * Class MemberShoppingCart
 * Perform CRUD operations on the member shopping cart table.
 */
class MemberShoppingCart
{
    /** @var PDO Database connection */
    private $conn;

    /**
     * MemberShoppingCart constructor.
     * @param PDO $conn Database connection
     */
    public function __construct(PDO $conn)
    {
        $this->conn = $conn;
    }

    /**
     * Add a product to the member's shopping cart.
     *
     * @param int $memberId Member ID
     * @param int $productId Product ID
     * @param string $suffix Suffix
     * @param int $quantity Quantity of the product
     * @return bool True on success, False on failure
     */
    public function addProductToCart($memberId, $productId, $suffix, $quantity = 1)
    {
        try {
            $sql = <<<EOF
            INSERT INTO member_shopping_cart (member_id, product_id, suffix, quantity)
            VALUES (:member_id, :product_id, :suffix, :quantity)
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':suffix', $suffix, PDO::PARAM_STR); // Bind the suffix parameter
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve all products in the member's shopping cart.
     *
     * @param int $memberId Member ID
     * @return array Array of product data in the member's shopping cart
     */
    public function getProductsInCart($memberId)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM member_shopping_cart WHERE member_id = :member_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Remove a product from the member's shopping cart.
     *
     * @param int $memberId Member ID
     * @param int $productId Product ID
     * @param string $suffix Suffix
     * @return bool True on success, False on failure
     */
    public function removeProductFromCart($memberId, $productId, $suffix)
    {
        try {
            $sql = <<<EOF
            DELETE FROM member_shopping_cart WHERE member_id = :member_id AND product_id = :product_id AND suffix = :suffix
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':suffix', $suffix, PDO::PARAM_STR); // Bind the suffix parameter
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Clear all products from the member's shopping cart.
     *
     * @param int $memberId Member ID
     * @return bool True on success, False on failure
     */
    public function clearCart($memberId)
    {
        try {
            $sql = <<<EOF
                DELETE FROM member_shopping_cart WHERE member_id = :member_id
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }
}
