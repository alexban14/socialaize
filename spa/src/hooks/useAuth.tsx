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
  refreshUser: () => Promise<void>;
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

  const refreshUser = async () => {
    const currentToken = localStorage.getItem('authToken');
    if (currentToken) {
      api.defaults.headers.common['Authorization'] = `Bearer ${currentToken}`;
      try {
        const userData = await authService.getAuthenticatedUser();
        setUser(userData);
      } catch (error) {
        console.error('Failed to fetch user during refresh', error);
      }
    }
  };

  useEffect(() => {
    const initializeAuth = async () => {
      const currentToken = localStorage.getItem('authToken');
      if (currentToken) {
        setLoading(true);
        api.defaults.headers.common['Authorization'] = `Bearer ${currentToken}`;
        try {
          const userData = await authService.getAuthenticatedUser();
          setUser(userData);
        } catch (error) {
          console.error('Failed to fetch user on initial load', error);
          setToken(null);
          setUser(null);
          localStorage.removeItem('authToken');
          delete api.defaults.headers.common['Authorization'];
        } finally {
          setLoading(false);
        }
      } else {
        setUser(null);
        setLoading(false);
      }
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
    <AuthContext.Provider value={{ user, token, loading, login, logout, isAuthenticated, refreshUser }}>
      {children}
    </AuthContext.Provider>
  );
};
