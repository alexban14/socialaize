import api from '@/lib/api';
import oauthApi from '@/lib/oauthApi';
import { loginSchema, signupSchema } from '@/schemas/auth';
import { z } from 'zod';
import { User } from '@/schemas/user';

type LoginData = z.infer<typeof loginSchema>;
type SignupData = z.infer<typeof signupSchema>;

// IMPORTANT: You need to replace these with your actual Laravel Passport client credentials.
// You can get these by running `php artisan passport:client --password` in your Laravel backend directory.
const OAUTH_CLIENT_ID = import.meta.env.VITE_OAUTH_CLIENT_ID || 'YOUR_CLIENT_ID';
const OAUTH_CLIENT_SECRET = import.meta.env.VITE_OAUTH_CLIENT_SECRET || 'YOUR_CLIENT_SECRET';

export const login = async (credentials: LoginData) => {
  const response = await oauthApi.post('/oauth/token', {
    grant_type: 'password',
    client_id: OAUTH_CLIENT_ID,
    client_secret: OAUTH_CLIENT_SECRET,
    username: credentials.email,
    password: credentials.password,
    scope: '*',
  });
  return response.data;
};

export const register = async (data: SignupData) => {
  const response = await api.post('/register', data);
  return response.data;
};

export const logout = async () => {
  return await api.post('/logout');
};

export const resendVerificationEmail = async () => {
  return await api.post('/email/verification-notification');
};

export const getAuthenticatedUser = async (): Promise<User> => {
  const response = await api.get('/user');
  return response.data;
};

export const forgotPassword = async (data: { email: string }) => {
  const response = await api.post('/forgot-password', data);
  return response.data;
};

export const resetPassword = async (data: any) => {
  const response = await api.post('/reset-password', data);
  return response.data;
};

export const updatePassword = async (data: any) => {
  const response = await api.post('/update-password', data);
  return response.data;
};

export const updateUserProfile = async (data: FormData) => {
  const response = await api.post('/user', data, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });
  return response.data;
};


