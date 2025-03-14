import { Controller, Sse, UseGuards } from '@nestjs/common';
import { Observable, interval, map, mergeMap } from 'rxjs';
import { AdminNotificationsService } from '../services/notifications.service';
import { IsAdminGuard } from '../guards/is-admin.guard';

@Controller('admin/notifications')
@UseGuards(IsAdminGuard)
export class AdminNotificationsStreamController {
  constructor(
    private readonly notificationsService: AdminNotificationsService,
  ) {}

  @Sse('stream')
  streamNotifications(): Observable<MessageEvent> {
    return interval(3000).pipe(
      mergeMap(() => this.notificationsService.getLatestNotifications()),
      map(notifications => ({
        data: notifications,
        id: Date.now().toString(),
        type: 'notification',
      } as MessageEvent)),
    );
  }
}
