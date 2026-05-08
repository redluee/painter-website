import { config, fields, collection, singleton } from '@keystatic/core';

export default config({
  storage: {
    kind: 'local',
  },
  collections: {
    projecten: collection({
      label: 'Projecten',
      slugField: 'titel',
      path: 'src/content/projecten/*',
      format: { contentField: 'beschrijving' },
      schema: {
        titel: fields.slug({ name: { label: 'Titel' } }),
        datum: fields.date({ label: 'Datum', defaultValue: { kind: 'today' } }),
        afbeeldingen: fields.array(
          fields.image({
            label: 'Afbeelding',
            directory: 'public/images/projecten',
            publicPath: '/images/projecten',
          }),
          {
            label: 'Afbeeldingen',
            itemLabel: props => props.value || 'Nieuwe afbeelding',
          }
        ),
        categorie: fields.select({
          label: 'Categorie',
          options: [
            { label: 'Binnen', value: 'binnen' },
            { label: 'Buiten', value: 'buiten' },
            { label: 'Zakelijk', value: 'zakelijk' },
            { label: 'Particulier', value: 'particulier' },
          ],
          defaultValue: 'particulier',
        }),
        beschrijving: fields.markdoc({
          label: 'Beschrijving',
        }),
      },
    }),
  },
  singletons: {
    instellingen: singleton({
      label: 'Algemene Instellingen',
      path: 'src/content/instellingen',
      schema: {
        bedrijfsgegevens: fields.object({
          label: 'Bedrijfsgegevens',
          fields: {
            telefoon: fields.text({ label: 'Telefoonnummer' }),
            email: fields.text({ label: 'E-mailadres' }),
            adres: fields.text({ label: 'Adres', multiline: true }),
          },
        }),
        kvk: fields.text({ label: 'KvK-nummer' }),
        servicegebied: fields.text({ label: 'Servicegebied', defaultValue: 'Heel Nederland' }),
      },
    }),
  },
});
