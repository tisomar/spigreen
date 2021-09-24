/**
 * Script contendo as janelas do atendimento, todas as fun??es cujos nomes terminam com 
 * Window (ChatWindow, ClientWindow etc..) herdam as propriedades da classe Window
 * 
 * @author  H?di Carlos Minin - hedicarlos@gmail.com
 */

function ChatWindow(id) {

    Window.apply(this, arguments);
    var parent = this;

    this.icon('image/talk_icon.png');
    this.bounds(0, 0, 340, 430);

    this.callStarted = false;
    this.messageFrom = 2;

    this.layoutOption = $('<div></div>').addClass('talk_options');
    this.layoutInfo = $('<div></div>').addClass('talk_info');
    this.layoutTalk = $('<div></div>').addClass('talk').attr('title', 'Mensagens enviadas e recebidas');
    this.layoutInput = $('<div></div>').addClass('talk_input');
    this.input = $('<textarea></textarea>').attr('title', 'Digite sua mensagem e pressione enter para enviar');
    this.layoutTyping = $('<span></span>');
    this.layoutAlert = $('<div></div>').addClass('talk_alert');
    this.endCallButton = $('<a></a>')
            .attr('href', 'javascript:void(0)')
            .attr('id', 'talk_end_call')
            .attr('title', 'Encerrar atendimento')
            .click(function() {
                parent.endCall();
            });
    this.sendEmailButton = $('<a></a>')
            .attr('href', 'javascript:void(0)')
            .attr('id', 'talk_send_email')
            .attr('title', 'Enviar email')
            .click(function() {
                SendEmail(parent.id);
            });

    $(this.layoutOption).append(this.sendEmailButton);
    $(this.layoutOption).append(this.endCallButton);
    $(this.layoutOption).append(this.layoutTyping);
    $(this.layoutInput).append(this.input);

    this.append(this.layoutInfo);
    this.append(this.layoutAlert);
    this.append(this.layoutTalk);
    this.append(this.layoutOption);
    this.append(this.layoutInput);

    $(this.input).keyup(function(e) {
        for (var i in _predefined_messages) {
            if (_predefined_messages[i].shortcut == $(this).val()) {
                $(this).val(_predefined_messages[i].message);
            }
        }
    });

    $(this.input).keydown(function(e) {

        _typing = 1;

        if ($(this).val().length > 300) {
            $(this).val($(this).val().substr(0, 300));
        }

        if (e.shiftKey == true) {
            switch (e.keyCode) {
                case 35:
                    parent.endCall();
                    break;
                case 46:
                    $(parent.layoutTalk).children('p').remove();
                    break;
            }
        }

        if (e.keyCode == 27) {
            parent.close();
        }

        if (e.keyCode == 13) {
            if (e.shiftKey == true) {
                parent.maximize(parent.isMaximized == false ? true : false);
                return false;
            }

            if (e.ctrlKey == false) {

                var message = $(this).val();

                if (message.replace(/\n/g, '') == '') {
                    setTimeout(function() {
                        parent.inputClear();
                    }, 10);
                    return false;
                }

                parent.send(message);

                addMessageToBuffer(parent.id, message);

                setTimeout(function() {
                    parent.inputClear();
                }, 10);

            } else {
                $(this).val($(this).val() + '\n')
            }
        }

    });

    this.onfocus = function() {
        setTimeout(function() {
            parent.inputFocus()
        }, 10);
    }

    this.info = function(text) {
        $(parent.layoutInfo).text(text);
        return this;
    }

    this.inputClear = function() {
        $(parent.input).val('');
    }

    this.inputFocus = function() {
        $(parent.input).focus();
    }

    this.showAlert = function(message) {
        $(parent.layoutAlert).text(message);
        $(parent.layoutAlert).show();
    }

    this.prepareCall = function() {

        if (parent.callStarted == true) {
            return this;
        }

        $(parent.layoutInfo).append($('<div></div>').addClass('simple_load').text('Iniciando atendimento...'));
        $(parent.input).hide();
        $(parent.endCallButton).hide();
        $(parent.sendEmailButton).hide();
        $(parent.layoutInput).css('backgroundColor', '#DDD');

        $.ajax({
            url: 'start_call.php',
            type: 'POST',
            data: ({client_id: parent.id}),
            timeout: 15000,
            success: function(data) {

                $(parent.endCallButton).show();
                $(parent.sendEmailButton).show();
                $(parent.layoutInfo).children('div').remove();

                switch (parseInt(data.call_status)) {
                    case 1:
                        parent.startCall(1);
                        break;
                    case 2:
                        parent.startCall(2);
                        break;
                    case 4:
                        $(parent.layoutInfo).text('Atendimento j� iniciado');
                        parent.showAlert('O atendimento a este cliente j� foi iniciado por outro atendente.');
                        $(parent.endCallButton).hide();
                        $(parent.sendEmailButton).hide();
                        break;
                }
            },
            error: function(XMLHttpRequest) {
                $(parent.layoutInfo).text('Falha ao iniciar atendimento, tente novamente');
            }
        });

        return this;
    }

    this.startCall = function(status) {

        parent.callStarted = true;

        $(parent.layoutInfo).text('Atendimento em andamento');
        $(parent.input).show();
        $(parent.layoutInput).css('backgroundColor', '#FFF');

        if (status == 1) {
            parent.send(_config.initialMessage.replace('{NAME}', parent.windowTitle));
            addMessageToBuffer(parent.id, _config.initialMessage.replace('{NAME}', parent.windowTitle));
        }

        parent.inputFocus();
        return this;

    }

    this.endCall = function() {

        if (parent.callStarted == true) {

            $(parent.layoutInfo).text('');
            $(parent.layoutInfo).append($('<div></div>').addClass('simple_load').text('Encerrando atendimento...'));
            $(parent.input).hide();
            $(parent.endCallButton).hide();
            $(parent.sendEmailButton).hide();
            $(parent.layoutInput).css('backgroundColor', '#DDD');

            $.ajax({
                url: 'end_call.php',
                type: 'POST',
                data: ({client_id: parent.id}),
                timeout: 15000,
                success: function(data) {

                    if (data.call_status == 3) {
                        parent.close();
                    }

                },
                error: function(XMLHttpRequest) {
                    $(parent.layoutInfo).text('Falha ao encerrar atendimento, tente novamente');
                }
            });

        } else {
            parent.close();
        }

        return this;

    }

    this.typing = function(typing) {
        $(parent.layoutTyping).text(parseInt(typing) == 1 ? 'Digitando' : '');
        return this;
    }

    this.send = function(message) {

        if (parent.messageFrom != 1) {
            $(parent.layoutTalk).append($('<p></p>').addClass('talk_user').text(_user.name + ':'));
            parent.messageFrom = 1;
        }
        $(parent.layoutTalk).append($('<p></p>').html(message.replace(/\n/g, '<br />')));
        $(parent.layoutTalk).scrollTop($(parent.layoutTalk).attr('scrollHeight'));

        return this;

    }

    this.addMessage = function(message) {

        if (parent.messageFrom != 0) {
            $(parent.layoutTalk).append($('<p></p>').addClass('talk_client').text(parent.windowTitle + ':'));
            parent.messageFrom = 0;
        }
        $(parent.layoutTalk).append($('<p></p>').html(message.replace(/\n/g, '<br />')));
        $(parent.layoutTalk).scrollTop($(parent.layoutTalk).attr('scrollHeight'));

        return this;
    }

}

function ClientWindow(id) {

    Window.apply(this, arguments);
    var parent = this;

    this.icon('image/client_icon.png');
    this.bounds(0, $(document).width() - 360, 280, 500);
    this.closeable(false);

    this.lastClient = 0;

    this.layoutList = $('<div></div>').addClass('client_list');
    this.layoutInfo = $('<div></div>').addClass('client_list_info').text('Inicializando...');
    this.layoutStatus = $('<div></div>').addClass('client_list_status');

    this.append(this.layoutInfo);
    this.append(this.layoutList);
    this.append(this.layoutStatus);

    this.info = function(text) {
        $(parent.layoutInfo).text(text);
    }

    this.updateStatus = function() {
        $(parent.layoutStatus).html('&Uacute;ltima atualiza&ccedil;&atilde;o: ' + getCurrentTime())
    }

    this.addUsers = function(list) {

        if (list == null || list == undefined) {
            return this;
        }

        $(parent.layoutInfo).text(list.length > 0 ? list.length + ' cliente(s) conectado(s)' : 'Nenhum cliente conectado');
        $(parent.layoutList).empty();

        for (var i in list) {

            var client = list[i];

            if (client.client_id > parent.lastClient) {
                parent.lastClient = client.client_id;
                if (_browserFocused == false) {
                    if (_options.enableSound == true) {
                        flashControl('sound').play();
                    }
                    titleScroll(0, _config.windowTitle + ' - Novo cliente conectado...');
                }
            }

            var clientStatus = client.status == 1 ? 'Aguardando atendimento' : 'Atendimento em andamento';
            var listClass = client.status == 1 ? 'status_wait' : 'status_talk';

            var a = $('<a></a>')
                    .attr('href', 'javascript:void(0)')
                    .attr('client_id', client.client_id)
                    .attr('client_name', client.name)
                    .attr('title', 'Iniciar atendimento')
                    .text(client.name)
                    .click(function() {
                        new ChatWindow($(this).attr('client_id'))
                                .title($(this).attr('client_name'))
                                .build()
                                .prepareCall()
                                .focus();
                    }).append(
                    $('<span></span>').text(clientStatus)
                    ).addClass(listClass);

            $(parent.layoutList).append(a);
        }

        return this;
    }

}

function HistoryWindow(id) {

    Window.apply(this, arguments);
    var parent = this;

    this.title('Hist�rico de atendimentos');
    this.icon('image/history_icon.png');
    this.bounds(0, 0, 950, 500);
    this.user_id = 0;

    this.layoutUser = $('<div></div>').attr('id', 'user_history');
    this.layoutClient = $('<div></div>').attr('id', 'client_history').text('Selecione um atendente');
    this.layoutTalk = $('<div></div>').attr('id', 'talk_history');

    $(this.layoutUser).append($('<label></label>').text('Data:'))
            .append($('<input />').attr('type', 'text').attr('id', 'history_date').attr('maxlength', '10').val(getCurrentDate()))
            .append($('<div></div>').attr('id', 'user_history_list'));

    this.append(this.layoutUser);
    this.append(this.layoutClient);
    this.append(this.layoutTalk);

    this.loadUsers = function() {

        $('#user_history_list').empty().text('Buscando antendentes...');

        $.ajax({
            url: 'history_user.php',
            type: 'POST',
            timeout: 15000,
            success: function(data) {

                $('#user_history_list').empty();

                for (var i in data.users) {
                    $('#user_history_list').append(
                            $('<a></a>')
                            .attr('href', 'javascript:void(0)')
                            .attr('user_id', data.users[i].user_id)
                            .text(data.users[i].name)
                            .click(function() {
                                parent.loadClients(this);
                            })
                            );
                }

            },
            error: function(XMLHttpRequest) {
                $('#user_history_list').text('Falha ao carregar atendentes, tente novamente');
            }
        });

    }

    this.loadClients = function(obj) {

        $('#user_history_list > a').removeClass('history_selected');
        $(obj).addClass('history_selected');

        parent.user_id = $(obj).attr('user_id');

        $(parent.layoutClient).empty().text('Buscando clientes...');
        $(parent.layoutTalk).empty();

        $.ajax({
            url: 'history_client.php',
            type: 'POST',
            data: ({user_id: parent.user_id, call_date: $('#history_date').val()}),
            timeout: 15000,
            success: function(data) {

                if (data.hasErrors) {
                    $(parent.layoutClient).empty().text('Selecione um atendente');
                    showErrors(data);
                } else {

                    $(parent.layoutClient).empty();

                    if (data.clients.length == 0) {
                        $(parent.layoutClient).text('Nenhum atendimento nesta data');
                    } else {
                        $(parent.layoutTalk).text('Selecione um cliente');

                        for (var i in data.clients) {
                            $(parent.layoutClient).append(
                                    $('<a></a>')
                                    .attr('href', 'javascript:void(0)')
                                    .attr('client_id', data.clients[i].client_id)
                                    .attr('title', 'Visualizar hit?rico')
                                    .text(data.clients[i].name)
                                    .click(function() {
                                        parent.loadTalk(this);
                                    })
                                    .append($('<span></span>').text(data.clients[i].email))
                                    );
                        }
                    }

                }

            },
            error: function(XMLHttpRequest) {
                $(parent.layoutClient).text('Falha ao carregar clientes, tente novamente');
            }
        });

    }

    this.loadTalk = function(obj) {

        $(parent.layoutClient).children('a').removeClass('history_selected');
        $(obj).addClass('history_selected');

        var client_id = $(obj).attr('client_id');

        $(parent.layoutTalk).empty().text('Buscando hist�rico...');

        $.ajax({
            url: 'history_message.php',
            type: 'POST',
            data: ({user_id: parent.user_id, client_id: client_id}),
            timeout: 15000,
            success: function(data) {

                $(parent.layoutTalk).empty();

                var type = 2;
                var p = null;

                if (data.messages.length == 0) {
                    $(parent.layoutTalk).text('Nenhuma mensagem encontrada');
                } else {

                    for (var i in data.messages) {
                        if (type != data.messages[i].type) {
                            $(parent.layoutTalk).append(
                                    $('<span></span>')
                                    .text(data.messages[i].type == 0 ? data.messages[i].user_name + ':' : data.messages[i].client_name + ':')
                                    .addClass(data.messages[i].type == 0 ? 'talk_user' : 'talk_client')
                                    );
                            p = $('<p></p>');
                            $(parent.layoutTalk).append(p);
                        }

                        type = data.messages[i].type;
                        $(p).append(
                                $('<span></span>')
                                .html('<strong>' + data.messages[i].time + '</strong>' + data.messages[i].message.replace(/\n/g, '<br />'))
                                );
                    }

                }
            },
            error: function(XMLHttpRequest) {
                $(parent.layoutTalk).text('Falha ao carregar mensagens, tente novamente');
            }
        });
    }

}

function UsersWindow(id) {

    Window.apply(this, arguments);
    var parent = this;

    var form = [
        {field: 'text', label: 'Nome:', attributes: {id: 'user_add_name'}},
        {field: 'text', label: 'E-mail:', attributes: {id: 'user_add_email'}},
        {field: 'text', label: 'Usu�rio:', attributes: {id: 'user_add_user'}},
        {field: 'select', label: 'Tipo de usu�rio:', attributes: {id: 'user_add_level'},
            options: [
                {value: '2', label: 'Atendente'},
                {value: '1', label: 'Administrador'}
            ]
        },
        {field: 'password', label: 'Senha:', attributes: {id: 'user_add_password'}},
        {field: 'password', label: 'Repita senha:', attributes: {id: 'user_add_confirm_password'}}
    ];

    this.edit_id = 0;
    this.title('Usu�rios');
    this.icon('image/user_icon.png');
    this.bounds(0, 0, 900, 470);
    this.resizable(false);
    this.maximizable(false);

    this.layoutListUser = $('<div></div>').attr('id', 'user_list');
    this.layoutAddUser =
            $('<div></div>')
            .addClass('user_add')
            .addClass('form')
            .append($('<h3></h3>').text('Adicionar usu�rio'))
            .append(formBuilder(form))
            .append(
                    $('<a></a>')
                    .attr('href', 'javascript:void(0)')
                    .text('Enviar')
                    .click(function() {

                        $.ajax({
                            url: parent.edit_id == 0 ? 'user_add.php' : 'user_edit.php',
                            type: 'POST',
                            data: ({
                                user_id: parent.edit_id,
                                name: $('#user_add_name').val(),
                                email: $('#user_add_email').val(),
                                user: $('#user_add_user').val(),
                                level: $('#user_add_level').val(),
                                password: $('#user_add_password').val(),
                                confirm_password: $('#user_add_confirm_password').val()
                            }),
                            timeout: 15000,
                            success: function(data) {

                                if (data.hasErrors) {
                                    showErrors(data);
                                } else {
                                    showErrors(data);
                                    parent.cancel();
                                    parent.loadUsers();
                                }

                            },
                            error: function(XMLHttpRequest) {

                            }
                        });

                    })
                    )
            .append(
                    $('<a></a>')
                    .attr('href', 'javascript:void(0)')
                    .text('Cancelar')
                    .click(function() {
                        parent.cancel();
                    })
                    )
            .append(
                    $('<a></a>')
                    .attr('href', 'javascript:void(0)')
                    .attr('id', 'user_add_remove')
                    .text('Excluir')
                    .click(function() {
                        parent.remove();
                    })
                    );

    this.append(this.layoutAddUser);
    this.append(this.layoutListUser);

    this.remove = function() {

        $.ajax({
            url: 'user_remove.php',
            type: 'POST',
            data: ({user_id: parent.edit_id}),
            timeout: 15000,
            success: function(data) {

                if (data.hasErrors) {
                    showErrors(data);
                } else {
                    showErrors(data);
                    parent.cancel();
                    parent.loadUsers();
                }

            },
            error: function(XMLHttpRequest) {

            }
        });

    }

    this.edit = function(obj) {

        $(parent.layoutAddUser).children('h3').text('Editar usu�rio');

        parent.edit_id = $(obj).attr('user_id');

        $('#user_add_remove').show();
        $('#user_add_name').val($(obj).attr('user_name'));
        $('#user_add_email').val($(obj).attr('user_email'));
        $('#user_add_user, #user_add_password, #user_add_confirm_password').attr('disabled', true);

        var options = $('#user_add_level').find('option');
        for (var i = 0; i < options.length; i++) {
            if ($(options[i]).attr('value') == $(obj).attr('user_level')) {
                $(options[i]).attr('selected', 'selected');
            }
        }

    }

    this.cancel = function() {

        $(parent.layoutAddUser).children('h3').text('Adicionar usu�rio');

        parent.edit_id = 0;

        $('#user_add_remove').hide();
        $('#user_add_user, #user_add_password, #user_add_confirm_password').attr('disabled', false);
        $('#user_add_name, #user_add_email, #user_add_user, #user_add_password, #user_add_confirm_password').val('');

    }

    this.loadUsers = function() {

        $(parent.layoutListUser).empty().text('Buscando usu�rios...');

        $.ajax({
            url: 'user_list.php',
            type: 'POST',
            timeout: 15000,
            success: function(data) {

                $(parent.layoutListUser).empty();

                for (var i in data.users) {
                    $(parent.layoutListUser).append(
                            $('<a></a>')
                            .attr('href', 'javascript:void(0)')
                            .attr('user_id', data.users[i].user_id)
                            .attr('user_email', data.users[i].email)
                            .attr('user_level', data.users[i].level)
                            .attr('user_name', data.users[i].name)
                            .text(data.users[i].name)
                            .append($('<span></span>').text(data.users[i].email))
                            .append($('<strong></strong>').text(data.users[i].level == 1 ? 'Administrador' : 'Atendente'))
                            .click(function() {
                                parent.edit(this);
                            })
                            );
                }

            },
            error: function(XMLHttpRequest) {
                $(parent.layoutListUser).text('Falha ao carregar usu�rios, tente novamente');
            }
        });

    }

}

function Options() {

    new Window('options')
            .title('Op��es')
            .icon('image/options_icon.png')
            .bounds(0, 0, 300, 200)
            .resizable(false)
            .maximizable(false)
            .append(
                    $('<div></div>')
                    .addClass('options')
                    .append(
                            $('<label></label>')
                            .text('Ativar sons')
                            .append(
                                    $('<input/>')
                                    .attr('type', 'checkbox')
                                    .attr('checked', _options.enableSound)
                                    .click(function() {
                                        _options.enableSound = $(this).attr('checked') == true ? true : false;
                                    })
                                    )
                            ).append(
                    $('<label></label>')
                    .text('Mostrar conte�do da janela ao arrastar')
                    .append(
                            $('<input/>')
                            .attr('type', 'checkbox')
                            .attr('checked', _performanceMode == true ? false : true)
                            .click(function() {
                                _performanceMode = $(this).attr('checked') == true ? false : true;
                            })
                            )
                    )
                    )
            .build()
            .focus();

}

function ChangePassword() {

    var form = [
        {field: 'password', label: 'Senha atual:', attributes: {id: 'change_password_password'}},
        {field: 'password', label: 'Nova senha:', attributes: {id: 'change_password_new_password'}},
        {field: 'password', label: 'Repita Nova senha:', attributes: {id: 'change_password_confirm_password'}}
    ];

    new Window('changePassword')
            .title('Alterar senha')
            .icon('image/password_icon.png')
            .bounds(0, 0, 400, 250)
            .resizable(false)
            .maximizable(false)
            .append(
                    $('<div></div>')
                    .addClass('form')
                    .append(formBuilder(form))
                    .append(
                            $('<a></a>')
                            .attr('href', 'javascript:void(0)')
                            .text('Enviar')
                            .click(function() {

                                $.ajax({
                                    url: 'change_password.php',
                                    type: 'POST',
                                    data: ({
                                        password: $('#change_password_password').val(),
                                        new_password: $('#change_password_new_password').val(),
                                        confirm_password: $('#change_password_confirm_password').val()
                                    }),
                                    timeout: 15000,
                                    success: function(data) {

                                        if (data.hasErrors) {
                                            showErrors(data);
                                        } else {
                                            getWindow('changePassword').close();
                                            showErrors(data);
                                        }

                                    },
                                    error: function(XMLHttpRequest) {
                                        $(parent.layoutTalk).text('Falha ao buscar mensagens, tente novamente');
                                    }
                                });

                            })
                            )
                    )
            .build()
            .focus();

}

function SendEmail(id) {

    var client = findClient(id);

    var form = [
        {field: 'text', label: 'Para:', attributes: {id: 'send_email_to', value: client.email}},
        {field: 'text', label: 'Assunto:', attributes: {id: 'send_email_subject'}},
        {field: 'textarea', label: 'Mensagem:', attributes: {id: 'send_email_message', cols: 40, rows: 5, value: client.name}}
    ];

    new Window('sendEmail')
            .title('Enviar E-mail - ' + client.name)
            .icon('image/email_icon.png')
            .bounds(0, 0, 600, 380)
            .resizable(false)
            .maximizable(false)
            .append(
                    $('<div></div>')
                    .addClass('form')
                    .addClass('send_email')
                    .append(formBuilder(form))
                    .append(
                            $('<a></a>')
                            .attr('href', 'javascript:void(0)')
                            .text('Enviar')
                            .click(function() {

                                $.ajax({
                                    url: 'send_email.php',
                                    type: 'POST',
                                    data: ({
                                        from: _user.email,
                                        to: $('#send_email_to').val(),
                                        subject: $('#send_email_subject').val(),
                                        message: $('#send_email_message').val()
                                    }),
                                    timeout: 15000,
                                    success: function(data) {

                                        if (data.hasErrors) {
                                            showErrors(data);
                                        } else {
                                            getWindow('sendEmail').close();
                                            showErrors(data);
                                        }

                                    },
                                    error: function(XMLHttpRequest) {

                                    }
                                });

                            })
                            )
                    )
            .build()
            .focus();

}

function showErrors(data) {

    var alertDialog = $('<div></div>').addClass('alert');
    for (var i in data.errors) {
        $(alertDialog).append($('<span></span>').text(data.errors[i]));
    }

    for (var i in data.messages) {
        $(alertDialog).append($('<span></span>').text(data.messages[i]));
    }

    new Window('alertDialog')
            .title('Aten��o')
            .icon('image/alert_icon.png')
            .bounds(0, 0, 470, 160)
            .resizable(false)
            .maximizable(false)
            .minimizable(false)
            .append(alertDialog)
            .append(
                    $('<a></a>')
                    .addClass('alert_close')
                    .attr('href', 'javascript:void(0)')
                    .text('Ok')
                    .click(function() {
                        getWindow('alertDialog').close();
                    })
                    )
            .build()
            .focus();

}


function Users() {

    new UsersWindow('userManager')
            .build()
            .focus()
            .loadUsers();

}

function History() {

    new HistoryWindow('chatHistory')
            .build()
            .focus()
            .loadUsers();

}

function About() {

    var html = new Array();
    html.push('<div class="about">');
    html.push('<h2>' + _info.name + ' - ' + _info.version + '</h2>');
    html.push('<p>' + _info.author + ' <br /> ' + _info.email + '</p>');
    html.push('<ul>');
    html.push('<li>Livre para uso e  modifica��es :)</li>');
    html.push('<li>Mantenha os cr�ditos :)</li>');
    html.push('<li>Cr�ticas e sugest�es ser�o bem vindas :)</li>');
    html.push('<li>Seja feliz, utilize o Firefox ou Chrome :)</li>');
    html.push('</ul>');
    html.push('</div>');


    new Window('about')
            .title('Sobre o brTalk')
            .icon('image/info_icon.png')
            .bounds(0, 0, 400, 300)
            .content(html.join(''))
            .resizable(false)
            .maximizable(false)
            .minimizable(false)
            .build()
            .focus();

}

function Tips() {

    var html = new Array();
    html.push('<div class="info">');
    html.push('<p>Pressione Shift + Enter na caixa de digita��o para maximizar/restaurar a janela de conversa��o.</p>');
    html.push('<p>Pressione Shift + Delete na caixa de digita��o para limpar a conversa.</p>');
    html.push('<p>Pressione Shift + End na caixa de digita��o para encerrar o atendimento.</p>');
    html.push('</div>');

    new Window('info')
            .title('Dicas de utiliza��o')
            .icon('image/tip_icon.png')
            .bounds(0, 0, 400, 400)
            .content(html.join(''))
            .build()
            .focus();

}

function PredefinedMessages() {

    var div = $('<div></div>')
            .addClass('predefined_messages')
            .append(
                    $('<h4></h4>').text('As mensagens pr�-definidas encontram-se no arquivo: atendente/js/config.js')
                    );

    for (var i in _predefined_messages) {
        $(div).append(
                $('<p></p>').html('<strong>' + _predefined_messages[i].shortcut + '</strong>' + _predefined_messages[i].message)
                );
    }


    new Window('predefined_messages')
            .title('Mensagens pr�-definidas')
            .icon('image/keyboard_icon.png')
            .bounds(0, 0, 400, 400)
            .append(div)
            .build()
            .focus();

}