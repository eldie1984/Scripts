import tornado.ioloop
import tornado.web
import csv

class MainHandler(tornado.web.RequestHandler):
    def get(self):
		self.render("home.html", title="My title")

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
	q = self.get_argument("q")
        for u in csv.reader(open("csv/ses_upr.csv")):
		print u
		if (u[0] == SES or SES == "ALL") and (q in u[1]) :                
			upr_list.append([u[0],u[1],u[2]])
			for ses, nom in csv.reader(open("csv/sesiones.csv")):
		                if u[0] == ses and not [ses,nom] in sesion_list:
        		                sesion_list.append([ses,nom,"yvasa850"])

        if len(upr_list) > 0 :
                self.render("upr.html", title="My title", datos=upr_list, sesiones=sesion_list)
        else:
                self.write("No rows")


application = tornado.web.Application([
    (r"/", MainHandler),
    (r"/SES/", SESHandler),
    (r"/UPR/([^/]+)",  UprHandler),
])

if __name__ == "__main__":
	application.listen(8888)
	tornado.ioloop.IOLoop.instance().start()
