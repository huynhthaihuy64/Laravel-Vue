<body style="background-color:grey">
    <table align="center" border="0" cellpadding="0" cellspacing="0"
           width="550" bgcolor="white" style="border:2px solid black">
        <tbody>
            <tr>
                <td align="center">
                    <table align="center" border="0" cellpadding="0"
                           cellspacing="0" class="col-550" width="550">
                        <tbody>
                            <tr>
                                <td align="center" style="background-color: #4cb96b;
                                           height: 50px;">
   
                                    <a href="#" style="text-decoration: none; ">
                                        <p style="color:white;
                                                  font-weight:bold;
                                                  display:flex;
                                                  justify-content:center;
                                                  align-items:center;">
                                            Bubble Cafe <img src="http://localhost:8000/images/Bubble_Cafe-removebg-preview.png" style="margin-left: 10px" height="50px"/>
                                        </p>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </td>
            </tr>
            <tr style="height: 300px;">
                <td align="center" style="border: none;
                           border-bottom: 2px solid #4cb96b; 
                           padding-right: 20px;padding-left:20px">
   
                    <p style="font-weight: bolder;font-size: 42px;
                              letter-spacing: 0.025em;
                              color:black;">
                        Hello {{$detail['name']}}!
                        <br> We have received a contact email from {{$detail['email']}}
                    </p>
                </td>
            </tr>
   
            <tr style="display: inline-block;">
                <td style="height: 150px;
                           padding: 20px;
                           border: none; 
                           border-bottom: 2px solid #361B0E;
                           background-color: white;">
                     
                    <h2 style="text-align: left;
                               align-items: center;">
                        Subject: {{$detail['subject']}}
                   </h2>
                    <p class="data"
                       style="text-align: justify-all;
                              align-items: center; 
                              font-size: 15px;
                              padding-bottom: 12px;">
                        Content: {{$detail['message']}}
                    </p>
                    <h2 style="text-align: left;
                               align-items: center;">
                        Thank you for contacting us, we will handle it as soon as possible.
                   </h2>
                   <h2 style="text-align: left;
                               align-items: center;">
                        If you need any information, please contact us by:
                   </h2>
                    <p>
                        <a href=
"http://localhost:8000/contact"
                           style="text-decoration: none; 
                                  color:black; 
                                  border: 2px solid #4cb96b; 
                                  padding: 10px 30px;
                                  font-weight: bold;"> 
                           Contact
                      </a>
                    </p>
                </td>
            </tr>
            <tr style="border: none; 
            background-color: #4cb96b; 
            height: 40px; 
            color:white; 
            padding-bottom: 20px; 
            text-align: center;">
                  
<td height="40px" align="center">
    <p style="color:white; 
    line-height: 1.5em;">
    Bubble Cafe
    </p>
    <a href="https://twitter.com/home" 
    style="border:none;
           text-decoration: none; 
           padding: 5px;"> 
             
    <img height="30" 
    src=
"https://extraaedgeresources.blob.core.windows.net/demo/salesdemo/EmailAttachments/icon-twitter_20190610074030.png" 
    width="30" /> 
    </a> 
      
    <a href="https://www.instagram.com/"
    style="border:none;
    text-decoration: none; 
    padding: 5px;"> 
      
    <img height="30" 
    src=
"https://extraaedgeresources.blob.core.windows.net/demo/salesdemo/EmailAttachments/icon-linkedin_20190610074015.png" 
width="30" /> 
    </a>
      
    <a href="https://www.tiktok.com/en/" 
    style="border:none;
    text-decoration: none;
    padding: 5px;"> 
      
    <img height="20"
    src=
"https://extraaedgeresources.blob.core.windows.net/demo/salesdemo/EmailAttachments/facebook-letter-logo_20190610100050.png" 
        width="24" 
        style="position: relative; 
               padding-bottom: 5px;" />
    </a>
</td>
</tr>
<tr>
<td style="font-family:'Open Sans', Arial, sans-serif;
           font-size:11px; line-height:18px; 
           color:#999999;" 
    valign="top"
    align="center">
<a href="#"
   target="_blank" 
   style="color:#999999; 
          text-decoration:underline;">PRIVACY STATEMENT</a> 
          | <a href="#" target="_blank" 
          style="color:#999999; text-decoration:underline;">TERMS OF SERVICE</a> 
          | <a href="#"
          target="_blank" 
          style="color:#999999; text-decoration:underline;">RETURNS</a><br>
                  © 2023 Bubble Cafe. All Rights Reserved.<br>
                  If you do not wish to receive any further 
                  emails from us, please
                  <a href="#"
                  target="_blank"
                  style="text-decoration:none; 
                         color:#999999;">unsubscribe</a>
            </td>
              </tr>
            </tbody></table></td>
        </tr>
        <tr>
          <td class="em_hide"
          style="line-height:1px;
                 min-width:700px;
                 background-color:#ffffff;">
              <img alt="" 
              src="images/spacer.gif" 
              style="max-height:1px; 
              min-height:1px; 
              display:block; 
              width:700px; 
              min-width:700px;" 
              width="700"
              border="0" 
              height="1">
              </td>
        </tr>
        </tbody>
    </table>
</body>