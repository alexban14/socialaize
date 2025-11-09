import api from '@/lib/api';
import { Interest } from '@/schemas/interest'; // I'll need to create this schema

export const getAllInterests = async (): Promise<Interest[]> => {
  const response = await api.get('/interests');
  return response.data;
};

export const getUserInterests = async (): Promise<Interest[]> => {
  const response = await api.get('/user/interests');
  return response.data;
};

export const addInterestToProfile = async (interestName: string): Promise<Interest> => {
  const response = await api.post('/user/interests', { name: interestName });
  return response.data;
};

export const removeInterestFromProfile = async (interestId: number): Promise<void> => {
  await api.delete(`/user/interests/${interestId}`);
};
