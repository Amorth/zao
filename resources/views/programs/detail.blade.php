@extends('layouts.default')

@section('content')
<article itemtype="http://schema.org/Article" data-date="{{ $program->dates->id }}">
    <h1 class="post-title" original-title="@if ($contributers['topic'])by <a @if ($contributers['topic']['url']) href='{{ $contributers['topic']['url'] }}' target='_blank' @else href='#' @endif>{{ $contributers['topic']['name'] }}</a>@else @if (empty($program->topic)) 🐶 话题 🐶 @endif @endif">@if ($program->topic) {{ $program->topic }} @else 空 @endif @if ($program->dates->id == $appdate)（APP同期节目）@endif</h1>
    <ul class="post-meta" original-title="@if ($contributers['participants'])by <a @if ($contributers['participants']['url']) href='{{ $contributers['participants']['url'] }}' target='_blank' @else href='#' @endif>{{ $contributers['participants']['name'] }}</a>@else @if (empty($program->participants->toArray())) 🐰参与人|参与人 🐰 @endif @endif">
        <li>{{ str_replace('-', '.', $program->date) }}</li>
        <li>周{{ $program->dates->dayNum}}</li>
        @if ( ! $program->participants->isEmpty())
        <li>
            @foreach ($program->participants as $participant)
            <a>{{ $participant->name }}</a>
            @endforeach
        </li>
        @endif
        <li>
            <span id="post-view-counts">
                <i class="fa fa-spinner fa-spin"></i>
            </span>
            次收听
        </li>
    </ul>
    <div class="post-content">
        @if (Agent::isAndroidOS() or Agent::isIOS())
        @foreach ($audios as $audio)
        <p>{{ $audio->title }}</p>
        <audio controls="controls" preload="none">
            <source src="{{ $audio->url }}" type="audio/mpeg"/>
            Your browser does not support the audio element.
        </audio>
        @endforeach
        @else
        @foreach ($audios as $audio)
        <p>{{ $audio->title }}</p>
        <video width="85%" height="30" controls="controls" preload="none">
            <source src="{{ $audio->url }}" />
        </video>
        @endforeach
        @endif
    </div>
    <span class="post-contributers">
        @if ( ! empty($contributers['topic']) or ! empty($contributers['participants']))
            @if ( ! empty($contributers['topic']))
                ( 话题 by <a href="{{ $contributers['topic']['url'] }}" target="_blank">{{ $contributers['topic']['name'] }}</a> )
            @endif
            @if ( ! empty($contributers['participants']))
                ( 参与人 by <a href="{{ $contributers['participants']['url'] }}" target="_blank">{{ $contributers['participants']['name'] }}</a> )
            @endif
        @else
            @if (empty($program->topic) or $program->participants->isEmpty())
                ( 了解参与贡献内容的<a href="http://zaoaoaoaoao.com/about#contribute">方式</a> )
            @endif
        @endif
    </span>
    <ul class="post-near">
        @if ($pages->prev)
        <li class="prev">前一天: <a href="/programs/{{ $pages->prev->dates->id }}">@if ($pages->prev->topic) {{ $pages->prev->topic }} @else 空 @endif</a></li>
        @endif
        @if ($pages->next)
        <li class="next">后一天: <a href="/programs/{{ $pages->next->dates->id }}">@if ($pages->next->topic) {{ $pages->next->topic }} @else 空 @endif</a></li>
        @endif
    </ul>

    <link rel="stylesheet" href="/static/??css/player.css,css/duoshuo.css?v={{ env('STATIC_FILE_VERSION') }}">
    <script src="/static/??module/mediaelement/mediaelement-and-player.min.js,js/detail.js?v={{ env('STATIC_FILE_VERSION') }}"></script>
</article>

@include('layouts.duoshuo')

@endsection
