# Pastimes - Second-Hand Clothing E-Commerce Platform

> A modern PHP-based e-commerce platform for buying and selling pre-loved clothing items

## Project Overview

Pastimes is a full-featured second-hand clothing marketplace built with PHP, MySQL, and vanilla JavaScript. It provides a seamless experience for both buyers and sellers, with comprehensive admin controls for platform management.

### Key Features

- **User Management**: Registration, authentication, seller verification workflow
- **Product Listings**: Browse, search, and filter clothing items by category and condition
- **Shopping Cart**: Add/remove items, checkout process with order tracking
- **Messaging System**: Direct communication between buyers and sellers about products
- **Wishlist**: Save favorite items for later purchase
- **Admin Dashboard**: Comprehensive statistics, user verification, product management, order tracking
- **Seller Tools**: List items, manage sales, track orders
- **Buyer Protection**: Seller verification, product condition ratings, order status tracking

## Technical Stack

- **Backend**: PHP 7.4+ with MySQLi (prepared statements)
- **Database**: MySQL with 9 core tables (users, products, orders, messages, wishlist, etc.)
- **Frontend**: HTML5, CSS3 (dark theme), Vanilla JavaScript (no frameworks)
- **Architecture**: MVC-inspired with session-based authentication
- **Security**: Password hashing (bcrypt), prepared statements, HTML escaping, CSRF considerations

## Database Schema

### Core Tables

| Table | Purpose |
|-------|---------|
| `tblUser` | User accounts with roles (buyer/seller/admin) |
| `tblSellerRequests` | Seller application workflow |
| `categories` | Product categories |
| `tblProducts` | Product listings with pricing and conditions |
| `cart_items` | Shopping cart state |
| `tblOrders` | Customer orders |
| `order_items` | Order line items |
| `tblMessages` | Direct messaging between users |
| `tblWishlist` | Saved products |
| `tblReviews` | Product reviews (for future) |

## Directory Structure

```
pastimes_v2/
├── admin/                 # Admin management pages
│   ├── dashboard.php      # Statistics & overview
│   ├── verify_users.php   # User & seller verification
│   ├── users.php          # User management
│   ├── add_user.php       # Create new user
│   ├── edit_user.php      # Edit user details
│   ├── delete_user.php    # Delete user
│   ├── products.php       # Product management
│   └── orders.php         # Order management
├── auth/                  # Authentication
│   ├── login.php          # User login
│   ├── register.php       # New user registration
│   ├── admin_login.php    # Admin authentication
│   ├── logout.php         # Session termination
│   └── request_seller.php # Become seller request
├── cart/                  # Shopping cart
│   ├── add.php            # Add to cart
│   ├── remove.php         # Remove from cart
│   ├── update.php         # Update quantity
│   └── index.php          # Cart display
├── config/                # Configuration
│   ├── db.php             # Database connection
│   └── DBConn.php         # MySQLi wrapper
├── includes/              # Shared components
│   ├── header.php         # Page header
│   ├── footer.php         # Page footer
│   ├── functions.php      # Utility functions
│   ├── ShoppingCart.php   # Cart class
│   └── TextScanner.php    # Text processing
├── messages/              # Messaging system
│   ├── send.php           # Send message
│   ├── chat.php           # View conversation
│   └── inbox.php          # Message list
├── orders/                # Order management
│   ├── checkout.php       # Checkout process
│   ├── confirm.php        # Order confirmation
│   ├── manage.php         # Seller order management
│   └── track.php          # Buyer order tracking
├── products/              # Product pages
│   ├── add.php            # List new item
│   ├── edit.php           # Edit listing
│   ├── delete.php         # Remove listing
│   ├── index.php          # Browse products
│   └── view.php           # Product details
├── wishlist/              # Wishlist
│   ├── add.php            # Add to wishlist
│   ├── remove.php         # Remove from wishlist
│   └── index.php          # View wishlist
├── assets/
│   ├── css/
│   │   └── main.css       # Dark theme styles
│   ├── js/
│   │   └── main.js        # Client-side interactions
│   └── images/            # Product images
├── index.php              # Homepage
├── database.sql           # Database schema & seed data
└── README.md              # This file
```

## Installation & Setup

### Prerequisites

- PHP 7.4+
- MySQL 5.7+
- Web server (Apache/Nginx)
- Composer (optional, no dependencies required)

### Step 1: Database Setup

```sql
-- Import the database schema
mysql -u root -p < database.sql
```

Default sample data included:
- **Admin**: admin@pastimes.co.za / password
- **Sellers**: sarah@example.com, mike@example.com, demo@pastimes.co.za / password
- **Buyers**: john@example.com, buyer@pastimes.co.za / password
- **30 sample products** across 8 categories

### Step 2: Configure Database Connection

Edit `config/DBConn.php`:

```php
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'pastimes';
```

### Step 3: Web Server Configuration

#### Apache (.htaccess)

```apache
<IfModule mod_rewrite.c>
    RewriteEngine On
    RewriteBase /
    RewriteRule ^index\.html$ - [L]
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteCond %{REQUEST_FILENAME} !-d
</IfModule>
```

#### Nginx

```nginx
location ~ \.php$ {
    try_files $uri =404;
    fastcgi_pass unix:/var/run/php-fpm.sock;
    fastcgi_index index.php;
    fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
    include fastcgi_params;
}
```

### Step 4: Create Uploads Directory

```bash
chmod 755 assets/images/uploads/
```

### Step 5: Access the Application

```
http://localhost/pastimes/
```

## User Roles & Permissions

### Buyer
- Browse and search products
- Add items to cart and checkout
- View order history and tracking
- Create wishlist
- Message sellers
- Request seller status

### Seller
- List and manage products
- View buyer messages about products
- Manage orders (pack, track)
- View sales statistics
- Cannot access admin panel

### Admin
- Full user management (create, edit, delete, verify)
- Product oversight (view, delete listings)
- Order management (update status)
- Seller request approval/rejection
- Comprehensive dashboard with analytics

## Security Features

### Authentication
- Session-based authentication (`session_start()`)
- Bcrypt password hashing (`password_hash()`, `password_verify()`)
- Session regeneration on login (`session_regenerate_id()`)

### Data Protection
- Prepared statements for all queries (MySQLi)
- HTML escaping with `htmlspecialchars()` (h() helper)
- Input sanitization (`sanitize()` helper)
- CORS-friendly setup

### Authorization
- Role-based access guards (`requireAdmin()`, `requireSeller()`, etc.)
- Verified status checks for sellers
- User ownership verification on resources

## API Patterns

### Standard Query Pattern

```php
// Prepared statement with parameters
$sql = "SELECT * FROM tblProducts WHERE category_id = ? AND status = ? ORDER BY created_at DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 'is', $category_id, 'active');
mysqli_stmt_execute($stmt);
$products = mysqli_fetch_all(mysqli_stmt_get_result($stmt), MYSQLI_ASSOC);
mysqli_stmt_close($stmt);
```

### Form Handling Pattern

```php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = sanitize($_POST['field'] ?? '');
    
    // Validation
    if (empty($data)) {
        $errors[] = 'Field is required';
    }
    
    // Process if valid
    if (empty($errors)) {
        // Database operations
    }
}
```

### Authentication Guard Pattern

```php
requireLogin();              // Redirect if not logged in
requireVerified();           // Redirect if not verified
requireSeller();             // Redirect if not a seller
requireAdmin();              // Redirect if not admin
requireSellerOrAdmin();      // Redirect if neither
```

## Key Functions

### Authentication (`includes/functions.php`)

```php
isLoggedIn()               // Check if user is logged in
isSeller()                 // Check if user is verified seller
isAdmin()                  // Check if user is admin
isVerified()               // Check if user is verified
requireLogin()             // Guard: redirect if not logged in
requireAdmin()             // Guard: redirect if not admin
```

### Utilities

```php
sanitize($data)            // Trim and strip slashes
h($str)                    // HTML escape output
getProductImage($img)      // Get image URL or placeholder
redirect($url)             // Redirect and exit
```

## Frontend Architecture

### CSS (`assets/css/main.css`)

- **Dark Theme**: CSS custom properties for easy theming
- **Mobile-First**: Breakpoints at 768px and 1024px
- **Flexbox/Grid**: Modern layout techniques
- **Classes**: BEM-inspired for consistency

### JavaScript (`assets/js/main.js`)

- **Mobile Menu**: Toggle navigation on small screens
- **Wishlist**: Add/remove without page reload
- **Notifications**: Toast messages for user feedback
- **Helpers**: Debounce, validation, formatting utilities
- **No Dependencies**: Pure JavaScript, no jQuery or frameworks

## Sample Data

### Included Users
- **Admins**: 1 (admin@pastimes.co.za)
- **Sellers**: 3 verified sellers
- **Buyers**: 2 buyers

### Included Products
- **Total**: 30 products
- **Categories**: 8 (Vintage, Streetwear, Outerwear, Shoes, Accessories, Sportswear, Men's, Women's)
- **Conditions**: Range from "New" to "Poor"
- **Price Range**: R85 - R1800

### Sample Orders
- **Total**: Ready for seed data expansion

## Testing Workflow

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

## Troubleshooting

### White Screen / 500 Error
- Check PHP error logs
- Verify database connection in `config/DBConn.php`
- Ensure MySQL service is running

### Login Not Working
- Verify `session_start()` is called in `config/db.php`
- Check cookies are enabled
- Verify user email exists in database

### Products Not Displaying
- Check `UPLOAD_DIR` path exists and is writable
- Verify images exist in `assets/images/`
- Ensure product status is 'active'

### Permission Denied Errors
- Run `chmod 755 assets/images/uploads/`
- Check file ownership (often www-data:www-data on Linux)

## Performance Optimization

### Current Implementation
- MySQLi with prepared statements (no N+1 queries)
- Lazy loading images with data-src
- CSS/JS minification ready
- LIMIT clauses on all list queries

### Future Improvements
- Add database query caching (Redis)
- Implement pagination for large datasets
- CDN for static assets
- Image optimization/resizing
- API rate limiting

## SEO & Social

### Meta Tags
- Responsive viewport configuration
- Open Graph ready (can be added)
- Schema.org structured data ready

### URL Structure
- Clean URLs with query parameters
- Category filtering via `?category=ID`
- Search via `?search=TERM`
- Sorting via `?sort=created_at`

## Deployment Checklist

- [ ] Update `BASE_URL` in `config/db.php`
- [ ] Set database credentials in `config/DBConn.php`
- [ ] Update UPLOAD_DIR path if different
- [ ] Run `database.sql` on production database
- [ ] Set file permissions: `chmod 755 assets/images/uploads/`
- [ ] Enable HTTPS for production
- [ ] Configure error logging (don't display errors to users)
- [ ] Set `session.secure=1` for HTTPS-only cookies

## Contributing Guidelines

### Code Style
- Follow PSR-12 for PHP
- Use prepared statements for all queries
- Escape all output with `h()`
- Use existing utility functions

### Adding New Pages
1. Start with auth guard if needed (`requireAdmin()`, etc.)
2. Include header: `require_once __DIR__ . '/../includes/header.php';`
3. Include footer: `require_once __DIR__ . '/../includes/footer.php';`
4. Use prepared statements
5. Follow existing naming conventions

### Git Workflow
```bash
git checkout -b feature/feature-name
git commit -m "Add feature description"
git push origin feature/feature-name
```

## License

Proprietary - WEDE6021 POE Project

## Support

For issues or questions, contact the development team.

---

**Last Updated**: June 2026  
**Version**: 1.0  
**Status**: Production Ready
