<script>
    const chatRooms = [];
    let activeChatRoom = null;

    const user_id = `{{ auth()->id() }}`;

    class WS {
        constructor(details) {
            this.details = details;
            this.init();
        }
        init() {
            this.ws = new WebSocket(this.details.url + '?user_id=' + this.details.user_id);
            this.ws.onopen = () => {
                this.ws.send(JSON.stringify({
                    action: 'subscribe',
                    user_id: this.details.user_id
                }));
            }
            this.ws.onclose = () => {
                this.userAuthenticationFailed();
            }
            this.ws.onmessage = (event) => {
                const data = JSON.parse(event.data);
                const eventData = data.data.data;
                switch (eventData.type) {
                    case 'typing':
                        this.typingEvent(eventData);
                        break;
                    case 'message':
                        this.messageEvent(eventData);
                        break;
                }
            }
        }
        typingEvent(data) {
            const chatRoom = chatRooms.find(room => room.details.chat_room_id === data.chat_room);
            if (chatRoom) {
                const isTypingElement = chatRoom.template.querySelector('.is_typing');
                isTypingElement.classList.remove('hidden');

                // Clear any existing timeout
                if (chatRoom.typingTimeout) {
                    clearTimeout(chatRoom.typingTimeout);
                }

                // Set a new timeout to hide the is_typing element after 5 seconds
                chatRoom.typingTimeout = setTimeout(() => {
                    isTypingElement.classList.add('hidden');
                    chatRoom.typingTimeout = null;
                }, 3000);
            }
        }

        messageEvent(data) {
            const chatRoom = chatRooms.find(room => room.details.chat_room_id === data.chat_room);
            if (chatRoom) {
                chatRoom.template.querySelector('.last_message').innerText = data.message;
                chatRoom.template.querySelector('.unread__message__count').innerText = Number(chatRoom.template
                    .querySelector('.unread__message__count').innerText) + 1;
            }
            if(chatRoom === activeChatRoom){
                new Message({
                    chat_room: activeChatRoom.details.chat_room_id,
                    created_at: getCurrentTime(),
                    id: null,
                    is_read: 0,
                    media_type: null,
                    media_url: null,
                    message: data.message,
                    receiver_id: user_id,
                    sender_id: activeChatRoom.details.user_details.id,
                    type: "receive",
                    updated_at: getCurrentTime()
                }, true);
            }
        }
        sendMessage(body) {
            this.ws.send(JSON.stringify({
                action: 'publish',
                channel: body.channel,
                data: body.data
            }));
        }
        userAuthenticationFailed() {
            alert('Please login first');
        }
    }

    class Message {
        constructor(details, isAppend) {
            this.details = details;
            this.details.type = 'sent';
            if (details.receiver_id == user_id) {
                this.details.type = 'received';
            }
            this.template = document.getElementById(`message__${this.details.type}__template`).cloneNode(true);
            if (this.details.type === 'received') {
                this.template.querySelector('.sender__image').src = activeChatRoom.template.querySelector(
                    '.user_image').src;
            }
            this.template.removeAttribute('id');
            this.template.classList.remove('hidden');
            this.template.querySelector('.message').innerHTML = this.details.message;
            this.template.querySelector('.message__time').innerText = this.details.created_at;
            if(isAppend){
                document.getElementById('messages__ele').append(this.template);
                this.template.scrollIntoView({
                    behavior: "smooth",
                    block: "end"
                })
            } else {
                document.getElementById('messages__ele').prepend(this.template);
            }

        }
    }

    class ChatRoom {
        constructor(chatRoomDetails) {
            this.details = chatRoomDetails;
            this.template = document.getElementById('chat__room__ele').cloneNode(true);
            this.template.removeAttribute('id');
            this.template.classList.remove('hidden');
            this.template.querySelector('.user__name').innerText = chatRoomDetails.user_details.name;
            this.template.querySelector('.user_image').src =
                `{{ asset('profile-pictures') }}/${chatRoomDetails.user_details.photo}`;
            this.template.querySelector('.last_message').innerHTML = this.details.last_message;

            this.addEvents();
            this.getUnreadMessages();
            document.getElementById('chat__rooms').appendChild(this.template);
        }
        addEvents() {
            this.template.querySelector('.chat').addEventListener('click', (e) => {
                if (activeChatRoom !== this) {
                    this.activateChat(e);
                }
            });
        }
        deactivateChat() {
            if (this.template.querySelector('.chat').classList.contains('active-chat')) {
                this.template.querySelector('.chat').classList.remove('active-chat');
            }
        }
        activateChat(ele) {
            chatRooms.forEach(chatRoom => {
                chatRoom.deactivateChat();
            });
            if (!this.template.querySelector('.chat').classList.contains('active-chat')) {
                this.template.querySelector('.chat').classList.add('active-chat');
            }
            activeChatRoom = this;
            if (activeChatRoom !== null) {
                document.getElementById('left__side__chat').classList.remove('hidden');
            }
            document.getElementById('active__user__name').innerText = activeChatRoom.details.user_details.name;
            this.emptyMessages();
            this.getMessages();
        }
        emptyMessages() {
            document.getElementById('messages__ele').innerHTML = '';
        }
        getMessages() {
            fetch(`{{ route('chat.get.messages') }}?chat_room=${this.details.chat_room_id}`, {
                method: 'GET'
            }).then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            }).then(data => {
                data.data.forEach(message => {
                    new Message(message);
                });
            }).then(() => {
                setTimeout(() => {
                    document.getElementById('messages__ele').lastElementChild?.scrollIntoView({
                        block: "end"
                    })
                })
            }).catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        }
        getUnreadMessages() {
            fetch(`{{ route('chat.get.unread.message.count') }}?chat_room=${this.details.chat_room_id}`, {
                method: 'GET'
            }).then(res => {
                if (!res.ok) {
                    throw new Error('Network response was not ok');
                }
                return res.json();
            }).then(data => {
                if (data.count > 0) {
                    this.template.querySelector('.unread__message__count').classList.remove('hidden');
                    this.template.querySelector('.unread__message__count').innerText = data.count;
                }
            }).catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
        }
    }



    const ws = new WS({
        url: 'ws://localhost:8090',
        user_id: user_id
    });


    function fetchChatRooms() {
        fetch(`{{ route('chat.get.chat.rooms') }}`, {
            headers: {
                'X-CSRF-TOKEN': `{{ csrf_token() }}`
            }
        }).then(res => {
            if (!res.ok) {
                throw new Error('Network response was not ok');
            }
            return res.json();
        }).then(data => {
            data.data.forEach(chatRoom => {
                chatRooms.push(new ChatRoom(chatRoom));
            })
        }).catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
    }

    fetchChatRooms();

    function sendMessage(req) {
        ws.sendMessage({
            channel: 'user_' + activeChatRoom.details.user_details.id,
            data: req.data
        });
    }

    function emptyField() {
        document.querySelector('.emojionearea-editor').innerHTML = '';
    }

    function getCurrentTime() {
        var currentDate = new Date();
        var formattedDate = currentDate.toLocaleString('en-US', {
            timeZone: 'UTC',
            // year: 'numeric',
            // month: '2-digit',
            // day: '2-digit',
            hour: '2-digit',
            minute: '2-digit',
            second: '2-digit',
            hour12: true
        });

        return formattedDate;
    }

    document.getElementById('send__btn').addEventListener('click', () => {
        if (document.querySelector('.emojionearea-editor').querySelectorAll('img').length !== 0 || document
            .querySelector('.emojionearea-editor').innerText.trim().length !== 0) {
            sendMessage({
                data: {
                    type: 'message',
                    message: document.querySelector('.emojionearea-editor').innerHTML.replaceAll("&nbsp;", " "),
                    from_user: user_id,
                    chat_room: activeChatRoom.details.chat_room_id,
                    to_user: activeChatRoom.details.user_details.id
                }
            })
            new Message({
                chat_room: activeChatRoom.details.chat_room_id,
                created_at: getCurrentTime(),
                id: null,
                is_read: 0,
                media_type: null,
                media_url: null,
                message: document.querySelector('.emojionearea-editor').innerHTML.replaceAll("&nbsp;", " "),
                receiver_id: activeChatRoom.details.user_details.id,
                sender_id: user_id,
                type: "sent",
                updated_at: getCurrentTime()
            }, true);
            emptyField();
        }
    });

    async function applyFilter() {
        await $('#message__field').emojioneArea({
            emojiPlaceholder: ":smilenew_cat:",
            pickerPosition: "top",
            placement: 'absright',
        });
    }

    applyFilter().then(() => {
        setTimeout(() => {
            document.querySelector('.emojionearea-editor').addEventListener('keydown', (e) => {
                if (event.key === 'Enter' && !event.shiftKey) {
                    if (document.querySelector('.emojionearea-editor').querySelectorAll('img')
                        .length !== 0 || document.querySelector('.emojionearea-editor')
                        .innerText.trim().length !== 0) {
                        sendMessage({
                            data: {
                                type: 'message',
                                message: document.querySelector('.emojionearea-editor')
                                    .innerHTML.replaceAll("&nbsp;", " "),
                                from_user: user_id,
                                chat_room: activeChatRoom.details.chat_room_id,
                                to_user: activeChatRoom.details.user_details.id
                            }
                        });
                        new Message({
                            chat_room: activeChatRoom.details.chat_room_id,
                            created_at: getCurrentTime(),
                            id: null,
                            is_read: 0,
                            media_type: null,
                            media_url: null,
                            message: document.querySelector('.emojionearea-editor').innerHTML.replaceAll("&nbsp;", " "),
                            receiver_id: activeChatRoom.details.user_details.id,
                            sender_id: user_id,
                            type: "sent",
                            updated_at: getCurrentTime()
                        }, true);
                        setTimeout(() => {
                            emptyField();
                        })
                    }
                } else {
                    sendMessage({
                        data: {
                            type: 'typing',
                            from_user: user_id,
                            chat_room: activeChatRoom.details.chat_room_id,
                            to_user: activeChatRoom.details.user_details.id
                        }
                    });
                }
            })
        }, 1000);
    })



    document.getElementById('message__field').addEventListener('keydown', function(event) {
        if (event.key === 'Enter' && !event.shiftKey) {
            sendMessage({
                data: {
                    type: 'message',
                    message: document.getElementById('message__field').value,
                    from_user: user_id,
                    chat_room: activeChatRoom.details.chat_room_id,
                    to_user: activeChatRoom.details.user_details.id
                }
            });
            emptyField();
            event.preventDefault();
        } else if (event.key === 'Enter' && event.shiftKey) {
            const textarea = event.target;
            const start = textarea.selectionStart;
            const end = textarea.selectionEnd;
            textarea.value = textarea.value.substring(0, start) + '\n' + textarea.value.substring(end);
            textarea.selectionStart = textarea.selectionEnd = start + 1;
            event.preventDefault();
        }
    });
</script>
