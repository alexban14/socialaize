import { z } from 'zod';

export const interestSchema = z.object({
  id: z.number(),
  name: z.string(),
  created_at: z.string(),
  updated_at: z.string(),
});

export type Interest = z.infer<typeof interestSchema>;
