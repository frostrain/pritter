@extends('layouts.app')

@section('content')
  <div class="col-md-12">
    <div class="panel panel-default">
      <!-- Default panel contents -->
      <div class="panel-heading">推特</div>

      <!-- List group -->
      <ul class="list-group">
        @foreach ($lists as $tweet)
          <li class="list-group-item">
            <div class="media">
              @if (isset($tweet->retweeted_status))
                <div>
                  {{ $tweet->user->name }} 转推了
                </div>
                <div class="media-left media-middle">
                  <a href="#">
                    <img class="media-object"  alt="...">
                  </a>
                </div>
                <div class="media-body">
                  <h4 class="media-heading">
                    {{ $loop->index }} :
                    {{ $tweet->retweeted_status->user->name }} {{ '@'.$tweet->retweeted_status->user->screen_name }} - {{ $tweet->retweeted_status->created_at }}
                  </h4>
                  {{ $tweet->retweeted_status->text }}
                </div>
              @else
                <div class="media-left media-middle">
                  <a href="#">
                    <img class="media-object"  alt="...">
                  </a>
                </div>
                <div class="media-body">
                  <h4 class="media-heading">
                    {{ $loop->index }} :
                    {{ $tweet->user->name }} {{ '@'.$tweet->user->screen_name }} - {{ $tweet->created_at }}
                  </h4>
                  {{ $tweet->text }}
                </div>
              @endif
            </div>
            </li>
        @endforeach
      </ul>
    </div>
  </div>
@endsection
