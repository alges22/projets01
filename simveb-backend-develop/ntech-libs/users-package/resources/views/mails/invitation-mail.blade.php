@component('mail::message')
    <h2>Bonjour,</h2>
    <p>
        Vous avez reçu une invitation pour vous inscrire sur la plateforme. Veuillez cliquer sur le bouton ci-dessous
        pour procéder à l'inscription.
        S'il ne s'agit pas de vous, alors ne faites rien.
    </p>
    <p>
        @component('mail::button', ['url' => $url])
            S'inscrire
        @endcomponent
    </p>
    @component('mail::panel')
        Vous n'arrivez pas à cliquer sur le bouron? Veuillez copier et coller le lien
        ci-dessous dans votre navigateur.
        <br>
        <a href="{{$url}}">{{$url}}</a>
    @endcomponent

    Cordialement,<br>
    {{ config('app.name') }}<br>
@endcomponent
