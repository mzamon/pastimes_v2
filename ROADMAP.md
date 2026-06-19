# Pastimes v2 — Complete Creation Roadmap
**Build Strategy:** Efficient file creation with dependency ordering  
**Generated:** June 19, 2026  
**Status:** Ready for implementation

---

## 📋 FILE CREATION PRIORITY & ROADMAP

### ⚡ PHASE 1: ESSENTIAL SHOPPING SYSTEM (Core Rubric Requirements)
*These files implement the required rubric functionality: AddItem, RemoveItem, Checkout, EmptyCart, Login, ProcessInput*

#### 1. Product Management (Sellers/Admin)
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|----------|
| 1.1 | `products/add.php` | Create new listing (form + upload handler) | ShoppingCart.php, functions.php, auth check, image upload | HIGH |
| 1.2 | `products/edit.php` | Edit existing listing (prepopulated form) | products/add.php logic, session seller check | HIGH |
| 1.3 | `products/delete.php` | Delete listing (hard/soft delete toggle) | Auth check, image cleanup | HIGH |

**Creation Notes:**
- `add.php` & `edit.php` share form structure (use form reuse)
- Include image validation: jpg/png only, max 5MB
- Database transaction for image + record
- Sticky form implementation
- Seller permission check via session

---

#### 2. Order Management (Checkout Flow)
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|----------|
| 2.1 | `orders/checkout.php` | Delivery form + payment selection | Cart session, ShoppingCart.php, form validation | CRITICAL |
| 2.2 | `orders/confirm.php` | Order confirmation + reference number generation | Database transaction, orders table insert | CRITICAL |
| 2.3 | `orders/track.php` | Purchase history + grand total display | Orders query, status tracking | HIGH |
| 2.4 | `orders/manage.php` | Seller order management (update status) | Seller auth check, order update | MEDIUM |

**Creation Notes:**
- `checkout.php`: Sticky form, address validation, phone validation (10 digits)
- `confirm.php`: Generate unique reference (e.g., PAST-2026-XXXXX), email confirmation (optional), clear cart session
- Database transaction: INSERT order + order_items, UPDATE product stock, UPDATE cart to processed
- `track.php`: GROUP BY order_id, SUM(order_items.price), ORDER BY date DESC
- Grand total calculation: SUM(price * quantity)

---

#### 3. Messaging System (User Communication)
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|----------|
| 3.1 | `messages/inbox.php` | List all conversations (user-specific) | Database query, session user | MEDIUM |
| 3.2 | `messages/chat.php` | Display conversation (single thread view) | messages table, pagination | MEDIUM |
| 3.3 | `messages/send.php` | Send message handler (AJAX endpoint) | Session validation, message insert | MEDIUM |

**Creation Notes:**
- Inbox: Show last message preview, timestamp, unread count
- Chat: Display threaded messages (sender/recipient bubble layout)
- Participants filtered: if sender=user_id OR recipient=user_id
- Mark as read on fetch

---

#### 4. Wishlist System (User Collections)
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|----------|
| 4.1 | `wishlist/index.php` | Display user's saved items | Wishlist query, products joined | MEDIUM |
| 4.2 | `wishlist/add.php` | Add product to wishlist (AJAX endpoint) | Session validation, wishlist insert | MEDIUM |
| 4.3 | `wishlist/remove.php` | Remove from wishlist (AJAX endpoint) | Wishlist delete | MEDIUM |

**Creation Notes:**
- Check duplicate before add.php insert (unique user_id + product_id)
- AJAX endpoints return JSON (success/error)
- Show "Remove from Wishlist" button if item in wishlist

---

### 🛡️ PHASE 2: ADMIN DASHBOARD & USER MANAGEMENT
*Implements verification, admin controls, user/seller approval*

#### 5. Admin Dashboard
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|----------|
| 5.1 | `admin/dashboard.php` | Stats overview (users, sellers, products, orders, revenue) | COUNT/SUM queries, date range | HIGH |
| 5.2 | `admin/verify_users.php` | Approve/reject pending sellers (review user_type requests) | Users query (is_seller_pending), update user_type | CRITICAL |
| 5.3 | `admin/users.php` | List all users with filters (type, status, search) | Pagination, search/filter logic | HIGH |
| 5.4 | `admin/add_user.php` | Admin create new user (form, bcrypt password, 8+ chars) | Password validation, session check | MEDIUM |
| 5.5 | `admin/edit_user.php` | Admin edit user details (populate form, update) | User ID from GET, permission check | MEDIUM |
| 5.6 | `admin/delete_user.php` | Admin soft-delete or archive user (flag user as inactive) | User auth check, update status | MEDIUM |
| 5.7 | `admin/products.php` | View/manage all products (approve, flag, remove) | Product query, pagination, filters | HIGH |
| 5.8 | `admin/orders.php` | View all orders (status tracking, export option) | Orders joined with users/products, date filter | MEDIUM |

**Creation Notes:**
- `dashboard.php`: 
  - Total users/sellers (COUNT WHERE user_type)
  - Total products (COUNT)
  - Total orders (COUNT)
  - Monthly revenue (SUM(order_items.price) GROUP BY MONTH)
  - Chart: pending sellers count
- `verify_users.php`: Show pending seller requests, approve/reject buttons, update user_type column
- `users.php`: Search by email/name, filter by user_type, pagination (20 per page)
- `add_user.php`: Form validation including 8-character password minimum
- `delete_user.php`: Soft delete (UPDATE users SET is_active=0)
- All admin pages require admin auth check at top

---

### 🎨 PHASE 3: FRONTEND & STYLING
*Responsive design, mobile navigation, form interactions*

#### 6. Styling & UX Scripts
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|----------|
| 6.1 | `assets/css/main.css` | Dark theme responsive CSS (mobile-first) | None (static) | HIGH |
| 6.2 | `assets/js/main.js` | Mobile nav, form validation, AJAX helpers, confirmations | None initially | MEDIUM |

**Creation Notes:**
- `main.css`:
  - Dark background (#1a1a1a or #0d0d0d)
  - Red accents (#E74C3C or #C0392B)
  - Mobile breakpoint: 768px
  - Tablet: 768px-1024px
  - Desktop: 1024px+
  - Navigation: hamburger menu on mobile
  - Forms: full-width on mobile, centered on desktop
  - Grid: 1 column mobile, 2 columns tablet, 3-4 columns desktop
  - Sticky header/footer on mobile
- `main.js`:
  - Mobile nav toggle
  - Form validation (8-char password, email format, required fields)
  - AJAX helpers (add to cart, wishlist, messages)
  - Confirm dialogs (delete, checkout)
  - Error/success notifications
  - Image preview on file upload

---

### 📄 PHASE 4: HOMEPAGE & DOCUMENTATION
*Entry point and project documentation*

#### 7. Public Pages & Documentation
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|----------|
| 7.1 | `index.php` | Homepage with hero section + featured listings | products query, sessions | MEDIUM |
| 7.2 | `README.md` | Complete documentation (setup, usage, API, testing) | All completed files | LOW |

**Creation Notes:**
- `index.php`:
  - Include header.php
  - Hero section with search bar (category filter)
  - Featured products (SELECT * LIMIT 6, ORDER BY newest)
  - Browse categories (accessories, outerwear, sports-gear, streetwear, vintage-clothing)
  - "Add Item" button (seller-only, checks session user_type)
  - Session check: if logged in show "My Cart", "Orders", "Messages"
  - Search functionality (form POST to products/index.php with filter)
  - Include footer.php
- `README.md`:
  - Setup instructions (database import, config)
  - User roles (admin, seller, buyer)
  - Features overview
  - API documentation (endpoints)
  - Testing instructions (sample users/data)
  - Database schema overview
  - Known limitations & future improvements

---

### 🧪 PHASE 5: SAMPLE DATA FOR TESTING (POE Submission)
*Demo data for proof of execution*

#### 8. Test Data Files
| # | File Path | Description | Dependencies | Priority |
|---|-----------|-------------|--------------|---------|
| 8.1 | `userData.txt` | 5 sample users (admin, 2 sellers, 2 buyers) | Format: email\|password\|name\|user_type | LOW |
| 8.2 | `clothesData.txt` | 5 sample clothing items (different categories, prices) | Format: seller_id\|title\|desc\|price\|category | LOW |
| 8.3 | `ordersData.txt` | 5 sample orders (shows purchase history) | Format: buyer_id\|seller_id\|product_id\|qty\|date | LOW |

**Creation Notes:**
- `userData.txt`: Include credentials (e.g., admin@pastimes.local / Password123!)
- Format as CSV or tab-delimited for easy reference
- Document in README.md how to use these for testing
- Include copy-paste instructions for importing via database

---

## 🔗 DEPENDENCY CHAIN & CREATION ORDER

```
✅ EXISTING (Already created):
├── config/DBConn.php
├── config/db.php
├── includes/functions.php
├── includes/ShoppingCart.php
├── includes/header.php
├── includes/footer.php
├── includes/TextScanner.php
├── auth/register.php
├── auth/login.php
├── auth/admin_login.php
├── auth/logout.php
├── auth/request_seller.php
├── cart/add.php
├── cart/remove.php
├── cart/update.php
├── cart/index.php
├── products/index.php
├── products/view.php
├── database.sql
└── loadClothingStore.php

📋 CREATE IN THIS ORDER:

BATCH 1 (Shopping Core - No dependencies):
├── products/add.php ✓ (required for other product operations)
├── products/edit.php ✓ (depends on add.php form structure)
├── products/delete.php ✓ (simple delete logic)
├── orders/checkout.php ✓ (critical: cart → order conversion)
└── orders/confirm.php ✓ (depends on checkout)

BATCH 2 (Order Management - Depends on BATCH 1):
├── orders/track.php ✓ (reads orders table)
└── orders/manage.php ✓ (updates orders table)

BATCH 3 (Messaging & Wishlist - Independent):
├── messages/inbox.php ✓
├── messages/chat.php ✓ (depends on inbox query structure)
├── messages/send.php ✓ (independent endpoint)
├── wishlist/index.php ✓
├── wishlist/add.php ✓ (independent endpoint)
└── wishlist/remove.php ✓ (independent endpoint)

BATCH 4 (Admin Panel - Depends on auth check):
├── admin/dashboard.php ✓ (statistics only)
├── admin/verify_users.php ✓ (critical: seller approval)
├── admin/users.php ✓ (list + filter)
├── admin/add_user.php ✓ (creates users)
├── admin/edit_user.php ✓ (updates users)
├── admin/delete_user.php ✓ (soft delete)
├── admin/products.php ✓ (list + status)
└── admin/orders.php ✓ (reporting)

BATCH 5 (Frontend Assets - After all PHP):
├── assets/css/main.css ✓ (global styling)
└── assets/js/main.js ✓ (interactions)

BATCH 6 (Entry Point & Docs - Last):
├── index.php ✓ (requires all other pages)
├── README.md ✓ (documentation)
├── userData.txt ✓ (reference only)
├── clothesData.txt ✓ (reference only)
└── ordersData.txt ✓ (reference only)
```

---

## 🎯 RUBRIC REQUIREMENTS MAPPING

### Requirement: **ShoppingCart Class Methods**
| Method | File | Location | Status |
|--------|------|----------|--------|
| `AddItem()` | includes/ShoppingCart.php | Already created | ✓ Used by cart/add.php |
| `RemoveItem()` | includes/ShoppingCart.php | Already created | ✓ Used by cart/remove.php |
| `Checkout()` | includes/ShoppingCart.php | Already created | ✓ Used by orders/confirm.php |
| `EmptyCart()` | includes/ShoppingCart.php | Already created | ✓ Used by orders/confirm.php |

### Requirement: **ProcessInput()**
| Usage | File | Purpose |
|-------|------|---------|
| Form sanitization | All product/order/admin/message forms | Prevent SQL injection, XSS |
| Search filters | products/index.php, admin/users.php | Safe query building |
| User input in checkout | orders/checkout.php | Address/phone validation |

### Requirement: **Login/Session Management**
| File | Implementation |
|------|-----------------|
| auth/login.php | ✓ Already created |
| auth/admin_login.php | ✓ Already created |
| Session checks | All admin/seller pages check `$_SESSION['user_id']` & `$_SESSION['user_type']` |
| 8-char password | Admin add_user.php, forms validate strlen >= 8 |

### Requirement: **Database Transactions**
| File | Transaction |
|------|-------------|
| orders/confirm.php | INSERT order + order_items, UPDATE stock, UPDATE cart status |
| admin/verify_users.php | UPDATE user_type (seller → verified) |
| wishlist/add.php | INSERT with duplicate check |

### Requirement: **Search & Filter**
| File | Implementation |
|------|-----------------|
| products/index.php | Filter by category, price range, search term |
| admin/users.php | Filter by user_type, search by email/name, pagination |
| admin/products.php | Filter by status, category, seller, sort options |
| admin/orders.php | Filter by date range, status, buyer/seller |

### Requirement: **Image Upload**
| File | Implementation |
|------|-----------------|
| products/add.php | Upload to assets/images/uploads/, validate type/size |
| products/edit.php | Update existing image or keep original |
| admin/edit_user.php | Profile picture upload (optional) |

### Requirement: **Mobile Responsive**
| Component | Breakpoint | Layout |
|-----------|-----------|--------|
| Navigation | < 768px | Hamburger menu (main.js toggle) |
| Product Grid | Mobile | 1 col, Tablet | 2 col, Desktop | 3-4 col |
| Forms | All | Full-width mobile, centered desktop |
| Checkout | < 768px | Stacked form fields |

---

## 📊 IMPLEMENTATION EFFICIENCY TIPS

### Form Reuse Strategy
- **Create shared template:** Create `includes/form_product.php` with add/edit form markup
  - Used by: `products/add.php` (new) & `products/edit.php` (prepopulated)
  - Passes mode ('add' vs 'edit'), prepopulated values as params
  - Single submit handler logic in both files

- **Create shared checkout form:** `includes/form_checkout.php`
  - Used by: `orders/checkout.php`
  - Sticky form fields on validation error

### Validation Reuse
- **Centralize in functions.php:**
  - `validate_email()` - email format
  - `validate_phone()` - 10 digits
  - `validate_password()` - 8+ chars, complexity
  - `validate_file_upload()` - jpg/png, max 5MB
  - `validate_address()` - not empty, length check
  - `sanitize_input()` - ProcessInput equivalent (strip_tags, htmlspecialchars, trim)

### Query Reuse
- **Pagination helper in functions.php:**
  - `paginate_results($query, $page, $per_page)` - returns LIMIT clause + total pages
  - Used by: admin/users.php, admin/products.php, admin/orders.php, messages/inbox.php

- **Category filter in functions.php:**
  - `get_category_filter()` - returns WHERE clause for category filter
  - Used by: products/index.php, admin/products.php

### Database Queries Standardized
- **All SELECT:** `SELECT * FROM table WHERE conditions ORDER BY latest DESC LIMIT offset, count`
- **All INSERT:** Use prepared statements with placeholders
- **All UPDATE:** Use transactions if multiple table modifications
- **All DELETE:** Soft delete (UPDATE status/is_active) unless explicitly hard delete

---

## 🔒 SECURITY CHECKLIST

For each new file implement:
- [ ] `session_start()` at top of file
- [ ] `$_SESSION['user_id']` check (or admin check)
- [ ] `ProcessInput()` on all $_GET, $_POST, $_FILES inputs
- [ ] Prepared statements (never string concat in SQL)
- [ ] File upload validation (type, size, extension)
- [ ] CSRF token on all forms (hidden input, session check)
- [ ] Redirect non-authenticated users to login
- [ ] Redirect non-authorized (wrong user_type) to error page
- [ ] Log admin actions (verify, delete user, etc.) - optional

---

## ⏱️ ESTIMATED CREATION TIME

| Phase | Files | Est. Time | Total |
|-------|-------|-----------|-------|
| 1. Shopping System | 7 files | 3-4 hours | 3-4h |
| 2. Admin Dashboard | 8 files | 3-4 hours | 3-4h |
| 3. Frontend Assets | 2 files | 2-3 hours | 2-3h |
| 4. Homepage & Docs | 5 files | 1-2 hours | 1-2h |
| **TOTAL** | **28 files** | **~10-13 hours** | |

---

## 📝 FILE CREATION SUMMARY TABLE

| Category | File Path | Purpose | Type | Rubric Match |
|----------|-----------|---------|------|--------------|
| **Products** | products/add.php | Create listing | PHP Form | AddItem/ProcessInput |
| | products/edit.php | Modify listing | PHP Form | ProcessInput |
| | products/delete.php | Remove listing | PHP Handler | None |
| **Orders** | orders/checkout.php | Delivery/Payment form | PHP Form | Checkout/ProcessInput |
| | orders/confirm.php | Confirm + reference | PHP Handler | Checkout/EmptyCart |
| | orders/track.php | Purchase history | PHP Display | None |
| | orders/manage.php | Seller status updates | PHP Handler | None |
| **Messaging** | messages/inbox.php | Conversation list | PHP Display | None |
| | messages/chat.php | Message thread | PHP Display | None |
| | messages/send.php | Send message | PHP Handler | None |
| **Wishlist** | wishlist/index.php | Saved items | PHP Display | None |
| | wishlist/add.php | Add to wishlist | PHP Handler | None |
| | wishlist/remove.php | Remove from wishlist | PHP Handler | None |
| **Admin** | admin/dashboard.php | Statistics | PHP Display | None |
| | admin/verify_users.php | Approve sellers | PHP Handler | Login/Database |
| | admin/users.php | User management | PHP Display | ProcessInput |
| | admin/add_user.php | Create user | PHP Form | Login/Password |
| | admin/edit_user.php | Edit user | PHP Form | ProcessInput |
| | admin/delete_user.php | Deactivate user | PHP Handler | None |
| | admin/products.php | Product management | PHP Display | ProcessInput |
| | admin/orders.php | Order reports | PHP Display | None |
| **Frontend** | assets/css/main.css | Styling | CSS | Mobile Responsive |
| | assets/js/main.js | Interactions | JavaScript | Form Validation |
| **Pages** | index.php | Homepage | PHP Display | None |
| **Docs** | README.md | Documentation | Markdown | None |
| **Data** | userData.txt | Test users | Text | None |
| | clothesData.txt | Test products | Text | None |
| | ordersData.txt | Test orders | Text | None |

---

## ✅ SIGN-OFF CHECKLIST

Before moving to code implementation, verify:

- [ ] All 28 file paths above confirmed with project lead
- [ ] Database schema (database.sql) finalized and tested
- [ ] User roles (admin, seller, buyer) clearly defined
- [ ] Image upload directory structure created (assets/images/uploads/)
- [ ] Session timeout duration decided (e.g., 30 min inactivity)
- [ ] Email notification requirements clarified (optional or required)
- [ ] Payment gateway placeholder defined
- [ ] Search/filter requirements finalized
- [ ] Admin dashboard metrics confirmed
- [ ] Mobile breakpoints approved (768px, 1024px)
- [ ] Dark theme color palette finalized
- [ ] Error page (404, 403, 500) handling planned

---

**Next Step:** Begin BATCH 1 implementation (products/add.php through orders/confirm.php)
