import { z } from 'zod';

export const userSchema = z.object({
  id: z.number(),
  name: z.string(),
  email: z.string().email(),
  email_verified_at: z.string().nullable(),
  avatar: z.string().nullable(),
  bio: z.string().nullable(),
  website: z.string().nullable(),
  location: z.string().nullable(),
  cover_image: z.string().nullable(),
  created_at: z.string(),
  updated_at: z.string(),
});

export type User = z.infer<typeof userSchema>;
