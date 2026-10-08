<?php

namespace App\Notifications;

use App\Filament\Resources\MaintenanceLogResource;
use App\Models\MaintenanceLog;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaintenanceAssigned extends Notification
{
    use Queueable;

    public function __construct(public MaintenanceLog $maintenanceLog)
    {
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        $this->maintenanceLog->loadMissing('asset');

        return [
            'title' => 'Maintenance task assigned',
            'body' => "{$this->maintenanceLog->asset?->asset_tag}: {$this->maintenanceLog->symptom}",
            'status' => $this->maintenanceLog->status,
            'maintenance_log_id' => $this->maintenanceLog->getKey(),
            'url' => MaintenanceLogResource::getUrl('edit', [
                'record' => $this->maintenanceLog,
            ]),
        ];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('Maintenance task assigned')
            ->line($this->maintenanceLog->symptom)
            ->action('Open maintenance task', MaintenanceLogResource::getUrl('edit', [
                'record' => $this->maintenanceLog,
            ]));
    }
}
