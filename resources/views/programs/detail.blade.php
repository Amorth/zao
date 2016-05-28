@extends('layouts.default')

@section('content')
<article itemtype="http://schema.org/Article" data-date="{{ $program->dates->id }}">
    <h1 class="post-title" original-title="@if ($contributers['topic'])by <a @if ($contributers['topic']['url']) href='{{ $contributers['topic']['url'] }}' rel='nofollow' target='_blank' @else href='#' @endif>{{ $contributers['topic']['name'] }}</a>@else @if (empty($program->topic)) 🐶 话题 🐶 @endif @endif">@if ($program->topic) {{ $program->topic }} @else 空 @endif @if ($program->dates->id == $appdate)（APP同期节目）@endif</h1>
    <ul class="post-meta" original-title="@if ($contributers['participants'])by <a @if ($contributers['participants']['url']) href='{{ $contributers['participants']['url'] }}' rel='nofollow' target='_blank' @else href='#' @endif>{{ $contributers['participants']['name'] }}</a>@else @if (empty($program->participants->toArray())) 🐰参与人|参与人 🐰 @endif @endif">
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
        @foreach ($audios as $audio)
        <p>
            {{ $audio->title }}
            @if ($audio->download_url)
            <a href="{{ $audio->download_url }}" rel="nofollow" target="_blank">（下载）</a>
            @endif
        </p>
        @if (Agent::isAndroidOS() or Agent::isIOS())
        <audio controls="controls" preload="none">
            <source src="{{ $audio->url }}" type="audio/mpeg"/>
            Your browser does not support the audio element.
        </audio>
        @else
        <video width="85%" height="30" controls="controls" preload="none">
            <source src="{{ $audio->url }}" />
        </video>
        @endif
        @endforeach
    </div>
    <span class="post-contributers">
        @if ( ! empty($contributers['topic']) or ! empty($contributers['participants']))
            @if ( ! empty($contributers['topic']))
                ( 话题 by <a href="{{ $contributers['topic']['url'] }}" rel="nofollow" target="_blank">{{ $contributers['topic']['name'] }}</a> )
            @endif
            @if ( ! empty($contributers['participants']))
                ( 参与人 by <a href="{{ $contributers['participants']['url'] }}" rel="nofollow" target="_blank">{{ $contributers['participants']['name'] }}</a> )
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
</article>

<div class="ds-thread" data-thread-key="{{ $program->dates->id }}" data-title="{{ $program->date }} - {{ $program->topic }}" data-url="{{ Config::get('app.url') }}/programs/{{ $program->dates->id }}"></div>

<link rel="stylesheet" href="/static/??css/player.css,css/duoshuo.css?v={{ env('STATIC_FILE_VERSION') }}">
<script src="/static/??module/mediaelement/mediaelement-and-player.min.js,js/duoshuo.js,js/detail.js?v={{ env('STATIC_FILE_VERSION') }}"></script>

@endsection
