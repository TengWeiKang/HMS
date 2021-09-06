@component('mail::message')
Dear {{ $housekeeper->username }},

You have been assign to housekeep the Room <a href="{{ route("login", ["redirect" => route("dashboard.room.view", ["room" => $room])]) }}">{{ $room->room_id }}</a>.

@component('mail::button', ["url" => route("login", ["redirect" => route("dashboard.room.view", ["room" => $room])])])
Update Clean
@endcomponent

Please update the status after cleaning the room.

Thanks & Regards,<br>
{{ config('app.name') }}
@endcomponent
