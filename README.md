# Pastimes - Second-Hand Clothing E-Commerce Platform

> A modern PHP-based e-commerce platform for buying and selling pre-loved clothing items

**Repository:** [mzamon/pastimes_v2](https://github.com/mzamon/pastimes_v2)  
**Module:** WEDE6021 POE  
**Developer:** Mzamo Ndlovu  
**Year:** 2026

---

## 🔐 Test Accounts

All accounts use the password: `Kookemooi10!`

| Role | Email | Password |
|------|-------|----------|
| **Admin** | `admin@pastimes.co.za` | `Kookemooi10!` |
| **Buyer** | `koos@gmail.com` | `Kookemooi10!` |
| **Seller** | `sarah@example.com` | `Kookemooi10!` |
| **Seller** | `mike@example.com` | `Kookemooi10!` |
| **Demo Seller** | `demo@pastimes.co.za` | `Kookemooi10!` |
| **Demo Buyer** | `buyer@pastimes.co.za` | `Kookemooi10!` |

> **Password policy**: Minimum 8 characters, at least 3 of the following: uppercase letter, number, special character.  
> **Email validation**: Must contain `@` and `.`.

---

## 📋 Quick Links

| Resource | URL |
|----------|-----|
| **Homepage** | `http://localhost:8080/pastimes_v2/` |
| **User Login** | `http://localhost:8080/pastimes_v2/auth/login.php` |
| **Admin Login** | `http://localhost:8080/pastimes_v2/auth/admin_login.php` |
| **Browse Products** | `http://localhost:8080/pastimes_v2/products/index.php` |
| **Shopping Cart** | `http://localhost:8080/pastimes_v2/cart/index.php` |
| **Database Setup** | `http://localhost:8080/pastimes_v2/loadClothingStore.php` |

---

## 🛠️ Technology Stack

| Category | Technology | Purpose |
|----------|------------|---------|
| **Backend** | PHP 8.0+ | Server-side scripting & business logic |
| **Database** | MySQL 5.7+ / MariaDB 10.3+ | Data storage & management |
| **Database Driver** | MySQLi (improved) | Secure database connectivity |
| **Frontend** | HTML5 | Semantic markup structure |
| **Styling** | CSS3 | Dark theme, responsive layout |
| **Interactivity** | Vanilla JavaScript | Mobile menu, notifications, cart UX |
| **Architecture** | MVC-inspired | Separation of concerns |
| **Authentication** | Session-based | User login & role management |
| **Security** | bcrypt | Password hashing |
| **Security** | Prepared Statements | SQL injection prevention |
| **Security** | `htmlspecialchars()` | XSS prevention |
| **Server** | Apache / Nginx | Web server |
| **Local Development** | XAMPP 8.x | Local environment |
| **Version Control** | Git & GitHub | Source code management |
| **Documentation** | Markdown | README & project docs |

---

## 📁 Directory Structure

```text
pastimes_v2/
├── admin/                 # Admin management pages
│   ├── add_user.php       # Create new user
│   ├── dashboard.php      # Admin dashboard with stats
│   ├── delete_user.php    # Delete user
│   ├── edit_user.php      # Edit user details
│   ├── orders.php         # View all orders
│   ├── products.php       # Manage all products
│   ├── users.php          # View all users
│   └── verify_users.php   # Verify users & sellers
├── assets/
│   ├── css/
│   │   └── main.css       # Dark theme, responsive
│   ├── images/            # Product images
│   │   ├── placeholder/   # Placeholder images
│   │   ├── uploads/       # User-uploaded images
│   │   ├── vintage-clothing/
│   │   ├── streetwear/
│   │   ├── sports-gear/
│   │   ├── outerwear/
│   │   └── accessories/
│   └── js/
│       └── main.js        # Mobile nav, confirmations
├── auth/                  # Authentication
│   ├── admin_login.php    # Separate admin login
│   ├── login.php          # User login
│   ├── logout.php         # Logout
│   ├── register.php       # Registration with 8-char password
│   └── request_seller.php # Become a seller
├── cart/                  # Shopping cart
│   ├── add.php            # Add item
│   ├── index.php          # View cart
│   ├── remove.php         # Remove item
│   └── update.php         # Update quantity
├── config/
│   ├── DBConn.php         # Database connection
│   └── db.php             # Bootstrap connection
├── includes/
│   ├── footer.php         # Site footer
│   ├── functions.php      # Auth guards, helpers
│   ├── header.php         # Navigation
│   ├── ShoppingCart.php   # OOP Shopping Cart class
│   └── TextScanner.php    # Input sanitisation
├── messages/
│   ├── chat.php           # Conversation thread
│   ├── inbox.php          # Message inbox
│   └── send.php           # Send message
├── orders/
│   ├── checkout.php       # Checkout with delivery details
│   ├── confirm.php        # Order confirmation
│   ├── manage.php         # Seller manage orders
│   └── track.php          # Buyer order history
├── products/
│   ├── add.php            # Add product (with brand)
│   ├── delete.php         # Delete product
│   ├── edit.php           # Edit product (with brand)
│   ├── index.php          # Browse products
│   └── view.php           # Product detail
├── wishlist/
│   ├── add.php            # Add to wishlist
│   ├── index.php          # View wishlist
│   └── remove.php         # Remove from wishlist
├── clothesData.txt        # Sample clothing data (5 entries)
├── createTable.php        # Drops/recreates tblUser from userData.txt
├── database.sql           # Full schema + sample data (30+ entries)
├── index.php              # Homepage
├── loadClothingStore.php  # Complete DB setup with 30+ entries
├── myClothingStore.sql    # DDL export for lecturer
├── ordersData.txt         # Sample order data (5 entries)
├── reset_users.php        # Reset all users to Kookemooi10!
├── userData.txt           # Sample user data (5 entries)
└── README.md              # This file


---
## 📊 Database Schema

### Core Tables

| Table | Purpose | Key Constraints |
|-------|---------|-----------------|
| `tblUser` | User accounts (buyers, sellers, admins) | `email` UNIQUE, `role` ENUM, `is_verified` TINYINT |
| `tblSellerRequests` | Seller application workflow | FK → `tblUser(id)` ON DELETE CASCADE |
| `categories` | Product categories | `name` UNIQUE |
| `tblProducts` | Product listings with brand | FK → `tblUser(id)`, `categories(id)`, FULLTEXT |
| `cart_items` | Normalised cart storage | UQ(`user_id`, `product_id`) |
| `tblOrders` | Order headers | FK → `tblUser(id)` ON DELETE RESTRICT |
| `order_items` | Order line items (preserves price) | FK → `tblOrders(id)`, `tblProducts(id)` |
| `tblMessages` | Direct messaging | FK → `tblUser(id)` ×2, `tblProducts(id)` |
| `tblReviews` | Product ratings (1-5 stars) | FK → `tblUser(id)`, `tblProducts(id)` |
| `tblWishlist` | Saved items per user | UQ(`user_id`, `product_id`) |

---

## 📥 Installation & Setup

### Prerequisites

- PHP 7.4+ (8.0+ recommended)
- MySQL 5.7+ / MariaDB 10.3+
- Web server (Apache / Nginx)
- XAMPP 8.x (for local development)

---

## 👥 User Roles & Permissions

### Buyer
- Browse and search products
- View product details with brand
- Add items to cart and checkout
- View order history and tracking
- Create wishlist
- Message sellers
- Request seller status

### Seller
- List and manage products (with brand)
- Manage orders (update status)
- View buyer messages
- Cannot access admin panel

### Admin
- Full user management (add, edit, delete, verify)
- Product oversight (view, delete listings)
- Order management (update status, tracking)
- Seller request approval/rejection
- Comprehensive dashboard with analytics

---

## 🛡️ Security Features

### Authentication
- Session-based authentication (`session_start()`)
- Bcrypt password hashing (`password_hash()`, `password_verify()`)
- Session regeneration on login (`session_regenerate_id()`)
- Secure logout (session destroy + cookie expiry)

### Data Protection
- **Prepared statements** for all queries (SQL injection prevention)
- HTML escaping with `h()` helper (`htmlspecialchars()`)
- Input sanitisation with `sanitize()` helper
- File upload whitelist (extensions + size limit)

### Authorization
- Role-based access guards:
  - `requireLogin()` – authenticated only
  - `requireAdmin()` – admin only
  - `requireSeller()` – verified seller only
  - `requireVerified()` – verified users only
- Ownership verification on resources
- Admin override available

---

## 🛒 Shopping Cart Features

| Feature | Implementation |
|---------|---------------|
| **Add Item** | `AddItem()` – increments quantity if already in cart |
| **Remove Item** | `RemoveItem()` – removes from session |
| **Update Quantity** | `UpdateQuantity()` – updates or removes if < 1 |
| **Checkout** | `Checkout()` – transactional with rollback on failure |
| **Empty Cart** | `EmptyCart()` – auto-called after checkout |
| **Authentication** | `Login()` – used by both user and admin |
| **Input Sanitisation** | `ProcessInput()` – recursive sanitisation |

---

## 🧪 Testing Workflow

### Test User Registration
1. Visit `/auth/register.php`
2. Create account as buyer
3. Login to verify

### Test Seller Workflow
1. Login as buyer
2. Navigate to "Become Seller"
3. Submit request with motivation
4. Login as admin
5. Approve request in "Verify Users"
6. Return to verify account can now list items

### Test Checkout Flow
1. Login as buyer
2. Browse `/products/index.php`
3. Add items to cart
4. Proceed to checkout
5. Complete order

### Test Admin Dashboard
1. Login as admin
2. View `/admin/dashboard.php`
3. Navigate sections:
   - User management (`/admin/users.php`)
   - Product oversight (`/admin/products.php`)
   - Order tracking (`/admin/orders.php`)
   - Seller verification (`/admin/verify_users.php`)

---

## 🎬 Video Recording Checklist

For the POE submission, record a screen capture (10–15 minutes) showing:

1. **Homepage** – Hero, goals, categories, featured products
2. **Browse** – Product grid with brand, search, filter
3. **Register** – 8-character password with confirmation
4. **Login** – Sticky forms, "User X is logged in", associative table
5. **Admin Login** – Separate admin login page
6. **Admin Dashboard** – Stats (users, products, orders, revenue)
7. **Admin Verify Users** – Approve/reject pending accounts and seller requests
8. **Admin Manage Users** – Add, edit, delete users
9. **Admin Manage Products** – Product list with brand, add, edit, delete
10. **Add to Cart** – Multiple items, quantity increments
11. **View Cart** – Brand, update quantities, remove items
12. **Checkout** – Delivery details, order ref, session ID
13. **My Orders** – Order history with grand total at bottom
14. **Wishlist** – Add/remove items, brand displayed
15. **Messages** – Send/receive messages (inbox and chat)

---

## 🐛 Troubleshooting

| Problem | Cause | Solution |
|---------|-------|----------|
| **"Database connection failed"** | MySQL not running or DB missing | Start XAMPP MySQL; run `loadClothingStore.php` |
| **"Invalid admin credentials"** | Wrong role or password hash | Run `reset_users.php` to fix all accounts |
| **Images not showing** | Missing placeholder or wrong path | Ensure `assets/images/placeholder/no-image.jpg` exists |
| **Image upload fails** | `uploads/` not writable | Set folder permissions to allow Apache write |
| **Styles broken** | Wrong `BASE_URL` | Verify `BASE_URL` in `functions.php` matches folder name |
| **Admin link loops to login** | Not logged in as admin | Log in with `admin@pastimes.co.za` / `Kookemooi10!` |
| **Can't delete user** | User has orders (FK constraint) | Edit user instead; or remove orders first |

---

## 📄 License

Proprietary – WEDE6021 POE Project

---

## 👨‍💻 Credits

| Role | Name |
|------|------|
| **Developer** | Mzamo Ndlovu |
| **Module** | WEDE6021 – Web Development & eCommerce |
| **Institution** | Independent Institute of Education (IIE) |
| **Year** | 2026 |

---

**Last Updated:** June 2026  
**Version:** 1.0  
**Status:** Production Ready

---

*Built with care for the WEDE6021 POE – Pastimes Second-Hand Clothing Marketplace.*