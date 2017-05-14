@extends('layouts.app')

@section('content')
  <div class="col-md-12">
    <div class="panel panel-default">
      <div class="panel-heading">推文</div>

      <div class="tac">
        {{ $lists->render() }}
      </div>

      <ul class="list-group">
        @foreach ($lists as $tweet)
          <li class="list-group-item">
            <div class="media">
              @if ($tweet->isRetweet())
                {{-- 转推 --}}
                <div>
                  {{ $tweet->user->name }} {{ $tweet->created_at }} 转推了
                </div>
                @include('home._tweet-card', ['tweet' => $tweet->retweeted_status])
              @else
                {{-- 不是转推 --}}
                @include('home._tweet-card', ['tweet' => $tweet])
              @endif
            </div>
            </li>
        @endforeach
      </ul>

      <div class="tac">
        {{ $lists->render() }}
      </div>

    </div>
  </div>
@endsection
