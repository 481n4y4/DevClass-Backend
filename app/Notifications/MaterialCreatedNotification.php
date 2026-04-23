<?php

namespace App\Notifications;

use App\Models\Material;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MaterialCreatedNotification extends Notification
{
    use Queueable;

    public function __construct(private readonly Material $material) {}

    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $deadline = $this->material->deadline
            ? $this->material->deadline->format('Y-m-d H:i')
            : 'Tidak ada';

        return (new MailMessage)
            ->subject('Materi baru: ' . $this->material->title)
            ->greeting('Halo, ' . ($notifiable->name ?? 'Siswa'))
            ->line('Materi baru telah ditambahkan.')
            ->line('Judul: ' . $this->material->title)
            ->line('Deadline: ' . $deadline)
            ->line('Silakan login ke aplikasi untuk melihat detail materi.');
    }
}
