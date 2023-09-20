<div class="py-5 bg-black text-white space-y-5 min-h-screen">
    <x-atoms.breadcrumb :routes="[
                ['title' => 'Tv Shows', 'route' => route('tv-shows.home') ],
                ['title' => $tvShow->title, 'route' => null]
    ]" />
    <div class="container space-y-7">
        

        <section class="grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-7">
                
                <div class="video-container">
                    <video id="player" playsinline controls >
                        <source src="{{ file_path($selectedEpisode->recorded_video ?? $tvShow->trailer) }}">
                    </video>
                </div>


                <header class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                    <div class="space-y-1">
                        <h2 class="font-semibold text-xl">{{ $selectedEpisode->title ?? $tvShow->title }}</h2>

                        @if($selectedEpisode)
                            <p>Published on {{ $selectedEpisode->releaseAt() }}</p>
                        @else
                            <p>Published on {{ $tvShow->releaseAt() }}</p> 
                        @endIf
                    </div>

                    <div class="flex flex-col items-end space-y-5">
                        <button>
                            <img src="{{ asset('svg/3-dots-horizontal.svg') }}" alt="" />
                        </button>

                        <div class="flex items-center space-x-3">
                            <div>
                                <i class="lar la-eye"></i>
                                @if($selectedEpisode)
                                <span>{{ view_count($selectedEpisode->views->count()) }} viewers</span>
                                @else 
                                <span>{{ view_count($tvShow->views->count()) }} viewers</span>
                                @endIf
                            </div>

                            @if($selectedEpisode)
                                <span>{{ convert_seconds_to_time($selectedEpisode->duration) }}</span>
                            @else 
                                <span>{{ convert_seconds_to_time($tvShow->episodes()->sum('duration')) }}</span>
                            @endIf
                        </div>
                    </div>
                </header>
                <section class="space-y-5" x-data="{ show : false }">
                   <div x-show="!show">
                        {!! Str::limit($selectedEpisode->description ?? $tvShow->description, 200, '...') !!}
                   </div>

                   @if(strlen($selectedEpisode->description ?? $tvShow->description) > 200)
                        <div x-show="show">
                                {!! $selectedEpisode->description ?? $tvShow->description !!}
                        </div>
                        
                        <div class="flex justify-center" x-show="show === false">
                            <button class="text-sm text-[#0012B6] flex items-center" x-on:click="show = !show">
                                <span>Read More</span>
                                <img src="{{ asset('svg/arrow-circle-down.svg') }}" alt="">
                            </button>
                        </div>
                    @endIf
                </section>

            @if($casts->count() > 0)
                <section class="space-y-5">
                    <h1 class="font-semibold text-2xl">Cast</h1>
                    <section class="grid sm:grid-cols-2 lg:grid-cols-3 gap-5 md:gap-10">

                        @foreach ($casts as $cast)
                            <div class="flex space-x-2">
                                <img src="{{ file_path($cast->image) }}" alt="" class="w-[93px] h-[115px] object-cover rounded-2xl" />
                                <div>
                                    <h3 class="font-semibold text-xl">{{ $cast->name }}</h3>
                                    <span class="opacity-60 text-sm">{{ $cast->role }}</span>
                                </div>
                            </div>
                        @endforeach
                    </section>
                </section>
            @endIf
            </div>


            <div class="space-y-7">

                <section class="bg-dark p-5 rounded-2xl relative">
                    <img src="{{ file_path($selectedEpisode->thumbnail ?? $tvShow->thumbnail) }}" alt="" class="h-[483px] w-full object-cover rounded-xl" />

                    <button class="rounded-md absolute top-7 right-7 bg-white hover:bg-danger text-danger hover:text-white w-[40px] h-[40px]">
                        <i class="las la-heart"></i>
                    </button>
                </section>

                <section class="bg-dark p-5 rounded-2xl space-y-5">
                    <h3 class="text-right opacity-50 text-sm space-x-2 relative">
                        <select class="text-white select-season-form appearance-none mr-2" wire:model='season_number'>
                            @foreach ($seasons as $season)
                                <option value="{{ $season }}" class="bg-black">Season {{ $season }}</option>
                            @endforeach
                        </select>
                        <i class="las la-caret-down absolute top-3.5 right-0"></i>
                    </h3>

                    <x-atoms.loading target="season_number" />
                
                    <section 
                        wire:loading.class="hidden"  
                        wire:target="season_number"
                        class="overflow-y-auto min-h-[20vh] space-y-3 scrollbar-thin scrollbar-thumb-gray-300 scrollbar-track-gray-100">
                        @foreach ($episodes as $key => $episode)
                        <button class="flex items-center space-x-3 text-left" wire:click="selectEpisode({{ $episode->id }})">
                            <img src="{{ file_path($episode->thumbnail) }}" alt="" class="h-[80px] w-[80px] rounded-xl" />
                            <div>
                                <h2 class="font-semibold">{{ $key + 1 }}. {{ $episode->title }}</h2>
                                <div class="opacity-50 text-sm">
                                    <span>{{ convert_seconds_to_time($episode->duration) }}</span><br />
                                    <span>Published on {{ $episode->releaseAt() }}</span>
                                </div>
                            </div>
                        </button>
                        @endforeach
                    </section>
                </section>


                <section class="bg-dark p-5 space-y-3 rounded-2xl">
                    <h3 class="font-semibold text-lg">Share</h3>
                
                    <div class="flex flex-wrap  text-sm">
                        <a x-bind:href="`https://www.facebook.com/sharer/sharer.php?u=${window.location.href}`" target="_blank" class="mr-2 mb-1">
                            <img src="{{ asset('images/facebook.png') }}" alt="" class="h-[40px]" />
                        </a>
                        {{-- <a href="#" class="mr-2 mb-1">
                            <img src="{{ asset('images/youtube.png') }}" alt="" class="h-[40px]" />
                        </a> --}}
                        <a x-bind:href="`https://twitter.com/intent/tweet?url=${window.location.href}`" target="_blank" class="mr-2 mb-1">
                            <img src="{{ asset('images/twitter.png') }}" alt="" class="h-[40px]" />
                        </a>
                        <a x-bind:href="`https://www.linkedin.com/sharing/share-offsite/?url=${window.location.href}`" target="_blank" class="mr-2 mb-1">
                            <img src="{{ asset('images/linkedIn.png') }}" alt="" class="h-[40px]" />
                        </a>
                        <a x-bind:href="`https://www.pinterest.com/pin/create/button/?url=${window.location.href}`" data-pin-do="buttonPin" target="_blank" class="mr-2 mb-1">
                            <img src="{{ asset('images/pinterest.png') }}" alt="" class="h-[40px]" />
                        </a>
                        <a href="javascript:void(0)" class="mr-2 mb-1" x-on:click="() => {
                            navigator.share({
                                title: '{{ $selectedEpisode->title ?? $tvShow->title }}',
                                text: 'Check out this video!',
                                url: window.location.href,
                            })
                            .then(() => console.log('Shared successfully'))
                            .catch((error) => console.error('Share failed', error));    
                        }">
                            <img src="{{ asset('images/custom-link.png') }}" alt="" class="h-[40px]" />
                        </a>
                    </div>
                </section>


                @if(count($tvShow->tags) > 0)
                    <section class="bg-dark p-5 space-y-3 rounded-2xl">
                        <h3 class="font-semibold text-lg">Tags</h3>

                        <div class="flex flex-wrap  text-sm">
                            @foreach ($tvShow->tags as $tag)
                                <a href="{{ route('search', ['q' => $tag ]) }}" class="mr-2 mb-1">#{{ $tag }}</a>
                            @endforeach
                        </div>
                    </section>
                @endIf
            </div>
        </section>
    </div>




    @livewire("home.partials.partners")


    @livewire("home.partials.newsletter")
</div>

@push('script')
<script>
    const videoPlayer = document.getElementById('player');

    document.addEventListener('DOMContentLoaded', () => {
            const player = new Plyr('#player', {
                autoplay: true, // Autoplay is initially set to false
            });
    });

    document.addEventListener('change-episode', (event) => {
        videoPlayer.src = event.detail.video_url;
        videoPlayer.load(); // Load the new video source
        videoPlayer.play(); // Play the new video

        setTimeout(() => {
            window.history.replaceState(null, null, `?season=${event.detail.season}&episode=${event.detail.episode}`);
        }, 2000);


        
    })
</script>
@endPush