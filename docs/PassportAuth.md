# API Authentication with Laravel Passport

This document outlines how the `socialaize` backend leverages Laravel Passport to provide secure OAuth2 authentication for its API.

## 1. Introduction to Laravel Passport

Laravel Passport is an OAuth2 server implementation for Laravel. It provides a full suite of tools to secure your API, allowing client applications (like our SPA frontend or mobile apps) to request authorized access to protected resources on behalf of a user.

In this project, we use the **Password Grant** flow. This is a common choice for trusted, first-party clients. In this flow, the client application collects the user's email and password and exchanges them directly for an access token.

## 2. Core Configuration

The project has been configured to use Passport for API authentication. Here are the key setup points:

- **API Guard:** The `auth:api` middleware is configured in `config/auth.php` to use the `passport` driver. Any route protected by this middleware will require a valid Passport access token.
- **User Model:** The `App\Models\User` model uses the `HasApiTokens` trait, which adds the necessary methods and relationships to the User model for Passport to function.
- **Passport Routes:** Passport's routes (e.g., `/oauth/token`) are automatically registered by its service provider.
- **Database Migrations:** The necessary `oauth_*` tables have been created to store clients, access tokens, and refresh tokens.

## 3. Authentication Flow (Password Grant)

The authentication process follows these steps:

1.  **Client Request:** The frontend application collects the user's email and password.
2.  **Token Request:** The frontend makes a `POST` request to the `/oauth/token` endpoint. This request includes the user's credentials and a special "Password Grant Client" ID and secret.
3.  **Token Issuance:** If the credentials are valid, Laravel Passport generates and returns an `access_token` and a `refresh_token`.
4.  **Authenticated Requests:** The frontend stores the `access_token` and includes it in the `Authorization` header for all subsequent requests to protected API endpoints (e.g., `/api/user`).
5.  **Token Refresh:** The `access_token` is short-lived. Once it expires, the frontend can use the `refresh_token` to request a new access token from the `/oauth/token` endpoint without requiring the user to log in again.

## 4. Testing with Postman

Here is a step-by-step guide to test the API authentication flow using Postman.

### Step 1: Start the Server

Ensure your local Laravel development server is running. You can typically do this from the `socialaize/backend` directory:

```bash
php artisan serve
```

This will usually start the server at `http://127.0.0.1:8000`.

### Step 2: Create a User

Make sure you have at least one user in your `users` table. The default `DatabaseSeeder` creates a user:

- **Email:** `test@example.com`
- **Password:** `password`

### Step 3: Create a Password Grant Client

Passport needs a special client to issue tokens using the password grant. Run the following command in your terminal from the `socialaize/backend` directory:

```bash
php artisan passport:client --password
```

The command will output a **Client ID** and a **Client Secret**. Copy these values, as you will need them in the next step.

### Step 4: Request an Access Token

1.  Open Postman and create a new `POST` request.
2.  Set the URL to `http://127.0.0.1:8000/oauth/token`.
3.  Go to the **Headers** tab and add:
    - `Accept`: `application/json`
4.  Go to the **Body** tab and select `x-www-form-urlencoded`.
5.  Add the following key-value pairs:
    - `grant_type`: `password`
    - `client_id`: *Paste the Client ID from Step 3*
    - `client_secret`: *Paste the Client Secret from Step 3*
    - `username`: `test@example.com` (or your user's email)
    - `password`: `password`
    - `scope`: `*` (or leave empty for all scopes)

6.  Send the request. You should receive a JSON response like this:

    ```json
    {
        "token_type": "Bearer",
        "expires_in": 31536000,
        "access_token": "ey...",
        "refresh_token": "def..."
    }
    ```

### Step 5: Access a Protected Route

1.  Copy the `access_token` from the response above.
2.  Create a new `GET` request in Postman.
3.  Set the URL to `http://127.0.0.1:8000/api/user`.
4.  Go to the **Headers** tab and add:
    - `Accept`: `application/json`
    - `Authorization`: `Bearer <YOUR_ACCESS_TOKEN>` (replace `<YOUR_ACCESS_TOKEN>` with the token you copied).

5.  Send the request. You should receive a JSON response with the authenticated user's details.

### Step 6: Refresh the Access Token

When the `access_token` expires, you can get a new one using the `refresh_token`.

1.  Go back to your `/oauth/token` request in Postman.
2.  Change the **Body** to the following:
    - `grant_type`: `refresh_token`
    - `client_id`: *Your Client ID*
    - `client_secret`: *Your Client Secret*
    - `refresh_token`: *The `refresh_token` you received in Step 4*

3.  Send the request. You will receive a new `access_token` and `refresh_token`.
