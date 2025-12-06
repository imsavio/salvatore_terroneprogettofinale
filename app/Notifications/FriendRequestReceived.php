<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class FriendRequestReceived extends Notification
{
    use Queueable;

    public function __construct(public User $sender)
    {
    }

    public function via($notifiable): array
    {
        return ['database'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message' => "{$this->sender->name} ti ha inviato una richiesta di amicizia.",
            'type' => 'friend_request',
        ];
    }

    public function toArray($notifiable): array
    {
        return [
            'sender_id' => $this->sender->id,
            'sender_name' => $this->sender->name,
            'message' => "{$this->sender->name} ti ha inviato una richiesta di amicizia.",
            'type' => 'friend_request',
        ];
    }
}
