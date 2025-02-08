# Food Delivery App

This is a **Food Delivery App** built for learning purposes and to be added to my portfolio. It is not a real commercial project but demonstrates how a food ordering system works.

## Features

- User authentication (Login/Register) with Laravel Sanctum
- Browse food items and categories
- Add food items to the cart
- Place orders
- Payment integration with Stripe
- Responsive UI using React and Tailwind CSS
- Backend API built with Laravel

## Tech Stack

### Frontend:
- React.js
- Tailwind CSS
- Axios (for API requests)
- React Query (for data fetching)
- React Redux (for state management)
- React Router DOM (for client-side routing)

### Backend:
- Laravel (with Sanctum for authentication)
- MySQL
- Laravel Eloquent ORM
- RESTful API design
- Stripe for payments (using stripe/stripe-php)

## Folder Structure

```
food_delivery_app/
│-- frontend/   # React app (client-side)
│   │-- src/
│   │   │-- assets/
│   │   │-- api/
│   │   │-- components/
│   │   │-- libs/
│   │   │-- pages/
│   │   │-- redux/
│   │-- public/
│   │-- package.json
│
│-- backend/    # Laravel API (server-side)
│   │-- app/                # Application logic
│   │-- bootstrap/          # Bootstrapping files
│   │-- config/             # Configuration files
│   │-- database/           # Migrations & seeders
│   │-- public/             # Public assets
│   │-- resources/          # Views & frontend assets
│   │-- routes/             # Route definitions (web.php, api.php)
│   │-- storage/            # Storage (logs, uploaded files)
│   │-- tests/              # Automated tests
│   │-- .env                # Environment configuration
│   │-- artisan             # Laravel CLI tool
│   │-- composer.json       # PHP dependencies
```

## Installation & Setup

### Backend Setup

1. Navigate to the backend folder:
   ```sh
   cd backend
   ```
2. Install dependencies:
   ```sh
   composer install
   ```
3. Copy the environment file and configure database settings:
   ```sh
   cp .env.example .env
   ```
4. Run database migrations:
   ```sh
   php artisan migrate
   ```
5. Start the Laravel development server:
   ```sh
   php artisan serve
   ```

### Frontend Setup

1. Navigate to the frontend folder:
   ```sh
   cd frontend
   ```
2. Install dependencies:
   ```sh
   npm install
   ```
3. Start the React development server:
   ```sh
   npm run dev
   ```

## API Routes

### Web Routes (web.php)

| Method | Endpoint    | Controller & Action |
|--------|------------|---------------------|
| POST   | /register  | RegisteredUserController@store |
| POST   | /login     | AuthenticatedSessionController@store |

### API Routes (api.php)

#### User Routes
| Method | Endpoint      | Controller & Action |
|--------|--------------|---------------------|
| GET    | /user        | Returns authenticated user |

#### Dish Routes
| Method | Endpoint     | Controller & Action |
|--------|-------------|---------------------|
| GET    | /dishes     | DishController@index |
| POST   | /dishes     | DishController@store (Admin only) |
| DELETE | /dishes     | DishController@delete (Admin only) |
| GET    | /dishes/{id} | DishController@show (Authenticated users) |
| PUT    | /dishes/{id} | DishController@update (Authenticated users) |

#### Cart Routes (Authenticated users only)
| Method | Endpoint              | Controller & Action |
|--------|----------------------|---------------------|
| POST   | /edit-quantity       | CartController@editQuantity |
| POST   | /add-to-cart         | CartController@addToCart |
| GET    | /cart-items          | CartController@getCartItems |
| DELETE | /remove-cart-item/{id} | CartController@removeCartItem |
| GET    | /number-of-cart-items | CartController@getNumberOfItems |

#### Order Routes (Authenticated users only)
| Method | Endpoint            | Controller & Action |
|--------|--------------------|---------------------|
| GET    | /checkout          | OrderController@checkout |
| GET    | /getCustomerOrders | OrderController@index |
| POST   | /orders            | OrderController@store |

#### Admin Routes (EnsureUserIsAdmin middleware)
| Method | Endpoint                | Controller & Action |
|--------|------------------------|---------------------|
| GET    | /admin/login           | Admin login (not implemented) |
| GET    | /admin/orders          | OrderController@getAllOrders |
| POST   | /admin/modify-order-status/{id} | OrderController@modifyOrderStatus |
