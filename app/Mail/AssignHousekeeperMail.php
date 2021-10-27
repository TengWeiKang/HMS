<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use App\Models\Room;
use App\Models\Employee;

class AssignHousekeeperMail extends Mailable
{
    use Queueable, SerializesModels;

    private Room $room;
    private Employee $housekeeper;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($housekeeper, $room)
    {
        $this->room = $room;
        $this->housekeeper = $housekeeper;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject("Houskeeper Assigned to Room")
            ->markdown('email.assign-housekeeper', ["housekeeper" => $this->housekeeper, "room" => $this->room]);
    }
}
