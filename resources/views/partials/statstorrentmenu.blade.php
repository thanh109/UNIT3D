<div class="block pv-5">
    <div class="btn-group text-center">
        <a href="{{ route('seeded') }}" class="btn btn-sm btn-primary"><i
                    class="{{ config('other.font-awesome') }} fa-upload"></i> {{ trans('torrent.top-seeded') }}</a>
        <a href="{{ route('leeched') }}" class="btn btn-sm btn-primary"><i
                    class="{{ config('other.font-awesome') }} fa-download"></i> {{ trans('torrent.top-leeched') }}</a>
        <a href="{{ route('completed') }}" class="btn btn-sm btn-primary"><i
                    class="{{ config('other.font-awesome') }} fa-arrow-up"></i> {{ trans('torrent.top-completed') }}</a>
        <a href="{{ route('dying') }}" class="btn btn-sm btn-primary"><i
                    class="{{ config('other.font-awesome') }} fa-exclamation-triangle"></i> {{ trans('torrent.top-dying') }}</a>
        <a href="{{ route('dead') }}" class="btn btn-sm btn-primary"><i
                    class="{{ config('other.font-awesome') }} fa-recycle"></i> {{ trans('torrent.top-dead') }}</a>
    </div>
</div>
