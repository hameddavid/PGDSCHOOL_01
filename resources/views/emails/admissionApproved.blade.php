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
                    <strong><img src="{{URL::asset('/logo/logo.jpg')}}" width="172" height="141" align="left" />REDEEMER&rsquo;S UNIVERSITY </strong>
                    </h2>

                    <p align="right"><font color="#990099"><i>P.M.B. 230,Ede,<br />

                            </i></font><font color="#990099"> <i>        

                                Osun State Nigeria,</i></font><br />

                        <font color="#990099"><i> Tel: +234 (0)700-700-8000 ; +234 (0)807-300-4715<br />

                                Website:  <u>www.run.edu.ng</u>	E-mail:  <u>registrar@run.edu.ng</u></i>

                        </font> </p><hr /></td>

            </tr>

            <tr>

            <td width="36%">RUN/REG/PG/ADM/{{$emailParams['progCode']}}/{{substr($emailParams['session'],2,2)}}-{{substr($emailParams['session'],7,2)}}/{{$emailParams['applicant_id']}}</td>

                <td width="37%"><div align="right">{{$emailParams['date_admitted']}}</div></td>

            </tr>

            <tr>

                <td colspan="2"><p><span class="surname">{{$emailParams['surname']}}</span>, {{$emailParams['firstname']}}<br />

                {{$emailParams['address']}}<br />


            </tr>

            <tr>

                <td colspan="2">Dear {{$emailParams['title']}}. {{$emailParams['surname']}}, </td>

            </tr>

            <tr>

                <td colspan="2"><strong><u>OFFER OF PROVISIONAL ADMISSION FOR POSTGRADUATE STUDIES:
                 {{$emailParams['session']}} ACADEMIC SESSION</u></strong></td>

            </tr>

            <tr>

                <td colspan="2" valign="top"><p>I am pleased to inform you that following
                 your application for postgraduate programme 
                in Redeemer’s University, you have been offered admission into
                <strong> {{$emailParams['apply_for']}}</strong> 
                Degree in <strong>{{$emailParams['programme']}}</strong> 
                in the Department of <strong>{{$emailParams['dept']}}</strong>,
                 Faculty of <strong>{{$emailParams['college']}}</strong>. 
                  <em>Your admission is for the {{$emailParams['semester']}} 
                of {{$emailParams['session']}} academic session </em>. </p>

                <p>The graduate programmes at Redeemer’s University are among the
                 very best in Nigeria. We have outstanding academic staff, 
                 and a stimulating intellectual atmosphere that enhances thorough research.</p>

                    <p>Your mode of study is full-time, and the minimum duration of your
                     programme is three (3) semesters, at the University permanent Campus, Ede, Osun State.</p>


                    <p>Please, contact the College of Postgraduate Studies as soon as possible for further information on registration. Late registration will attract a penalty of Ten Thousand Naira (N10, 000.00). At the point of registration, you will be required to present original copies of your credentials for sighting along with three (3) sets of photocopies and four (5) passport photographs. </p>

                   <p>The schedule of payment of school fees, including accommodation fee is available on the University Website (www.run.edu.ng/cpgs). The College may allow for payment in two (2) installments of 60% and 40% for First and Second Semesters respectively.</p>
            
                <p> Please note that the Redeemer’s University is a Faith-based Institution where moral values and Christian norms are entrenched.</p>
            </tr>

            <tr>

                <td colspan="2"><p>We look forward to meeting you early.</p>
                    <p>Yours truly,</p>
                    <img  src="{{URL::asset('/admin/adms.jpg')}}" /><br />      
                     <span id="yui_3_7_2_1_1358250129829_2527" lang="EN-GB"
                      xml:lang="EN-GB"><strong>Mr. E. K. Adeyanju,</strong>
                      </span><br />
                      Secretary, CPGS</td>

            </tr>


            <tr>

                <td height="40" colspan="2">&nbsp;</td>

            </tr>

        </table>

    </body>

</html>

