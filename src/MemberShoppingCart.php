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
     * @param int $site_id Site ID
     * @param int $visitorId Visitor ID
     * @param int $memberId Member ID
     * @param int $productId Product ID
     * @param string $suffix Suffix
     * @param int $quantity Quantity of the product
     * @param int $mainSpec Main Spec ID
     * @param int $subSpec Sub spec ID
     * @return bool True on success, False on failure
     */
    public function addProductToCart($site_id, $visitorId, $memberId, $productId, $suffix, $quantity = 1, $mainSpec, $subSpec)
    {
        try {
            $sql = <<<EOF
            INSERT INTO member_shopping_cart (site_id, visitor_id, member_id, product_id, suffix, quantity,main_spec_id,sub_spec_id)
            VALUES (:site_id, :visitor_id, :member_id, :product_id, :suffix, :quantity,:main_spec_id,:sub_spec_id)
EOF;

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_INT);
            $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':suffix', $suffix, PDO::PARAM_STR); // Bind the suffix parameter
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            $stmt->bindParam(':main_spec_id', $mainSpec, PDO::PARAM_INT);
            $stmt->bindParam(':sub_spec_id', $subSpec, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Retrieve product in the member's shopping cart.
     *
     * @param int $site_id Site ID
     * @param int $visitorId Visitor ID
     * @param int $memberId Member ID
     * @param int $productId Product ID
     * @param string $suffix Suffix
     * @param int $mainSpec Main Spec ID
     * @param int $subSpec Sub spec ID
     * @return array Array of product data in the member's shopping cart
     */
    public function checkProductInCart($site_id, $visitorId, $memberId, $productId, $suffix, $mainSpec, $subSpec)
    {
        try {
            $sql = <<<EOF
                SELECT * FROM member_shopping_cart WHERE site_id = :site_id AND member_id = :member_id AND product_id = :product_id AND suffix=:suffix
EOF;
            if ($memberId == 0) {
                $sql = <<<EOF
                    SELECT * FROM member_shopping_cart WHERE site_id = :site_id AND visitor_id = :visitor_id AND product_id = :product_id AND suffix=:suffix
EOF;
            }
            if ($mainSpec != null) {
                $sql .= <<<EOF
                    AND main_spec_id = :main_spec_id
EOF;
            }
            if ($subSpec != null) {
                $sql .= <<<EOF
                AND sub_spec_id = :sub_spec_id
EOF;
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            if ($memberId != 0) {
                $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_INT);
            }

            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':suffix', $suffix, PDO::PARAM_STR); // Bind the suffix parameter
            if ($mainSpec != null && $mainSpec != '') {
                $stmt->bindParam(':main_spec_id', $mainSpec, PDO::PARAM_INT);
            }
            if ($subSpec != null && $subSpec != '') {
                $stmt->bindParam(':sub_spec_id', $subSpec, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Retrieve all products in the member's shopping cart.
     *
     * @param int $siteId Site ID
     * @param int $visitorId Visitor ID
     * @param int $memberId Member ID, Default = 0
     * @return array Array of product data in the member's shopping cart
     */
    public function getProductsInCart($siteId, $visitorId, $memberId = 0)
    {
        try {
            $sql = 'SELECT * FROM member_shopping_cart WHERE site_id = :site_id ';
            $sql.= ($memberId == 0)
                ? 'AND visitor_id = :visitor_id'
                : 'AND member_id = :member_id';

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':site_id', $siteId, PDO::PARAM_INT);
            if ($memberId != 0) {
                $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_INT);
            }
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return [];
        }
    }

    /**
     * Remove a product from the member's shopping cart by id.
     *
     * @param int $id item ID
     * @return bool True on success, False on failure
     */
    public function removeProductFromCartById($id)
    {
        try {
            $sql = <<<EOF
            DELETE FROM member_shopping_cart WHERE id = :id
EOF;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
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
    public function removeProductFromCart($site_id, $visitorId, $memberId, $productId, $suffix, $mainSpec, $subSpec)
    {
        try {
            $sql = <<<EOF
            DELETE FROM member_shopping_cart WHERE site_id = :site_id AND member_id = :member_id AND product_id = :product_id AND suffix = :suffix
EOF;
            if ($memberId == 0) {
                $sql = <<<EOF
                    DELETE FROM member_shopping_cart WHERE site_id = :site_id AND visitor_id = :visitor_id AND product_id = :product_id AND suffix = :suffix
EOF;
            }
            if ($mainSpec != null) {
                $sql .= <<<EOF
                    AND main_spec_id = :main_spec_id
EOF;
            }
            if ($subSpec != null) {
                $sql .= <<<EOF
                AND sub_spec_id = :sub_spec_id
EOF;
            }

            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            if ($memberId != 0) {
                $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_INT);
            }

            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':suffix', $suffix, PDO::PARAM_STR); // Bind the suffix parameter
            if ($mainSpec != null && $mainSpec != '') {
                $stmt->bindParam(':main_spec_id', $mainSpec, PDO::PARAM_INT);
            }
            if ($subSpec != null && $subSpec != '') {
                $stmt->bindParam(':sub_spec_id', $subSpec, PDO::PARAM_INT);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * move visitor's cart to member's cart
     *
     * @param int $site_id Site ID
     * @param int $visitorId Visitor ID
     * @param int $memberId Member ID
     * @return bool True on success, False on failure
     */
    public function moveCart($site_id, $visitorId, $memberId, $quantity)
    {
        try {
            $sql = <<<EOF
                UPDATE member_shopping_cart
                SET quantity = :quantity , member_id = :member_id
                WHERE site_id = :site_id AND visitor_id = :visitor_id
EOF;
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_INT);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Update the quantity of a product in the member's shopping cart.
     *
     * @param int $site_id Site ID
     * @param int $visitorId Visitor ID
     * @param int $memberId Member ID
     * @param int $productId Product ID
     * @param string $suffix Suffix
     * @param int $quantity New quantity of the product
     * @return bool True on success, False on failure
     */
    public function updateProductQuantity($site_id, $visitorId, $memberId, $productId, $suffix, $quantity, $mainSpec, $subSpec)
    {
        try {
            $sql = <<<EOF
                UPDATE member_shopping_cart
                SET quantity = :quantity
                WHERE site_id = :site_id AND visitor_id = :visitor_id AND product_id = :product_id AND suffix = :suffix
EOF;
            if ($memberId != 0) {
                $sql = <<<EOF
                    UPDATE member_shopping_cart
                    SET quantity = :quantity
                    WHERE site_id = :site_id AND member_id = :member_id AND product_id = :product_id AND suffix = :suffix
EOF;
            }
            if ($mainSpec != null) {
                $sql .= <<<EOF
                    AND main_spec_id = :main_spec_id
EOF;
            }
            if ($subSpec != null) {
                $sql .= <<<EOF
                AND sub_spec_id = :sub_spec_id
EOF;
            }
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':site_id', $site_id, PDO::PARAM_INT);
            if ($memberId != 0) {
                $stmt->bindParam(':member_id', $memberId, PDO::PARAM_INT);
            } else {
                $stmt->bindParam(':visitor_id', $visitorId, PDO::PARAM_INT);
            }
            $stmt->bindParam(':product_id', $productId, PDO::PARAM_INT);
            $stmt->bindParam(':suffix', $suffix, PDO::PARAM_STR);
            $stmt->bindParam(':quantity', $quantity, PDO::PARAM_INT);
            if ($mainSpec != null) {
                $stmt->bindParam(':main_spec_id', $mainSpec, PDO::PARAM_INT);
            }
            if ($subSpec != null) {
                $stmt->bindParam(':sub_spec_id', $subSpec, PDO::PARAM_INT);
            }
            return $stmt->execute();
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    public function test()
    {
        echo "test";
    }

}
