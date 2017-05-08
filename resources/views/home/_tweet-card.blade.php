{{-- 显示一条推特的内容 --}}

<div class="media-left">
  <a href="#">
    <img class="media-object" src="{{ $tweet->user->profile_image_url }}" width="50" />
  </a>
</div>
<div class="media-body">
  <h4 class="media-heading">
    {{ $tweet->user->name }} {{ '@'.$tweet->user->screen_name }} - {{ $tweet->created_at }}
  </h4>
  @if ($tweet->isReply())
    <div>回复 {{ '@'.$tweet->in_reply_to_screen_name }}</div>
  @endif
  {{ $tweet->text }}
  @if ($tweet->media->count())
    <div class="row">
    @foreach ($tweet->media as $item)
      <div class="col-md-3">
        <img src="{{ $item->url }}" style="max-width: 100%;" />
      </div>
    @endforeach
    </div>
  @endif
  @if ($tweet->quoted_status)
    <blockquote>
      <div class="media-left media-middle">
        <a href="#">
          <img class="media-object"
               src="{{ $tweet->quoted_status->user->profile_image_url }}" />
        </a>
      </div>
      <div class="media-body">
        <h4 class="media-heading">
          {{ $tweet->quoted_status->user->name }} {{ '@'.$tweet->quoted_status->user->screen_name }}
        </h4>
        <div>
          {{ $tweet->quoted_status->text }}
        </div>
      </div>
    </blockquote>
  @endif
</div>
