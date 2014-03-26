import tornado.ioloop
import tornado.web
import csv
from connexion import *

class MainHandler(tornado.web.RequestHandler):
    def get(self):
        conn=conectarse('yvasa850','e449806','Ema84nue')
        conn.make_sudo('mwpsyz01','Sam0la\'P','mwpsyz01 >')
	salida=conn.get_now()
	self.render("home.html", title="My title",datos=salida)

class SYZHandler(tornado.web.RequestHandler):
    def get(self):
        salida=[]
	salida.append(["1","-1","SYZQTI","green"])
	self.render("syzame.html", title="My title",datos=salida)


class SESHandler(tornado.web.RequestHandler):
    def get(self):
        sesiones=[]
	q = self.get_argument("q")
        for ses, nom in csv.reader(open("csv/sesiones.csv")):
		if q.upper() in ses :
	                sesiones.append([ses,nom,"yvasa850"])
        if len(sesiones) > 0 :
                self.render("template.html", title="My title", datos=sesiones)
        else:
                self.write("No rows")

class UprHandler(tornado.web.RequestHandler):
    def get(self,SES):
        upr_list=[]
	sesion_list=[]
	conn=conectarse('yvasa850','e449806','Ema84nue')
	conn.make_sudo('mwpsyz01','Sam0la\'P','mwpsyz01 >')
	q = self.get_argument("q")
        for u in csv.reader(open("csv/ses_upr.csv")):
		if (u[0] == SES or SES == "ALL") and (q in u[1]) :                
			conn.get_upr_task(u[1])
	#		salida=['0','0','0']
			upr_list.append([u[0],u[1],u[2],conn.fecha_st,conn.fecha_end,'0'])
			#print [u[0],u[1],u[2],salida[0],salida[1],salida[2]]
			for ses, nom in csv.reader(open("csv/sesiones.csv")):
		                if u[0] == ses and not ([ses,nom,"yvasa850"] in sesion_list):
        		                sesion_list.append([ses,nom,"yvasa850"])
	conn.close()
	logging.info(upr_list)
        if len(upr_list) > 0 :
                self.render("upr.html", title="My title", datos=upr_list, sesiones=sesion_list)
        else:
                self.write("No rows")


application = tornado.web.Application([
    (r"/", MainHandler),
    (r"/SES/", SESHandler),
    (r"/UPR/([^/]+)",  UprHandler),
    (r"/SYZ", SYZHandler),
])

if __name__ == "__main__":
	application.listen(8888)
	tornado.ioloop.IOLoop.instance().start()
