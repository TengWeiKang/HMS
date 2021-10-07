@component('mail::message')
Dear {{ $housekeeper->username }},

You have been assign to housekeep the Room <a href="{{ route("login", ["redirect" => route("dashboard.room.view", ["room" => $room])]) }}">{{ $room->room_id }}</a>.

@component('mail::button', ["url" => route("login", ["redirect" => route("dashboard.room.update-available", ["room" => $room])])])
Done Cleaning
@endcomponent


@component('mail::button', ["url" => route("login", ["redirect" => route("dashboard.room.update-cleaning", ["room" => $room])])])
Update to Repairing
@endcomponent

Please update the status after cleaning the room.
In order to provide any note to the room, please access to the room <a href="{{ route("login", ["redirect" => route("dashboard.room.view", ["room" => $room])]) }}">{{ $room->room_id }}</a> page to update the status

Thanks & Regards,<br>
{{ config('app.name') }}
@endcomponent
