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
        document.querySelector('#client-list').innerHTML = data;
    },
    count : function(count){
        document.querySelector('.count').innerHTML = count;
    },
    message : function(){},
}


