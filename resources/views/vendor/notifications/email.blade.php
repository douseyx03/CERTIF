<div style="font-family: Arial, sans-serif; color: black; background-color: white; padding: 20px;">
    {{-- Greeting --}}
    @if (! empty($greeting))
    <h1>{{ $greeting }}</h1>
    @else
    @if ($level === 'error')
    <h1>Whoops!</h1>
    @else
    <h1>Bonjour!</h1>
    @endif
    @endif

    {{-- Intro Lines --}}
    @foreach ($introLines as $line)
    <p>{{ $line }}</p>
    @endforeach

    {{-- Action Button --}}
    @isset($actionText)
    <?php
        $color = match ($level) {
            'success', 'error' => $level,
            default => 'primary',
        };
    ?>
    <button style="background-color: {{ $color }}; color: white; padding: 10px; border: none; border-radius: 5px;">
        {{ $actionText }}
    </button>
    @endisset

    {{-- Outro Lines --}}
    @foreach ($outroLines as $line)
    <p>{{ $line }}</p>
    @endforeach

    {{-- Salutation --}}
    @if (! empty($salutation))
    <p>{{ $salutation }}</p>
    @else
    <p>Merci</p>
    @endif

    {{-- Subcopy --}}
    @isset($actionText)
    <p>
        Si vous rencontrez des difficultés pour cliquer sur le bouton "{{ $actionText }}", copiez et collez l'URL ci-dessous dans votre navigateur:
        <a href="{{ $actionUrl }}">{{ $displayableActionUrl }}</a>
    </p>
    @endisset
</div>