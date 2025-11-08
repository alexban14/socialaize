import { z } from 'zod';

export const userProfileSchema = z.object({
  id: z.number(),
  user_id: z.number(),
  profile_type: z.enum(['personal', 'business', 'academic', 'creator']),
  title: z.string().nullable(),
  bio: z.string().nullable(),
  location: z.string().nullable(),
  website: z.string().nullable(),
  skills: z.array(z.string()).nullable().optional(),
  interests: z.array(z.string()).nullable().optional(),
  is_active: z.boolean(),
  created_at: z.string(),
  updated_at: z.string(),
});

export const userSchema = z.object({
  id: z.number(),
  name: z.string(),
  email: z.string().email(),
  email_verified_at: z.string().nullable(),
  avatar: z.string().nullable(),
  cover_image: z.string().nullable(),
  created_at: z.string(),
  updated_at: z.string(),
  profiles: z.array(userProfileSchema).optional(),
  active_profile: userProfileSchema.nullable().optional(),
});

export type User = z.infer<typeof userSchema>;
export type UserProfile = z.infer<typeof userProfileSchema>;
