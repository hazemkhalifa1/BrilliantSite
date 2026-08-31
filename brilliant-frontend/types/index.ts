export interface ApiResponse<T> {
  success: boolean;
  message: string;
  data: T;
  statusCode: number;
}

export interface PagedResult<T> {
  items: T[];
  totalCount: number;
  pageIndex: number;
  pageSize: number;
}

export interface LoginRequest {
  email: string;
  password: string;
}

export interface AuthResponse {
  token: string;
  refreshToken: string;
  expiresAt: string;
  email: string;
  fullName: string | null;
  roles: string[];
}

export interface Product {
  id: number;
  name: string;
  nameAr: string | null;
  description: string;
  descriptionAr: string | null;
  imagePath: string;
  documentationUrl: string;
  categoryId: number;
  categoryName: string;
  brandId: number | null;
  brandName: string;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface ProductBrand {
  id: number;
  name: string;
  nameAr: string | null;
  description: string;
  descriptionAr: string | null;
  backgroundImagePath: string;
  order: number;
  isActive: boolean;
  createdAt: string;
  categories?: ProductCategory[];
}

export interface ProductCategory {
  id: number;
  name: string;
  nameAr: string | null;
  brandId: number;
  brandName: string;
  isActive: boolean;
  createdAt: string;
}

export interface Service {
  id: number;
  title: string;
  titleAr: string | null;
  description: string;
  descriptionAr: string | null;
  iconPath: string;
  categoryId: number;
  categoryName: string;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface ServiceCategory {
  id: number;
  name: string;
  nameAr: string | null;
  description: string;
  descriptionAr: string | null;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface Project {
  id: number;
  title: string;
  titleAr: string | null;
  description: string;
  descriptionAr: string | null;
  imagePath: string;
  clientName: string;
  clientNameAr: string | null;
  year: number;
  typeId: number;
  typeName: string;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface ProjectType {
  id: number;
  name: string;
  nameAr: string | null;
  isActive: boolean;
  createdAt: string;
}

export interface Tag {
  id: number;
  name: string;
  slug: string;
}

export interface BlogPost {
  id: number;
  title: string;
  titleAr: string | null;
  content: string;
  contentAr: string | null;
  coverImagePath: string;
  slug: string;
  metaTitle: string;
  metaTitleAr: string | null;
  metaDescription: string;
  metaDescriptionAr: string | null;
  publishedAt: string | null;
  order: number;
  isPublished: boolean;
  createdAt: string;
  tags: Tag[] | null;
}

export interface TeamMember {
  id: number;
  name: string;
  nameAr: string | null;
  jobTitle: string;
  jobTitleAr: string | null;
  imagePath: string;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface Client {
  id: number;
  name: string;
  nameAr: string | null;
  logoPath: string;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface HeroSection {
  id: number;
  headlineTop: string;
  headlineTopAr: string | null;
  headlineBottom: string;
  headlineBottomAr: string | null;
  subText: string;
  subTextAr: string | null;
  primaryBtnText: string;
  primaryBtnTextAr: string | null;
  primaryBtnUrl: string;
  secondaryBtnText: string;
  secondaryBtnTextAr: string | null;
  secondaryBtnUrl: string;
  updatedAt: string;
  stats: HeroStat[];
}

export interface HeroStat {
  id: number;
  value: string;
  label: string;
  labelAr: string | null;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface ContactInfo {
  id: number;
  phone1: string;
  phone2: string;
  email: string;
  email2: string;
  address: string;
  addressAr: string | null;
  mapEmbedUrl: string;
  updatedAt: string;
}

export interface SocialLink {
  id: number;
  platform: string;
  url: string;
  iconClass: string;
  order: number;
  isActive: boolean;
  createdAt: string;
}

export interface ReorderItem {
  id: number;
  order: number;
}

export interface MetaDto {
  title: string;
  description: string;
  keywords: string | null;
  canonicalUrl: string;
  ogImage: string | null;
  ogType: string | null;
}

export interface SitemapItem {
  url: string;
  lastModified: string;
  changeFrequency: string;
  priority: number;
}

export interface ContactMessage {
  name: string;
  email: string;
  phone: string;
  message: string;
}

export interface DashboardStats {
  projects: number;
  products: number;
  blogPosts: number;
  teamMembers: number;
  services: number;
  clients: number;
}
