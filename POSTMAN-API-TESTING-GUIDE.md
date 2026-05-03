# E-Commerce API Postman Collection

## 📋 Overview

This Postman collection contains all API endpoints for the E-Commerce application. The collection is organized by functionality and includes proper authentication headers and sample request bodies.

## 🚀 Getting Started

### 1. Import the Collection
1. Open Postman
2. Click "Import" button
3. Select "File" tab
4. Choose `E-Commerce-API-Postman-Collection.json`
5. Click "Import"

### 2. Set Environment Variables
The collection uses these variables (automatically managed):
- `{{base_url}}` - Your API base URL (default: `http://localhost:8000`)
- `{{token}}` - User authentication token (auto-saved after login)
- `{{admin_token}}` - Admin authentication token (manual setup required)

### 3. Update Base URL (if needed)
If your Laravel app runs on a different port/host:
1. Go to Collection Variables
2. Update `base_url` variable
3. Save changes

## 📁 Collection Structure

### 🔐 Authentication
- **Register User** - Create new customer account
- **Login User** - Authenticate and get token
- **Get Current User** - Get authenticated user info
- **Logout** - Invalidate current token
- **Forgot Password** - Request password reset
- **Reset Password** - Reset password with token

### 🛍️ Products (Public)
- **Get All Products** - List all available products
- **Get Single Product** - Get detailed product info

### 🛒 Cart (Authenticated)
- **Get Cart** - View current cart items
- **Add to Cart** - Add product to cart
- **Update Cart Item** - Change item quantity
- **Remove from Cart** - Remove item from cart
- **Clear Cart** - Empty entire cart

### 📦 Orders (Authenticated)
- **Get User Orders** - List user's order history
- **Get Single Order** - View order details
- **Checkout** - Create order from cart

### 👨‍💼 Admin - Products
- **Get All Products (Admin)** - Admin product listing
- **Create Product (Admin)** - Add new product
- **Update Product (Admin)** - Modify product details
- **Delete Product (Admin)** - Remove product

### 👨‍💼 Admin - Orders
- **Get All Orders (Admin)** - View all orders
- **Update Order Status (Admin)** - Change order status

## 🔑 Authentication Flow

### For Regular Users:
1. Run **Register User** or **Login User**
2. Token is automatically saved to `{{token}}` variable
3. All subsequent requests use this token

### For Admin Users:
1. Login with admin credentials using **Login User**
2. Manually copy the token from response
3. Set it as `{{admin_token}}` variable in collection variables
4. Admin endpoints will use this token

## 📝 Sample Data

### User Registration/Login:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

### Add to Cart:
```json
{
  "product_id": 1,
  "quantity": 2
}
```

### Checkout:
```json
{
  "shipping_address": "123 Main St, City, State 12345"
}
```

### Create Product (Admin):
```json
{
  "name": "New Product",
  "description": "Product description",
  "price": 99.99,
  "stock": 100,
  "category": "Electronics"
}
```

## ⚠️ Important Notes

1. **Token Management**: User tokens are auto-saved after login. Admin tokens need manual setup.

2. **Order of Execution**: Run authentication requests first, then use protected endpoints.

3. **IDs**: Replace placeholder IDs (like `1`) with actual IDs from your database.

4. **Stock Validation**: Cart operations validate product stock availability.

5. **Admin Access**: Admin endpoints require admin user authentication.

## 🧪 Testing Tips

1. **Start with Authentication**: Always run login/register first
2. **Check Response Codes**: 200 (success), 201 (created), 401 (unauthorized), 403 (forbidden), 422 (validation error)
3. **Use Console**: Check Postman console for detailed request/response logs
4. **Environment Variables**: Verify tokens are saved correctly in collection variables

## 🔧 Troubleshooting

### Common Issues:
- **401 Unauthorized**: Check if token is set and valid
- **403 Forbidden**: Ensure user has admin privileges for admin endpoints
- **422 Validation Error**: Check request body format and required fields
- **404 Not Found**: Verify endpoint URLs and resource IDs

### Debug Steps:
1. Check collection variables are set correctly
2. Verify base URL is accessible
3. Ensure Laravel server is running (`php artisan serve`)
4. Check Laravel logs for server-side errors

## 📊 Response Format

All API responses follow this consistent format:
```json
{
  "status": "success|error",
  "message": "Optional message",
  "data": { /* Response data */ },
  "pagination": { /* Pagination info for lists */ }
}
```

Happy testing! 🎉