<?php

namespace App\Notifications;

use App\Models\Employee;
use App\Models\Room;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\NexmoMessage;
use Illuminate\Notifications\Notification;

class HousekeeperSmsNotification extends Notification
{
    use Queueable;
    private Employee $housekeeper;
    private Room $room;
    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($housekeeper, $room)
    {
        $this->housekeeper = $housekeeper;
        $this->room = $room;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['nexmo'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\NexmoMessage
     */
    public function toNexmo($notifiable)
    {
        return (new NexmoMessage)
                    ->from(env('NEXMO_FROM', '80000'))
                    ->content('Dear ' . $this->housekeeper->username . ',
You have been assign to housekeep for Room ' . $this->room->room_id . " " . $this->room->name ."
Click on url below to update the room status
" . route("login", ["redirect" => route("dashboard.room.view", ["room" => $this->room])]))
                    ->unicode();
    }

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }
}
