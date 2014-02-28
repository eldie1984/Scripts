 # -*- coding: utf-8 -*-
import urllib
     
class enviar_datos:
     
 def conectar(self,host,campo,valor):
    self.variables=[]
    self.valores=[]
    self.campo = campo
    self.valor = valor
    self.host = host
    self.datos = {}
    for campo_variables,valor_variables in zip(self.campo.split(":"),self.valor.split(":")):
      self.variables.append(campo_variables)
      self.valores.append(valor_variables)
    for variable,valor in zip(self.variables,self.valores):
      self.datos['%s'%variable] = valor
      try:
        return urllib.urlopen(self.host,urllib.urlencode(self.datos)).read()
      except:
        return "No se puede conectar a %s"%(self.host)
     
url = raw_input("Inserta la URL ::> ")
variables = raw_input("Inserta las variables, separadas por ':' ::> ")
valores = raw_input("Inserta los valores, separados por ':' ::> ")
conec = enviar_datos()
     
print conec.conectar(url,variables,valores)