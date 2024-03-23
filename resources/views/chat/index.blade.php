<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.css" />
<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Chat') }}
        </h2>
    </x-slot>

    <style>
        .active-chat{
            outline: 2px solid #4ade80!important;
            box-shadow: 5px 0px 25px #4ade80;
            animation: glow-animation 3s infinite alternate-reverse;
        }
        @keyframes glow-animation{
            0% {
                box-shadow: 0px 0px 25px #4ade80;
            }
            50% {
                box-shadow: 0px 0px 0px #4ade80;
            }
            100% {
                box-shadow: 0px 0px 25px #4ade80;
            }
        }
        #emoji__picker__btn {
            height: 30px;
            width: 30px;
        }
        #emoji__picker__btn > em-emoji-picker {
            position: relative;
            top: -480px;
        }
        /* emoji */
        .emojionearea.emojionearea-inline {
            border-radius: 30px;
            margin-top: 2px;
            border: 1px solid #000000!important;
        }
        .emojionearea.emojionearea-inline>.emojionearea-editor {
            left: 45px;
            display: flex;
        }
        .emojionearea.emojionearea-inline>.emojionearea-editor {
            padding-top: 9px;
        }
        .emojionearea.emojionearea-inline>.emojionearea-editor {
            overflow-y: auto;
            /* height: 45px !important; */
            display: block !important;
            white-space: normal;
            width: 81%;
            padding-top: 10px;
        }
        .emojionearea .emojionearea-editor:empty:before {
            height: auto;
            padding-top: 3px !important;
        }
        .emojionearea .emojionearea-editor {
            min-height: 2em;
        }
        .emojionearea .emojionearea-button {
            top: 22px;
            left: -35px;
        }
        .emojionearea .emojionearea-picker.emojionearea-picker-position-top {
            left: -46px !important;
        }
        .message > .emojioneemoji {
            display: inline;
        }
        .parent-message-ele {
            border-radius: 20px!important;
        }
        .truncate-text {
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }
    </style>

    <div class="py-1">
        <div class="mx-auto">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="container my-24 mx-auto md:px-6">
                        <div class="flex-1 flex h-full">
                            <div class="sidebar hidden lg:flex w-1/3 flex-2 flex-col pr-6">
                                <div class="search flex-2 pb-6 px-2">
                                    <input type="text" class="outline-none py-2 block w-full bg-transparent border-b-2 border-gray-200" placeholder="Search">
                                </div>
                                <div class="flex-1 h-full px-2" id="chat__rooms">
                                    {{-- <div class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md">
                                        <div class="flex-2">
                                            <div class="w-12 h-12 relative">
                                                <img class="w-12 h-12 rounded-full mx-auto" src="../resources/profile-image.png" alt="chat-user">
                                                <span class="absolute w-4 h-4 bg-green-400 rounded-full right-0 bottom-0 border-2 border-white"></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 px-2">
                                            <div class="truncate w-32"><span class="text-gray-800">Ryann Remo</span></div>
                                            <div><small class="text-gray-600">Yea, Sure!</small></div>
                                        </div>
                                        <div class="flex-2 text-right">
                                            <div><small class="text-gray-500">15 April</small></div>
                                            <div>
                                                <small class="text-xs bg-red-500 text-white rounded-full h-6 w-6 leading-6 text-center inline-block">
                                                    23
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md">
                                        <div class="flex-2">
                                            <div class="w-12 h-12 relative">
                                                <img class="w-12 h-12 rounded-full mx-auto" src="../resources/profile-image.png" alt="chat-user">
                                                <span class="absolute w-4 h-4 bg-gray-400 rounded-full right-0 bottom-0 border-2 border-white"></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 px-2">
                                            <div class="truncate w-32"><span class="text-gray-800">Karp Bonolo</span></div>
                                            <div><small class="text-gray-600">Yea, Sure!</small></div>
                                        </div>
                                        <div class="flex-2 text-right">
                                            <div><small class="text-gray-500">15 April</small></div>
                                            <div>
                                                <small class="text-xs bg-red-500 text-white rounded-full h-6 w-6 leading-6 text-center inline-block">
                                                    10
                                                </small>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md border-l-4 border-red-500">
                                        <div class="flex-2">
                                            <div class="w-12 h-12 relative">
                                                <img class="w-12 h-12 rounded-full mx-auto" src="../resources/profile-image.png" alt="chat-user">
                                                <span class="absolute w-4 h-4 bg-gray-400 rounded-full right-0 bottom-0 border-2 border-white"></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 px-2">
                                            <div class="truncate w-32"><span class="text-gray-800">Mercedes Yemelyan</span></div>
                                            <div><small class="text-gray-600">Yea, Sure!</small></div>
                                        </div>
                                        <div class="flex-2 text-right">
                                            <div><small class="text-gray-500">15 April</small></div>
                                        </div>
                                    </div>
                                    <div class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md">
                                        <div class="flex-2">
                                            <div class="w-12 h-12 relative">
                                                <img class="w-12 h-12 rounded-full mx-auto" src="../resources/profile-image.png" alt="chat-user">
                                                <span class="absolute w-4 h-4 bg-gray-400 rounded-full right-0 bottom-0 border-2 border-white"></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 px-2">
                                            <div class="truncate w-32"><span class="text-gray-800">Cadi Kajetán</span></div>
                                            <div><small class="text-gray-600">Yea, Sure!</small></div>
                                        </div>
                                        <div class="flex-2 text-right">
                                            <div><small class="text-gray-500">15 April</small></div>
                                        </div>
                                    </div>
                                    <div class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md">
                                        <div class="flex-2">
                                            <div class="w-12 h-12 relative">
                                                <img class="w-12 h-12 rounded-full mx-auto" src="../resources/profile-image.png" alt="chat-user">
                                                <span class="absolute w-4 h-4 bg-gray-400 rounded-full right-0 bottom-0 border-2 border-white"></span>
                                            </div>
                                        </div>
                                        <div class="flex-1 px-2">
                                            <div class="truncate w-32"><span class="text-gray-800">Rina Samuel</span></div>
                                            <div><small class="text-gray-600">Yea, Sure!</small></div>
                                        </div>
                                        <div class="flex-2 text-right">
                                            <div><small class="text-gray-500">15 April</small></div>
                                        </div>
                                    </div> --}}
                                </div>
                            </div>
                            <div class="chat-area flex-1 flex flex-col hidden" id="left__side__chat">
                                <div class="flex-3">
                                    <h2 class="text-xl py-1 mb-8 border-b-2 border-gray-200">Chatting with <b id="active__user__name">Mercedes Yemelyan</b></h2>
                                </div>
                                <div class="messages flex-1 overflow-auto" id="messages__ele" style="max-height: 50vh;">

                                </div>
                                <div class="flex-2 pt-4 pb-10">
                                    <div class="write bg-white shadow flex rounded-lg">
                                        <div class="flex-3 flex content-center items-center text-center p-4 pr-0">
                                            <span class="block text-center text-gray-400 hover:text-gray-800" id="emoji__picker__btn">

                                            </span>
                                        </div>
                                        <div class="flex-1">
                                            <textarea style="outline: 2px solid #ffffff;outline-offset: -2px;--tw-ring-shadow: none;color: #2f2f2f;" id="message__field" name="message" class="w-full block outline-none py-4 px-4 bg-transparent" rows="1" placeholder="Type a message..." autofocus=""></textarea>
                                        </div>
                                        <div class="flex-2 w-32 p-2 flex content-center items-center">
                                            <div class="flex-1 text-center">
                                                <span class="text-gray-400 hover:text-gray-800">
                                                    <span class="inline-block align-text-bottom">
                                                        <svg fill="none" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" stroke="currentColor" viewBox="0 0 24 24" class="w-6 h-6"><path d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    </span>
                                                </span>
                                            </div>
                                            <div class="flex-1">
                                                <button class="bg-blue-400 w-10 h-10 rounded-full inline-block" id="send__btn">
                                                    <span class="inline-block align-text-bottom">
                                                        <svg fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" viewBox="0 0 24 24" class="w-4 h-4 text-white"><path d="M5 13l4 4L19 7"></path></svg>
                                                    </span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- <section class="mb-32 text-center">
                            <div class="lg:gap-xl-12 grid gap-x-6 md:grid-cols-2 lg:grid-cols-4" id="search__results">

                            </div>
                        </section> --}}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="chat__room__ele" class="hidden">
        <div class="entry cursor-pointer transform hover:scale-105 duration-300 transition-transform bg-white mb-4 rounded p-4 flex shadow-md chat">
            <div class="flex-2">
                <div class="w-12 h-12 relative">
                    <img class="w-12 h-12 rounded-full mx-auto user_image" src="" alt="chat-user" style="object-fit: cover;">
                    <span class="absolute w-4 h-4 bg-green-400 rounded-full right-0 bottom-0 border-2 border-white online__status"></span>
                </div>
            </div>
            <div class="flex-1 px-2" style="width: 50%;">
                <div class="truncate w-32" style="overflow: visible;"><span class="text-gray-800 user__name">Ryann Remo</span> <span class="is_typing text-zinc-700 italic hidden">(typing...)</span></div>
                <div class="truncate-text"><small class="text-gray-600 last_message ">Yea, Sure!</small></div>
            </div>
            <div class="flex-2 text-right">
                <div><small class="text-gray-500">15 April</small></div>
                <div>
                    <small class="text-xs bg-red-500 text-white rounded-full h-6 w-6 leading-6 text-center inline-block unread__message__count hidden">
                        0
                    </small>
                </div>
            </div>
        </div>
    </div>

    <div class="message mb-4 flex hidden" id="message__received__template">
        <div class="flex-2">
            <div class="w-12 h-12 relative">
                <img class="w-12 h-12 rounded-full mx-auto sender__image" src="" alt="chat-user">
                <span class="absolute w-4 h-4 bg-gray-400 rounded-full right-0 bottom-0 border-2 border-white"></span>
            </div>
        </div>
        <div class="flex-1 px-2">
            <div class="inline-block bg-gray-300 rounded-full p-2 px-6 text-gray-700 parent-message-ele">
                <span class="message">message</span>
            </div>
            <div class="pl-4"><small class="text-gray-500 message__time">15 April</small></div>
        </div>
    </div>

    <div class="message me mb-4 flex text-right hidden" id="message__sent__template">
        <div class="flex-1 px-2">
            <div class="inline-block bg-blue-600 rounded-full p-2 px-6 text-white parent-message-ele">
                <span class="message">message</span>
            </div>
            <div class="pr-4"><small class="text-gray-500 message__time">15 April</small></div>
        </div>
    </div>


</x-app-layout>

<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/emojionearea/3.4.2/emojionearea.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/emoji-mart@latest/dist/browser.js"></script>
@include('chat.websocket')


