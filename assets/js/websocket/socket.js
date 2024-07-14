var ws = new WebSocket('ws://127.0.0.1:8080');

ws.onopen = function(e) {
    console.log("Connection established!");

    //btn test
    if(document.querySelector('.test-socket')){
        document.querySelector('.test-socket').addEventListener('click', function (e){
            ws.send('Salut !');
        });
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
            c.innerHTML = count;
        }
    },
    message : function(){},
}


