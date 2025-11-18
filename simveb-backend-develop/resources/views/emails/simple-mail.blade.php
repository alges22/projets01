
@component('mail::message')
    <h2>Bonjour,</h2>
    <p>
       Lorem ipsum dolor sit amet, consectetur adipisicing elit. Aliquam esse magnam veniam. Ad consequatur cupiditate distinctio est eveniet libero provident quas quidem, soluta, tempora voluptas voluptatem? Commodi dolor impedit quis.
    </p>
    <p>
        @component('mail::button', ['url' => "#"])
            Lorem
        @endcomponent
    </p>

    @component('mail::panel')
        Vous n'arrivez pas à cliquer sur le bouron? Veuillez copier et coller le lien
        ci-dessous dans votre navigateur.
        <br>
        <a href="#"></a>
    @endcomponent

    @component('mail::subcopy')
        <p>Cordialement,</p>
        <p>{{ config('app.name') }}</p>
    @endcomponent

@endcomponent

