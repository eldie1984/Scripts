#Datos comunes a todas las aplicaciones
#-*- coding: latin-1 -*-

import smtplib
from email.mime.text import MIMEText
import email.utils
from email.mime.multipart import MIMEMultipart
from email.MIMEImage import MIMEImage
import logging
from datetime import date
import psycopg2
from xml.dom import minidom
import urllib
import ConfigParser
from os import environ



config=ConfigParser.ConfigParser()
config.read(environ['SCRIPTS_HOME']+'/config.cfg')
PgDwHost=config.get('Datawarehouse','host')
PgDwUser=config.get('Datawarehouse','user')
PgDwPass=config.get('Datawarehouse','pass')
PgDwDbname=config.get('Datawarehouse','db')
PgDwEsquema=config.get('Datawarehouse','esquema')
nominatim=config.get('Nominatim','url')
entorno=config.get('Entorno','entorno')




# Datos de envio de mails
sender=['alarmas.mpf.gov.ar']
barrios={"MONSERRAT":"2001010002",
"PUERTO MADERO":"2001010003",
"RETIRO":"2001010004",
"SAN TELMO":"2001010006",
"RECOLETA":"2002010001",
"BALVANERA":"2003010001",
"BARRACAS":"2004010001",
"LA BOCA":"2004010002",
"NUEVA POMPEYA":"2004010003",
"PARQUE PATRICIOS":"2004010004",
"ALMAGRO":"2005010001",
"BOEDO":"2005010002",
"CABALLITO":"2006010001",
"FLORES":"2007010001",
"PARQUE CHACABUCO":"2007010002",
"VILLA LUGANO":"2008010001",
"VILLA RIACHUELO":"2008010002",
"VILLA SOLDATI":"2008010003",
"LINIERS":"2009010001",
"MATADEROS":"2009010002",
"PARQUE AVELLANEDA":"2009010003",
"FLORESTA":"2010010001",
"MONTE CASTRO":"2010010002",
u'V\xc9LEZ SARSFIELD':"2010010003",
"VERSALLES":"2010010004",
"VILLA LURO":"2010010005",
"VILLA REAL":"2010010006",
"VILLA DEL PARQUE":"2011010001",
"VILLA DEVOTO":"2011010002",
"VILLA GENERAL MITRE":"2011010003",
"VILLA SANTA RITA":"2011010004",
"COGHLAN":"2012010001",
"SAAVEDRA":"2012010002",
"VILLA URQUIZA":"2012010004",
"BELGRANO":"2013010001",
"COLEGIALES":"2013010002",
u'N\xda\xd1EZ':"2013010003",
u'SAN CRIST\xd3BAL':"2003010002",
u'VILLA PUEYRRED\xd3N':"2012010003",
"PALERMO":"2014010001",
"CHACARITA":"2015010002",
"PARQUE CHAS":"2015010003",
"LA PATERNAL":"2015010004",
"VILLA CRESPO":"2015010005",
u'VILLA ORT\xdaZAR':"2015010006",
u'AGRONOM\xcdA':"2015010001",
u'CONSTITUCI\xd3N':"2001010001",
u'SAN NICOL\xc1S':"2001010005"}
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

    def __init__(self,to,titleFrom):
        self.to=to
        self.titleFrom=titleFrom

    def createHtmlHeader(self,*args):
        size=len(args)
        for i in range(size):
            self.html=self.html+self.initRowHtml+args[i]+self.endRowHtml
        self.html=self.html+"</tr>"

    def addDataHtml(self,*args):
        size=len(args)
        self.html=self.html+"<tr>"
        for i in range(size):
            self.html=self.html+self.initRowHtmlData+str(args[i])+self.endRowHtmlData
        self.html=self.html+"</tr>"
    

    def send_mail(self,sender, receivers, text, html, path='FALSE'):
        msg = MIMEMultipart('related')
        msg_alt = MIMEMultipart('alternative')
        msg.attach(msg_alt)
        msg['To'] = self.to
        msg['From'] = email.utils.formataddr((self.titleFrom, 'alarmas@mpf.gov.ar'))
        
        msg['Subject'] = "%s" % (text)
        part1 = MIMEText(text, 'plain')
        part2 = MIMEText(html, 'html')
  
        msg_alt.attach(part1)
        msg_alt.attach(part2)
        fp = open(environ['SCRIPTS_HOME']+'/logo_firma.png', 'rb')
        msgImage = MIMEImage(fp.read())
        fp.close()
        msgImage.add_header('Content-ID', '<logo>')
        msg.attach(msgImage)

        if (path != 'FALSE'):
            fp = open(path, 'rb')
            msgImage = MIMEImage(fp.read())
            fp.close()
            # Define the image's ID as referenced above
            msgImage.add_header('Content-ID', '<image1>')
            msg.attach(msgImage)

  
        try:
            mailServer = smtplib.SMTP('localhost')
            mailServer.sendmail(sender, receivers, msg.as_string())
            logging.info("Successfully sent email")
        except Exception:
            logging.error("Error: unable to send email")
            mailServer.close()

def fifteenth_day_of_month(d):
    return date(d.year, d.month, 15)
    
def first_day_of_month(d):
    return date(d.year, d.month, 1)

def coonectPg():
#########################################################
#
#   Se realiza un chequeo de la conexion a la base de   #
#   datos y en caso que de error se aborta el script    #
#   y se emite un mensaje de error
#
########################################################
  try:
    cnxn = psycopg2.connect("dbname=%s user=%s password=%s host=%s" %(PgDwDbname,PgDwUser,PgDwPass,PgDwHost))
    logging.debug("Se realizo correctamente la conexion a la base")
    
  except Exception,e:
    logging.error("No se pudo realizar la conexion a la base")
    logging.error(str(e))
    exit(2) 
  return cnxn



def commitQuery (query):
  cnxn=coonectPg()
  cur = cnxn.cursor()
  logging.debug("Se genero correctamente el cursor")
  logging.info("Se comienza con la correccion de datos")
  try:
    logging.debug("Se ejecuta el script :\n %s" % query)
    cur.execute("SET search_path = %s;" % PgDwEsquema)
    cur.execute(query)
    cnxn.commit()
  except Exception,e:
    logging.error("No se ejecuta el script :\n ")
    logging.error(str(e))
  logging.info("Se finalizo la correccion de datos")
  cur.close()
  cnxn.close
  return 

def executeQuery (query):
  cnxn=coonectPg()
  cur = cnxn.cursor()
  try:
    logging.debug("Se ejecuta el script :\n %s" % query)
    cur.execute("SET search_path = %s;" % PgDwEsquema)
    cur.execute(query)
    rows=cur.fetchall()
  except Exception,e:
    logging.error("No se ejecuta el script :\n %s" % query)
    logging.error(str(e))
    exit(2)
  logging.info("Se ejecuto la captura de datos")
  cur.close()
  cnxn.close
  return rows


def getBarrio(lat,lng,alt=10):
  #url_str ='https://nodejs-prod-dac.mpf.gov.ar/obtener_direccion' 
  # url_str =nodejs+'/obtener_direccion' 
  # values = {'lat' : lat, 'lon'  : lng}
  # data = urllib.urlencode(values)
  # req = urllib2.Request(url_str, data,{'Content-Type':'application/json'})
  # jsonDump = json.dumps(values)
  # logging.debug(url_str)
  # resp_str = urllib2.urlopen(req,jsonDump).read()
  # resp_dict=json.loads(resp_str)
  # print resp_dict['resp_dict']
  url_str =nominatim+'reverse.php?format=xml&lat=%s&lon=%s&zoom=%s&addressdetails=1' %(lat,lng,alt)
  logging.debug(url_str)
  xml_str = urllib.urlopen(url_str).read()
  xmldoc = minidom.parseString(xml_str)
  city_district = xmldoc.getElementsByTagName('city_district')
  suburb = xmldoc.getElementsByTagName('suburb')
  if len(suburb) > 0:
    obs_values=suburb[0].firstChild.nodeValue.upper()
    logging.debug(obs_values)
  else:
    if len(city_district) > 0:
      obs_values=city_district[0].firstChild.nodeValue.upper()
      logging.debug(obs_values)
    else:
      logging.warning("falla web : %s" % url_str)
      return 0
  return barrios[obs_values]

#  return '0'

def getBarrioNom(lat,lng,alt=12):
  url_str ='http://nominatim.openstreetmap.org/reverse.php?format=xml&lat=%s&lon=%s&zoom=%s&addressdetails=1' %(lat,lng,alt)
  logging.debug(url_str)
  xml_str = urllib.urlopen(url_str).read()
  xmldoc = minidom.parseString(xml_str)
  city_district = xmldoc.getElementsByTagName('city_district')
  suburb = xmldoc.getElementsByTagName('suburb')
  if len(suburb) > 0:
    obs_values=suburb[0].firstChild.nodeValue.upper()
    logging.debug(obs_values)
  else:
    if len(city_district) > 0:
      obs_values=city_district[0].firstChild.nodeValue.upper()
      logging.debug(obs_values)
    else:
      logging.warning("falla web : %s" % url_str)
      obs_values='error'
  return barrios[obs_values]
