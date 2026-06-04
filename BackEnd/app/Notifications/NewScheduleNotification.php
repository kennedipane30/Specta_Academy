<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class NewScheduleNotification extends Notification
{
    use Queueable;

    protected $messageData;

    // Tambahkan "= null" agar parameter ini opsional
    public function __construct($messageData = null)
    {
        $this->messageData = $messageData;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toArray($notifiable)
    {
        return [
            'title' => 'Jadwal Baru!',
            'message' => $this->messageData ?? 'Ada jadwal pelajaran baru untuk Anda.',
            'type' => 'schedule'
        ];
    }
}