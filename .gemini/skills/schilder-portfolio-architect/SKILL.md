---
name: schilder-portfolio-architect
description: Expert in het bouwen van snelle, visuele multi-page portfolio-websites voor Nederlandse schilders. Focus op conversie, lokale SEO en een extreem simplistisch beheer zonder externe afhankelijkheden.
---

## 1. Technische Stack & Architectuur
- **Framework**: Astro (Multi-Page Application architectuur). Dit zorgt voor ultrasnelle laadtijden en individuele projectpagina's voor optimale SEO.
- **Styling**: Tailwind CSS. Gebruik utility-classes voor een "Industrial Modern" design met veel witruimte, strakke grijstinten en een focus op de fotografie.
- **CMS**: Keystatic of Outstatic (File-based, schrijft naar Markdown/JSON in de repo). De interface MOET volledig in het Nederlands zijn.
- **Authenticatie (Custom)**: Een simpele, op maat gemaakte login-route (`/admin/login`).
  - Slechts één inlogveld voor e-mail en wachtwoord (gevalideerd via `.env` variabelen of een simpel lokaal script).
  - Verleen toegang via een beveiligde session-cookie.
  - GEEN registratie-optie, GEEN "wachtwoord vergeten" flow. Simpel en afgesloten.
- **Hosting**: Vercel, Netlify of Cloudflare Pages (Serverless, zero cost).

## 2. Project & Bedrijfs Schema (CMS Beheer)
- **Projecten Collectie**:
  - `titel` (Bijv. "Houtrot reparatie en buitenschilderwerk in Utrecht")
  - `slug` (Voor de unieke URL, bijv. `/projecten/houtrot-utrecht`)
  - `datum`
  - `afbeeldingen` (Array)
  - `categorie` (Binnen, Buiten, Zakelijk, Particulier)
  - `beschrijving` (Rijke tekst)
- **Algemene Instellingen (Global)**:
  - `Bedrijfsgegevens`: Telefoon, Emailadres (voor lead-ontvangst), Fysiek adres.
  - `KvK-nummer`: Verplicht in de footer.
  - `Servicegebied`: Standaard ingesteld op "Heel Nederland", maar aanpasbaar.

## 3. Core Workflows & Functionaliteiten
- **Lokale Beeldverwerking**: Gebruik Astro's ingebouwde `<Image />` component. Alle geüploade foto's moeten tijdens het build-proces lokaal worden gecomprimeerd en omgezet naar WebP/AVIF formaten. Geen externe CDN of afhankelijkheden.
- **Formulier & Beveiliging**: 
  - Het contactformulier (offerte-aanvraag) verstuurt leads via e-mail (bijv. via Resend, Formspree of een Astro server-endpoint).
  - **Beveiliging**: Integreer Google reCAPTCHA (v3) of Cloudflare Turnstile om de inbox van de schilder te beschermen tegen spam en bots.
- **Multi-Page Routing**: Zorg ervoor dat elk project zijn eigen unieke, indexeerbare URL krijgt (`/projecten/[slug].astro`), wat essentieel is voor lokale zoekopdrachten.

## 4. UI/UX & Design Richtlijnen
- **Taal**: De volledige front-end (knoppen, formulieren, foutmeldingen) is in het **Nederlands**.
- **Call to Action (CTA)**: "Vraag een offerte aan" moet als opvallende knop in de hoofdnavigatie staan en onderaan elke individuele projectpagina terugkomen.
- **Mobiel Eerst**: Ontwerp met een mobile-first benadering. Het portfolio moet feilloos werken met touch-bediening (bijv. image lightboxes).

## 5. SEO & Compliance (NL)
- **Alt-teksten**: Genereer automatische of verplichte alt-tags voor elke projectfoto via het CMS.
- **Schema.org JSON-LD**: Implementeer `LocalBusiness` structured data op de homepagina met velden voor `iso6523Code` (KvK) en het `areaServed` (werkgebied).
- **Semantische HTML**: Gebruik correcte `<header>`, `<main>`, `<article>`, en `<nav>` tags, wat cruciaal is in een multi-page setup voor screenreaders en zoekmachines.