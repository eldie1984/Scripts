import smtplib
from email.mime.text import MIMEText
import email.utils
from email.mime.multipart import MIMEMultipart

initRowHtml="""<td style="border: 1px solid #808080; font-family: Arial; font-size: 14px; text-align: left;  color: #B45F04;"><strong>"""
endRowHtml="</strong></td>"
initRowHtmlData="""<td style="border: 1px solid #808080; font-family: Arial; font-size: 13px; text-align: left;">"""
endRowHtmlData="</td>"
html="""<table style="width: 98%; border: 1px solid #808080;" border="0" cellspacing="0" cellpadding="1">
        <tbody>
        <tr align="center" valign="middle">"""
to=''
titleFrom=''