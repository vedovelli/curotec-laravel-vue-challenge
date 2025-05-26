import type { PageProps } from '@inertiajs/core';
import type { LucideIcon } from 'lucide-vue-next';
import type { Config } from 'ziggy-js';

export interface Auth {
  user: User;
}

export interface BreadcrumbItem {
  title: string;
  href: string;
}

export interface NavItem {
  title: string;
  href: string;
  icon?: LucideIcon;
  isActive?: boolean;
}

export interface SharedData extends PageProps {
  name: string;
  quote: { message: string; author: string };
  auth: Auth;
  ziggy: Config & { location: string };
  sidebarOpen: boolean;
}

export interface User {
  id: number;
  name: string;
  email: string;
  avatar?: string;
  email_verified_at: string | null;
  created_at: string;
  updated_at: string;
}

export interface Task {
  id: number;
  title: string;
  description: string | null;
  status: 'pending' | 'completed';
  due_date: string | null;
  created_at: string;
  updated_at: string;
  // Computed properties from Laravel model
  status_text: string;
  is_overdue: boolean;
  is_completed: boolean;
  days_until_due: number | null;
  formatted_due_date: string | null;
  priority_level: string;
}

export type TaskStatus = 'all' | 'pending' | 'completed';

export type BreadcrumbItemType = BreadcrumbItem;
