{{ auth()->id() }}

<br><br>
User ID -<br>
<input id="to_user" type="number">
<br>
<input id="message" type="text">
<button id="send__btn">send message</button>

<script>
    class WS {
        constructor(details){
            this.details = details;
            this.init();
        }
        init(){
            this.ws = new WebSocket(this.details.url);
            this.ws.onopen = () => {
                this.ws.send(JSON.stringify({
                    action: 'subscribe',
                    user_id : this.details.user_id
                }));
            }
            this.ws.onclose = () => {
                this.userAuthenticationFailed();
            }
        }
        sendTextMessage(body){
            this.ws.send(JSON.stringify({
                action: 'publish',
                channel: body.channel,
                message: body.message,
                from_user : this.details.user_id
            }));
        }
        userAuthenticationFailed(){
            alert('Please login first');
        }
    }


    const ws = new WS({
        url: 'ws://localhost:8090',
        user_id: '{{ auth()->id() }}'
    });

    document.getElementById('send__btn').addEventListener('click', ()=>{
        ws.sendTextMessage({
            channel : 'user_' + document.getElementById('to_user').value,
            message: document.getElementById('message').value,
        })
    });

    document.getElementById('message').addEventListener('input', (e)=>{
        ws.sendTextMessage({
            channel : 'user_' + document.getElementById('to_user').value,
            message: document.getElementById('message').value,
        })
    })



    // const ws = new WebSocket('ws://localhost:8090?user_id={{ auth()->id()}}');

    // const encryptedId = `{{ auth()->id()}}`;
    // const userId = "user_5";

    // // On WebSocket connection open
    // ws.onopen = () => {

    //     const subscriptionMessage = {
    //         action: 'subscribe',
    //         channel: userId,
    //         user_id : 6
    //     };

    //     ws.send(JSON.stringify(subscriptionMessage));
    // };

    // function sendMessage(){
    //     ws.send(JSON.stringify({
    //         action: 'publish',
    //         channel: 'user_5',
    //         message: 'Hello from another user!'
    //     }))
    // }

    // setTimeout(() =>{
    //     sendMessage();
    // },4000);
</script>
