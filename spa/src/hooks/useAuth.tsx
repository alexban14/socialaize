import { createContext, useContext, useEffect, useState, ReactNode } from 'react';
import { User } from '@/schemas/user';
import api from '@/lib/api';
import { useNavigate } from 'react-router-dom';
import * as authService from '@/services/authService';

interface AuthContextType {
  user: User | null;
  token: string | null;
  loading: boolean;
  login: (token: string) => void;
  logout: () => void;
  isAuthenticated: boolean;
}

const AuthContext = createContext<AuthContextType | null>(null);

export const useAuth = () => {
  const context = useContext(AuthContext);
  if (!context) {
    throw new Error('useAuth must be used within an AuthProvider');
  }
  return context;
};

export const AuthProvider = ({ children }: { children: ReactNode }) => {
  const [user, setUser] = useState<User | null>(null);
  const [token, setToken] = useState<string | null>(() => localStorage.getItem('authToken'));
  const [loading, setLoading] = useState(true);
  const navigate = useNavigate();

  useEffect(() => {
    const initializeAuth = async () => {
      if (token) {
        api.defaults.headers.common['Authorization'] = `Bearer ${token}`;
        try {
          const userData = await authService.getAuthenticatedUser();
          setUser(userData);
        } catch (error) {
          console.error('Failed to fetch user', error);
          setToken(null);
          localStorage.removeItem('authToken');
        }
      }
      setLoading(false);
    };
    initializeAuth();
  }, [token]);

  const login = (newToken: string) => {
    localStorage.setItem('authToken', newToken);
    setToken(newToken);
  };

  const logout = async () => {
    try {
      await authService.logout();
    } catch (error) {
      console.error('Logout failed', error);
    } finally {
      localStorage.removeItem('authToken');
      setToken(null);
      setUser(null);
      delete api.defaults.headers.common['Authorization'];
      navigate('/auth');
    }
  };

  const isAuthenticated = !!user && !!token;

  return (
    <AuthContext.Provider value={{ user, token, loading, login, logout, isAuthenticated }}>
      {children}
    </AuthContext.Provider>
  );
};
