@extends('layouts.default')

@section('content')
<div class="archive">
    {!! $archive !!}
</div>

<div class="tuning">
    <i class="tuning-prev fa fa-angle-up" data-date=""></i>
    <i class="tuning-last fa fa-circle" data-date=""></i>
    <i class="tuning-next fa fa-angle-down" data-date=""></i>
</div>

<script src="/static/??module/jquery/jquery.scrollspy.js,js/index.js?v={{ env('STATIC_FILE_VERSION') }}"></script>
@endsection
