<?php

namespace App\Notifications;

use App\Models\LeaveRequest;
use Illuminate\Notifications\Notification;

class LeaveNotification extends Notification
{
    public function __construct(
        public readonly LeaveRequest $leaveRequest,
        public readonly string $title,
        public readonly string $message,
        public readonly string $status,
    ) {}

    /**
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * @return array<string, mixed>
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * @return array<string, mixed>
     */
    private function payload(): array
    {
        return [
            'title' => $this->title,
            'message' => $this->message,
            'leave_request_id' => $this->leaveRequest->getKey(),
            'status' => $this->status,
        ];
    }
}
