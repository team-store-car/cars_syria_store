@component('mail::message')

<h2 class="welcome-title">
@if (! empty($greeting))
{{ $greeting }}
@else
@if ($level === 'error')
@lang('عذراً! حدث خطأ ما')
@else
@lang('مرحباً بك في سوق السيارات السورية')
@endif
@endif
</h2>

<div class="welcome-subtitle">
@foreach ($introLines as $line)
{{ $line }}
@endforeach
</div>

@isset($actionText)
<?php
    $color = match ($level) {
        'success', 'error' => $level,
        default => 'primary',
    };
?>
<div style="text-align: center;">
    <a href="{{ $actionUrl }}" class="verify-button">
    {{ $actionText }}
    </a>
</div>
@endisset

<div class="additional-info">
@foreach ($outroLines as $line)
{{ $line }}
@endforeach

@if (! empty($salutation))
{{ $salutation }}
@else
<div class="signature">
    <p><strong>@lang('مع أطيب التحيات')</strong></p>
    <p>{{ __('فريق سوق السيارات السورية') }}</p>
</div>
@endif

@isset($actionText)
@slot('subcopy')
<div class="footer-note">
    <p>
    @lang(
        "إذا كنت تواجه مشكلة في النقر على زر \":actionText\"، يمكنك نسخ الرابط التالي ولصقه في متصفحك",
        [
            'actionText' => $actionText,
        ]
    )
    </p>
    <div class="url-container">
    {{ $displayableActionUrl }}
    </div>
</div>
@endslot
@endisset
</div>

@endcomponent
