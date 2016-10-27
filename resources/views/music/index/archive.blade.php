<div class="intro">
    <div class="title">
        <div class="cate">
            <span>歌曲</span>
        </div>
        <h1 title="{{ $title }}">{{ $title }}</h1>
    </div> 
    @if(isset($data['artist']['counts']))<p>歌手：<span>{{ $data['artist']['counts']}} 名</span></p>@endif
    @if(isset($data['music']['counts']))<p>歌曲：<span>{{ $data['music']['counts'] }} 首</span></p>@endif
</div>

@if ( ! empty($data['artist']['list']))
<table class="table-box">
    @if ('artist' === $cate)
    @else
    <caption># Top {{ $limit }} 歌手 <a href="{{ URL('music') }}?cate=artist">(全部)</a></caption>
    @endif
    <tr class="title">
        <th>歌手</th>
        <th>播放次数</th>
    </tr>
    @foreach ($data['artist']['list'] as $artist)
    <tr class="row">
        <td><a href="{{ URL('music/artist/' . $artist->id) }}">{{ $artist->name }}</a></td>
        <td>{{ $artist->counts }}</td>
    </tr>
    @endforeach
</table>
@endif

@if ( ! empty($data['music']['list']))
<table class="table-box">
    @if ('music' === $cate)
    @else
    <caption># Top {{ $limit }} 歌曲 <a href="{{ URL('music') }}?cate=music">(全部)</a></caption>
    @endif
    <tr class="title">
        <th>歌曲</th>
        <th>歌手</th>
        <th>专辑</th>
        <th>播放次数</th>
    </tr>
    @foreach ($data['music']['list'] as $music)
    <tr class="row">
        <td><a href="{{ URL('music/' . $music->id) }}">{{ $music->title }}</a></td>
        <td>@foreach ($music->artists as $artist)<a href="{{ URL('music/artist/' . $artist->id) }}">{{ $artist->name }}</a> @endforeach</td>
        <td>{{ $music->album }}</td>
        <td>{{ $music->counts }}</td>
    </tr>
    @endforeach
</table>
@endif
