import { IsString, IsNumber, IsOptional, IsBoolean, Min, Max } from 'class-validator';

export class CreateModelDto {
  @IsString()
  name: string;

  @IsString()
  alias: string;

  @IsString()
  carMarqueId: string;

  @IsString()
  @IsOptional()
  gammeId?: string;

  @IsNumber()
  @Min(1900)
  @Max(2100)
  yearFrom: number;

  @IsOptional()
  @IsNumber()
  @Min(1900)
  @Max(2100)
  yearTo?: number;

  @IsOptional()
  @IsNumber()
  sort?: number;
}

export class UpdateModelDto {
  @IsString()
  @IsOptional()
  name?: string;

  @IsString()
  @IsOptional()
  alias?: string;

  @IsString()
  @IsOptional()
  carMarqueId?: string;

  @IsString()
  @IsOptional()
  gammeId?: string;

  @IsNumber()
  @IsOptional()
  @Min(1900)
  @Max(2100)
  yearFrom?: number;

  @IsNumber()
  @IsOptional()
  @Min(1900)
  @Max(2100)
  yearTo?: number;

  @IsBoolean()
  @IsOptional()
  display?: boolean;

  @IsNumber()
  @IsOptional()
  sort?: number;
}
