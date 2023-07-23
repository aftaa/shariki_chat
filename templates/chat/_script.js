function command(command) {
    command = JSON.stringify(command);
    socket.send(command);
}

function setStatus(connected) {
    status = connected;
    if (connected) {
        $('#status').addClass('connected');
    } else {
        $('#status').removeClass('connected');
    }
}

function drawSession(session, prepend) {
    let $div = $('footer .session').clone(true, true);
    $div.attr('id', 'session-' + session.name);
    $('.open', $div).attr('data-session', session.name);
    $('.archive', $div).attr('data-session', session.name);
    $('.message-count', $div).html(session.message_count);
    $('.last-message', $div).html(session.last_message);
    $('.started', $div).html(session.started);
    $('.chat', $div).attr('id', 'chat-' + session.name).hide();
    $('.messages', $div).attr('id', 'messages-' + session.name).html('');
    $('input, button', $div).attr('data-session', session.name);
    $('input', $div).attr('id', 'text-' + session.name);
    if (session.has_new_message) {
        $div.addClass('new-message');
    }
    if (!prepend) {
        $('main').append($div);
    } else {
        $('main').prepend($div);
    }
}

function drawMessage(message) {
    if ('none' !== $('#chat-' + message.session).css('display')) {
        let $messages = $('#messages-' + message.session);
        let $message = $('footer ' + (message.isOperator ? '.operator' : '.visitor')).clone();
        $('.text', $message).html(message.message);
        $('.datetime', $message).html(message.created);
        $messages.append($message);
        scrollToBottom(message.session);
    } else {
        $('#session-' + message.session).addClass('new-message').prependTo('main');
    }
}

function scrollToBottom(session) {
    let chatMessages = document.getElementById("messages-" + session);
    if (chatMessages) {
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
}

function sendMessage(session, text) {
    command({
        command: 'operator_add_message',
        session: session,
        message: text,
        name: 'Оператор',
        isOperator: true
    });

    if ($('main .session:first').attr('id') !== 'session-' + session) {
        $('#session-' + session).prependTo('main');
    }
}

function updateSession(message) {
    let $div = $('#session-' + message.session);
    if ($div.length) {
        $('.message-count', $div).html(message.message_count);
        $('.last-message', $div).html(message.last_message);
    }
}

$(function () {
    $('.session .open').on('click', function () {
        $('#messages-' + this.dataset.session).html('');
        command({
            command: 'new_message',
            session: this.dataset.session
        });
        $('.chat').hide();
        $('#chat-' + this.dataset.session).show();
        $('#session-' + this.dataset.session).removeClass('new-message');
    })

    $('button').on('click', function () {
        let session = this.dataset.session;
        let text = $('#text-' + session).val();
        if ($.trim(text)) {
            sendMessage(session, text);
            $('#text-' + session).val('')
        }
    })

    $('input').on('keypress', function (event) {
        if (13 === event.keyCode) {
            let session = this.dataset.session;
            let text = this.value;
            if ($.trim(text)) {
                sendMessage(session, text);
                this.value = '';
            }
        }
    })

    let width = $('.messages').width();
    $('.form input').width(width - 42);
    $('footer').hide();
});
