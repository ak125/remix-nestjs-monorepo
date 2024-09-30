import {
  Controller,
  Get,
  Next,
  Post,
  Query,
  Redirect,
  Req,
  Res,
  UseGuards,
} from '@nestjs/common';
import { NextFunction, Response } from 'express';
import { LocalAuthGuard } from './local-auth.guard';

@Controller('api') // You can change this base path if needed
export class AuthController {
  @UseGuards(LocalAuthGuard)
  @Post('/login') // Define the route for login
  login(@Req() request: Express.Request) {
    // You can add additional logic here if needed
    return { message: 'Login successful', user: request.user };
  }

  @Get('/authenticate')
  @Redirect('/')
  authenticate(@Query('redirectTo') redirectTo: string) {
    return {
      url: redirectTo || '/',
    };
  }

  @Post('/logout') // Define the route for logout
  async logout(
    @Req() request: Express.Request,
    @Res() response: Response,
    @Next() next: NextFunction,
  ) {
    request.logOut(function (err) {
      if (err) {
        return next(err);
      }
      request.session.destroy(() => {
        response.clearCookie('connect.sid'); // The name of the session cookie
        response.redirect('/'); // Redirect to homepage after logout
      });
    });
  }
}
