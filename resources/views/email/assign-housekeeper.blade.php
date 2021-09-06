@component('mail::message')
Dear {{ $housekeeper->username }},

You have been assign to housekeep the Room <a href="{{ route("dashboard.room.view", ["room" => $room]) }}">{{ $room->room_id }}</a>.

@component('mail::button', ["url" => route("login", ["redirect" => route("dashboard.room.view", ["room" => $room])])])
Update Clean
@endcomponent

Thanks & Regards,<br>
{{ config('app.name') }}
@endcomponent
