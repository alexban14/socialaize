import api from '@/lib/api';
import { Skill } from '@/schemas/skill'; // I'll need to create this schema

export const getAllSkills = async (): Promise<Skill[]> => {
  const response = await api.get('/skills');
  return response.data;
};

export const getUserSkills = async (profileType: string): Promise<Skill[]> => {
  const response = await api.get(`/user/profiles/${profileType}/skills`);
  return response.data;
};

export const addSkillToProfile = async (skillName: string, profileType: string): Promise<Skill> => {
  const response = await api.post(`/user/profiles/${profileType}/skills`, { name: skillName });
  return response.data;
};

export const removeSkillFromProfile = async (skillId: number, profileType: string): Promise<void> => {
  await api.delete(`/user/profiles/${profileType}/skills/${skillId}`);
};
