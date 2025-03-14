import { IsString, IsNumber, IsOptional, IsBoolean, Min, Max } from 'class-validator';

export class CreateModeleDto {
  @IsString()
  name: string;

  @IsString()
  alias: string;

  @IsString()
  marqueId: string;

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

export class UpdateModeleDto {
  @IsOptional()
  @IsString()
  name?: string;

  @IsOptional()
  @IsString()
  alias?: string;

  @IsOptional()
  @IsString()
  marqueId?: string;

  @IsOptional()
  @IsNumber()
  @Min(1900)
  @Max(2100)
  yearFrom?: number;

  @IsOptional()
  @IsNumber()
  @Min(1900)
  @Max(2100)
  yearTo?: number;

  @IsOptional()
  @IsBoolean()
  display?: boolean;

  @IsOptional()
  @IsNumber()
  sort?: number;
}
