<div class="py-5 bg-black text-white space-y-5 min-h-screen">
    <x-atoms.breadcrumb :routes="[
                ['title' => 'Tv Shows', 'route' => route('tv-shows.home') ],
                ['title' => $tvShow->title, 'route' => null]
    ]" />
     <div class="container space-y-7">
        

        <section class="grid lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-7 overflow-hidden">
                
                <div class="video-container" wire:ignore>
                    <video 
                        id="player" 
                        src="{{ file_path($selectedEpisode->recorded_video ?? $tvShow->trailer) }}" 
                        playsinline controls  
                        data-plyr-config='{ "title": "{{ $selectedEpisode->title ?? $tvShow->title }}", "debug" : "true" }'>
                    </video>
                </div>


                <header class="flex flex-col md:flex-row md:items-center md:justify-between space-y-3 md:space-y-0">
                    <div class="flex items-center justify-between">
                        <div class="space-y-1">
                            <h2 class="font-semibold text-xl">{{ $selectedEpisode->title ?? $tvShow->title }}</h2>
                            
                            @if($selectedEpisode)
                            <p>Published on {{ $selectedEpisode->releaseAt() }}</p>
                            @else
                            <p>Published on {{ $tvShow->releaseAt() }}</p>
                            @endIf
                        </div>

                            @if(is_user_logged_in())
                            <button  wire:click.prevent="saveToWatchlist({{ $tvShow->id }})"
                                class="md:hidden rounded-md w-[40px] h-[40px] {{ $tvShow->watchlists()->where('user_id', auth()->id())->count() > 0 ? 'bg-danger text-white' : 'bg-white text-danger' }}">
                                <i class="las la-heart"></i>
                            </button>
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
                            <button class="text-sm text-danger flex items-center" x-on:click="show = !show">
                                <span>Read More</span>
                                <img src="{{ asset('svg/arrow-circle-down.svg') }}" alt="">
                            </button>
                        </div>
                    @endIf
                </section>

            @if($casts->count() > 0)
                <section class="space-y-5" wire:ignore>
                    <h1 class="font-semibold text-2xl">Cast</h1>
                    <section class="swiper cast">
                        <div class="swiper-wrapper">
                        @foreach ($casts as $cast)
                            <div class="flex space-x-2 swiper-slide">
                                <img src="{{ file_path($cast->image) }}" alt="" class="w-[93px] h-[115px] object-cover rounded-2xl" />
                                <div>
                                    <h3 class="font-semibold text-xl">{{ $cast->name }}</h3>
                                    <span class="opacity-60 text-sm">{{ $cast->role }}</span>
                                </div>
                            </div>
                        @endforeach
                        </div>
                        <div class="swiper-pagination"></div>
                    </section>
                </section>
            @endIf
            </div>


            <div class="space-y-7">

                <section class="bg-dark p-5 rounded-2xl relative hidden md:block">
                    <img src="{{ file_path($selectedEpisode->thumbnail ?? $tvShow->thumbnail) }}" alt="" class="h-[483px] w-full object-cover rounded-xl" />

                   @if(is_user_logged_in())
                       <button wire:click.prevent="saveToWatchlist({{ $tvShow->id }})"
                            class="rounded-md absolute top-7 right-7 w-[40px] h-[40px] {{ $tvShow->watchlists()->where('user_id', auth()->id())->count() > 0 ? 'bg-danger text-white' : 'bg-white text-danger' }}">
                            <i class="las la-heart"></i>
                        </button>
                   @endIf
                </section>

                @if(count($seasons) > 0)
                <section class="bg-dark p-5 rounded-2xl space-y-2">
                    <div class="flex justify-end text-sm space-x-2 relative" wire:ignore>
                        <select 
                            class="text-white select-season-form appearance-none mr-2 overflow-hidden season_number bg-black" 
                            wire:model='season_number' id="season_number">
                            @foreach ($seasons as $season)
                                <option value="{{ $season }}" class="bg-black hover:bg-danger">Season {{ $season }}</option>
                            @endforeach
                        </select>
                    </div>


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
                @endIf


                <section class="bg-dark p-5 space-y-3 rounded-2xl">
                    <h3 class="font-semibold text-lg">Share</h3>
                
                    <div class="flex flex-wrap  text-sm">
                        <a x-bind:href="`https://www.facebook.com/sharer/sharer.php?u=${window.location.href}`" target="_blank" class="mr-2 mb-1">
                            <img src="{{ asset('images/facebook.png') }}" alt="" class="h-[40px]" />
                        </a>
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

<script>
    var swiper = new Swiper(".cast", {
          slidesPerView: 3,
          spaceBetween: 30,
          grid: {
            rows: 2,
            fill : 'row'
          },
          pagination: {
            el: ".swiper-pagination",
            clickable: true,
          },
        breakpoints: {
            0: {
                slidesPerView: 2
            },
            768: {
                slidesPerView: 2
            },
            1024: {
                slidesPerView: 3
            },
            1500: {
                slidesPerView: 4
            }
        }
        });
</script> 

<script>
    new SlimSelect({
        select: '#season_number'
      })
</script>

@endPush

@push('header')
    <style>
        :root {
            --plyr-color-main: #FF0207;
        }

        select option {
            background-color: #000 !important;
            background: #000;
        }

        option:checked, option:hover {
            background-color: red !important;
            background: red !important;
        }
        
        select option:hover {
            box-shadow: 0 0 10px 10px #000 inset !important;
        }
        
        .swiper-wrapper {
            flex-direction: unset !important;
            flex-wrap: wrap !important;
            align-items: center !important;
        } 

        .swiper-pagination-bullet {
            background-color: #ff5733; 
        }
        
         .swiper-pagination-bullet-active {
            background-color: #ff0000; 
            opacity: 1; 
        }  
    </style>
    <script src="https://unpkg.com/slim-select@latest/dist/slimselect.min.js"></script>
    <link href="https://unpkg.com/slim-select@latest/dist/slimselect.css" rel="stylesheet" />
@endPush

