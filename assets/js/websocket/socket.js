var ws = new WebSocket('ws://127.0.0.1:8080');

ws.onopen = function(e) {
    console.log("Connection established!");

    if(document.querySelector('.test-socket')){
        document.querySelector('.test-socket').addEventListener('click', function (e){
            ws.send('Salut !');
        });
    }
};

ws.onmessage = function(e) {
    console.log(e.data);
};

ws.onerror = function (e){
    console.log(e)
}

ws.onclose = function (e){
    console.log('web socket closed')
}


