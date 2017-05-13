{{-- 显示一条推特的内容 --}}

<div class="media-left">
    <img class="media-object" src="{{ $tweet->user->profile_image_url }}" width="48" />
</div>
<div class="media-body">
  <h4 class="media-heading">
    {{ $tweet->user->name }} {{ '@'.$tweet->user->screen_name }} - {{ $tweet->created_at }}
  </h4>
  @if ($tweet->isReply())
    <div>回复 {{ '@'.$tweet->in_reply_to_screen_name }}</div>
  @endif
  {!! $tweet->text !!}
  @if ($tweet->media->count())
    <div class="row">
      <div class="col-md-12">
        @foreach ($tweet->media as $item)
          <a href="{{ $item->url }}" data-fancybox="group-{{ $tweet->id }}">
            <img src="{{ $item->url }}" style="max-width:100%;height:200px;" />
          </a>
        @endforeach
      </div>
    </div>
  @endif

  @if ($tweet->hasQuote())
    <blockquote>
      <div class="media-left media-middle">
          <img class="media-object" width="48"
               src="{{ $tweet->quoted_status->user->profile_image_url }}" />
      </div>
      <div class="media-body">
        <h4 class="media-heading">
          {{ $tweet->quoted_status->user->name }} {{ '@'.$tweet->quoted_status->user->screen_name }}
        </h4>
        <div>
          {!! $tweet->quoted_status->text !!}
        </div>
      </div>
    </blockquote>
  @endif
</div>
