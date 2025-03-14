import { 
  Controller, 
  Get, 
  Patch, 
  Param,
  Query,
  UseGuards,
  Session,
} from '@nestjs/common';
import { AdminNotificationsService } from '../services/notifications.service';
import { IsAdminGuard } from '../guards/is-admin.guard';

@Controller('admin/notifications')
@UseGuards(IsAdminGuard)
export class AdminNotificationsController {
  constructor(private readonly notificationsService: AdminNotificationsService) {}

  @Get()
  getNotifications(
    @Query('isRead') isRead?: string,
    @Query('type') type?: string,
    @Query('page') page?: string,
    @Query('limit') limit?: string,
  ) {
    return this.notificationsService.getNotifications({
      isRead: isRead === 'true' ? true : isRead === 'false' ? false : undefined,
      type,
      page: page ? parseInt(page, 10) : undefined,
      limit: limit ? parseInt(limit, 10) : undefined,
    });
  }

  @Patch(':id/read')
  markAsRead(
    @Param('id') id: string,
    @Session() session: Record<string, any>,
  ) {
    return this.notificationsService.markAsRead(id, session.user.id);
  }

  @Patch('read-all')
  markAllAsRead(@Session() session: Record<string, any>) {
    return this.notificationsService.markAllAsRead(session.user.id);
  }

  @Get('unread-count')
  async getUnreadCount() {
    const count = await this.notificationsService.getUnreadCount();
    return { count };
  }
}
