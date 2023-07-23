const pingTimeout = 3000;

function connect() {
    socket = new WebSocket(socket_addr);
    socket.addEventListener("open", function () {
        $('#status').html("Подключено");

        let command = {
            command: 'operator_get_work_mode'
        };
        socket.send(JSON.stringify(command));

        command = {
            command: 'operator_get_welcome_message'
        };
        socket.send(JSON.stringify(command));

        command = {
            command: 'operator_get_timeout_message'
        };
        socket.send(JSON.stringify(command));
    });

    socket.addEventListener("message", function (e) {
        const message = JSON.parse(e.data);
        switch (message.command) {
            case 'operator_get_work_mode':
                let work_mode = message.content.work_mode;
                let select = document.getElementById('work-mode-select');
                switch (work_mode) {
                    case 'bot':
                        select.selectedIndex = 0;
                        break;
                    default:
                        select.selectedIndex = 1;
                }
                break;
            case 'operator_get_welcome_message':
                $('#welcome-message input').val(message.content.welcome_message);
                break;
            case 'operator_get_timeout_message':
                $('#timeout-message input').val(message.content.timeout_message);
                break;
        }
    });

    socket.addEventListener("close", function () {
        $('#status').html("Не подключено");
    });
}

connect();

setInterval(function () {
    let status = $('#status').html();
    if ('Не подключено' === status) {
        console.log('Подключаемся к серверу: ', socket_addr);
        connect();
    }
}, pingTimeout);

$('#work-mode-select').on('change', function () {
    let work_mode = $('option:selected', this).val();
    console.log(work_mode);
    let message = {
        command: 'operator_set_work_mode',
        content: {
            work_mode: work_mode
        }
    };
    message = JSON.stringify(message);
    socket.send(message);
});

$('#welcome-message button').on('click', function () {
    let welcome_message = $('#welcome-message input').val();
    let message = {
        command: 'operator_set_welcome_message',
        content: {
            welcome_message: welcome_message
        }
    };
    message = JSON.stringify(message);
    socket.send(message);
});

$('#timeout-message button').on('click', function () {
    let timeout_message = $('#timeout-message input').val();
    let message = {
        command: 'operator_set_timeout_message',
        content: {
            timeout_message: timeout_message
        }
    };
    message = JSON.stringify(message);
    socket.send(message);
});
