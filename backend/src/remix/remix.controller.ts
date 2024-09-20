import { getServerBuild } from '@fafa/frontend';
import { All, Controller, Next, Req, Res } from '@nestjs/common';
import { createRequestHandler } from '@remix-run/express';
import { NextFunction, Request, Response } from 'express';
import { RemixService } from './remix.service';

@Controller()
export class RemixController {
  constructor(private remixService: RemixService) {}

  @All('*')
  async handler(
    @Req() request: Request,
    @Res() response: Response,
    @Next() next: NextFunction,
  ) {
    // Ensure getServerBuild is properly awaited and its return type is handled
    const build = await getServerBuild() as any; // Cast to 'any' or the appropriate type

    return createRequestHandler({
      build: build, // Make sure build is correctly assigned
      getLoadContext: () => ({
        user: request.user,       // Ensure there's no duplicate
        remixService: this.remixService, // Ensure there's no duplicate
      }),
    })(request, response, next);
  }
}
