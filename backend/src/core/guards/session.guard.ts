import { Injectable, CanActivate, ExecutionContext } from '@nestjs/common';
import { Request } from 'express';

@Injectable()
export class SessionGuard implements CanActivate {
  canActivate(context: ExecutionContext): boolean {
    const request = context.switchToHttp().getRequest<Request>();
    
    if (!request.session || !request.session['user']) {
      return false;
    }

    const isValidSession = request.session['user'].valide === true;
    return isValidSession;
  }
}
