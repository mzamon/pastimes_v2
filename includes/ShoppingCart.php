<?php
/**
 * includes/ShoppingCart.php
 * WEDE6021 OOP requirement.
 * Methods required by rubric: AddItem, RemoveItem, Checkout,
 * EmptyCart, Login, ProcessInput.
 */
class ShoppingCart
{
    private $conn;
    private $userId;

    public function __construct($conn, int $userId = 0)
    {
        $this->conn   = $conn;
        $this->userId = $userId;

        if (!isset($_SESSION['cart']) || !is_array($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }
    }

    /**
     * Login()
     * Single authentication method used by BOTH buyer/seller login
     * and admin login — one implementation, no drift.
     * Supports bcrypt (register.php) AND legacy MD5 (userData.txt).
     */
    public function Login(string $email, string $password)
    {
        if (!$this->conn) return false;

        $stmt = mysqli_prepare($this->conn,
            "SELECT id, name, email, password_hash, role, is_verified, seller_request
             FROM tblUser WHERE email = ? LIMIT 1");
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, 's', $email);
        mysqli_stmt_execute($stmt);
        $user = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$user) return false;

        $hash = $user['password_hash'] ?? '';
        $ok   = password_verify($password, $hash)
             || hash_equals(md5($password), (string)$hash);

        return $ok ? $user : false;
    }

    /**
     * ProcessInput()
     * Sanitises a value (or array of values) from user input.
     */
    public function ProcessInput($value)
    {
        if (is_array($value)) {
            return array_map([$this, 'ProcessInput'], $value);
        }
        return trim(stripslashes((string)$value));
    }

    /**
     * AddItem()
     * Increments quantity if item already in cart (rubric requirement).
     * Caps at available stock so you cannot order more than exists.
     * Stores brand in session for display in cart.
     */
    public function AddItem(int $productId, int $qty = 1): bool
    {
        if (!$this->conn) return false;

        $stmt = mysqli_prepare($this->conn,
            "SELECT id, title, brand, price, seller_id, image, quantity
             FROM tblProducts WHERE id = ? AND status = 'active' LIMIT 1");
        if (!$stmt) return false;

        mysqli_stmt_bind_param($stmt, 'i', $productId);
        mysqli_stmt_execute($stmt);
        $p = mysqli_fetch_assoc(mysqli_stmt_get_result($stmt));
        mysqli_stmt_close($stmt);

        if (!$p) return false;

        $stock = max((int)$p['quantity'], 1);

        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] =
                min($_SESSION['cart'][$productId]['quantity'] + $qty, $stock);
        } else {
            $_SESSION['cart'][$productId] = [
                'id'        => $p['id'],
                'title'     => $p['title'],
                'brand'     => $p['brand'] ?? '',
                'price'     => $p['price'],
                'quantity'  => min($qty, $stock),
                'seller_id' => $p['seller_id'],
                'image'     => $p['image'],
            ];
        }
        return true;
    }

    /**
     * RemoveItem()
     * Removes a product line from the cart.
     */
    public function RemoveItem(int $productId): bool
    {
        if (isset($_SESSION['cart'][$productId])) {
            unset($_SESSION['cart'][$productId]);
            return true;
        }
        return false;
    }

    /**
     * UpdateQuantity()
     * Qty < 1 triggers removal (handles "Update" input control).
     */
    public function UpdateQuantity(int $productId, int $qty): bool
    {
        if ($qty < 1) return $this->RemoveItem($productId);
        if (isset($_SESSION['cart'][$productId])) {
            $_SESSION['cart'][$productId]['quantity'] = $qty;
            return true;
        }
        return false;
    }

    public function GetItems(): array   { return $_SESSION['cart'] ?? []; }

    public function GetSubtotal(): float
    {
        $t = 0.0;
        foreach ($this->GetItems() as $item) {
            $t += $item['price'] * $item['quantity'];
        }
        return $t;
    }

    /**
     * EmptyCart()
     * Called automatically after a successful Checkout().
     */
    public function EmptyCart(): void { $_SESSION['cart'] = []; }

    /**
     * Checkout()
     * Wrapped in a DB transaction — if ANY write fails, everything
     * rolls back so you never get a half-created order.
     * Decrements stock; marks listing 'sold' when it hits zero.
     * Returns order_id, order_num (ORD-000001), session_id, total.
     */
    public function Checkout(array $delivery): array
    {
        $items = $this->GetItems();
        if (empty($items)) throw new Exception('Cart is empty.');

        $fee     = 50.00;
        $total   = $this->GetSubtotal() + $fee;
        $address = $delivery['address'] . ', ' . $delivery['city'] . ', ' . $delivery['postal'];

        mysqli_begin_transaction($this->conn);
        $ok = true;

        $ins = mysqli_prepare($this->conn,
            "INSERT INTO tblOrders (buyer_id, total, delivery_address, status, payment_method)
             VALUES (?, ?, ?, 'Pending', ?)");
        mysqli_stmt_bind_param($ins, 'idss', $this->userId, $total, $address, $delivery['payment_method']);
        $ok = $ok && mysqli_stmt_execute($ins);
        $orderId = mysqli_insert_id($this->conn);
        mysqli_stmt_close($ins);

        foreach ($items as $pid => $item) {
            $li = mysqli_prepare($this->conn,
                "INSERT INTO order_items (order_id, product_id, quantity, price_at_purchase)
                 VALUES (?, ?, ?, ?)");
            mysqli_stmt_bind_param($li, 'iiid', $orderId, $pid, $item['quantity'], $item['price']);
            $ok = $ok && mysqli_stmt_execute($li);
            mysqli_stmt_close($li);

            $dec = mysqli_prepare($this->conn,
                "UPDATE tblProducts
                 SET quantity = GREATEST(quantity - ?, 0),
                     status   = IF(quantity - ? <= 0, 'sold', status)
                 WHERE id = ?");
            mysqli_stmt_bind_param($dec, 'iii', $item['quantity'], $item['quantity'], $pid);
            $ok = $ok && mysqli_stmt_execute($dec);
            mysqli_stmt_close($dec);
        }

        if (!$ok) {
            mysqli_rollback($this->conn);
            throw new Exception('Checkout failed — no changes saved. Please try again.');
        }

        mysqli_commit($this->conn);
        $this->EmptyCart();

        return [
            'order_id'   => $orderId,
            'order_num'  => 'ORD-' . str_pad($orderId, 6, '0', STR_PAD_LEFT),
            'session_id' => session_id(),
            'total'      => $total,
        ];
    }
}
?>