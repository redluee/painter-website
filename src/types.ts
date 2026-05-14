export interface Project {
  name: string;
  slug: string;
  paintType: string[];
  description: string;
  pictures: string[];
  review?: {
    stars: number;
    description: string;
  };
}

export interface BusinessInfo {
  name: string;
  intro: string;
  phone: string;
  email: string;
  location: string;
  kvk: string;
}

export interface SiteContent {
  businessInfo: BusinessInfo;
  aboutMe: string;
  tarievenContent: string;
  partnersContent: string;
  profileImage: string;
}

export interface ThemeSettings {
  accent1: string;
  accent2: string;
  sectionBg: string;
  navbarBg: string;
  tokenVersion: number;
}
