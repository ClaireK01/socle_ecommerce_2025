

var ws = new WebSocket('ws://127.0.0.1:8080');

ws.onopen = function(e) {
    console.log("Connection established!");

    let btn = document.querySelector('.btn-send-message');
    if(btn){
        btn.addEventListener('click', function(e){
            e.preventDefault();
            //mettre dans une function
            let convo = document.querySelector('[data-convo]').dataset.convo;
            let messageContainer = document.querySelector('.message-text');

            let json = JSON.stringify({"mess" : messageContainer.value, "convo" : convo});
            ws.send(json)
            messageContainer.value = '';
        })
    }
};

ws.onmessage = function(e) {
    let data = JSON.parse(e.data);

    for(let id in data){
        app[id](data[id]);
    }
};

ws.onerror = function (e){
    console.log(e)
}

ws.onclose = function (e){
    console.log('web socket closed')
}

app = {
    clients : function(data){
        let clients = document.querySelector('.client-list');
        if(clients){
            clients.innerHTML = data;
        }
        let btns = document.querySelectorAll('.btn-sendmessage');
        if(btns){
            btns.forEach((el) => {
                el.addEventListener('click', function (e){
                    e.preventDefault();
                    let link = e.currentTarget.dataset.message;
                    window.location = link;
                })
            })
        }
    },
    count : function(count){
        let c = document.querySelector('.count');
        if(c){
            let num = parseInt(count) - 1;
            c.innerHTML = num.toString();
        }
    },
    conversation : function(data){
        let messages = document.querySelector('.message-list');
        if(messages){
            messages.innerHTML = data;
        }
    },
    message : function(msg){
        console.log(msg)
    },
}


