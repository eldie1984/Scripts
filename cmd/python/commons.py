#Datos comunes a todas las aplicaciones

import smtplib
from email.mime.text import MIMEText
import email.utils
from email.mime.multipart import MIMEMultipart
import logging


# Datos de conexion a DB
db_host = "v480uprod.int.cmd.com.ar"
db_user = "openreports"
db_pass = "0p3nr1p"
db_database = "bac"

#datos de conexion a DB PROD

db_host_pr = "v130uprod.int.cmd.com.ar"
db_user_pr = "soporte"
db_pass_pr = "Soporte.Toca.Prod.con.CuidadO"

#datos de conexion a LY

db_host_ly = "v372uprod.int.cmd.com.ar"
db_user_ly = "consulta"
db_pass_ly = "c0nsult4"
db_database_ly = "libertya_prod"


#datos de conexion a LY

db_host_ly_pr = "v372uprod.int.cmd.com.ar"
db_user_ly_pr = "soporte"
db_pass_ly_pr = "s0p0rt36755"


db_host_cc = "s139uprod.int.cmd.com.ar"
db_user_cc = "ccupon_consulta"
db_pass_cc = "yoconsultoclubcupon"
db_database_cc = "clubcupon"

# Datos de envio de mails
sender=['soporte@cmd.com.ar']

# log
FORMAT = '%(asctime)-15s %(levelname)s %(message)s'
#### Clase del mails

class mail(object):
	initRowHtml="""<td style="border: 1px solid #808080; font-family: Arial; font-size: 14px; text-align: left;  color: #B45F04;"><strong>"""
	endRowHtml="</strong></td>"
	initRowHtmlData="""<td style="border: 1px solid #808080; font-family: Arial; font-size: 13px; text-align: left;">"""
	endRowHtmlData="</td>"
	html="""<table style="width: 98%; border: 1px solid #808080;" border="0" cellspacing="0" cellpadding="1">
        <tbody>
        <tr align="center" valign="middle">"""
	html_head=""

	def __init__(self,to,titleFrom):
		self.to=to
		self.titleFrom=titleFrom
	
	def createHtmlHeader(self,*args):
		size=len(args)
		for i in range(size):
			self.html=self.html+self.initRowHtml+args[i]+self.endRowHtml
		self.html=self.html+"</tr>"
		self.html_head=self.html+"</tr>"

	def addDataHtml(self,*args):
		size=len(args)
		self.html=self.html+"<tr>"
		for i in range(size):
			self.html=self.html+self.initRowHtmlData+str(args[i])+self.endRowHtmlData
		self.html=self.html+"</tr>"


	def send_mail(self,sender, receivers, text, html):
        	msg = MIMEMultipart('alternative')
        	msg['To'] = self.to
        	msg['From'] = email.utils.formataddr((self.titleFrom, 'soporte@cmd.com.ar'))
        	msg['Subject'] = "%s" % (text)
        	part1 = MIMEText(text, 'plain')
        	part2 = MIMEText(html, 'html')
	
        	msg.attach(part1)
        	msg.attach(part2)
	
        	try:
        	        mailServer = smtplib.SMTP('localhost')
        	        mailServer.sendmail(sender, receivers, msg.as_string())
        	        logging.info("Successfully sent email")
        	except Exception:
        	        logging.error("Error: unable to send email")
        	mailServer.close()
	def truncate(self):
		self.html=self.html_head

