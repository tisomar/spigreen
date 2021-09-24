<?php $data_extenso = sprintf('%s, %s de %s de %s', get_dia_semana(date('N')), date('d'), get_mes_extenso(date('m')), date('Y')); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <meta name="viewport" content="width=device-width; initial-scale=1.0; maximum-scale=1.0;">

    <title>Email Template</title>

    <style type="text/css">

        body{
            width: 100%;
            background-color: #<?php echo $this->style('background_body') ?>;
            margin:0;
            padding:0;
            -webkit-font-smoothing: antialiased;
            font-family: arial;
        }

        html{
            width: 100%;
        }

        table, td, th {
            font-size: 12px;
            border: 0;
            vertical-align: top;
        }

        a {
            color: <?php echo $this->style('color-principal') ?>;
        }

    </style>

</head>

<body leftmargin="0" topmargin="0" marginheight="0" marginwidth="0" cz-shortcut-listen="true">

<table border="0" cellpadding="0" cellspacing="0" width="100%">

    <tbody>

    <tr><td height="30"></td></tr>

    <tr bgcolor="#<?php echo $this->style('background_body') ?>">
        <td align="center" bgcolor="#<?php echo $this->style('background_body') ?>" valign="top" width="100%">

            <!--  top header -->
            <table class="container" align="center" border="0" cellpadding="0" cellspacing="0" width="600">
                <tbody>
                <tr bgcolor="<?php echo $this->style('color-principal'); ?>"><td height="15"></td></tr>
                <tr bgcolor="<?php echo $this->style('color-principal'); ?>">
                    <td align="center">
                        <table class="container-middle" align="center" border="0" cellpadding="0" cellspacing="0" width="560">
                            <tbody>
                            <tr>
                                <td>
                                    <table class="top-header-left" align="left" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td align="center">
                                                <table class="date" border="0" cellpadding="0" cellspacing="0">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <img style="display: block;" src="<?php echo asset('/mailer/images/icon-cal.png') ?>" alt="icon 1" width="13">
                                                        </td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td style="<?php echo $this->style('font-header'); ?>">
                                                            <singleline>
                                                                <?php echo $data_extenso; ?>
                                                            </singleline>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>

                                    <table class="top-header-right" align="left" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr><td height="20" width="30"></td></tr>
                                        </tbody>
                                    </table>

                                    <table class="top-header-right" align="right" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td align="center">
                                                <table class="tel" align="center" border="0" cellpadding="0" cellspacing="0">
                                                    <tbody>
                                                    <tr>
                                                        <td>
                                                            <img style="display: block;" src="<?php echo asset('/mailer/images/icon-tel.png') ?>" alt="icon 2" width="17">
                                                        </td>
                                                        <td>&nbsp;&nbsp;</td>
                                                        <td style="<?php echo $this->style('font-header'); ?>">
                                                            <singleline>
                                                                Contato: <?php echo Config::get('empresa_telefone_contato') ?>
                                                            </singleline>
                                                        </td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <tr bgcolor="<?php echo $this->style('color-principal'); ?>"><td height="10"></td></tr>
                </tbody>
            </table>
            <!--  end top header  -->


            <!-- main content -->
            <table class="container" align="center" border="0" cellpadding="0" cellspacing="0" width="600" bgcolor="ffffff">

                <tbody>

                <!--Header-->
                <tr><td height="30"></td></tr>

                <tr>
                    <td>
                        <table class="container-middle" align="center" border="0" cellpadding="0" cellspacing="0" width="560">
                            <tbody>
                            <tr>
                                <td>
                                    <table class="logo" align="center" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr>
                                            <td align="center">
                                                <a href="" style="display: block;">
                                                    <img style="display: block;" src="<?php echo  Config::getLogo()->forceUrlImageResize('height=45') ?>" alt="logo" height="45">
                                                </a>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>

                <tr><td height="30"></td></tr>
                <!-- end header -->

                <!-- main section -->
                <tr>
                    <td>
                        <table class="container-middle" align="center" border="0" cellpadding="0" cellspacing="0" width="560">

                            <tbody>

                            <tr><td height="10"></td></tr>

                            <tr>
                                <td>
                                    <?php echo $this->block('content') ?>
                                </td>
                            </tr>

                            <tr><td height="30"></td></tr>

                            </tbody>

                        </table>
                    </td>
                </tr>
                <!-- end main section -->


                <!-- prefooter -->
                <tr>
                    <td>
                        <table class="container-middle" align="center" border="0" cellpadding="0" cellspacing="0" width="560">
                            <tbody>
                            <tr>
                                <td>
                                    <table class="nav" align="center" border="0" cellpadding="0" cellspacing="0">
                                        <tbody>
                                        <tr><td height="10"></td></tr>
                                        <tr>
                                            <td style="font-size: 13px; font-family: Helvetica, Arial, sans-serif;" align="center">
                                                <table align="center" border="0" cellpadding="0" cellspacing="0">
                                                    <tbody>
                                                    <tr>
                                                        <?php $separator = ''; ?>
                                                        <?php foreach(RedePeer::getAtivos() as $objRede):
                                                            $img = null; ?>
                                                            <?php echo $separator; ?>

                                                            <?php
                                                            $domain = str_ireplace(array('www.', '.com', '.br'), '', parse_url($objRede->getLink(), PHP_URL_HOST));
                                                            switch ($domain) {

                                                                case 'plus.google':
                                                                    $img =  '<img style="display: block;" src="' . asset('/mailer/images/social-google.png') .'">';
                                                                    break;

                                                                case 'facebook':
                                                                    $img =  '<img style="display: block;" src="' . asset('/mailer/images/social-facebook.png') .'">';
                                                                    break;

                                                                case 'youtube':
                                                                    $img =  '<img style="display: block;" src="' . asset('/mailer/images/social-youtube.png') . '">';
                                                                    break;

                                                                case 'instagram':
                                                                    $img =  '<img style="display: block;" src="' . asset('/mailer/images/social-instagram.png') . '">';
                                                                    break;

                                                                case 'twitter':
                                                                    $img =  '<img style="display: block;" src="' . asset('/mailer/images/social-twitter.png') . '">';
                                                                    break;

                                                                case 'linkedin':
                                                                case 'br.linkedin':
                                                                    $img =  '<img style="display: block;" src="' . asset('/mailer/images/social-linkedin.png') . '">';
                                                                    break;

                                                                case 'wordpress':
                                                                    $img =  '<img style="display: block;" src="' . asset('/mailer/images/social-wordpress.png') . '">';
                                                                    break;

                                                            }

                                                            if ($img != null):
                                                                ?>
                                                                <td>
                                                                    <a style="display: block; width: 16px;" href="<?php echo $objRede->getLink(); ?>">
                                                                        <?php echo $img; ?>
                                                                    </a>
                                                                </td>
                                                                <?php $separator = '<td>&nbsp;&nbsp;&nbsp;</td>'; ?>
                                                            <?php endif; ?>

                                                        <?php endforeach; ?>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                            </td>
                                        </tr>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </td>
                </tr>
                <!-- end prefooter  -->

                <tr><td height="20"></td></tr>

                <tr>
                    <td style="color: #939393; font-size: 11px; font-weight: normal; font-family: Helvetica, Arial, sans-serif;" class="prefooter-subheader" align="center">
                        <span style="color: #<?php echo $this->style('color-principal'); ?>">Contato:</span> <?php echo Config::get('empresa_telefone_contato') ?>    &nbsp;&nbsp;&nbsp;
                        <span style="color: #<?php echo $this->style('color-principal'); ?>">E-mail:</span> <?php echo Config::get('email_contato') ?>
                    </td>
                </tr>

                <tr><td height="30"></td></tr>

                </tbody>
            </table>
            <!--end main Content -->

            <!-- end footer-->
        </td>
    </tr>

    <tr><td height="30"></td></tr>

    </tbody>
</table>



</body>
</html>