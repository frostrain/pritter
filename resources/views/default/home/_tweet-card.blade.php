{{-- 显示一条推特的内容 --}}

<div class="media-left">
  <img class="media-object avatar"
       src="{{ $tweet->user->profile_image_url }}" />
</div>
<div class="media-body">
  <div class="media-heading">
    <span class="username">{{ $tweet->user->name }}</span>
    <span class="screen-name">{{ '@'.$tweet->user->screen_name }}</span>
    <span class="time">{{ $tweet->created_at }}</span>
  </div>
  @if ($tweet->isReply())
    <div>回复 {{ '@'.$tweet->in_reply_to_screen_name }}</div>
  @endif
  <div class="content">
    {!! $tweet->text !!}
  </div>
  @if ($tweet->media->count())
    <div class="row">
      <div class="col-md-12">
        @foreach ($tweet->media as $item)
          <a href="{{ $item->url }}" data-fancybox="group-{{ $tweet->id }}">
            <img class="photo" src="{{ $item->url }}" />
          </a>
        @endforeach
      </div>
    </div>
  @endif

  {{-- 引用了其他推文 --}}
  @if ($tweet->hasQuote())
    <div class="panel panel-default mt20">
      <div class="panel-body" id="{{ $tweet->quoted_status->id }}">
        <div class="media-left">
          <img class="media-object avatar"
               src="{{ $tweet->quoted_status->user->profile_image_url }}" />
        </div>
        <div class="media-body">
          <div class="media-heading">
            <span class="username">{{ $tweet->quoted_status->user->name }}</span>
            <span class="screen-name">
              {{ '@'.$tweet->quoted_status->user->screen_name }}
            </span>
          </div>
          <div class="content">
            {!! $tweet->quoted_status->text !!}
          </div>
          @if ($tweet->quoted_status->media->count())
            <div class="row">
              <div class="col-md-12">
                @foreach ($tweet->quoted_status->media as $item)
                  <a href="{{ $item->url }}" data-fancybox="group-{{ $tweet->id }}">
                    <img class="photo" src="{{ $item->url }}" />
                  </a>
                @endforeach
              </div>
            </div>
          @endif

        </div>
      </div>
    </div>
  @endif
</div>
