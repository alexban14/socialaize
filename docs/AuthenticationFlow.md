# Authentication Flow Documentation

This document provides a comprehensive, step-by-step breakdown of the authentication flows (Login, Signup, and Logout) for the Socialaize platform.

---

## 1. Login Flow (Password Grant)

The login process uses the OAuth2 Password Grant flow. The frontend directly exchanges the user's email and password for a user-specific access token.

1.  **Frontend - UI (`src/pages/Auth.tsx`)**
    *   The user enters their email and password into the login form.
    *   On submission, the `loginMutation` is triggered, which calls the `authService.login` function.

2.  **Frontend - Service Layer (`src/services/authService.ts`)**
    *   An HTTP `POST` request is sent to the `/oauth/token` endpoint on the Laravel backend.
    *   The request body contains two sets of credentials:
        *   **Client Credentials**: `grant_type: 'password'`, `client_id`, and `client_secret`. These identify the React SPA as a trusted application.
        *   **User Credentials**: `username` (the user's email) and `password`.

3.  **Backend - Laravel Passport (`/oauth/token`)**
    *   Passport's internal route receives the request.
    *   It first authenticates the **client** by checking the `client_id` and `client_secret` against the `oauth_clients` table in the database.
    *   It then authenticates the **user** by verifying their email and password against the `users` table.

4.  **Backend - Database**
    *   If both client and user are valid, Passport generates a new `access_token` and `refresh_token`.
    *   The new token is stored in the `oauth_access_tokens` table, linked to the user's ID.

5.  **Frontend - State Management (`src/hooks/useAuth.tsx`)**
    *   The frontend receives the `access_token` in the response.
    *   The `login()` function within the `useAuth` hook is called. This function saves the token to `localStorage` and updates the React state.
    *   This state update triggers a `useEffect`, which makes an authenticated `GET` request to `/api/v1/user` to fetch the full user profile.
    *   Once the user data is loaded, the `isAuthenticated` flag becomes `true`, and the router redirects the user to the main application page.

---

## 2. Signup (Registration) Flow

Because a new user does not yet exist in the database, the signup flow hits a custom API endpoint first.

1.  **Frontend - UI (`src/pages/Auth.tsx`)**
    *   The user fills out the registration form (name, email, password).
    *   The `signupMutation` is triggered, calling `authService.register`.

2.  **Frontend - Service Layer (`src/services/authService.ts`)**
    *   An HTTP `POST` request is sent to the custom `/api/v1/register` endpoint.

3.  **Backend - Controller (`app/Http/Controllers/Api/V1/AuthController.php`)**
    *   The `register` method validates the incoming data.
    *   It then calls the `PassportAuthService` to handle the registration logic.

4.  **Backend - Service & Database (`PassportAuthService.php`, `UserRepository.php`)**
    *   The service uses the repository to create a new user record in the `users` table. The password is automatically hashed.

5.  **Backend - Token Generation**
    *   For a seamless user experience, after creating the user, the `PassportAuthService` immediately generates the first `access_token` for this new user.

6.  **Frontend - State Management (`src/hooks/useAuth.tsx`)**
    *   The backend returns a single response containing both the new user object and their first `access_token`.
    *   From here, the process is identical to the login flow: the token is stored, the user profile is fetched, and the user is redirected into the application.

---

## 3. Logout Flow

The logout process ensures the session is terminated on both the server and the client.

1.  **Frontend - UI (`src/components/layout/Navbar.tsx`)**
    *   The user clicks the "Log out" button, which calls the `logout()` function from the `useAuth` hook.

2.  **Frontend - API Call (`useAuth.tsx`)**
    *   The `logout` function sends an authenticated `POST` request to the `/api/v1/logout` endpoint.

3.  **Backend - Controller & Token Revocation (`AuthController.php`)**
    *   The `logout` method is protected by the `auth:api` middleware, so Laravel knows which user and token are making the request.
    *   The code calls `$request->user()->token()->revoke()`.

4.  **Backend - Database**
    *   The `revoked` flag for the specific token in the `oauth_access_tokens` table is set to `true`. This token is now invalid and cannot be used again.

5.  **Frontend - Client-Side Cleanup (`useAuth.tsx`)**
    *   In a `finally` block (to ensure it always runs), the client-side state is cleared: the token is removed from `localStorage`, the user and token are set to `null` in the React state, and the user is redirected to the `/auth` login page.
