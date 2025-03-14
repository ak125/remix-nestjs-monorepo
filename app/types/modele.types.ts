export interface Marque {
  id: string;
  name: string;
  nameMeta?: string;
  alias?: string;
  logo?: string;
  display: boolean;
  sort: number;
  top?: boolean;
}

export interface Modele {
  id: string;
  name: string;
  alias: string;
  marqueId: string;
  yearFrom: number;
  yearTo: number | null;
  display: boolean;
  sort: number;
  createdAt: string;
  updatedAt: string;
  marque?: Marque;
}

export interface Type {
  id: string;
  modeleId: string;
  name: string;
  fuel?: string;
  powerPS?: number;
  display: boolean;
}

export interface ModeleWithTypes extends Modele {
  types: Type[];
  typesCount: number;
}

export interface YearOption {
  value: number;
  label: string;
  favorite: boolean;
}

export interface PaginatedResponse<T> {
  data: T[];
  pagination: {
    total: number;
    page: number;
    limit: number;
    totalPages: number;
  };
}

export interface ModeleFilters {
  search?: string;
  marqueId?: string;
  yearFrom?: string;
  yearTo?: string;
  page?: string;
  limit?: string;
}

export interface CreateModeleData {
  name: string;
  alias: string;
  marqueId: string;
  yearFrom: number;
  yearTo?: number | null;
  sort?: number;
  display?: boolean;
}

export interface UpdateModeleData extends Partial<CreateModeleData> {}
