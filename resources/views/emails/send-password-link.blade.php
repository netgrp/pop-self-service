@component('mail::message')
PREHADER: Brug dette link til at nulstille dit kordeord. Linket er kun gyldigt i 24 timer.

# Hej {{ $user['name'] }}

Du har for nyligt anmodet om at nulstille dit kordeord til din K-Net bruger hos {{config('app.name')}}. Brug knappen nedenfor til at nulstille dit det. **Denne mail er kun gyldig i 24 timer.**

@component('mail::button', ['url' => $pass])
Nulstil dit kordord
@endcomponent

Denne anmodning blev modtaget fra en {{ $platform }} enhed med browseren {{ $browser }}. Hvis du ikke har lavet denne anmodning, så kan du blot se bort fra denne mail, eller [kontakte netgruppen]({{ env('SUPPORT_URL') }}), hvis du har sprøgsmål.

Med venlig hilsen,<br>
Netgruppen på {{ config('app.name') }}

@component('mail::subcopy')
Hvis du har problemer med knappen ovenfor så kan du kopiere og indsætte webadressen nedenfor i din browser:

{{$pass}}
@endcomponent

@endcomponent