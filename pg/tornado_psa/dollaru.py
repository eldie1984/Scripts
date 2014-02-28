import csv
from connexion import *
#import markdown
import os.path
import re
import tornado.auth
import tornado.httpserver
import tornado.ioloop
import tornado.options
import tornado.web
import unicodedata



from tornado.options import define, options

define("port", default=8888, help="run on the given port", type=int)

class MainHandler(tornado.web.RequestHandler):
    def get(self):
        conn=conectarse('xxxxxx','xxxxxxxx','xxxxxx')
        conn.make_sudo('xxxxxx','Sam0la\'P','xxxxxx >')
	salida=conn.get_now()
	self.render("home.html", title="My title",datos=salida)

class SYZHandler(tornado.web.RequestHandler):
    def get(self):
        salida=[]
	salida.append([1,-1,"SYZQTI","green"])
	salida.append([2,1,"SYZQTI","blue"])
	self.render("syzame.html", title="My title",datos=salida)


class SESHandler(tornado.web.RequestHandler):
    def get(self):
        sesiones=[]
	q = self.get_argument("q")
        for ses, nom in csv.reader(open("csv/sesiones.csv")):
		if q.upper() in ses :
	                sesiones.append([ses,nom,"xxxxxx"])
        if len(sesiones) > 0 :
                self.render("template.html", title="My title", datos=sesiones)
        else:
                self.write("No rows")

class UprHandler(tornado.web.RequestHandler):
    def get(self,SES):
        upr_list=[]
	sesion_list=[]
	conn=conectarse('xxxxxx','xxxxxxxx','xxxxxx')
	conn.make_sudo('xxxxxx','Sam0la\'P','xxxxxx >')
	q = self.get_argument("q")
        for u in csv.reader(open("csv/ses_upr.csv")):
		if (u[0] == SES or SES == "ALL") and (q in u[1]) :                
			conn.get_upr_task(u[1])
			upr_list.append([u[0],u[1],u[2],conn.fecha_st,conn.fecha_end,'0'])
			#print [u[0],u[1],u[2],salida[0],salida[1],salida[2]]
			for ses, nom in csv.reader(open("csv/sesiones.csv")):
		                if u[0] == ses and not ([ses,nom,"xxxxxx"] in sesion_list):
        		                sesion_list.append([ses,nom,"xxxxxx"])
	conn.close()
	logging.info(upr_list)
        if len(upr_list) > 0 :
                self.render("upr.html", title="My title", datos=upr_list, sesiones=sesion_list)
        else:
                self.write("No rows")


class Application(tornado.web.Application):
	def __init__(self):
		handlers = [
			    (r"/", MainHandler),
			    (r"/SES/", SESHandler),
			    (r"/UPR/([^/]+)",  UprHandler),
			    (r"/SYZ", SYZHandler),
			    (r'/img/(.*)', tornado.web.StaticFileHandler, {'path': 'static/img/'}),
		]
		settings = dict(
			app_title=u"Tablero PSA",
			template_path=os.path.join(os.path.dirname(__file__), "templates"),
			static_path=os.path.join(os.path.dirname(__file__), "static"),
			xsrf_cookies= False,
			cookie_secret="__TODO:_GENERATE_YOUR_OWN_RANDOM_VALUE_HERE__",
			debug=True,
		)
		tornado.web.Application.__init__(self, handlers, **settings)


def main():
	tornado.options.parse_command_line()
	http_server = tornado.httpserver.HTTPServer(Application())
	http_server.listen(options.port)
	tornado.ioloop.IOLoop.instance().start()

if __name__ == "__main__":
	main()
