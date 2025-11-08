import api from '@/lib/api';
import { User, UserProfile } from '@/schemas/user';

export const getAuthenticatedUser = async (): Promise<User> => {
  const response = await api.get('/user');
  return response.data;
};

export const updateUserProfile = async (data: FormData) => {
  const response = await api.post('/user/profile', data, {
    headers: {
      'Content-Type': 'multipart/form-data',
    },
  });
  return response.data;
};

export const getProfiles = async (): Promise<UserProfile[]> => {
    const response = await api.get('/user/profiles');
    return response.data;
}

export const createProfile = async (data: Partial<UserProfile>): Promise<UserProfile> => {
    const response = await api.post('/user/profiles', data);
    return response.data;
}

export const updateProfile = async (profileType: string, data: Partial<UserProfile>): Promise<UserProfile> => {
    const response = await api.put(`/user/profiles/${profileType}`, data);
    return response.data;
}

export const setActiveProfile = async (profileType: string): Promise<UserProfile> => {
    const response = await api.post(`/user/profiles/active/${profileType}`);
    return response.data;
}

export const synthesizeProfile = async (content: string) => {
  const response = await api.post('/ai/synthesize-profile', { content });
  return response.data;
};
