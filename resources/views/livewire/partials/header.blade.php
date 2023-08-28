<nav class="bg-[#0d0d0d] text-white">
    <div class="container flex items-center justify-between py-2">
        <div class="flex items-center space-x-10">
            <a href="{{ route('home') }}">
                <img src="{{ asset('images/logo-white.png') }}" alt="" class="h-[80px] w-auto" />
            </a>
            
            <form class="hidden xl:flex items-center bg-black h-5 w-100 rounded-2xl gap-2 py-1 px-5 flex max-[1750px]:hidden ">
                <i class="fa-solid fa-magnifying-glass text-lg text-white"></i>
                <input type="text" placeholder="Search titles here..." class="bg-black form-control border-0" />
            </form>
        </div>

        <ul class="hidden xl:flex items-center space-x-5">
            <li>
                <a href="{{ route('home') }}" class="hover:text-danger">Home</a>
            </li>

            <li class="relative group">
                <a href="{{ route('tv-shows.home') }}">Tv Shows</a>
            </li>

            <li>
                <a href="{{ route('live-channel.show') }}" class="hover:text-danger">
                    <span class="text-danger">&bull;</span>
                    Live
                </a>
            </li>

            <li>
                <a href="{{ route('pedicab-streams.home') }}" class="hover:text-danger">Pedicab Streams</a>
            </li>

            <li class="relative group">
                <a href="javascript:void(0)" class="hover:text-danger">
                    <span>More</span>
                    <i class="fa-solid fa-angle-down"></i>
                </a>
            
                <ul class="absolute py-1 whitespace-nowrap space-y-1 bg-dark rounded-xl min-w-[150px] text-sm z-50 hidden group-hover:block">
                    <li>
                        <a href="{{ route('about') }}" class="px-4 py-2 block hover:text-danger">About Us</a>
                    </li>
                    <li>
                        <a href="{{ route('contact') }}" class="px-4 py-2 block hover:text-danger">Contact Us</a>
                    </li>
                    <li>
                        <a href="{{ route('product.home') }}" class="px-4 py-2 block hover:text-danger">Our Products</a>
                    </li>

                    <li>
                        <a href="{{ route('celebrity-shoutout.home') }}" class="px-4 py-2 block hover:text-danger">Celebritity shoutouts</a>
                    </li>

                    <li>
                        <a href="{{ route('gallery.home') }}" class="px-4 py-2 block hover:text-danger">Our Gallery</a>
                    </li>
                </ul>
            </li>
        </ul>


        <ul class="hidden xl:flex items-center space-x-5">
            <li title="Wishlist">
                <a href="#" class="hover:text-danger text-lg">
                    <i class="fa fa-solid fa-heart"></i>
                </a>
            </li>
            @if(!auth()->check())
            <li>
                <a href="{{ route('login') }}" class="btn btn-xl rounded-2xl border hover:bg-danger hover:border-danger">Sign in</a>
            </li>
        
            <li>
                <a href="{{ route('register') }}" class="btn btn-xl rounded-2xl btn-danger">Register</a>
            </li>
            @else 

            <li title="Notification">
                <a href="#" class="hover:text-danger text-lg">
                    <i class="fa fa-solid fa-bell"></i>
                </a>
            </li>

            <li class="relative group">
                <a href="javascript:void(0)" class="hover:text-danger border flex items-center space-x-2 rounded-xl p-3">
                    <i class="las la-user-circle text-xl"></i>
                    <span>{{ user()->username }}</span>
                    <i class="fa fa-solid fa-caret-down"></i>
                </a>

                <ul
                    class="absolute top-[56px] right-[0px] py-3 whitespace-nowrap space-y-1 bg-dark rounded-xl min-w-[180px] text-sm z-50 hidden group-hover:block border border-danger">
                    <li class="bg-danger p-3 text-md">
                        <i class="las la-user-circle text-2xl"></i>
                        <div class="font-bold">{{ user()->first_name }} {{ user()->last_name }}</div>
                        <span>({{ user()->username }})</span>
                    </li>
                    <li>
                        <a href="{{ route('about') }}" class="px-4 py-1 space-x-3 flex items-center space-x-1 hover:text-danger">
                            <i class="las la-thumbs-up text-xl"></i>
                            <span>Favourites</span>
                        </a>
                        <a href="{{ route('about') }}" class="px-4 py-1 space-x-3 flex items-center space-x-1 hover:text-danger">
                            <i class="las la-cloud-upload-alt text-xl"></i>
                            <span>Upload</span>
                        </a>
                        <a href="{{ route('about') }}" class="px-4 py-1 space-x-3 flex items-center space-x-1 hover:text-danger">
                            <i class="las la-crown text-xl"></i>
                            <span>Subscription</span>
                        </a>

                        <a href="{{ route('user.dashboard') }}" 
                            class="px-4 py-1 space-x-3 flex items-center space-x-1 hover:text-danger">
                            <i class="las la-user-circle text-xl"></i>
                            <span>My Profile</span>
                        </a>

                        <a href="{{ route('logout') }}" 
                            class="px-4 py-1 space-x-3 flex items-center space-x-1 hover:text-danger">
                            <i class="las la-sign-out-alt text-xl"></i>
                            <span>Logout</span>
                        </a>
                    </li>
                </ul>
            </li>

            @endIf
        </ul>


        <button class="text-white inline-block xl:hidden" type="button"
            x-on:click="$dispatch('toggle-mobile-nav')">
            <i class="las la-bars text-3xl"></i>
        </button>

    </div>


    <section x-cloak x-data="{ show : false }" @toggle-mobile-nav.window="show = !show" :class="show ? 'top-0' : '-top-[5000px]'"
        class="transition-all duration-700 ease-in-out w-screen fixed z-[1000] h-screen overflow-y-auto p-7 bg-dark">
        <section class="space-y-10 min-h-screen overflow-y-auto">
            <header class="flex justify-between items-center">
                <img src="{{ asset('images/logo-white.png') }}" alt="" class="h-[70px] w-auto" />
            
                <button class="text-xl text-white" x-on:click="show = false; isBtn = false">
                    Close <i class="las la-times"></i>
                </button>
            </header>
            
            <ul class="flex-1 space-y-5 text-lg md:text-2xl font-bold text-white text-center">
                <li>
                    <a href="{{ route('home') }}" class="hover:text-secondary">Home</a>
                </li>
            
                <li>
                    <a href="{{ route('tv-shows.home') }}" class="hover:text-secondary">Tv Shows</a>
                </li>
            
                <li>
                    <a href="{{ route('live-channel.show') }}" class="hover:text-secondary">
                        <span class="text-danger">&bull;</span>
                        Live
                    </a>
                </li>
            
                <li>
                    <a href="{{ route('pedicab-streams.home') }}" class="hover:text-secondary">Pedicab Streams</a>
                </li>

                <li class="relative group">
                    <a href="javascript:void(0)" class="hover:text-danger">
                        <span>More</span>
                        <i class="fa-solid fa-angle-down"></i>
                    </a>
                
                    <ul
                        class="py-1 whitespace-nowrap space-y-1 bg-dark rounded-xl  z-50 hidden group-hover:block">
                        <li>
                            <a href="{{ route('about') }}" class="px-4 py-2 block hover:text-danger">About Us</a>
                        </li>
                        <li>
                            <a href="{{ route('contact') }}" class="px-4 py-2 block hover:text-danger">Contact Us</a>
                        </li>
                        <li>
                            <a href="{{ route('product.home') }}" class="px-4 py-2 block hover:text-danger">Our Products</a>
                        </li>
                
                        <li>
                            <a href="{{ route('celebrity-shoutout.home') }}" class="px-4 py-2 block hover:text-danger">Celebritity
                                shoutouts</a>
                        </li>
                
                        <li>
                            <a href="{{ route('gallery.home') }}" class="px-4 py-2 block hover:text-danger">Our Gallery</a>
                        </li>
                    </ul>
                </li>
            
            </ul>
            
            <ul class="flex justify-center items-center space-x-5 border-t border-secondary py-7">
                <li>
                    <a href="{{ route('login') }}" class="btn btn-xl rounded-2xl border hover:bg-danger hover:border-danger">
                        Sign in
                    </a>
                </li>
            
                <li>
                    <a href="{{ route('register') }}" class="btn btn-xl rounded-2xl btn-danger">Register</a>
                </li>
            </ul>
            
            
            <ul class="flex items-center justify-center">
                <li>
                    <a href="#" class="text-2xl flex items-center justify-center rounded-2xl h-[50px] min-w-[50px] hover:bg-danger">
                        <i class="lab la-facebook-f"></i>
                    </a>
                </li>
            
                <li>
                    <a href="#" class="text-2xl flex items-center justify-center rounded-2xl h-[50px] min-w-[50px] hover:bg-danger">
                        <i class="lab la-youtube"></i>
                    </a>
                </li>
            
                <li>
                    <a href="#" class="text-2xl flex items-center justify-center rounded-2xl h-[50px] min-w-[50px] hover:bg-danger">
                        <i class="lab la-twitter"></i>
                    </a>
                </li>
            
            
                <li>
                    <a href="#" class="text-2xl flex items-center justify-center rounded-2xl h-[50px] min-w-[50px] hover:bg-danger">
                        <i class="lab la-linkedin"></i>
                    </a>
                </li>
            
                <li>
                    <a href="#" class="text-2xl flex items-center justify-center rounded-2xl h-[50px] min-w-[50px] hover:bg-danger">
                        <i class="lab la-instagram"></i>
                    </a>
                </li>
            </ul>
        </section>
    </section>

</nav>
<script src="https://kit.fontawesome.com/4286a4e89d.js"></script>
