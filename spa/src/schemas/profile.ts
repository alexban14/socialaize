import { z } from "zod";

export const profileSchema = z.object({
  name: z.string().min(2, { message: "Name must be at least 2 characters" }),
  title: z.string().max(50, { message: "Title cannot be longer than 50 characters" }).optional().nullable(),
  bio: z.string().max(160, { message: "Bio cannot be longer than 160 characters" }).optional().nullable(),
  location: z.string().max(50, { message: "Location cannot be longer than 50 characters" }).optional().nullable(),
  website: z.string().url({ message: "Please enter a valid URL" }).optional().or(z.literal('')).nullable(),
});

export type ProfileFormValues = z.infer<typeof profileSchema>;
