import * as React from 'react';
import { motion, AnimatePresence } from 'framer-motion';
import {
    Bell,
    X,
    Check,
    AlertTriangle,
    Info,
    CheckCircle,
    XCircle,
    Trash2,
    MarkAsRead,
    Clock,
} from 'lucide-react';
import {
    Button,
    Card,
    CardContent,
    CardHeader,
    CardTitle,
    Badge,
    Dialog,
    DialogContent,
    DialogHeader,
    DialogTitle,
    DialogTrigger,
    ScrollArea,
} from '@/components/ui';
import { useNotifications, Notification, NotificationType } from '@/stores/notificationStore';
import { cn } from '@/lib/cn';

// Notification type configurations
const notificationConfig = {
    success: {
        icon: CheckCircle,
        bgColor: 'bg-success/10',
        borderColor: 'border-success/20',
        iconColor: 'text-success',
        titleColor: 'text-success-foreground',
    },
    error: {
        icon: XCircle,
        bgColor: 'bg-destructive/10',
        borderColor: 'border-destructive/20',
        iconColor: 'text-destructive',
        titleColor: 'text-destructive-foreground',
    },
    warning: {
        icon: AlertTriangle,
        bgColor: 'bg-warning/10',
        borderColor: 'border-warning/20',
        iconColor: 'text-warning',
        titleColor: 'text-warning-foreground',
    },
    info: {
        icon: Info,
        bgColor: 'bg-blue-50',
        borderColor: 'border-blue-200',
        iconColor: 'text-blue-600',
        titleColor: 'text-blue-900',
    },
};

// Individual notification item component
interface NotificationItemProps {
    notification: Notification;
    onRemove: (id: string) => void;
    onMarkAsRead: (id: string) => void;
    showActions?: boolean;
    compact?: boolean;
}

const NotificationItem: React.FC<NotificationItemProps> = ({
                                                               notification,
                                                               onRemove,
                                                               onMarkAsRead,
                                                               showActions = true,
                                                               compact = false,
                                                           }) => {
    const config = notificationConfig[notification.type];
    const Icon = config.icon;

    const formatTime = (timestamp: number) => {
        const now = Date.now();
        const diff = now - timestamp;
        const minutes = Math.floor(diff / (1000 * 60));
        const hours = Math.floor(diff / (1000 * 60 * 60));
        const days = Math.floor(diff / (1000 * 60 * 60 * 24));

        if (minutes < 1) return 'Just now';
        if (minutes < 60) return `${minutes}m ago`;
        if (hours < 24) return `${hours}h ago`;
        return `${days}d ago`;
    };

    return (
        <motion.div
            layout
            initial={{ opacity: 0, y: -10, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, x: 100, scale: 0.95 }}
            transition={{ duration: 0.2 }}
            className={cn(
                'relative overflow-hidden rounded-lg border',
                config.bgColor,
                config.borderColor,
                !notification.isRead && 'ring-2 ring-primary/20'
            )}
        >
            <div className={cn('p-4', compact && 'p-3')}>
                <div className="flex items-start gap-3">
                    {/* Icon */}
                    <div className={cn(
                        'flex-shrink-0 mt-0.5',
                        compact ? 'w-4 h-4' : 'w-5 h-5'
                    )}>
                        <Icon className={cn('w-full h-full', config.iconColor)} />
                    </div>

                    {/* Content */}
                    <div className="flex-1 min-w-0">
                        <div className="flex items-start justify-between gap-2">
                            <div className="flex-1 min-w-0">
                                <h4 className={cn(
                                    'font-medium line-clamp-2',
                                    config.titleColor,
                                    compact ? 'text-sm' : 'text-base'
                                )}>
                                    {notification.title}
                                </h4>
                                {notification.message && (
                                    <p className={cn(
                                        'text-muted-foreground line-clamp-3 mt-1',
                                        compact ? 'text-xs' : 'text-sm'
                                    )}>
                                        {notification.message}
                                    </p>
                                )}
                            </div>

                            {/* Actions */}
                            {showActions && (
                                <div className="flex items-center gap-1 flex-shrink-0">
                                    {!notification.isRead && (
                                        <Button
                                            variant="ghost"
                                            size="icon"
                                            onClick={() => onMarkAsRead(notification.id)}
                                            className="h-8 w-8 hover:bg-white/50"
                                        >
                                            <Check className="h-4 w-4" />
                                        </Button>
                                    )}
                                    <Button
                                        variant="ghost"
                                        size="icon"
                                        onClick={() => onRemove(notification.id)}
                                        className="h-8 w-8 hover:bg-white/50"
                                    >
                                        <X className="h-4 w-4" />
                                    </Button>
                                </div>
                            )}
                        </div>

                        {/* Action Button */}
                        {notification.action && (
                            <div className="mt-3">
                                <Button
                                    variant="outline"
                                    size="sm"
                                    onClick={notification.action.onClick}
                                    className="text-xs"
                                >
                                    {notification.action.label}
                                </Button>
                            </div>
                        )}

                        {/* Timestamp */}
                        <div className="flex items-center gap-1 mt-2 text-xs text-muted-foreground">
                            <Clock className="h-3 w-3" />
                            <span>{formatTime(notification.timestamp)}</span>
                            {!notification.isRead && (
                                <Badge variant="secondary" className="text-xs ml-2">
                                    New
                                </Badge>
                            )}
                        </div>
                    </div>
                </div>
            </div>

            {/* Unread indicator */}
            {!notification.isRead && (
                <div className="absolute top-2 right-2 w-2 h-2 bg-primary rounded-full" />
            )}
        </motion.div>
    );
};

// Main notification panel component
interface NotificationPanelProps {
    className?: string;
    maxHeight?: string;
}

export const NotificationPanel: React.FC<NotificationPanelProps> = ({
                                                                        className,
                                                                        maxHeight = '600px',
                                                                    }) => {
    const {
        notifications,
        unreadCount,
        removeNotification,
        markAsRead,
        markAllAsRead,
        clearAll,
        clearByType,
    } = useNotifications();

    const [filterType, setFilterType] = React.useState<NotificationType | 'all'>('all');

    const filteredNotifications = notifications.filter(notification =>
        filterType === 'all' || notification.type === filterType
    );

    const typeOptions: { value: NotificationType | 'all'; label: string; count: number }[] = [
        { value: 'all', label: 'All', count: notifications.length },
        { value: 'success', label: 'Success', count: notifications.filter(n => n.type === 'success').length },
        { value: 'error', label: 'Errors', count: notifications.filter(n => n.type === 'error').length },
        { value: 'warning', label: 'Warnings', count: notifications.filter(n => n.type === 'warning').length },
        { value: 'info', label: 'Info', count: notifications.filter(n => n.type === 'info').length },
    ].filter(option => option.count > 0);

    return (
        <div className={cn('space-y-4', className)}>
            {/* Header */}
            <div className="flex items-center justify-between">
                <div className="flex items-center gap-2">
                    <h2 className="text-lg font-semibold text-foreground">
                        Notifications
                    </h2>
                    {unreadCount > 0 && (
                        <Badge className="bg-primary text-primary-foreground">
                            {unreadCount}
                        </Badge>
                    )}
                </div>

                <div className="flex items-center gap-2">
                    {unreadCount > 0 && (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={markAllAsRead}
                            className="text-xs"
                        >
                            <MarkAsRead className="mr-1 h-3 w-3" />
                            Mark All Read
                        </Button>
                    )}
                    {notifications.length > 0 && (
                        <Button
                            variant="outline"
                            size="sm"
                            onClick={clearAll}
                            className="text-xs"
                        >
                            <Trash2 className="mr-1 h-3 w-3" />
                            Clear All
                        </Button>
                    )}
                </div>
            </div>

            {/* Filter Tabs */}
            {typeOptions.length > 1 && (
                <div className="flex flex-wrap gap-2">
                    {typeOptions.map((option) => (
                        <Button
                            key={option.value}
                            variant={filterType === option.value ? 'default' : 'outline'}
                            size="sm"
                            onClick={() => setFilterType(option.value)}
                            className="text-xs"
                        >
                            {option.label}
                            {option.count > 0 && (
                                <Badge
                                    variant="secondary"
                                    className="ml-2 text-xs"
                                >
                                    {option.count}
                                </Badge>
                            )}
                        </Button>
                    ))}
                </div>
            )}

            {/* Notifications List */}
            <div style={{ maxHeight }} className="overflow-hidden">
                <ScrollArea className="h-full">
                    <div className="space-y-3 pr-4">
                        <AnimatePresence mode="popLayout">
                            {filteredNotifications.length > 0 ? (
                                filteredNotifications.map((notification) => (
                                    <NotificationItem
                                        key={notification.id}
                                        notification={notification}
                                        onRemove={removeNotification}
                                        onMarkAsRead={markAsRead}
                                        showActions={true}
                                    />
                                ))
                            ) : (
                                <motion.div
                                    initial={{ opacity: 0, y: 20 }}
                                    animate={{ opacity: 1, y: 0 }}
                                    className="text-center py-12"
                                >
                                    <Bell className="h-12 w-12 text-muted-foreground mx-auto mb-4" />
                                    <h3 className="font-medium text-foreground mb-2">
                                        No notifications
                                    </h3>
                                    <p className="text-muted-foreground text-sm">
                                        {filterType === 'all'
                                            ? "You're all caught up!"
                                            : `No ${filterType} notifications`
                                        }
                                    </p>
                                </motion.div>
                            )}
                        </AnimatePresence>
                    </div>
                </ScrollArea>
            </div>

            {/* Clear by type button */}
            {filterType !== 'all' && filteredNotifications.length > 0 && (
                <div className="pt-4 border-t">
                    <Button
                        variant="outline"
                        size="sm"
                        onClick={() => clearByType(filterType as NotificationType)}
                        className="w-full text-xs"
                    >
                        <Trash2 className="mr-2 h-3 w-3" />
                        Clear All {filterType.charAt(0).toUpperCase() + filterType.slice(1)} Notifications
                    </Button>
                </div>
            )}
        </div>
    );
};

// Notification bell trigger component
export const NotificationTrigger: React.FC<{ className?: string }> = ({ className }) => {
    const { unreadCount, isOpen, setIsOpen } = useNotifications();

    return (
        <Dialog open={isOpen} onOpenChange={setIsOpen}>
            <DialogTrigger asChild>
                <Button
                    variant="ghost"
                    size="icon"
                    className={cn('relative', className)}
                >
                    <Bell className="h-5 w-5" />
                    {unreadCount > 0 && (
                        <motion.div
                            initial={{ scale: 0 }}
                            animate={{ scale: 1 }}
                            className="absolute -top-1 -right-1 bg-primary text-primary-foreground text-xs rounded-full min-w-[18px] h-[18px] flex items-center justify-center"
                        >
                            {unreadCount > 99 ? '99+' : unreadCount}
                        </motion.div>
                    )}
                </Button>
            </DialogTrigger>
            <DialogContent className="max-w-md max-h-[80vh]">
                <DialogHeader>
                    <DialogTitle className="sr-only">Notifications</DialogTitle>
                </DialogHeader>
                <NotificationPanel />
            </DialogContent>
        </Dialog>
    );
};

// Toast notification component (for auto-dismissing notifications)
export const NotificationToast: React.FC<{
    notification: Notification;
    onRemove: (id: string) => void;
}> = ({ notification, onRemove }) => {
    React.useEffect(() => {
        if (notification.duration > 0) {
            const timer = setTimeout(() => {
                onRemove(notification.id);
            }, notification.duration);

            return () => clearTimeout(timer);
        }
    }, [notification.duration, notification.id, onRemove]);

    return (
        <motion.div
            layout
            initial={{ opacity: 0, y: 50, scale: 0.95 }}
            animate={{ opacity: 1, y: 0, scale: 1 }}
            exit={{ opacity: 0, y: 50, scale: 0.95 }}
            transition={{ duration: 0.2 }}
            className="pointer-events-auto"
        >
            <NotificationItem
                notification={notification}
                onRemove={onRemove}
                onMarkAsRead={() => {}}
                showActions={true}
                compact={true}
            />
        </motion.div>
    );
};

// Toast container for displaying auto-dismissing notifications
export const NotificationToastContainer: React.FC = () => {
    const { notifications, removeNotification } = useNotifications();

    // Only show recent notifications as toasts (last 5 minutes)
    const recentNotifications = notifications.filter(
        n => Date.now() - n.timestamp < 5 * 60 * 1000 && !n.persistent
    ).slice(0, 5);

    return (
        <div className="fixed bottom-4 right-4 z-50 space-y-2 max-w-md">
            <AnimatePresence mode="popLayout">
                {recentNotifications.map((notification) => (
                    <NotificationToast
                        key={notification.id}
                        notification={notification}
                        onRemove={removeNotification}
                    />
                ))}
            </AnimatePresence>
        </div>
    );
};

export default NotificationPanel;