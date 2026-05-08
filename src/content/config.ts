import { defineCollection, z } from 'astro:content';

const projecten = defineCollection({
  type: 'content',
  schema: z.object({
    titel: z.string(),
    datum: z.date(),
    afbeeldingen: z.array(z.string()).optional(),
    categorie: z.enum(['binnen', 'buiten', 'zakelijk', 'particulier']),
  }),
});

export const collections = {
  projecten,
};
