@component('mail::message')
    <h2>Bonjour,</h2>
    <p>
        Votre compte a été créé avec succès. Veuillez cliquer sur le bouton ci-dessous
        pour vous connecter à votre espace avec les informations suivante.
        <p>Email : {{$data['email']}}</p>
        <p>Mot de passe : {{$data['password']}}</p>
        S'il ne s'agit pas de vous, alors ne faites rien.
    </p>
    @if(!$isAgent)
        <p>
            @component('mail::button', ['url' => $url])
                Se connecter
            @endcomponent
        </p>
        @component('mail::panel')
            Vous n'arrivez pas à cliquer sur le bouron? Veuillez copier et coller le lien
            ci-dessous dans votre navigateur.
            <br>
            <a href="{{$url}}">{{$url}}</a>
        @endcomponent
    @endif


    Cordialement,<br>
    {{ config('app.name') }}<br>
@endcomponent
