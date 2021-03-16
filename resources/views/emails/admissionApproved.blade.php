<?php
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
?>


<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">

    <head>

        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />

        <title>Redeemer's University Admission Letter</title>

        <style type="text/css">

            body,td,th {

                font-family: Arial, Helvetica, sans-serif;

                font-size: 14px;

                text-align: justify;

            }

            .run {

                color: #90C;

                text-align: right;

            }

            h2 {

                font-size: 24px;

                color: #009;

            }

            .new {

                font-weight: bold;

            }

            .surname {

                font-weight: bold;

            }

            body {

                background-image: url(logo-bg.jpg);

                background-repeat: no-repeat;

                margin-left: 5px;

            }

        </style>

    </head>



    <body>



        <table width="800" border="0" align="center" cellpadding="5" cellspacing="5">

            <tr>

                <td height="100" colspan="2" align="right" valign="top">
                <h2 align="right">
                    <strong><img src="logo.jpg" width="172" height="141" align="left" />REDEEMER&rsquo;S UNIVERSITY </strong>
                    </h2>

                    <p align="right"><font color="#990099"><i>P.M.B. 230,Ede,<br />

                            </i></font><font color="#990099"> <i>        

                                Osun State Nigeria,</i></font><br />

                        <font color="#990099"><i> Tel: +234 (0)700-700-8000 ; +234 (0)807-300-4715<br />

                                Website:  <u>www.run.edu.ng</u>	E-mail:  <u>registrar@run.edu.ng</u></i>

                        </font> </p><hr /></td>

            </tr>

            <tr>

                <td width="36%">RUN/REG/ADM/<?php echo trim($s_program_code); ?>/<?php echo substr($s_session_admitted,2,2) ; ?>-<?php echo substr($s_session_admitted,7,2) ; ?>/<?php echo $sRefNo ?></td>

                <td width="37%"><div align="right"><?php echo $s_date_admitted; ?></div></td>

            </tr>

            <tr>

                <td colspan="2"><p><span class="surname"><?php echo $s_surname; ?></span>, <?php echo $s_firstname; ?><br />

<?php echo $s_adddress_resident; ?><br />

<?php echo $s_city_resident; ?>,<br />

<?php echo $s_state_resident; ?> State</p></td>

            </tr>

            <tr>

                <td colspan="2">Dear <?php echo $s_title_given_gender; ?> <?php echo $s_surname; ?>, </td>

            </tr>

            <tr>

                <td colspan="2"><strong><u>OFFER OF PROVISIONAL ADMISSION: DEGREE PROGRAMME</u></strong></td>

            </tr>

            <tr>

                <td colspan="2" valign="top"><p>With reference to your application for  admission to a degree programme in this University and further to the screening  exercise, I have the pleasure to inform you that you have been offered  provisional admission to study for a degree course leading to the award of  <?php echo $s_Degree; ?>  Degree  in <strong> <?php echo $s_program_name; ?> </strong>of the 

                        <strong> <?php echo $Department; ?>  of  <?php echo $s_department_name; ?>.</strong></p>

                    <p>The duration of the programme is <?php echo $duration; ?> years.</p>

                    <p>Please note that this offer is provisional  and can be revoked if you fail to produce the documents listed in the 
                        <strong><u><a href="#">

                                    Notice to Candidates offered Provisional  Admission.</a></u></strong></p>

                    <p>If you accept the offer, please complete the  <strong><u><a href="acceptanceform.php?app_id=<?php echo $_REQUEST['app_id']; ?>">Acceptance Form</a></u></strong> and  return with an evidence  of payment of the Acceptance/Processing Fee (non-refundable deposit) of <strong><u><?php echo $s_non_refundable_deposit ; ?></u> only, not later than <?php $s_date_admitted = date('Y-m-d', strtotime($s_date_admitted. ' + 14 days')) ; echo date_format(date_create($s_date_admitted), ' l\, jS F\, Y'); ?>.</strong></p>
                    <p>Please note also that  the offer may be withdrawn if, within the stipulated time, you have not  completed and returned the Acceptance Form.</p>
                    <p>The University has since resumed for the <?php echo $s_session_admitted ;?> academic session on <strong><?php echo $s_resumption_date  ; ?></strong>. Please come along with the completed acceptance form to the registration venue.  <!-- Registration closes on <strong><?php echo $s_registration_closes  ; ?>.</strong></p> --></td>

            </tr>

            <tr>

                <td colspan="2"><p>Accept my congratulations.</p>

                    <img  src="Registrar_Signature_Mofoluso.jpg" /><br />       <span id="yui_3_7_2_1_1358250129829_2527" lang="EN-GB" xml:lang="EN-GB"><strong>Olukayode E. Akindele,</strong></span><br />
                    REGISTRAR</td>

            </tr>

            <tr>

                <td colspan="2"><p><strong><em>Please  download the underlisted documents from the University Website</em></strong><br />
                        (i)         Admission Acceptance Form<br />
                        (ii)        Notice to Candidates offered Provisional  Admission<br />
                        (iii)       Schedule of Fees</p>
                    <p>&nbsp;</p></td>

            </tr>

            <tr>

                <td height="40" colspan="2">&nbsp;</td>

            </tr>

        </table>

    </body>

</html>

