import api from '@/lib/api';
import { Skill } from '@/schemas/skill'; // I'll need to create this schema

export const getAllSkills = async (): Promise<Skill[]> => {
  const response = await api.get('/skills');
  return response.data;
};

export const getUserSkills = async (): Promise<Skill[]> => {
  const response = await api.get('/user/skills');
  return response.data;
};

export const addSkillToProfile = async (skillName: string): Promise<Skill> => {
  const response = await api.post('/user/skills', { name: skillName });
  return response.data;
};

export const removeSkillFromProfile = async (skillId: number): Promise<void> => {
  await api.delete(`/user/skills/${skillId}`);
};
