# Pastimes - Second-Hand Clothing E-Commerce Platform

> A modern PHP-based e-commerce platform for buying and selling pre-loved clothing items

## 🔐 Test Accounts

All accounts use the password: `Kookemooi10!` (except where noted)

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

## Project Overview

Pastimes is a full-featured second-hand clothing marketplace built with PHP, MySQL, and vanilla JavaScript. It provides a seamless experience for both buyers and sellers, with comprehensive admin controls for platform management.

### Key Features

- User Management
- Product Listings
- Shopping Cart
- Messaging System
- Wishlist
- Admin Dashboard
- Seller Tools
- Buyer Protection

## Technical Stack

- Backend: PHP 7.4+ with MySQLi
- Database: MySQL
- Frontend: HTML5, CSS3, Vanilla JavaScript
- Architecture: MVC-inspired
- Security: bcrypt, prepared statements, HTML escaping

## Database Schema

| Table | Purpose |
|--------|---------|
| `tblUser` | User accounts |
| `tblSellerRequests` | Seller applications |
| `categories` | Product categories |
| `tblProducts` | Product listings |
| `cart_items` | Shopping cart |
| `tblOrders` | Orders |
| `order_items` | Order line items |
| `tblMessages` | Messaging |
| `tblWishlist` | Wishlist |
| `tblReviews` | Reviews |

## Directory Structure

```text
pastimes_v2/
├── admin/
├── auth/
├── cart/
├── config/
├── includes/
├── messages/
├── orders/
├── products/
├── wishlist/
├── assets/
├── database.sql
├── index.php
└── README.md
```

## Installation

1. Import `database.sql`
2. Configure `config/DBConn.php`
3. Create uploads directory
4. Browse to:
   `http://localhost/pastimes/`

## User Roles

### Buyer
- Browse products
- Checkout
- Wishlist
- Messaging

### Seller
- List products
- Manage orders
- Track sales

### Admin
- Manage users
- Verify sellers
- Manage products
- Manage orders

## Security

- Password hashing
- Prepared statements
- Session authentication
- Role-based authorization
- HTML escaping

## Testing

- Login using the accounts above.
- Test buyer, seller and admin workflows.
- Verify seller approval process.
- Test checkout and messaging.

## License

Proprietary - WEDE6021 POE Project

---

**Last Updated:** June 2026  
**Version:** 1.0  
**Status:** Production Ready
