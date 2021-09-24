<?php
$formAction = Config::get('mailforweb.form.action');
if ($container->getRequest()->getMethod() == 'POST') {
    $email = $container->getRequest()->request->get('email');
    NewsletterPeer::save($email);
    if ($formAction != '') {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $formAction);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query(array('form' => array('email' => $email))));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_exec($ch);
        curl_close($ch);
    }   redirectTo(get_url_site() . '/mailforweb/lead/sucesso');
}
?>

<!DOCTYPE html>
<html>
<head>
    <script src="//code.jquery.com/jquery-1.11.3.min.js" type="text/javascript"></script>
    <link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css'>
    <style>
        body, input, button {font-family: 'Open Sans','open-sans', sans-serif;}
        main {
            position: fixed;
            width: 90%;
            padding: 30px 20px 5px;
            background: #fff;
            border-radius: 10px;
            box-sizing: border-box;
            left: 5%;
            top: 10%;

        }
        h1 {font: 700 22px/22px 'open-sans', 'Open Sans', 'OpenSansBold',sans-serif;}
        p {font: 400 16px 'open-sans', 'Open Sans', 'OpenSansRegular',sans-serif;}
        h1, p {
            color: #484848;
            margin: 0 0 15px;
        }
        .success img {vertical-align: middle;}
        .success {
            font-size: 20px;
            color: #3ab76e;
            text-shadow: 0px 1px 0px #fff;
        }
        input, #btn-enviar {
            font-size: 16px;
            box-sizing: border-box;
            display: block;
            width: 100%;
            border-radius: 6px;
            margin-bottom: 15px;
            line-height: 45px;
            outline: none;
        }
        input[type="email"] {
            padding-left: 15px;
            padding-right: 15px;
            background: #f6f6f6;
            border: 1px solid #DADADA;
            border-bottom-width: 2px;
            box-shadow: none;
        }
        input[type="email"]:focus {
            border-color: #777777;
        }
        [type="submit"] {
            background: #648d24;
            color: #FFF;
            font-size: 18px;
            border: none;
            border-bottom: 4px solid #3d5616;
            cursor: pointer;
        }
        [type="submit"]:disabled,
        [type="submit"]:active {
            margin-top: 17px;
            border-bottom: 2px solid #3d5616;
        }
        input[type="submit"].sending {
            border-bottom: 2px solid #3d5616;
            background: #648d24;
            background-image: url('<?php asset('/lead/assets/images/oval.svg') ?>');
            background-repeat: no-repeat;
            background-position: 2% center;
        }

        .initial-popup-close {
            position: absolute;
            top: 3px;
            right: 3px;
            background: transparent;
            border: 0;
            color: #484848;
            font-size: 22px;
            font-weight: 700;
            opacity: 0.65;
            cursor: pointer;
            transition: all 0.15s linear;
        }

        .initial-popup-close:hover {
            opacity: 1;
        }

        @media all and (min-width: 768px) {

            main {
                max-width: 630px;
                left: 50%;
                margin-left: -315px;
                top: 50%;
                margin-top: -142px;
                padding: 30px 40px 5px;
            }

        }
    </style>
</head>
<body>
<main>
    <div id="content">
        <?php if ($router->getArgument(0) == 'sucesso') : ?>
            <button class="initial-popup-close" type="button" title="Close (Esc)">×</button>
            <p class="success">
                <img src="data:image/png;base64,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">
                Seu e-mail foi cadastrado com sucesso!
            </p>
            <script>
                $(function() {
                    $('.initial-popup-close')
                    window.setTimeout(function() {
                        parent.$.magnificPopup.close();
                    }, 3000);
                })
            </script>
        <?php else : ?>
            <button class="initial-popup-close" type="button" title="Close (Esc)">×</button>
            <h1><?php echo Config::get('popup.title') ?></h1>
            <p><?php echo Config::get('popup.content') ?></p>
            <form id="form-newsletter" action="" method="post">
                <input type="email" name="email" placeholder="Digite seu e-mail..." required>
                <button type="submit" id="btn-enviar">Cadastrar agora!</button>
            </form>
            <script>
                $(function() {
                    $('body').on('submit', '#form-newsletter', function() {
                        $('#btn-enviar').attr('disabled', 'disabled').val('Enviando...').addClass('sending');
                    })
                })
            </script>
        <?php endif; ?>
        <script>
            $(function() {
                // Fecha o modal
                $('.initial-popup-close').click(function(){
                    parent.$.magnificPopup.close();
                });
            })
        </script>
    </div>
</main>
</body>
</html>
