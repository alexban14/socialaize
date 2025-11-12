import api from '@/lib/api';
import { Interest } from '@/schemas/interest'; // I'll need to create this schema

export const getAllInterests = async (): Promise<Interest[]> => {
  const response = await api.get('/interests');
  return response.data;
};

export const getUserInterests = async (profileType: string): Promise<Interest[]> => {
  const response = await api.get(`/user/profiles/${profileType}/interests`);
  return response.data;
};

export const addInterestToProfile = async (interestName: string, profileType: string): Promise<Interest> => {
  const response = await api.post(`/user/profiles/${profileType}/interests`, { name: interestName });
  return response.data;
};

export const removeInterestFromProfile = async (interestId: number, profileType: string): Promise<void> => {
  await api.delete(`/user/profiles/${profileType}/interests/${interestId}`);
};
