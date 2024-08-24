import { Injectable } from '@nestjs/common';

@Injectable()
export class RemixService {
  public readonly getHello = (): string => {
    return 'Hello World!';
  };
  public readonly getHello2 = (): string => {
    return 'Hello World!';
  };
}
